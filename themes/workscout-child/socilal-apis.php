<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olga
 * Date: 18.08.17
 * Time: 12:01
 * To change this template use File | Settings | File Templates.
 */
define('YOUTUBE_API_KEY', 'AIzaSyDfc0jxIEzPps2mbQ3syyuKPSeQy9i5Fc8');

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


function get_instagram_followers_count( $url = null ){

    if ( !$url ) return false;

    if ( !is_instagram( $url )) return false;

    if ( !($user_name = get_instagram_username($url)) )  return false;

    $response = wp_remote_get( 'https://instagram.com/'.$user_name );

    if ( is_array($response) && $response['response']['code'] !== 404 ) {

        $api_response = $response['body'];

        $arr = explode('window._sharedData = ', $api_response);

        $json = explode(';</script>', $arr[1]);

        $userArray = json_decode($json[0], true);

        $userData = $userArray['entry_data']['ProfilePage'][0]['user'];

        return $userData['followed_by']['count']; // или вот так

    }
}


function aj_get_instagram_followers_count( ){

    $url = esc_url_raw($_POST['insta_link']);

    if ( !is_instagram($url) ) wp_send_json_error( array('error'=> 'Not valid Instagram url') );

    if ( !($user_name = get_instagram_username($url)) )  wp_send_json_error( array('error'=> 'Instagram url should be a user link') );

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

/*               FaceBook                   */

require_once __DIR__ . '/inc/Facebook/autoload.php';

add_action('wp_ajax_aj_get_fb_users_count', 'aj_get_fb_users_count');

function aj_get_fb_users_count(){

    $user_id = get_current_user_id();

    if ( !$user_id )  wp_send_json_error( array('error'=> 'User not found') );

    $url = esc_url_raw( $_POST['link'] );

    $fb = new \Facebook\Facebook([
        'app_id' => '1886251131695070',
        'app_secret' => 'ce6870de776471f567948f762c9be157',
        'default_graph_version' => 'v2.10',
    ]);

    $helper = $fb->getJavaScriptHelper();
    $error = '';

    try {
        $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        $error .= 'Graph returned an error: ' ;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        $error .= 'Facebook SDK returned an error: ' . $e->getMessage();
    }

    if ( $accessToken) {
        try {
            $response_id = $fb->get('/'.$url, $accessToken);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            $error .= 'Graph returned an error: ' . $e->getMessage();
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            $error .= 'Facebook SDK returned an error: ' . $e->getMessage() ;
        }

        if ( $response_id -> isError() ) {
            $e = $response_id ->getThrownException();
            $error .= 'Error! Facebook SDK Said: ' . $e->getMessage();
        } else {
            $body = $response_id -> getDecodedBody();
            $id = $body['id'];

            try {
                $response = $fb->get('/'.$id.'?metadata=1', $accessToken);
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                $error .= 'Graph returned an error: ' . $e->getMessage();
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                $error .= 'Facebook SDK returned an error: ' . $e->getMessage();
            }

            if ( $response-> isError() ) {
                $e = $response_id ->getThrownException();
                $error .= 'Error! Facebook SDK Said: ' . $e->getMessage();
            } else {
                $body = $response -> getDecodedBody();
                $type = $body['metadata']['type'];

                switch ( $type ) {
                    case 'page':
                        $response_users = $fb->get('/'.$id.'?fields=fan_count', $accessToken);

                        if ( $response_users -> isError() ) {
                            $e = $response_id ->getThrownException();
                            $error .= 'Error! Facebook SDK Said: ' . $e->getMessage();
                        } else {
                            $body = $response_users -> getDecodedBody();
                            $count = $body['fan_count'];
                        }
                        break;

                    case 'user':
                        $response_users = $fb->get('/'.$id.'/friends', $accessToken);

                        if ( $response_users -> isError() ) {
                            $e = $response_id ->getThrownException();
                            $error .= 'Error! Facebook SDK Said: ' . $e->getMessage();
                            exit;
                        } else {
                            $body = $response_users -> getDecodedBody();
                            $count = $body['summary']['total_count'];
                        }
                        break;
                }
                if ( $error == '' && isset($count) ){
                    update_user_meta( $user_id, 'fb_subscribers_count',$count );
                    wp_send_json_success( array( 'count' => $count ) );
                }else
                    wp_send_json_error(array('error' => $error));
            }
        }
    }
    wp_die();
}


function get_fb_users_count( $user_id ){

    if ( !$user_id )  return false;

    $url = esc_url_raw( $_POST['link'] );

    $fb = new \Facebook\Facebook([
        'app_id' => '1886251131695070',
        'app_secret' => 'ce6870de776471f567948f762c9be157',
        'default_graph_version' => 'v2.10',
    ]);

    $helper = $fb->getJavaScriptHelper();
    $error = '';

    try {
        $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        $error .= 'Graph returned an error: ' ;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        $error .= 'Facebook SDK returned an error: ' . $e->getMessage();
    }

    if ( $accessToken) {
        try {
            $response_id = $fb->get('/'.$url, $accessToken);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            $error .= 'Graph returned an error: ' . $e->getMessage();
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            $error .= 'Facebook SDK returned an error: ' . $e->getMessage() ;
        }

        if ( $response_id -> isError() ) {
            $e = $response_id ->getThrownException();
            $error .= 'Error! Facebook SDK Said: ' . $e->getMessage();
        } else {
            $body = $response_id -> getDecodedBody();
            $id = $body['id'];

            try {
                $response = $fb->get('/'.$id.'?metadata=1', $accessToken);
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                $error .= 'Graph returned an error: ' . $e->getMessage();
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                $error .= 'Facebook SDK returned an error: ' . $e->getMessage();
            }

            if ( $response-> isError() ) {
                $e = $response_id ->getThrownException();
                $error .= 'Error! Facebook SDK Said: ' . $e->getMessage();
            } else {
                $body = $response -> getDecodedBody();
                $type = $body['metadata']['type'];

                switch ( $type ) {
                    case 'page':
                        $response_users = $fb->get('/'.$id.'?fields=fan_count', $accessToken);

                        if ( $response_users -> isError() ) {
                            $e = $response_id ->getThrownException();
                            $error .= 'Error! Facebook SDK Said: ' . $e->getMessage();
                        } else {
                            $body = $response_users -> getDecodedBody();
                            $count = $body['fan_count'];
                        }
                        break;

                    case 'user':
                        $response_users = $fb->get('/'.$id.'/friends', $accessToken);

                        if ( $response_users -> isError() ) {
                            $e = $response_id ->getThrownException();
                            $error .= 'Error! Facebook SDK Said: ' . $e->getMessage();
                            exit;
                        } else {
                            $body = $response_users -> getDecodedBody();
                            $count = $body['summary']['total_count'];
                        }
                        break;
                }

                if ( $error == '' && isset($count) ){
                    update_user_meta( $user_id, 'fb_subscribers_count',$count );
                    return $count ;
                }else
                    return false;
            }
        }
    }
    return false;

}


function update_audience(){

    $query_args = array(
        'post_type'              => 'resume',
        'post_status'            => 'any',
        'posts_per_page'         => -1,
        'fields'                 => 'ids'
    );

    $result = new WP_Query( $query_args );
    $resumes = $result ->posts;

    foreach ( $resumes as $resume_id) {
        $insta_link     = get_post_meta( $resume_id, '_instagram_link', true );

        $youtube        = get_post_meta( $resume_id, '_youtube_link', true );

        $newsletter     = intval( str_replace(array('.', ','), '' ,get_post_meta( $resume_id, '_newsletter', true )) );

        $newsletter_total = intval(get_post_meta( $resume_id, '_newsletter_total', true ));

        $twitter        = get_post_meta( $resume_id, '"_twitter_link', true );

        $website        = get_post_meta( $resume_id, '_influencer_website', true );

        $monthly_visitors = intval( str_replace(array('.', ','), '' ,get_post_meta( $resume_id, '_estimated_monthly_visitors', true )) );

        $jrrny_link = get_post_meta( $resume_id, '_jrrny_link', true );

        $jrrny_followers = get_user_followers_count($jrrny_link);

        $audience = 0;

        if ( $youtube ){
            $count = get_youtube_subscriber_count( $youtube );
            update_post_meta( $resume_id, '_yt_count', $count );
            $audience    += $count;
        }

        if ( $insta_link ){
            $count = get_instagram_followers_count( $insta_link );
            update_post_meta( $resume_id, '_inst_count', $count );
            $audience    +=  $count;
        }

        if ( $newsletter == 'yes' && $newsletter_total > 0 ){
            $count = $newsletter_total;
            update_post_meta( $resume_id, '_nwl_count', $count );
            $audience    += $count;
        }

        if ( $twitter ){
            $count =  get_twitter_followers_count( $twitter );
            update_post_meta( $resume_id, '_tw_count', $count );
            $audience    += $count;
        }

        if ( $website && $monthly_visitors > 0 ){
            $count =  $monthly_visitors;
            update_post_meta( $resume_id, '_mov_count', $count );
            $audience    += $count;
        }

        if ( $jrrny_link && $jrrny_followers > 0){
            $count = $jrrny_followers;
            update_post_meta( $resume_id, '_jrn_count', $count );
            $audience    +=$count;
        }
        echo $resume_id." - ".$audience."<br>";
        update_post_meta( $resume_id, '_audience', $audience );

    }

}

//update_audience();

function update_finished_companies(){

    $query_args = array(
        'post_type'              => 'resume',
        'post_status'            => 'any',
        'posts_per_page'         => -1,
        'fields'                 => 'ids'
    );

    $result = new WP_Query( $query_args );
    $resumes = $result ->posts;

    foreach ( $resumes as $resume_id) {
        $applications = new WP_Query( array(
            'post_type'      => 'job_application',
            'post_status'    => 'completed',
            'posts_per_page' => 1,
            'meta_key'       => '_resume_id',
            'meta_value'     => $resume_id,
            'fields'         => 'ids'
        ) );

        echo $resume_id." - ".$applications -> found_posts."<br>";
        update_post_meta( $resume_id, '_finished_companies', $applications -> found_posts );

    }

}


//update_finished_companies();
//die;
