<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olga
 * Date: 18.08.17
 * Time: 12:01
 * To change this template use File | Settings | File Templates.
 */
define(YOUTUBE_API_KEY, 'AIzaSyDfc0jxIEzPps2mbQ3syyuKPSeQy9i5Fc8');

/*                    YOUTUBE                             */
function is_youtube($url){
    $parse = parse_url($url);
    $host = $parse['host'];

    if (!in_array($host, array('youtube.com', 'www.youtube.com', 'youtu.be'))) {
        return false;
    }
    return true;

}

function get_youtube_id_from_url($link= '')
{
    $url = urldecode(rawurldecode($link));

    $result = preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:user|channel|c)\/))([^\?&\"'>]+)/", $url, $matches);

    if ($result) {
        $yt_id = trim($matches[1]);
        return $yt_id;
    }

    return false;
}

function is_youtube_channel($url){
    return ( preg_match("!channel/([^\?&\"'>]+)!i", $url) );
}

function is_youtube_user($url){
    return ( preg_match("!user/([^\?&\"'>]+)!i", $url) );
}

function is_confirmed_user($url){
    return ( preg_match("!c/([^\?&\"'>]+)!i", $url) );
}


function get_youtube_subscriber_count( $url = null ){

    if ( !$url ) return false;

    if ( !is_youtube( $url ) ) return false;

    $yt_id = get_youtube_id_from_url( $url );

    if ( is_youtube_channel( $url ) ){

        $api_request = urldecode("https://www.googleapis.com/youtube/v3/channels?part=statistics&id=".$yt_id."&key=".YOUTUBE_API_KEY);
        $data = wp_remote_get( $api_request );

        if ( is_array($data) && $data['response']['code'] !== 404 ) {

            $api_response = json_decode($data['body'], true);

            return $api_response['items'][0]['statistics']['subscriberCount'];

        }
    }elseif ( is_youtube_user( $url ) ){

        $api_request = urldecode("https://www.googleapis.com/youtube/v3/channels?part=statistics&forUsername=".$yt_id."&key=".YOUTUBE_API_KEY);
        $data = wp_remote_get( $api_request );

        if ( is_array($data) && $data['response']['code'] !== 404 ) {

            $api_response = json_decode($data['body'], true);

            return $api_response['items'][0]['statistics']['subscriberCount'];

        }

    } elseif ( is_confirmed_user( $url ) ){

        $api_request_for_channel = urldecode("https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=1&q=".$yt_id."&type=channel&key=".YOUTUBE_API_KEY);

        $data_for_channel = wp_remote_get( $api_request_for_channel );

        if ( is_array($data_for_channel) && $data_for_channel['response']['code'] !== 404 ) {

            $api_response_for_channel = json_decode($data_for_channel['body'], true);

            $channel_id = $api_response_for_channel['items'][0]['id']['channelId'];

            if ( $channel_id) {

                $api_request = urldecode("https://www.googleapis.com/youtube/v3/channels?part=statistics&id=".$channel_id."&key=".YOUTUBE_API_KEY);

                $data = wp_remote_get( $api_request );

                if ( is_array($data) && $data['response']['code'] !== 404 ) {

                    $api_response = json_decode($data['body'], true);

                    return $api_response['items'][0]['statistics']['subscriberCount'];

                }
            }

        }

    }

}

function aj_get_youtube_subscriber_count( ){

    $url = esc_url_raw($_POST['link']);

  //  if ( ! is_youtube($url)) wp_send_json_error( array('error'=> 'Not valid YouTube url') );

   // if ( !is_youtube_channel($url) && !is_youtube_user($url) && !is_confirmed_user($url))  wp_send_json_error( array('error'=> 'YouTube url should be a channel or user link') );

    $count = get_youtube_subscriber_count( $url );

  //  if ( !$count ) wp_send_json_error( array('error'=> 'Can\'t get number of subscribers. You must use a proper channel or user link'));

   // else
    wp_send_json_success(array('count'=>$count));

}

add_action('wp_ajax_aj_get_youtube_subscriber_count', 'aj_get_youtube_subscriber_count');
add_action('wp_ajax_nopriv_aj_get_youtube_subscriber_count', 'aj_get_youtube_subscriber_count');


/*                    INSTAGRAM                             */

define(INSTAGRAM_ACCESS_TOKEN, '1904189395.1c6b931.067e058dbf044b4096a461f283b1cccb'); //sandbox_mode
//define(INSTAGRAM_ACCESS_TOKEN, '2981334071.574a052.30a3d37b066049e08523f33a58c8755d');
function is_instagram( $url ){

    $regex = "/(?:(?:http|https):\/\/)?(?:www.)?(?:instagram.com|instagr.am)\/([A-Za-z0-9-_\.]+)/im";

    if(preg_match($regex, $url, $matches)) {
        return true;
    } else {
        return false;
    }

}

