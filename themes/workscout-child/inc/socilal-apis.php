<?php
/*  Social Apis Keys Here */

define('YOUTUBE_API_KEY', 'AIzaSyDfc0jxIEzPps2mbQ3syyuKPSeQy9i5Fc8');

define('TW_OAUTH_ACCESS_TOKEN', '898482885883310080-jfdF2UYfYMR6LtKSSud3QLf793PEuDr');
define('TW_OAUTH_ACCESS_SECRET', 'uDlPXp96xtGAD8fdD6E8vPqSzPawf4hbnqGOSgD417IT7');
define('TW_CONSUMER_KEY', '7MQ3H13Jy3HGjHYrXk2y9LOxd');
define('TW_CONSUMER_SECRET', 'gMH8PbRSLOguGVkIlP1jLLRNDDIsqVDWpdcqRsXVbFzkQ77TRj');
define('TW_REDIRECT', home_url());

define('FB_APP_ID', '1886251131695070');
define('FB_APP_SECRET', 'ce6870de776471f567948f762c9be157');

function aj_fb_login(){

    check_ajax_referer( 'ajax-login-nonce', 'security' );

    $email = $_POST['email'];

    if ( is_email( $email ) ) {

        $user = get_user_by( 'email', $email );

        if( $user ) {

            wp_set_current_user( $user->ID, $user->user_login );

            wp_set_auth_cookie( $user->ID );

            $role = $user->roles[0];

            $myaccount = get_permalink( wc_get_page_id( 'myaccount' ) );

            if( $role == 'employer' || $role == 'administrator' ) {

                if(get_option( 'job_manager_job_dashboard_page_id')) {

                    $redirect = home_url().'/job-dashboard';

                } else {

                    $redirect= home_url();

                };

            } elseif ( $role == 'candidate' ) {

                @update_audience_for_user($user->ID);

                @update_finished_companies_for_user( $user->ID );

                $redirect =  home_url().'/candidate-dashboard';


            } elseif ( $role == 'customer' || $role == 'subscriber' ) {

                $redirect = $myaccount;

            } else {

                $redirect = wp_get_referer() ? wp_get_referer() : home_url();

            }

            echo json_encode( array('loggedin'=>true, 'redirect'=>$redirect) );

        }else{
            echo json_encode( array('loggedin'=>false, 'redirect'=>false) );
        }

    }

    die();
}


function ajax_login_init(){
    add_action( 'wp_ajax_nopriv_aj_fb_login', 'aj_fb_login' );
}


if (!is_user_logged_in()) {
    add_action('init', 'ajax_login_init');
}


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

