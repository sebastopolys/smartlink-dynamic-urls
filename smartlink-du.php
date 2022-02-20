<?php
/**
 * @link              http://smartlinkdu.com/
 * @since             1.0.0
 * @package           Smartlink_Du
 * Plugin Name:       SmartLink Dynamic URLs
 * Plugin URI:        http://smartlinkdu.com/
 * Description:       A new concept of link
 * Name: 			  Silence Dog 
 * Version:           1.1.0
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
//smartlink_du_start();
require 'vendor/autoload.php';
use Smrtdu\Init\pluginisActive;
use Smrtdu\Init\initializeDefault;

if(is_admin()):
	new pluginisActive(__FILE__);
	$tee = new initializeDefault();
	$tee->test();
endif;
		