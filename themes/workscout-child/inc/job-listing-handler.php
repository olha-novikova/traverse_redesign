<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olga
 * Date: 24.10.17
 * Time: 12:50
 * To change this template use File | Settings | File Templates.
 */
add_action( 'init', 'add_post_status_to_job_listing', 12 );

function add_post_status_to_job_listing(){
    global $job_manager;

    register_post_status( 'pending_payment', array(
        'label'                     => __( 'Pending Payment' ),
        'public'                    => false,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => false,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>' ),
    ) );

     add_action( 'pending_payment_to_publish', array( $job_manager->post_types, 'set_expirey' ) );
}

add_filter( 'the_job_status',  'the_job_status_pending' , 10, 2 );

function the_job_status_pending( $status, $job ) {

    if ( $job->post_status == 'pending_payment' ) {
        $status = __( 'Pending Payment', 'wp-job-manager-simple-paid-listings' );
    }
    return $status;
}

add_filter( 'job_manager_valid_submit_job_statuses',  'valid_submit_job_statuses_pending' );

function valid_submit_job_statuses_pending( $status ) {

    $status[] = 'pending_payment';

    return $status;

}
add_filter( 'submit_job_steps',  'submit_job_steps_pending' , 10 );

function submit_job_steps_pending( $steps ) {

    $steps['preview']['handler'] =  'preview_handler_pending' ;

    return $steps;
}

function preview_handler_pending() {
    if ( ! $_POST ) {
        return;
    }

    $form = WP_Job_Manager_Form_Submit_Job::instance();

    if ( ! empty( $_POST['edit_job'] ) ) {
        $form->previous_step();
    }

    if ( ! empty( $_POST['continue'] ) ) {

        $job = get_post( $_POST['job_id']  );

        if ( $job->post_status == 'preview' ) {
            $update_job                  = array();
            $update_job['ID']            = $job->ID;
            $update_job['post_status']   = 'pending_payment';
            $update_job['post_author']   = get_current_user_id();

            wp_update_post( $update_job );

            $product_id = 1709;
            $found = false;

            if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
                foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
                    $_product = $values['data'];
                    if ( $_product->id == $product_id )
                        $found = true;
                }
                if ( ! $found )
                    WC()->cart->add_to_cart( $product_id );
            } else {
                WC()->cart->add_to_cart( $product_id );
            }

        }

        $form->next_step();
    }
}

add_action( 'woocommerce_add_order_item_meta','order_item_meta' , 10, 2 );

function order_item_meta( $item_id, $values ) {
    // Add the fields
    if ( isset( $values['job_id'] ) ) {
        $job = get_post( absint( $values['job_id'] ) );

        wc_add_order_item_meta( $item_id, __( 'Job Listing' ), $job->post_title );
        wc_add_order_item_meta( $item_id, '_job_id', $values['job_id'] );
    }
}

add_filter( 'woocommerce_get_item_data', 'get_item_data', 10, 2 );

function get_item_data( $data, $cart_item ) {
    if ( isset( $cart_item['job_id'] ) ) {
        $job = get_post( absint( $cart_item['job_id'] ) );

        $data[] = array(
            'name'  => __( 'Job Listing', 'wp-job-manager-wc-paid-listings' ),
            'value' => $job->post_title
        );
    }

    return $data;
}

add_filter( 'woocommerce_get_cart_item_from_session',  'get_cart_item_from_session', 10, 2 );

function get_cart_item_from_session( $cart_item, $values ) {
    if ( ! empty( $values['job_id'] ) ) {
        $cart_item['job_id'] = $values['job_id'];
    }

    return $cart_item;
}

add_action( 'woocommerce_order_status_completed', 'so_payment_complete' );

function so_payment_complete( $order_id ){

    $order = wc_get_order( $order_id );

    $order = new WC_Order( $order );

    $order_item = $order->get_items();

    foreach( $order_item as $item_id => $product ) {
        $job_id = wc_get_order_item_meta ($item_id, '_job_id');

        $job = get_post(  $job_id );

        if ( !$job || $job -> post_type != 'job_listing') return;

        if ( $job->post_status == 'pending_payment' ) {
            $update_job                  = array();
            $update_job['ID']            = $job->ID;
            $update_job['post_status']   = 'publish';

            wp_update_post( $update_job );
        }
    }
}

function done_publish_job( $job_id ) {
    wp_redirect( get_permalink( wc_get_page_id( 'checkout' ) ) );
}

add_action( 'job_manager_job_submitted', 'done_publish_job' );