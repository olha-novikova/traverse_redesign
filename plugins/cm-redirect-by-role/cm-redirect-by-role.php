<?php
/*
Plugin Name: Redirect Users by Role
Plugin URI:
Description: Redirects users based on their role
Version: 1.0
Author: SFNdesign, Curtis McHale
Author URI: http://sfndesign.ca
License: GPLv2 or later
*/
 
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
 
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
// TODO ?>

/**
 * Redirects users based on their role
 *
 * @since 1.0
 * @author SFNdesign, Curtis McHale
 *
 * @uses wp_get_current_user()          Returns a WP_User object for the current user
 * @uses wp_redirect()                  Redirects the user to the specified URL
 */
function cm_redirect_users_by_role() {
 
    $current_user   = wp_get_current_user();
    $role_name      = $current_user->roles[0];
 
    if ( 'employer' === $role_name ) {
        wp_redirect( 'http://traversebyjrrny.com/brandshomescreen/' );
    } // if
 
} // cm_redirect_users_by_role
add_action( 'admin_init', 'cm_redirect_users_by_role' );

function cm_redirect_users_by_role() {
 
    if ( ! defined( 'DOING_AJAX' ) ) {
 
        $current_user   = wp_get_current_user();
        $role_name      = $current_user->roles[0];
 
        if ( 'employer' === $role_name ) {
            wp_redirect( 'function cm_redirect_users_by_role() {
 
    if ( ! defined( 'DOING_AJAX' ) ) {
 
        $current_user   = wp_get_current_user();
        $role_name      = $current_user->roles[0];
 
        if ( 'subscriber' === $role_name ) {
            wp_redirect( 'http://yoursite.com/dashboard' );
        } // if $role_name
 
    } // if DOING_AJAX
 
} // cm_redirect_users_by_role
add_action( 'admin_init', 'cm_redirect_users_by_role' );' );
        } // if $role_name
 
    } // if DOING_AJAX
 
} // cm_redirect_users_by_role
add_action( 'admin_init', 'cm_redirect_users_by_role' );