function twitter_create(){

    if ( ( isset($_GET['twitter']) && $_GET['twitter'] == 'true') || ( isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'])) {

        if (!is_user_logged_in()) {

            if (!isset($_SESSION['access_token']) && !isset($_REQUEST['oauth_token'])) {

                require_once('twitteroauth.php');

                $connection = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET);

                $request_token = $connection->getRequestToken(TW_REDIRECT);

                $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
                $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

                switch ($connection->http_code){
                    case 200:
                        $url = $connection->getAuthorizeURL($token);
                        wp_redirect($url);
                        exit;
                        break;

                    default:
                        echo 'Could not connect to Twitter. Refresh the page or try again later.';
                }

            }

            if (isset($_SESSION['access_token'])){

                require_once('twitteroauth.php');

                $connection = new TwitterOAuth( TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $_SESSION['access_token'], $_SESSION['access_secret']);

                if (!$connection)
                    echo "No connection";

                $params = array('include_email' => 'true', 'include_entities' => 'false', 'skip_status' => 'true');

                $content = $connection->get('account/verify_credentials', $params);

                $email = $content -> email;

                if ( is_email( $email ) ) {

                    $user = get_user_by( 'email', $email );

                    if( $user ) {

                        wp_set_current_user( $user->ID, $user->user_login );

                        wp_set_auth_cookie( $user->ID );

                        $role = $user->roles[0];

                        $myaccount = get_permalink( wc_get_page_id( 'myaccount' ) );

                        if( $role == 'employer' || $role == 'administrator' ) {

                            if(get_option( 'job_manager_job_dashboard_page_id')) {

                                $redirect = home_url().'/job-dashboard';

                            } else {

                                $redirect= home_url();

                            };

                        } elseif ( $role == 'candidate' ) {

                            @update_audience_for_user($user->ID);

                            @update_finished_companies_for_user( $user->ID );

                            $redirect =  home_url().'/candidate-dashboard';


                        } elseif ( $role == 'customer' || $role == 'subscriber' ) {

                            $redirect = $myaccount;

                        } else {

                            $redirect = wp_get_referer() ? wp_get_referer() : home_url();

                        }

                        ?>
                        <script>

                            window.close();
                            window.opener.location.href = '<?php echo $redirect;?>';

                        </script>
                    <?php
                    }
                }else{
                    echo '<h3>Twitter API -- Rate limit exceeded</h3>';
                }?>
            <?php
            }

            if (isset($_REQUEST['oauth_token']) && isset($_SESSION['oauth_token'])){

                require_once('twitteroauth.php');

                $connection = new TwitterOAuth( TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

                $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

                $_SESSION['access_token'] = $access_token['oauth_token'];
                $_SESSION['access_secret'] = $access_token['oauth_token_secret'];

                unset($_SESSION['oauth_token']);
                unset($_SESSION['oauth_token_secret']);

                if (200 == $connection->http_code){
                    $_SESSION['status'] = 'verified';
                    echo '<script>window.location.reload();</script>';
                }
                else{
                    echo "Enter valid API Key and API Secret";
                }
            }
        }
    }

    add_action('wp_logout', 'logout_session_twitter');

    function logout_session_twitter(){
        session_destroy();
    }
}

add_action('init', 'twitter_create');

function get_twitter_followers_count( $url = null ){

    if ( !$url ) return false;

    if ( !is_twitter( $url )) return false;

    if ( !$username = get_twitter_username( $url )) return false;

    $settings = array(
        'oauth_access_token' => TW_OAUTH_ACCESS_TOKEN,
        'oauth_access_token_secret' => TW_OAUTH_ACCESS_SECRET,
        'consumer_key' => TW_CONSUMER_KEY,
        'consumer_secret' => TW_CONSUMER_SECRET
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

require_once __DIR__ . '/Facebook/autoload.php';

add_action('wp_ajax_aj_get_fb_users_count', 'aj_get_fb_users_count');

function aj_get_fb_users_count(){
    $error = '';

    $user_id = get_current_user_id();

    if ( ! $user_id ) $error .= 'User failed';

    $url = esc_url_raw( $_POST['link'] );

    $fb = new \Facebook\Facebook([
        'app_id' => FB_APP_ID,
        'app_secret' => FB_APP_SECRET,
        'default_graph_version' => 'v2.10',
    ]);

    $helper = $fb->getJavaScriptHelper();

    try {
        $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        $error .= 'Graph returned an error: ' . $e->getMessage();
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
                    $query_args = array(
                        'post_type'              => 'resume',
                        'post_status'            => 'any',
                        'posts_per_page'         => -1,
                        'fields'                 => 'ids',
                        'author'                => $user_id
                    );

                    $result = new WP_Query( $query_args );
                    $resumes = $result ->posts;

                    if ( $resumes ) {
                        foreach ( $resumes as $resume_id) {
                            update_post_meta( $resume_id, '_fb_count', $count );
                        }
                    }
                    wp_send_json_success( array( 'count' => $count ) );
                }else
                    wp_send_json_error(array('error' => $error));
            }
        }
    }
    wp_die();
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

        $fb             = get_post_meta( $resume_id, '"_facebook_link', true );

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

        if ( $fb ){
            $count =   get_post_meta( $resume_id, '_fb_count', true );
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

function update_audience_for_user( $user_id ){

    if ( !$user_id ) return;

    $user = get_userdata( $user_id );

    $role = $user->roles[0];

    if ( $role != 'candidate' )  return;


    $query_args = array(
        'post_type'              => 'resume',
        'post_status'            => 'any',
        'posts_per_page'         => -1,
        'fields'                 => 'ids',
        'author'                => $user_id
    );

    $result = new WP_Query( $query_args );
    $resumes = $result ->posts;

    if ( !$resumes )  return;

    foreach ( $resumes as $resume_id) {
        $insta_link     = get_post_meta( $resume_id, '_instagram_link', true );

        $youtube        = get_post_meta( $resume_id, '_youtube_link', true );

        $newsletter     = intval( str_replace(array('.', ','), '' ,get_post_meta( $resume_id, '_newsletter', true )) );

        $newsletter_total = intval(get_post_meta( $resume_id, '_newsletter_total', true ));

        $twitter        = get_post_meta( $resume_id, '_twitter_link', true );

        $fb             = get_post_meta( $resume_id, '_facebook_link', true );

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

        if ( $fb ){
            $count =   get_post_meta( $resume_id, '_fb_count', true );
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

        update_post_meta( $resume_id, '_audience', $audience );

    }

}


//update_audience();
function update_finished_companies_for_user( $user_id ){

    if ( !$user_id ) return;

    $user = get_userdata( $user_id );

    $role = $user->roles[0];

    if ( $role != 'candidate' )  return;

    $query_args = array(
        'post_type'              => 'resume',
        'post_status'            => 'any',
        'posts_per_page'         => -1,
        'fields'                 => 'ids',
        'author'                => $user_id
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

        update_post_meta( $resume_id, '_finished_companies', $applications -> found_posts );

    }

}


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

function create_resume_for_all_candidates(){

    $users = get_users( array('role'=>'candidate') );

    echo "<pre>";

    if ($users){

        foreach( $users as $user ){
            echo  $user->ID." ";
            $resumes = get_posts( array(
                'post_type'           => 'resume',
                'post_status'         => 'any',
                'ignore_sticky_posts' => 1,
                'posts_per_page'      => 1,
                'author'              => $user->ID

            ) );

            if ( $resumes ) {
                echo "User ". $user->ID." already have resume <br>";
            }else{
                echo  $user->ID." ";

                if ( get_user_meta( $user->ID, 'first_name', true ) &&  get_user_meta( $user->ID, 'last_name', true )){
                    $post_title = get_user_meta( $user->ID, 'first_name', true )." ".get_user_meta( $user->ID, 'last_name', true );
                }else {
                    $post_title = $user->user_nicename;
                }

                $post_content = '';

                $data = array(
                    'post_title'     => $post_title,
                    'post_content'   => $post_content,
                    'post_type'      => 'resume',
                    'comment_status' => 'closed',
                    'post_password'  => '',
                    'post_author'         => $user->ID
                );

                $data['post_status'] = 'preview';

                $resume_id = wp_insert_post( $data );

                echo "Resume for user  ". $user->ID." was created ( $resume_id )<br>";

                update_post_meta( $resume_id, '_candidate_name',get_the_title( $resume_id) );

                update_post_meta( $resume_id, '_candidate_email',( $user ->user_email ) );
            }

        }

    }
}
//create_resume_for_all_candidates();

function update_resume_for_all_candidates(){

    $users = get_users( array('role'=>'candidate') );

    echo "<pre>";

    if ($users){

        foreach( $users as $user ){
            echo  $user->ID." ";
            $resumes = get_posts( array(
                'post_type'           => 'resume',
                'post_status'         => 'preview',
                'ignore_sticky_posts' => 1,
                'posts_per_page'      => -1,
                'author'              => $user->ID

            ) );

            if ( $resumes ) {
               foreach ( $resumes as $resume){
                   echo  $resume->ID." ";
                   if (get_user_meta( $user->ID, 'website', true ))
                       update_post_meta( $resume->ID, '_influencer_website',get_user_meta( $user->ID, 'website', true) );
                   if (get_user_meta( $user->ID, 'jrrny_link', true ))
                       update_post_meta( $resume->ID, '_jrrny_link',get_user_meta( $user->ID, 'jrrny_link', true) );
                   if (get_user_meta( $user->ID, 'monthlyvisit', true ))
                       update_post_meta( $resume->ID, '_estimated_monthly_visitors',get_user_meta( $user->ID, 'monthlyvisit', true) );
                   if (get_user_meta( $user->ID, 'insta', true ))
                       update_post_meta( $resume->ID, '_instagram_link',get_user_meta( $user->ID, 'insta', true) );
                   if (get_user_meta( $user->ID, 'fb', true ))
                       update_post_meta( $resume->ID, '_facebook_link',get_user_meta( $user->ID, 'fb', true) );
                   if (get_user_meta( $user->ID, 'twitter', true ))
                       update_post_meta( $resume->ID, '_twitter_link',get_user_meta( $user->ID, 'twitter', true) );
                   if (get_user_meta( $user->ID, 'youtube', true ))
                       update_post_meta( $resume->ID, '_youtube_link',get_user_meta( $user->ID, 'youtube', true) );
                   if (get_user_meta( $user->ID, 'newsletter', true ))
                       update_post_meta( $resume->ID, '_newsletter',get_user_meta( $user->ID, 'newsletter', true) );
                   if (get_user_meta( $user->ID, 'newsletter_subscriber_count', true ))
                       update_post_meta( $resume->ID, '_newsletter_total',get_user_meta( $user->ID, 'newsletter_subscriber_count', true) );
                   if (get_user_meta( $user->ID, 'shortbio', true ))
                       update_post_meta( $resume->ID, '_portfolio_description',get_user_meta( $user->ID, 'website', true) );
                   if (get_user_meta( $user->ID, 'shortbio', true ))
                       update_post_meta( $resume->ID, '_short_influencer_bio',get_user_meta( $user->ID, 'shortbio', true) );
                   if (get_user_meta( $user->ID, 'location', true ))
                       update_post_meta( $resume->ID, '_resume_locations',get_user_meta( $user->ID, 'location', true) );
                   if (get_user_meta( $user->ID, 'website', true ))
                       update_post_meta( $resume->ID, '_influencer_number',get_user_meta( $user->ID, 'website', true) );

                   wp_update_post( array(
                       'ID'          => $resume->ID,
                       'post_status' => 'publish'
                   ) );

                   update_post_meta( $resume->ID, '_candidate_name',get_the_title( $resume->ID) );

                   update_post_meta( $resume->ID, '_candidate_email',( $user ->user_email ) );

               }
            }

        }

    }
}
//update_resume_for_all_candidates();
//die;