<?php
/**
 * to be called for every upgrade without versions check
 */
function wpdmp_install() {
   global $wpdb;   
   
   $step1 = date('Y-m-d H:i:s');
   $opt = get_option ("wpdmp_install_log");
   update_option( "wpdmp_install_log", $opt . "start create tables:" . $step1 );
   
   $maps_table_name = $wpdb->prefix . "wpdmp_map";
   $maps_sql = "CREATE TABLE $maps_table_name (
		id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
		map VARCHAR(100) DEFAULT '' NOT NULL,
		mapwidth SMALLINT DEFAULT 0 NOT NULL,
		mapheight SMALLINT DEFAULT 0 NOT NULL,
		popupstyle TEXT,
		popupoffsetx INT DEFAULT 0 NOT NULL,
		popupoffsety INT DEFAULT 0 NOT NULL,
		type VARCHAR(100) DEFAULT '' NOT NULL,
		popuplocation SMALLINT DEFAULT 0 NOT NULL,
		UNIQUE KEY id (id)
    );";
   
   $map_marker_table_name = $wpdb->prefix . "wpdmp_map_marker";
   $map_marker_sql = "CREATE TABLE $map_marker_table_name (
		id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
		mapid MEDIUMINT(9) NOT NULL,
		markerimg VARCHAR(100) DEFAULT '' NOT NULL,
		markerwidth SMALLINT DEFAULT 0 NOT NULL,
		markerheight SMALLINT DEFAULT 0 NOT NULL,
		UNIQUE KEY id (id)
    );";
   
   $ref_table_name = $wpdb->prefix . "wpdmp_ref_point";      
   $ref_sql = "CREATE TABLE $ref_table_name (
		id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
		mapid MEDIUMINT(9) NOT NULL,
		address TEXT NOT NULL,
		lat DOUBLE NOT NULL, 
		lng DOUBLE NOT NULL,
		x SMALLINT NOT NULL,
		y SMALLINT NOT NULL,		
		UNIQUE KEY id (id)
    );";
   
   $marker_table_name = $wpdb->prefix . "wpdmp_marker";      
   $marker_sql = "CREATE TABLE $marker_table_name (
		id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
		mapid MEDIUMINT(9) NOT NULL,
		address TEXT NOT NULL,
		lat DOUBLE NOT NULL, 
		lng DOUBLE NOT NULL,
		marker TEXT NOT NULL,		
		UNIQUE KEY id (id)
    );";
   
   $descr_table_name = $wpdb->prefix . "wpdmp_marker_descr";      
   $descr_sql = "CREATE TABLE $descr_table_name (
		id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
		markerid MEDIUMINT(9) NOT NULL,
		descr TEXT NOT NULL,		
		lang CHAR(2) NOT NULL,		
		UNIQUE KEY id (id)
    );";
   
   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $maps_sql );
   
   $step1 = date('Y-m-d H:i:s');
   $opt = get_option ("wpdmp_install_log");
   update_option( "wpdmp_install_log", $opt . "\r\ntable 1 done:" . $step1 );
   
   dbDelta( $map_marker_sql );
   
   $step1 = date('Y-m-d H:i:s');
   $opt = get_option ("wpdmp_install_log");
   update_option( "wpdmp_install_log", $opt . "\r\ntable 2 done:" . $step1 );
   
   dbDelta( $ref_sql );
   
   $step1 = date('Y-m-d H:i:s');
   $opt = get_option ("wpdmp_install_log");
   update_option( "wpdmp_install_log", $opt . "\r\ntable 3 done:" . $step1 );
   
   dbDelta( $marker_sql );
   
   $step1 = date('Y-m-d H:i:s');
   $opt = get_option ("wpdmp_install_log");
   update_option( "wpdmp_install_log", $opt . "\r\ntable 4 done:" . $step1 );
   
   dbDelta( $descr_sql );
   
   $step1 = date('Y-m-d H:i:s');
   $opt = get_option ("wpdmp_install_log");
   update_option( "wpdmp_install_log", $opt . "\r\ntable 5 done:" . $step1 );
 
   add_site_option( "wpdmp_version", WPDMP_VERSION );   
}

