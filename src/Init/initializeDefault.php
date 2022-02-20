<?php

namespace Smrtdu\Init;

class initializeDefault{

    public function test(){
    add_action('admin_init',[$this,'otit']);
    }
   
    public function otit(){
        if(get_option('smrtdu_installer')==='on'){	
            global $db_version;
            $db_version = '1.0';
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        global $wpdb;

        $tablename_smartlink =  $wpdb->prefix.'smartlinks';
        $sql_smartlink = "CREATE TABLE $tablename_smartlink (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            name tinytext NOT NULL,
            text text NOT NULL,
            url varchar(55) DEFAULT '' NOT NULL,
            UNIQUE KEY id (id)
            );";  
        maybe_create_table($tablename_smartlink, $sql_smartlink );

        $tablename_smartlink_urls =  $wpdb->prefix.'smartlinks_urls';
        $sql_smartlink_urls = "CREATE TABLE $tablename_smartlink_urls (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            name tinytext NOT NULL,
            text text NOT NULL,
            url varchar(55) DEFAULT '' NOT NULL,
            UNIQUE KEY id (id)
            );";

        maybe_create_table($tablename_smartlink_urls, $sql_smartlink_urls );

        update_option('smrtdu_installer','installed');  
        add_option( 'smartlink_db_version', $jal_db_version );
        }
    }
}