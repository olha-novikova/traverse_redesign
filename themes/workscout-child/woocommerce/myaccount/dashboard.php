<div class="section__container settings__workarea">
    <p class="form__header">Personal Information</p>
    <div class="form__inputs inputs">
    <?php
        printf(
            __( '<h2 class="my-acc-h2">Hello <strong>%1$s</strong></h2>', 'workscout' ) . ' ',
            $current_user->display_name,
            wc_get_endpoint_url( 'customer-logout', '', wc_get_page_permalink( 'myaccount' ) )
        );
    ?>

    <p class="woocommerce-dashboard-welcome">
        <?php
            echo sprintf( esc_attr__( 'From your account dashboard you can %1$sChange your password%2$s and %3$sPersonal Information%2$s.', 'workscout' ),
                '<a class="other_view" href="'. esc_url( wc_get_account_endpoint_url( 'edit-account' ) ).'?password=change' . '">',
                '</a>',
                '<a class="other_view" href="'. esc_url( wc_get_endpoint_url( 'edit-account' ) ) . '">' );
        ?>
    </p>

    <?php
        /**
         * My Account dashboard.
         *
         * @since 2.6.0
         */
        do_action( 'woocommerce_account_dashboard' );

        /**
         * Deprecated woocommerce_before_my_account action.
         *
         * @deprecated 2.6.0
         */
        do_action( 'woocommerce_before_my_account' );

        /**
         * Deprecated woocommerce_after_my_account action.
         *
         * @deprecated 2.6.0
         */
    do_action( 'woocommerce_after_my_account' );
    ?>
    </div>
</div>