/**
 * 
 */
function wpdmp_update_options() {
   
	if (!get_option("wpdmp_langs",false)){
   		add_option( "wpdmp_langs", array('en'));
	}
	
	if (!get_option("wpdmp_default_lang",false)){
   		add_option( "wpdmp_default_lang", "en");
	}
   
	//initial css (don't change to update, add a new delta var for changes)
	$css = "/*   CSS FOR EUROPA MAP MARKER*/
#mapoverlay[mapid=\'2\'] .ctrl, #mapoverlay[mapid=\'3\'] .ctrl {
   width: 25px;
   height: 25px;
}

/*   CSS FOR EUROPA MAP POPUP*/
#mapoverlay[mapid=\'2\'] .mappopup, #mapoverlay[mapid=\'3\'] .mappopup{
    filter:alpha(opacity=80); 
    -moz-opacity:0.8; 
    opacity:0.8; 
    border: 1px solid #fff;
    border-radius: 5px;
    opacity: 0.8;
    background-color: #fff;
    color:#09384A;
    line-height:14px;
    font-size: 14px;
    font-weight: bold;
    z-index:999;	
    padding: 20px 20px 10px 20px;
    min-height: 30px;
    min-width: 80px;
    text-align: center;
    font-family: Helvetica,Arial,sans-serif;
}	

/*
  EXAMPLE OF SCALE EFFECT VIA CSS3 

#mapoverlay[mapid=\'2\'] .ctrl:hover{
    transform: scale(2);
}
#mapoverlay[mapid=\'2\'] .ctrl {
   transition: all 0.4s linear;
}
#mapoverlay[mapid=\'2\'] .mappopupwrap:hover + #mapoverlay[mapid=\'2\'] .ctrl{
    transform: scale(2);
}*/";
	
	//initial effects (don't change to update, add a new delta var for changes)
	$effects = "/* THE NAME OF THE FUNCTION MUST BE add_custom_effects() */
function add_custom_effects(){
	add_custom_effects_map(2);
	add_custom_effects_map(3);
}