function get_instagram_username( $url ){

    $regex = "/(?:(?:http|https):\/\/)?(?:www.)?(?:instagram.com|instagr.am)\/([A-Za-z0-9-_\.]+)/im";
    preg_match($regex, $url, $matches);

    if(preg_match($regex, $url, $matches)) {
        return $matches[1];
    } else {
        return false;
    }

}

function get_instagram_user_id( $url = null ){

    if ( !$url ) return false;

    if ( !is_instagram( $url )) return false;

    if ( !$name  = get_instagram_username( $url ) ) return false;

    $data = wp_remote_get( 'https://api.instagram.com/v1/users/search?q='.$name.'&count=1&access_token='.INSTAGRAM_ACCESS_TOKEN );

    if ( is_array($data) && $data['response']['code'] !== 404 ) {

        $api_response = json_decode($data['body'], true);

        return $api_response['data'][0]['id'];

    }
}

function get_instagram_followers_count( $url = null ){

    if ( !$url ) return false;

    if ( !is_instagram( $url )) return false;

    if ( !$user_id = get_instagram_user_id( $url )) return false;

    $data = wp_remote_get( 'https://api.instagram.com/v1/users/'.$user_id.'/?access_token='.INSTAGRAM_ACCESS_TOKEN );

    if ( is_array($data) && $data['response']['code'] !== 404 ) {

        $api_response = json_decode($data['body'], true);

        return $api_response['data']['counts']['follows'];
    }
}


function aj_get_instagram_followers_count( ){

    $url = esc_url_raw($_POST['insta_link']);

    if ( !is_instagram($url)) wp_send_json_error( array('error'=> 'Not valid Instagram url') );

    if ( !get_instagram_username($url) )  wp_send_json_error( array('error'=> 'Instagram url should be a user link') );

    $user_id = get_instagram_user_id( $url );

    $count = get_instagram_followers_count( $url );

   // if ( !$count ) wp_send_json_error( array('error'=> 'Can\'t get number of subscribers. Please check URL. You must use a proper user link.'));

   // else

    if ( $count ) wp_send_json_success( array( 'count' => $count ) );

}

add_action('wp_ajax_aj_get_instagram_followers_count', 'aj_get_instagram_followers_count');
add_action('wp_ajax_nopriv_aj_get_instagram_followers_count', 'aj_get_instagram_followers_count');


/*                    TWITTER                             */

function is_twitter( $url ){

    $regex = "/http(?:s)?:\/\/(?:www\.)?twitter\.com\/([a-zA-Z0-9_]+)/i";

    if(preg_match($regex, $url, $matches)) {
        return true;
    } else {
        return false;
    }

}

function get_twitter_username( $url ){

    $regex = "/http(?:s)?:\/\/(?:www\.)?twitter\.com\/([a-zA-Z0-9_]+)/i";

    if(preg_match($regex, $url, $matches)) {
        return $matches[1];
    } else {
        return false;
    }

}
require_once('TwitterAPIExchange.php');


function get_twitter_followers_count( $url = null ){

    if ( !$url ) return false;

    if ( !is_twitter( $url )) return false;

    if ( !$username = get_twitter_username( $url )) return false;

    $settings = array(
        'oauth_access_token' => "898482885883310080-jfdF2UYfYMR6LtKSSud3QLf793PEuDr",
        'oauth_access_token_secret' => "uDlPXp96xtGAD8fdD6E8vPqSzPawf4hbnqGOSgD417IT7",
        'consumer_key' => "7MQ3H13Jy3HGjHYrXk2y9LOxd",
        'consumer_secret' => "gMH8PbRSLOguGVkIlP1jLLRNDDIsqVDWpdcqRsXVbFzkQ77TRj"
    );

    $url = 'https://api.twitter.com/1.1/users/show.json';

    $getfield = '?screen_name='.$username;

    $requestMethod = 'GET';

    $twitter = new TwitterAPIExchange($settings);
    $output =  $twitter->setGetfield($getfield)
        ->buildOauth($url, $requestMethod)
        ->performRequest();

    $rs = json_decode($output, true);

    return $rs['followers_count'];
}

function aj_get_twitter_followers_count( ){

    $url = esc_url_raw($_POST['twit_link']);

    if ( !is_twitter($url)) wp_send_json_error( array('error'=> 'Not valid twitter url') );

    if ( !get_twitter_username($url) )  wp_send_json_error( array('error'=> 'Twitter url should be a user link') );

    $count = get_twitter_followers_count( $url );

    if ( !$count ) wp_send_json_error( array('error'=> 'Can\'t get number of subscribers. Please, check url. You must use a proper user link'));

    else wp_send_json_success( array( 'count' => $count ) );

}

add_action('wp_ajax_aj_get_twitter_followers_count', 'aj_get_twitter_followers_count');
add_action('wp_ajax_nopriv_aj_get_twitter_followers_count', 'aj_get_twitter_followers_count');
