<?php

class smartlink_du_front{

	/* ~~~~~~~~~ */
	
	// user data stored in DDBB
	protected $user_data;
	// client IP
	protected $user_ip;
	// sandbox array of user data
	protected $linksand=array();
	// the final array were we store URLS and settings of metabox
	protected $link_arr=array();
	// the URL data that will be inserted in link
	protected $link_win;
	// number of URLS/data inserted in metabox
	protected $num_urls;

	/* ~~~~~~~~~ */
	
	public function smrtdu_fstart(){
		//add_action('wp', array($this,'build_link'));
		//add_action('wp', array($this,'shortcode'));
		//   set hook for shortcode
		add_shortcode( 'smartlink', array( $this, 'shortcode' ) );
		
	}	

	public function shortcode($atts, $content = null){		
		# Get post ID
		$url = get_permalink();
		$post_id=url_to_postid($url);
		# Get data from DDBB
		$metdat_post = maybe_unserialize(get_post_custom($post_id));	
		$user_data = $metdat_post['smartlink-1'];
		for ($rt=0; $rt < count($user_data); $rt++) { 
				$rest_data = maybe_unserialize($user_data[$rt]);	
			}

		# GET BACK OPS
		$rsback_ops=maybe_unserialize(get_option('back-ops'));
		$default_url=$rsback_ops[0];
		$defgt_url=$rsback_ops[1];


		// GT
		function getRealIP() {

	        if (!empty($_SERVER['HTTP_CLIENT_IP']))
	            return $_SERVER['HTTP_CLIENT_IP'];
	           
	        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	            return $_SERVER['HTTP_X_FORWARDED_FOR'];  

	        return $_SERVER['REMOTE_ADDR'];
		}

		$miip = getRealIP();	
	    //Get Country Code / Call to API
	    $url = 'http://ip-api.com/json/'.$miip;
		$codeCountry = '';
		$dataReq = array();
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($dataReq));
		$res = curl_exec($ch);
		$r_ip= json_decode($res)->query;
		
		if( $res && $r_ip!=='127.0.0.1' )
		{
			$res = json_decode($res, true);			
			$codeCountry  = $res['countryCode'];
		}
		curl_close($ch);		
		
		// DEFAULT VALUES //
		# GT Off
		$gt_match=false;
		# no gt urls
		$nogt_count=0;		
		# default URL		
		if($default_url==NULL||empty($default_url)){
			$def_arr=array(array("#","","",""));
		}
		else{
			$def_arr=array(array($default_url,"","",""));
		}		
		# default GT URL
		if($defgt_url==NULL||empty($defgt_url)){
			$defgt_arr=array(array("#","","",""));
		}
		else{
			$defgt_arr=array(array($defgt_url,"","",""));
		}	
		// END DEFAULT VALUES //


		// BUILD SMARTLINK //

		
		# how many URLS are ? = $ this->num_urls ///  clean empty urls = $ this->linksand
		for ($wap=0; $wap < count($rest_data); $wap++) { 
			if(!empty($rest_data[$wap][0])){
				array_push($this->linksand,$rest_data[$wap]);
				$this->num_urls++;
			}
		}

		# look for GT match/ if true=store in final array = this->link_arr && turn gt ON
		for ($wup=0; $wup < count($this->linksand); $wup++) { 
			# There is match / push to array	
			if($this->linksand[$wup][3]==$codeCountry){				
				array_push($this->link_arr,$this->linksand[$wup]);		
				$gt_match=true;	
				$nogt_count++;				
			}				
		}

		# There is no match / GT is still Off by default
		if($gt_match==false){
			for ($wip=0; $wip < count($this->linksand); $wip++) { 				
					if($this->linksand[$wip][3]=='false'){
					array_push($this->link_arr,$this->linksand[$wip]);	
					$nogt_count++;						
					}
			}
			# All URLS set for GT // no GT match // default GT url
			if( $nogt_count==0){				
				for ($wop=0; $wop < count($this->linksand); $wop++) { 
					# URL with no GT exists
					if(!empty($this->linksand[$wop][0])){
						$this->link_arr=$defgt_arr;				
					}
				}				
			}
			# default array / No urls entered (!)
			if(empty($this->link_arr)){
				$this->link_arr=$def_arr;
			}		
		}
		# winner URL 		
		$rnd_key=array_rand($this->link_arr);
		# Build link
		# rest_data[0]= URL
		# rest_data[1]=NoFollow
		# rest_data[2]=Target_blank
		# rest_data[3]=GeoTargeting
	

		# Build link with attrs
		// set variable for nofollow and target blank
		if($this->link_arr[$rnd_key][1]=="on"&&$this->link_arr[$rnd_key][2]=="on"){
			$smrtduchkval='target="_blank" rel="nofollow noopener noreferrer"';
		}
		elseif($this->link_arr[$rnd_key][1]=="on"&&$this->link_arr[$rnd_key][2]==""){
			$smrtduchkval='rel="nofollow"';
		}
		elseif($this->link_arr[$rnd_key][1]==""&&$this->link_arr[$rnd_key][2]=="on"){
			$smrtduchkval='target="_blank" rel="noopener noreferrer"';
		}
		else{
			$smrtduchkval='';
		}
		return '<a href="'.$this->link_arr[$rnd_key][0].'" '.$smrtduchkval.'>'.$content.'</a>';		
	# end shortcode
	}
# end class	
}