function add_custom_effects_map(mapid){
	\$jq(\'#mapoverlay[mapid=\"\'+mapid+\'\"] .ctrl\').on(\'mouseenter\', function(event){
		stopAndClearQueue(\$jq(this));
		\$jq(this).attr(\"markerentered\", \"1\");
		checkZoom(this);});
	
    \$jq(\'#mapoverlay[mapid=\"\'+mapid+\'\"] .mappopup\').on(\'mouseenter\', function(event){
        var mrid = \$jq(this).parent().attr(\'mrid\');
        stopAndClearQueue(\$jq(\'#m_\'+mrid));
        \$jq(\'#m_\'+mrid).attr(\"popupentered\", \"1\");
        checkZoom(\$jq(\'#m_\'+mrid));});
   
    \$jq(\'#mapoverlay[mapid=\"\'+mapid+\'\"] .ctrl\').on(\'mouseleave\', function(event){
    	stopAndClearQueue(\$jq(this));
    	\$jq(this).attr(\"markerentered\", \"0\");
		checkZoom(this); });
    
    \$jq(\'#mapoverlay[mapid=\"\'+mapid+\'\"] .mappopup\').on(\'mouseleave\', function(event){
    	var mrid = \$jq(this).parent().attr(\'mrid\');
    	stopAndClearQueue(\$jq(\'#m_\'+mrid));
        \$jq(\'#m_\'+mrid).attr(\"popupentered\", \"0\");
        checkZoom(\$jq(\'#m_\'+mrid));});
}

function stopAndClearQueue(el){
		\$jq(el).clearQueue();
    	\$jq(el).stop();
		var mrk = \$jq(el);
        \$jq(\'.\'+mrk.attr(\'id\')+\'_mo\').clearQueue();
        \$jq(\'.\'+mrk.attr(\'id\')+\'_mo\').stop();
}

function checkZoom(el){
	if(\$jq(el).attr(\"popupentered\")==1 || \$jq(el).attr(\"markerentered\")==1){
		marker_zoom(el,true);
	}else{
		marker_zoom(el,false);
	}
} ";
	
	if (!get_option("wpdmp_css",false)){
		//initial css by first setup
		add_option( "wpdmp_css", $css);
	}else{
		//paceholder for the changes of the css
	}
	
	if (!get_option("wpdmp_effects",false)){
		//initial css by first setup
		add_option( "wpdmp_effects", $effects);
	}else{
		//paceholder for the changes of the css
	}
   
   $step1 = date('Y-m-d H:i:s');
   $opt = get_option ("wpdmp_install_log");
   update_option( "wpdmp_install_log", $opt . "\r\noptions done:" . $step1 );
}

/**
 * 
 * 
 */
function wpdmp_install_data() {
   global $wpdb;
   
   //add maps and markers only by very first activation.
   $current_version = get_site_option( 'wpdmp_version' );
  
     
	   $maps_images = get_default_images("maps",1);
	   $marker_images = get_default_images("markers",1);
	   
	   $table_name = $wpdb->prefix . "wpdmp_map";   
	   $marker_table_name = $wpdb->prefix . "wpdmp_map_marker";
	   
	   foreach ($maps_images as $file){
	   		$attachment_id = wpdmp_create_attachment_from_file($file['path']);
	   		
	   		if ($attachment_id>0){ 
		        		
		    	$map_metadata = wp_get_attachment_metadata( $attachment_id );
	    
				$rows_affected = $wpdb->insert( $table_name, 
		   					array(  'map' => $attachment_id, 
					   				'mapwidth' => $map_metadata['width'],
					   				'mapheight'=> $map_metadata['height']) );

				if ($rows_affected!=1){
					_e('Installation: error during preparing of maps!','wp-design-maps-and-places');
				}
				 
				$mapid = $wpdb->insert_id;
				
		   	    if ($file['id']==2 || $file['id']==3){
		   	       $rows_affected = $wpdb->update($table_name, array( 'popupoffsetx' => -4, 'popupoffsety' => -5 ), array( 'id' => $mapid ) );
		   	    }
		   	    
		   	    if ($rows_affected!=1){
		   	    	_e('Installation: error during preparing of maps (popups offset)!','wp-design-maps-and-places');
		   	    }
			   	
				foreach ($marker_images as $marker_file){
				         
					if ($marker_file['id'] == $file['id']){//only for the current map
				        	$attachment_id = wpdmp_create_attachment_from_file($marker_file['path']);
				        	
				        	if ($attachment_id>0){ 
				        		
				        		$image_metadata = wp_get_attachment_metadata( $attachment_id );
			    
				   				$rows_affected = $wpdb->insert( $marker_table_name, 
				   					array(  'mapid' => $mapid, 
							   				'markerwidth' => $image_metadata['width'], 
							   				'markerimg' => $attachment_id,
							   				'markerheight' => $image_metadata['height'] ) );
				   					
				   				if ($rows_affected!=1){
				   					_e('Installation: error during preparing of markers!','wp-design-maps-and-places');
				   				}
				        	}else{
				        		echo sprintf(__('Installation: error during upload of the marker %s to attachment!','wp-design-maps-and-places') , $file['path']);
				        	}
					}
				}
		   		
		     }else{
		     	echo sprintf(__('Installation: error during upload of the map %s to attachment!','wp-design-maps-and-places') , $file['path']);
		     }
	   }
}

function get_default_images($subdir, $id_index){
	
	$tmpdir = WPDMP_PLUGIN_DIR . "/images/" . $subdir . "/";
	if (is_readable($tmpdir)) {
    	if ($dir = opendir($tmpdir)) {
			$files = array();
            while ($files[] = readdir($dir));
           	sort($files);
			closedir($dir);

			$extensions = array("png", "jpg", "gif", "jpeg");      
			
			$ind = 0;
			foreach ($files as $file){
			   
	        	$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
	        	$filename = strtolower(pathinfo($file, PATHINFO_FILENAME));
	        	
	        	$step1 = date('Y-m-d H:i:s');
              $opt = get_option ("wpdmp_install_log");
              update_option( "wpdmp_install_log", $opt . "\r\n File" .$filename.":" . $step1 );
	        	
	        	$parts = split("_",$filename);

	        	//$key = array_search($file, $files);
	        	
	        	if (!in_array($ext, $extensions) ||
	        		!is_numeric($parts[$id_index])) {
	        		//unset($files[$key]);
	            	continue;
	        	}
	        	
	        	$out[$ind]['filename'] = $filename . "." . $ext;
	        	$out[$ind]['path'] = $tmpdir . $file;
	        	$out[$ind]['id'] = $parts[$id_index];
	        	$ind = $ind +1;
			}
    	}
	}
	return $out;
}

/**
 * main updgrade/install function to be called by hook. Prepared for MultiSite and standalone WP.
 * 
 * @param string $new_version
 * @param string $current_version
 * @param boolean $networkwide
 */
function wpdmp_upgrade( $new_version, $current_version, $networkwide) {
   global $wpdb;
   
   if ($new_version > $current_version){
	   if (function_exists('is_multisite') && is_multisite()) {
	   	   		
	   		$opt = get_option ("wpdmp_install_log");
	   		update_option( "wpdmp_install_log", $opt . "\r\nIt's a Multisite." );
	   	
	        if ($networkwide) {
	             $old_blog = $wpdb->blogid;
	
	            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
	            foreach ($blogids as $blog_id) {
	                switch_to_blog($blog_id);
	                wpdmp_upgrade_blog( $new_version, $current_version);
	            }
	            switch_to_blog($old_blog);
	            
	        } else{
	        	$step1 = date('Y-m-d H:i:s');
	        	$opt = get_option ("wpdmp_install_log");
	        	update_option( "wpdmp_install_log", $opt . "\r\nBlog: " . $wpdb->blogid . "\r\nInstall for one blog1:" . $step1 );
	        	wpdmp_upgrade_blog( $new_version, $current_version);
	        } 
	    }else{
	       $opt = get_option ("wpdmp_install_log");
	       update_option( "wpdmp_install_log", $opt . "\r\nIt's NOT a Multisite." );
	       wpdmp_upgrade_blog( $new_version, $current_version);
	    }
	    
	    //must be the last call
	    update_site_option( "wpdmp_version", WPDMP_VERSION );
   }
}
   
/**
 * Upgrade/install function called internally for one blog
 * 
 * @param string $new_version
 * @param string $current_version
 */
function wpdmp_upgrade_blog( $new_version, $current_version){
    global $wpdb;
	
	$step1 = date('Y-m-d H:i:s');
    $opt = get_option ("wpdmp_install_log");
    update_option( "wpdmp_install_log", $opt . "\r\nBlog: " . $wpdb->blogid . "\r\n2:" . $step1 );
        
	if ($current_version == 0 || empty($current_version)){
		//new installation for the network
        wpdmp_install();     
        wpdmp_update_options();
        wpdmp_install_data();
     }else{     	
     	//not new installation for the network
     	$maps_table_name = $wpdb->prefix . "wpdmp_map";
     	if($wpdb->get_var("SHOW TABLES LIKE '$maps_table_name'") != $maps_table_name) {
     		//new installation for the blog
     		wpdmp_install();
     		wpdmp_update_options();
     		wpdmp_install_data();
     	}else{
     		//not new installation for the blog
     		//TBD: update default data (delta) by an upgrade
     		wpdmp_install(); //update DB, no need to add default data
     		wpdmp_update_options();
     	}
     }
          
     $opt = get_option ("wpdmp_install_log");
     $step4 = date('Y-m-d H:i:s');
     update_option( "wpdmp_install_log", $opt . "\r\n4:" . $step4 );
}
?>