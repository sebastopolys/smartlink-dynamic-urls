<?php
// https://wpmudev.com/blog/activate-deactivate-uninstall-hooks/
namespace Smrtdu\Init;

Class pluginisActive{
    public function __construct($file){       
        register_activation_hook($file,[ $this,'smrtdu_activate'] );
       

    }

    public function smrtdu_activate(){   
        add_option( 'smrtdu_installer', 'on' );
       
    }
   
}
