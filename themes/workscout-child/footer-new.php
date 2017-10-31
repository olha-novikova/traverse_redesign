<?php
/**
 * Footer for new design
 */
?>
        </div> <!-- class wrapper-->
        <footer>
        <div class="footer-container">
            <?php   wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false ) ); ?>
        </div>
        </footer>
        <div class="post-footer">
            <?php
            $copyrights = Kirki::get_option( 'workscout', 'pp_copyrights' );
            if (function_exists('icl_register_string')) {
                icl_register_string('Copyrights in footer','copyfooter', $copyrights);
                echo icl_t('Copyrights in footer','copyfooter', $copyrights);
            } else {
                echo wp_kses($copyrights,array('br' => array(),'em' => array(),'strong' => array(),'a' => array('href' => array(),'title' => array())));
            }
            ?>
        </div>
        <?php wp_footer(); ?>
    </body><!-- BODY -->
</html><!-- HTML -->