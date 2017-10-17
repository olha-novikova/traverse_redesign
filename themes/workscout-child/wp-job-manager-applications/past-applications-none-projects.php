<?php _e( 'You haven\'t made any Pitches yet!', 'wp-job-manager-applications' );

$job_page = get_option('job_manager_jobs_page_id');

if(!empty($job_page) && is_page($job_page)){
    ?>
    <a href="" class="button">View Available Jobs</a>
<?php
}
?>