<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olga
 * Date: 01.09.17
 * Time: 15:03
 * To change this template use File | Settings | File Templates.
 */

 function register_influencer_payment_post_type(){
    register_post_type('influencer_payment', array(
        'labels'             => array(
            'name'               => 'Influencer payments',
            'singular_name'      => 'Influencer payment',
            'add_new'            => 'Add new',
            'add_new_item'       => 'Add new Influencer payment',
            'edit_item'          => 'Edit Influencer payment',
            'new_item'           => 'New Influencer payment',
            'view_item'          => 'View Influencer payment',
            'search_items'       => 'Find Influencer payment',
            'not_found'          => 'No Influencer payment found',
            'not_found_in_trash' => 'No Influencer payments found in trash',
            'parent_item_colon'  => '',
            'menu_name'          => 'Influencer payments'

        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 25,
        'supports'           => array('title'),
        'menu_icon'          => 'dashicons-admin-network',
    ) );
}

add_action( 'init', 'register_influencer_payment_post_type');

add_filter('manage_influencer_payment_posts_columns', 'influencer_payment_columns_head', 10);
add_action('manage_influencer_payment_posts_custom_column', 'influencer_payment_columns_content', 10, 2);


function influencer_payment_columns_head($defaults) {
    $defaults['candidate_id']   = 'Influencer ID';
    $defaults['candidate_name'] = 'Influencer Name';
    $defaults['cashout_id']     = 'Cash Out ID';
    $defaults['cashout_date']   = 'Cash Out Date';
    $defaults['cashout_summ']   = 'Cash Out Sum';
    $defaults['cashout_status'] = 'Cash Out Status';
    $defaults['cashout_type']   = 'Cash Out Type';

    return $defaults;
}

function influencer_payment_columns_content($column_name, $post_ID) {

    if ($column_name == 'candidate_id') {

        $infl_id = get_post_meta($post_ID,'candidate_id', true);

        echo $infl_id;
    }

    if ($column_name == 'candidate_name') {

        $infl_name = get_post_meta($post_ID,'candidate_name', true);

        echo $infl_name;
    }

    if ($column_name == 'cashout_id') {

        $date = get_post_meta($post_ID,'cashout_id', true);

        echo $date;
    }

    if ($column_name == 'cashout_date') {

        $date = get_post_meta($post_ID,'cashout_date', true);

        echo date('Y/m/d g:i a', $date);
    }

    if ($column_name == 'cashout_summ') {

        $pay_summ = get_post_meta($post_ID,'cashout_summ', true);

        echo $pay_summ;
    }

    if ($column_name == 'cashout_status') {

        $pay_summ = get_post_meta($post_ID,'cashout_status', true);

        echo $pay_summ;
    }

    if ($column_name == 'cashout_type') {

        $pay_summ = get_post_meta($post_ID,'cashout_type', true);

        echo $pay_summ;
    }


}