<?php function tweak_ops(){?><?php settings_errors();
	if(isset($_GET['page'])){
		$active_tab=$_GET['page'];
	}
	if($active_tab=='smartlink_admin'){
		include_once plugin_dir_path(dirname(__FILE__ )).'Incs/back_home.php';
	}elseif($active_tab=='settings'){
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'Incs/back_settings.php';
	}
	else{echo"Ups.. Something went wrong...";}}