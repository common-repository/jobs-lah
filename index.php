<?php
/* Plugin Name: Jobs Lah
Plugin URI: https://www.jobslah.com/a/wp-careers
Description: Displays an employer's job listing from the Jobs Lah platform. Use the shortcode [jobslah employer=“X”]. Replace X with your Jobs Lah employer number. Optional parameters:  style="light" for light text (default is dark text) and limit="Y" where Y is the number of job listings per page (default is 5). You must have a free employer’s account and at least one job on https://www.JobsLah.com/employers.
Author: Jobs Lah Pte Ltd
Version: 0.1
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
include_once('includes/jobslah.php');
add_action('init', 'Jobslah::register_shortcodes');



/*EOF*/
