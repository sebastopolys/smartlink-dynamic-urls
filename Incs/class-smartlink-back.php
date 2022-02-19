<?php

class smartlink_du_back{	

	/* ~~~~~~~~~ */
	// plugin prefix
	protected $prefix = 'smrtdu';
	// plugin version
	protected $version = 1.1;
	// metadata of current post
	protected $post_metadata=array();
	// available countries for GT
	protected $geot = array('AR'=>'Argentina','AU'=>'Australia','BR'=>'Brasil','CA'=>'Canada','CL'=>'Chile','CN'=>'China','CO'=>'Colombia','DE'=>'Germany','ES'=>'Spain','GB'=>'United Kingdom','FR'=>'France','MX'=>'Mexico','US'=>'EEUU','IN'=>'India','IL'=>'Israel','IT'=>'Italy');
	// number of fields of Meta Box
	protected $num_links;

	/* ~~~~~~~~~ */

	function smrtdu_bstart(){		
		add_action('add_meta_boxes',array( $this,'smrtdu_add_metabox'));		
		add_action('save_post',array($this,'updat_userdata'));
		add_action('admin_menu',array($this,'smartlink_admin_menu'));	
		add_action('in_admin_header',array($this,'remove_notices'));
		add_action('init',array($this,'smartlink_back_styles'));		
		add_action('wp_loaded',array($this,'smartlink_back_scripts'));					
	}
	
	public function smartlink_back_scripts() { 
		wp_enqueue_script('infopopup',plugins_url('scripts/js/infopopup.js', __FILE__ ),array('jquery'),'1.0.0',true);
		wp_enqueue_script('infopopup');    
	}

	public function smartlink_back_styles(){		
		# admin
		if(isset($_GET['page'])){$active_page=$_GET['page'];}
		else{$active_page="";}
		if($active_page=='smartlink_admin'||$active_page=='settings'){add_action('admin_enqueue_scripts', array($this, 'back_style'));}
		#post metabox	
		add_action('admin_enqueue_scripts', array($this,'metabox_style'));
	}
	public function back_style(){
		wp_register_style( $this->prefix, plugins_url( 'scripts/CSS/back-style.css', __FILE__ ) );
		wp_enqueue_style(  $this->prefix, plugins_url( 'scripts/CSS/back-style.css', __FILE__ ));
	}
	public function metabox_style(){
		wp_register_style( $this->prefix, plugins_url( 'scripts/CSS/metabox-style.css', __FILE__ ) );
		wp_enqueue_style(  $this->prefix, plugins_url( 'scripts/CSS/metabox-style.css', __FILE__ ));
	}

	public function smartlink_admin_menu(){
		add_menu_page(
		        __( 'Smartlink DU', 'smartlink' ),
		        __( 'Smartlink DU','smartlink' ),
		        'manage_options','smartlink_admin',
		        array($this,'smartlink_admin_callback'),
		        IMGPATH.'/smartlink-dinamic-urls/assets/smartlink-icon.png'
		   			);

		add_submenu_page( 'smartlink_admin', 'Smartlink Dynamic URLs', 'Settings', 'manage_options', 
					'settings', array($this,'smartlink_admin_callback'));			
	}

	public function smartlink_admin_callback(){
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'Incs/back_ops.php';
		tweak_ops();
	}



	public function smrtdu_add_metabox() { 
    	add_meta_box( $this->prefix, __( 'Smartlink Creator ~ Meta Box', $this->prefix ), array($this,'smrtdumetabox'), 'post' ); 
	}	

