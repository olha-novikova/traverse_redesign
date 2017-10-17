<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olga
 * Date: 12.09.17
 * Time: 11:20
 * To change this template use File | Settings | File Templates.
 */

function get_user_followers_count( $user_link ){

    $keys = parse_url($user_link);
    $path = explode("/", $keys['path']);
    $user = end($path);

    if ( $user ){
        $opt_name = 'jrrny_api_path';

        $opt_val = get_option( $opt_name );

        $request = wp_remote_get( $opt_val.'/get_nonce/?controller=followers&method=get_author_followers_count' );

        $response = '';

        $add_body = false;

        if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ){

            $body = json_decode( wp_remote_retrieve_body( $request ) );

            $nonce = $body->nonce;

            if ( $nonce ){

                $add_request = wp_remote_get( $opt_val.'/followers/get_author_followers_count?user='.$user );

                if ( ! is_wp_error( $add_request ) || wp_remote_retrieve_response_code( $add_request ) === 200 ){

                    $add_body = json_decode( wp_remote_retrieve_body( $add_request ) );

                    if ( $add_body->status != 'error'){

                        $response = $add_body->followers_count;

                    }

                }else
                    return false;

            }else
                return false;

        }else
            return false;
    }else
        return false;

    return $response;

}