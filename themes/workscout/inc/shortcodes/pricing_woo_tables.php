<?php


function workscout_pricing_woo_tables($atts, $content) {
    extract(shortcode_atts(array(
        "type" => 'color-1',
        "from_vs" => 'no'
        ), $atts));
    ob_start();
    global $wp_query;

    $job_packages = new WP_Query( array(
        'post_type'  => 'product',
        'limit'      => -1,
        'tax_query'  => array(
            array(
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => 'job_package'
            )
        )) 
    );
    

    switch ($job_packages->found_posts) {
        case 2:
            $columns = "eight";
            break;      
        case 3:
            $columns = "one-third";
            break;          
        case 4:
            $columns = "four";
            break;
        
        default:
            $columns = "one-third";
            break;
    }
    $counter = 0; ?>
    <div class="woo_pricing_tables">
    <?php
    while ( $job_packages->have_posts() ) : $job_packages->the_post(); 
            switch ($counter) {
                case '0':
                    $place_class = " alpha";
                    break;
                case $job_packages->found_posts:
                    $place_class = " omega";
                    break;
                
                default:
                    # code...
                    break;
            }
            $counter++;
            
            $job_package = get_product( get_post()->ID ); ?>
        
            <div class="plan <?php if($job_package->is_featured()) { echo "color-2 "; } else { echo "color-1 "; } echo esc_attr($columns);  echo esc_attr($place_class); ?>  column">
                <div class="plan-price">

                    <h3><?php the_title(); ?></h3>
                    <?php echo '<div class="plan-price-wrap">'.$job_package->get_price_html().'</div>'; ?>

                </div>

                <div class="plan-features">
                    <ul>
                        <?php 
                        $jobslimit = $job_package->get_limit();
                        if(!$jobslimit){
                            echo "<li>";
                             esc_html_e('Unlimited number of jobs','workscout'); 
                             echo "</li>";
                        } else { ?>
                            <li>
                                <?php esc_html_e('This plan includes ','workscout'); printf( _n( '%d job', '%s jobs', $jobslimit, 'workscout' ) . ' ', $jobslimit ); ?>
                            </li>
                        <?php } ?>
                        <li>
                            <?php esc_html_e('Jobs are posted ','workscout'); printf( _n( 'for %s day', 'for %s days', $job_package->get_duration(), 'workscout' ), $job_package->get_duration() ); ?>
                        </li>

                    </ul>
                    <?php 
                        the_content(); 
                    
                        $link   = $job_package->add_to_cart_url();
                        $label  = apply_filters( 'add_to_cart_text', esc_html__( 'Add to cart', 'workscout' ) );
                
                    ?>
                    <a href="<?php echo esc_url( $link ); ?>" class="button"><i class="fa fa-shopping-cart"></i> <?php echo esc_html($label); ?></a>
                    
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    <?php $pricing__output =  ob_get_clean();
    wp_reset_postdata();
    return $pricing__output;
}

?>