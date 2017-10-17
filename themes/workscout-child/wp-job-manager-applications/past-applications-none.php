
<p class="margin-bottom-25 margin-top-25" >
<?php _e( 'You haven\'t made any Pitches yet!', 'wp-job-manager-applications' ); ?>
</p>
<p class="margin-bottom-25 margin-top-25">
<?php $job_page = get_option('job_manager_jobs_page_id');

if( !empty($job_page) && $page_link = get_permalink($job_page) ){
?>
   <a href="<?php echo $page_link;?>" class="button">View Available Jobs</a>
<?php
}
?>
</p>