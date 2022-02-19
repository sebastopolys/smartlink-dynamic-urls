<?php
/**
 * @link              http://smartlinkdu.com/
 * @since             1.0.0
 * @package           Smartlink_Du
 * Plugin Name:       SmartLink Dynamic URLs
 * Plugin URI:        http://smartlinkdu.com/
 * Description:       Insert up to 5 URLs to a single link and change URL of link randomly each time page is loaded. Select URL to use in link depending on Geolocalization of user. Set Target_Blank and nofollow attributes to each URL
 * Name: 			  Silence Dog 
 * Version:           1.0.9
 * Author:            Sebastopolys
 * Author URI:        https://smartlinkdu.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smartlink-dynamic-urls 
 */
if(!defined('ABSPATH')){die('-1');}
	function smartlink_du_start(){
		define('IMGPATH',plugins_url());
		require plugin_dir_path( __FILE__ ).'Incs/class-smartlink-back.php';
		require plugin_dir_path( __FILE__ ).'Incs/class-smartlink-front.php';		
		if(is_admin()==true){
		$run_back=new smartlink_du_back();
		$run_back->smrtdu_bstart();}
		$run_front=new smartlink_du_front();
		$run_front->smrtdu_fstart();
	}
smartlink_du_start();