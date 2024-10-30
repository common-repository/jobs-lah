<?php
/** * Jobslah * */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
class Jobslah{
/**    *    */

const default_shortcode_name = 'jobslah';
//
const option_key = 'content_chunks_shortcode';


/**    * Register the shortcodes used    */
public static function register_shortcodes()    {
					//get short code user input into the field
       add_shortcode(self::default_shortcode_name, 'Jobslah::jobslah_get_jobs');
}

/**
* Returns the content of a chunk, referenced via shortcode, e.g. put the
* following in the content of a post or page:
*    [jobslah employer="3"]
*    * See http://codex.wordpress.org/Function_Reference/get_page_by_ title
*    * @param   array   $raw_args   Any arguments included in the shortcode.
*                        E.g. [get-chunk x="1" y="2"] translates to array('x'=>'1','y'=>'2')
* @param   string   $content   Optional content if the shortcode encloses content with a closing tag,
*
* @return   string   The text that should replace the shortcode.
 */
public static function jobslah_get_jobs($raw_args, $content=null)    {
		$defaults = array('employer' => '',
						'limit' => 5,
						'style'=> '');
		$sanitized_args = shortcode_atts( $defaults, $raw_args );
		if ( empty($sanitized_args['employer']) )       {
				return '';
				}
				
				//display the JobsLah widget
				 include('listall.php');
}


}/*EOF*/