	function smrtdumetabox($post){	
	#  data  BBDD


		global $post;
		# no data is saved.  NEW POST
		$met_dat_post = maybe_unserialize(get_post_custom($post->ID));		
		if(!isset($met_dat_post['smartlink-1'])){
			echo "<span id='metabox-info'>User data is NULL</span>";
			$back_ops=maybe_unserialize(get_option('back-ops'));
			
		}
		// Data is saved - GET DATA = $rest_data
		else{
			$user_data = $met_dat_post['smartlink-1'];		
			for ($rt=0; $rt < count($user_data); $rt++) { 
				$rest_data = maybe_unserialize($user_data[$rt]);
			}	
			// Filter array / delete empty fields & get number of URLs / store in $ this->post_metadata
			for ($oc=0; $oc < count($rest_data); $oc++) { 
				
				if (!empty($rest_data[$oc][0])) {
				
					array_push($this->post_metadata, $rest_data[$oc]);
				}
			}	
			
			$this->num_links=count($this->post_metadata);
			echo "<span id='metabox-info'>You have entered ".$this->num_links." URLs.</span>";
			
		}	

		
		wp_nonce_field( basename( __FILE__ ), 'smrtdunonce' );
		echo "<table id='smrtdu-metabox'>";
		?>
		 <tr>
                <th></th>
                <th>NoFollow</th>
                <th>Target blank</th>
                <th>GEOTARGETING</th>
            </tr>
		<?php	
		for ($watt=1;$watt<=5;$watt++) { 
			?>			
				<tr id="td-url-<?php echo $watt;?>">
		           <td class="table-metatxt">
		                <label for="url1"><?php _e( "URL ".$watt.": ", "smrtdu-textdomain" )?></label>
		                <input type="url" name="mt-<?php echo $watt;?>" value="<?php if (isset($rest_data)){echo $rest_data[$watt-1][0];}?>" placeholder="Enter URL <?php echo $watt;?>"/>
		            </td>
					<td class="table-metanf"> 
		    			<input type="checkbox" name="nf-<?php echo $watt;?>"<?php if(isset($rest_data)&&$rest_data[$watt-1][1]=="on"){echo ' checked="checked"';}else{}?>/>
		    		</td>
					<td class="table-metatb">
		    			<input type="checkbox" name="tb-<?php echo $watt;?>"<?php if(isset($rest_data)&&$rest_data[$watt-1][2]=="on"){echo ' checked="checked"';}else{}?>/>
		  			</td>
		  			<td class="table-meta-geot">
		  				<select name="gt-<?php echo $watt;?>">
		   					<option value="false">---</option>   
		      				<?php foreach ($this->geot as $key => $value) {echo '<option value="'.$key.'"'; if(isset($rest_data)&&$rest_data[$watt-1][3]!=='false'&&$rest_data[$watt-1][3]==$key){echo 'selected';}else{}echo '>'.$value.'</option>';}?>
		     			</select>
					</td>
				</tr><?php	
		# end meta box loop
		}
		echo '</table>';		
	# End Metabox
	}

	public function updat_userdata( $post_id ) { 		
	  
		# Check if not Auto_draft / new post / in post editor page
		$cur_scr = get_current_screen();
		if(get_post_status($post_id)!=='auto-draft'&&$cur_scr->base=='post'){

		# get data from metabox
		$fin_arr_updat = array();	
			for ($wee=1; $wee <=5; $wee++) { 
				$mt='mt-'.$wee;$nf='nf-'.$wee;$tb='tb-'.$wee;$gt='gt-'.$wee;		
					$metxt=sanitize_text_field($_POST[$mt]);
					$metnf=sanitize_key($_POST[$nf]);
					$mettb=sanitize_key($_POST[$tb]);
					$metgt=sanitize_text_field($_POST[$gt]);

					$raw_arr=array($metxt,$metnf,$mettb,$metgt);
					array_push($fin_arr_updat, $raw_arr);
			}
			global $post;
			# update data 
			maybe_serialize(update_post_meta($post->ID, "smartlink-1", $fin_arr_updat));	
		}
	# end update user data
	}    

	public function remove_notices() {	
		if(isset($_GET['page'])){
			$is_my_admin_page=$_GET['page'];
			  if (!$is_my_admin_page)return ;
			  remove_all_actions('admin_notices');
			  remove_all_actions('all_admin_notices');
		} 		
	}
# end class
}

