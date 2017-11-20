<?php
/**
 * Template Name: Dashboard Page
 *
 * A page showing WooCommerce dashboard navigation.
 *
 *
 * @package WordPress
 * @subpackage workscout
 * @since workscout 1.0
 */

get_header(); 

    while ( have_posts() ) : the_post(); ?> 
<!-- Titlebar
================================================== -->
<?php 

$titlebar = get_post_meta( $post->ID, 'pp_page_titlebar', true ); 
$submit_job_page = get_option('job_manager_submit_job_form_page_id');
$resume_job_page = get_option('resume_manager_submit_resume_form_page_id');
if($titlebar == 'off') {
    // no titlebar
} else {
    if (!empty($submit_job_page) && is_page($submit_job_page) || !empty($resume_job_page) && is_page($resume_job_page)) { ?>
        <!-- Titlebar
        ================================================== -->
        
        <?php $header_image = get_post_meta($post->ID, 'pp_job_header_bg', TRUE); 
        if(!empty($header_image)) { ?>
            <div id="titlebar" class="photo-bg single submit-page" style="background: url('<?php echo esc_url($header_image); ?>')">
        <?php } else { ?>
            <div id="titlebar" class="single submit-page">
        <?php } ?>
            <div class="container">

                <div class="sixteen columns">
                    <h2><i class="fa fa-plus-circle"></i> <?php the_title(); ?></h2>
                </div>

            </div>
        </div>
    <?php } else { ?>
        <?php $header_image = get_post_meta($post->ID, 'pp_job_header_bg', TRUE); 
        if(!empty($header_image)) { ?>
            <div id="titlebar" class="photo-bg single" style="background: url('<?php echo esc_url($header_image); ?>')">
        <?php } else { ?>
            <div id="titlebar" class="single">
        <?php } ?>
            <div class="container">

                <div class="sixteen columns">
                    <h1><?php the_title(); ?></h1>
                    <?php if(function_exists('bcn_display')) { ?>
                    <nav id="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
                        <ul>
                            <?php bcn_display_list(); ?>
                        </ul>
                    </nav>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php 
    }
}
$layout  = get_post_meta( $post->ID, 'pp_sidebar_layout', true ); if ( empty( $layout ) ) { $layout = 'full-width'; }
$class = ($layout !="full-width") ? "eleven columns woocommerce-account" : "sixteen columns woocommerce-account"; ?>

<div class="container <?php echo esc_attr($layout); ?>">
    <article id="post-<?php the_ID(); ?>" <?php post_class($class); ?>>

        <?php do_action( 'woocommerce_before_account_navigation' ); ?>
        <nav class="woocommerce-MyAccount-navigation">
            <ul>
            <?php 

            $user = wp_get_current_user();
           
            if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) :
                $candidate_dashboard_page_id = get_option( 'resume_manager_candidate_dashboard_page_id' ); 
                printf( __( '<li><a href="%s"> Candidate Dashboard </a></li>', 'workscout' ),
                get_permalink($candidate_dashboard_page_id)
                );  
            endif;
            if ( in_array( 'employer', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) : 
                $employer_dashboard_page_id = get_option( 'job_manager_job_dashboard_page_id' );
                printf( __( '<li><a href="%s"> Employer Dashboard </a></li>', 'workscout' ),
                get_permalink($employer_dashboard_page_id)
                );
            endif;
            /*alerts*/
            $alerts_page_id = get_option( 'job_manager_alerts_page_id' ); 
            if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) || !empty($$alerts_page_id) ) :
                printf( __( '<li><a href="%s"> Job Alerts </a></li>', 'workscout' ),
                get_permalink($alerts_page_id)
                );  
            endif;
            $bookmarks_page_id = ot_get_option('pp_bookmarks_page');    
            if ( in_array( 'candidate', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) || !empty($bookmarks_page_id) ) :
                
                printf( __( '<li><a href="%s"> My Bookmarks </a></li>', 'workscout' ),
                get_permalink($bookmarks_page_id)
                );  
            endif;
            ?>
                <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
                    <li class="<?php //echo wc_get_account_menu_item_classes( $endpoint ); ?>">
                        <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php do_action( 'woocommerce_after_account_navigation' ); ?>

        <div class="woocommerce-MyAccount-content">
            <?php the_content(); ?>

            <?php
                wp_link_pages( array(
                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'workscout' ),
                    'after'  => '</div>',
                ) );
            ?>
        </div>

            <footer class="entry-footer">
                <?php edit_post_link( esc_html__( 'Edit', 'workscout' ), '<span class="edit-link">', '</span>' ); ?>
            </footer><!-- .entry-footer -->
    
            <?php
                if(get_option('pp_pagecomments','on') == 'on') {
                    
                    // If comments are open or we have at least one comment, load up the comment template
                    if ( comments_open() || get_comments_number() ) :
                        comments_template();
                    endif;
                }
            ?>
    </article>

    <?php if($layout !="full-width") { get_sidebar(); }?>

</div> <?php
    endwhile; // End of the loop. 

get_footer(); 

?>

