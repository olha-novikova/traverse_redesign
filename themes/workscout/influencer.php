<?php
/**
 * Template Name: login with infulancer
 *
 * @package WordPress
 * @subpackage workscout
 * @since workscout 1.0
 * style="margin-top: -95px;"
 */

get_header(); ?>            
           
<?php
while ( have_posts() ) : the_post(); ?>

<div class="container page-container home-page-container" >
    <article <?php post_class("sixteen columns"); ?>>
                <?php the_content(); ?>
    </article>
</div>
<?php endwhile;?> 

<?php
global $wpdb;

 $current_user_id= get_current_user_id();
$Id=$current_user_id= get_current_user_id();


	$sql = $wpdb->get_results("SELECT * FROM wp_usermeta where user_id='$Id'"); 
	//~ echo '<pre>';
	//~ print_r($sql);
	foreach($sql as $result){
	
	//echo 'first name'.$result->meta_value;
	//echo 'number'. $result->number;
	
	
	
  $user_id = $Id;
  $key = 'first_name';
    $key1 = 'number';
  $single = true;
 $user_last = get_user_meta( $user_id, $key, $single );
  $user_last1 = get_user_meta( $user_id, $key1, $single );
  
	if(!empty($user_last && $user_last1)){
			
			//echo"147258";
		}else{
			
			//echo"786";	
			}
	
}

?>




<?php get_footer(); ?>
