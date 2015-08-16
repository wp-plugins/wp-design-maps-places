<?php
/*#########################################
#  ref points functions
#########################################*/

if ( !function_exists('wpdmp_save_ref_point') ):
	function wpdmp_save_ref_point() {		
	   global $wpdb;
	
	   $table_name = $wpdb->prefix . "wpdmp_ref_point";   
	   $rows_affected = $wpdb->insert( $table_name, 
	      array( 
			'mapid' => $_POST['mapid'],
			'address' => $_POST['address'], 
			'lat' => $_POST['lat'],
	      	'lng' => $_POST['lng'],
            'x' => $_POST['x'],
			'y' => $_POST['y']
	      ) );
	   	   
		if ($rows_affected == 1){
			echo 'created';
		}else{
			echo 'error wpdmp_save_ref_point';
		}		
		
		die();
		exit;
	}
endif;

if ( !function_exists('wpdmp_save_ref_points_callback') ):
	function wpdmp_save_ref_points_callback() {
	   		
	   global $wpdb;
		   
       $mapid = $_POST['mapid'];
       $jsonstr = str_replace("\\","",$_POST['refpoints']);
	   $refpoints = json_decode($jsonstr);	   
	   //$errors = json_last_error();
	   
	   
	   $table_name = $wpdb->prefix . "wpdmp_ref_point";   
	   
	   $wpdb->delete( $table_name, array('mapid' => $_POST['mapid']));
	   
	   foreach ($refpoints as $rp){
      	   $rows_affected = $wpdb->insert( $table_name, 
      	      array( 
      			'mapid' => $mapid,
      			'address' => $rp->address, 
      			'lat' => $rp->lat,
      	      	'lng' => $rp->lng,
                'x' => $rp->x,
      			'y' => $rp->y
            ) );
      	    if ($rows_affected != 1){
               echo 'error wpdmp_save_ref_points';
      	       die();
		       exit;    
            }
      }         
      echo 'created';
		
      die();
      exit;
   }
endif;

if ( !function_exists('wpdmp_get_ref_points') ):
   function wpdmp_get_ref_points($mapid)  {
     
      global $wpdb;
	
      $table_name = $wpdb->prefix . "wpdmp_ref_point";
      $ref_points = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE mapid=%d",$mapid), ARRAY_A);
      
      return $ref_points;
   }
endif;

if ( !function_exists('wpdmp_get_map_type') ):
function wpdmp_get_map_type($mapid)  {
	 
	global $wpdb;

	$table_name = $wpdb->prefix . "wpdmp_map";
	$map_type = $wpdb->get_results($wpdb->prepare("SELECT type FROM $table_name WHERE id=%d",$mapid),ARRAY_A);

	return $map_type[0]['type'];
}
endif;

if ( !function_exists('wpdmp_delete_ref_point') ):
	function wpdmp_delete_ref_point() {		
      global $wpdb;
	
      $table_name = $wpdb->prefix . "wpdmp_ref_point";   
      $rows_affected = $wpdb->delete( $table_name, array('id' => $_POST['id']));
	   
      if ($rows_affected == 1){
         echo 'removed';
      }else{
         echo 'error wpdmp_delete_ref_point';
      }
	  			
      die();
      exit;
   }
endif;

/*#########################################
#  maps functions
#########################################*/

if ( !function_exists('wpdmp_add_map_callback') ):
   function wpdmp_add_map_callback()  {
      global $wpdb;
	
      $table_name = $wpdb->prefix . "wpdmp_map";         
      
      $att = wp_get_attachment_image_src( $_POST['attachmentid'], 'full' );
      
      $image_not_found_error = array(
      		'code' => 'ERROR',
      		'msg' =>__('Error: image not found','wp-design-maps-and-places'));
      if (!$att){
         echo json_encode($image_not_found_error);      	
         die();
         exit; 
         
      }else{
         $mapwidth = $att[1];
         $mapheight = $att[2];
      }
      
      $rows_affected = $wpdb->insert( $table_name, 
         array( 
			'map' => $_POST['attachmentid'],
			'mapwidth' => $mapwidth, 
			'mapheight' => $mapheight
	      ) );
      $map_save_error = array(
      		'code' => 'ERROR',
      		'msg' =>__('Error: the map could not be saved (Check whether the attachment is already used)!','wp-design-maps-and-places'));
	  if ($rows_affected != 1){
	     echo json_encode($map_save_error);
	     die();
	     exit;
	  }
	      
      echo $wpdb->insert_id;
	
      die();
      exit;
   }
endif;

if ( !function_exists('wpdmp_delete_map_callback') ):
   function wpdmp_delete_map_callback()  {
     global $wpdb;
	
     $id = $_POST['mapid'];
     $table_name = $wpdb->prefix . "wpdmp_map";   
     $deleted = $wpdb->delete($table_name, 
        array( 
			'id' => $id			
	      ) );
     $map_remove_error = array(
     		'code' => 'ERROR' , 
     		'msg' => __('Error: the map could not be removed!','wp-design-maps-and-places'));
     if ($deleted != 1){
        echo json_encode($map_remove_error);
        die();
        exit;
     }
     
     $table_name = $wpdb->prefix . "wpdmp_ref_point";
     $wpdb->delete($table_name, 
        array( 
			'mapid' => $id			
	      ) );
	      
     $table_name = $wpdb->prefix . "wpdmp_marker";
     $markers = $wpdb->get_results($wpdb->prepare("SELECT id FROM $table_name WHERE mapid=%d",$id), ARRAY_N);
     $table_name = $wpdb->prefix . "wpdmp_marker_descr"; 
     foreach ($markers as $marker){
        $wpdb->delete($table_name, 
        array( 
			'markerid' => $marker[0]
	      ) );
     }

     $table_name = $wpdb->prefix . "wpdmp_marker";
     $wpdb->delete($table_name, 
        array( 
			'mapid' => $id
	      ) );
	      
	 $table_name = $wpdb->prefix . "wpdmp_map_marker";
     $wpdb->delete($table_name, 
        array( 
			'mapid' => $id
	      ) );

     $map_remove_success = array(
     		'code' => 'SUCCESS' ,
     		'msg' => __('map removed','wp-design-maps-and-places'));
	 echo json_encode($map_remove_success);
	 die();
      exit;
   }
endif;

if ( !function_exists('wpdmp_get_map') ):
   function wpdmp_get_map($id)  {
     global $wpdb;
	
     $table_name = $wpdb->prefix . "wpdmp_map";   
     $map = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d",$id), ARRAY_A);
      
     wpdmp_convertAttIDtoURL($map);
     $map['markers'] = wpdmp_get_markers_available($map['id']);
     
     return $map;
   }
endif;

if ( !function_exists('wpdmp_get_maps') ):
   function wpdmp_get_maps()  {
     global $wpdb;
	
     $table_name = $wpdb->prefix . "wpdmp_map";   
     $maps = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC", ARRAY_A);
     
     foreach ($maps as &$map){
        wpdmp_convertAttIDtoURL($map);
        $map['markers'] = wpdmp_get_markers_available($map['id']);
     }
      
     return $maps;
   }
endif;

if ( !function_exists('wpdmp_convertAttIDtoURL') ):
   function wpdmp_convertAttIDtoURL(&$map)  {
      if (is_numeric($map['map'])){
         $map['map'] = wp_get_attachment_url($map['map']);
      }else{
         $map['map'] = WPDMP_PLUGIN_URL . "/images/maps/" . $map['map'];
      }
   }
endif;

/*#########################################
#  markers functions
#########################################*/

if ( !function_exists('wpdmp_convert_marker_att_id_to_url') ):
   function wpdmp_convert_marker_att_id_to_url(&$marker)  {
      if (is_numeric($marker['markerimg'])){
      	 $marker['attid'] = $marker['markerimg'];
         $marker['markerimg'] = wp_get_attachment_url($marker['markerimg']);
      }else{
         $marker['markerimg'] = WPDMP_PLUGIN_URL . "images/markers/" . $marker['markerimg'];
      }
   }
endif;

if ( !function_exists('wpdmp_get_markers_available') ):
   function wpdmp_get_markers_available($mapid)  {
      
      global $wpdb;      
	
      $table_name = $wpdb->prefix . "wpdmp_map_marker";   
      $mrs = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE mapid = %d ORDER BY id",$mapid), ARRAY_A);
          
      foreach ($mrs as &$mr){
      	wpdmp_convert_marker_att_id_to_url($mr);
      	$a=$b;
      }
      return $mrs;
   }
endif;

if ( !function_exists('wpdmp_set_markers_available') ):
   function wpdmp_set_markers_available($mapid,$att_ids)  {
      
      global $wpdb;
	
      $used = wpdmp_get_markers_available_usage($mapid);
      $to_delete = array();
      $not_removed = array();
      $no_action = array();
      $to_add = array();
	  
      for ($i = 0; $i < count($used); $i++) {
      	//for ($j = 0; $j < count($used[$i]); $j++) {
      		if ($used[$i][1]==0){
      			if(!in_array($used[$i][0],$att_ids)){
      				$to_delete[] = $used[$i][0];
      			}else{
      				//already exists
      				$no_action[] = $used[$i][0];
      			}
      		}else{
      			if(!in_array($used[$i][0],$att_ids)){
      				//not allowed to remove
      				$not_removed[] = $used[$i][0];
      			}else{
      				//already exists
      				$no_action[] = $used[$i][0];
      			}
      		}
      }
      //to be inserted
      $to_add = array_diff($att_ids, $no_action);
      
      $table_name = $wpdb->prefix . "wpdmp_map_marker";   
      
      if (sizeof($to_delete)>0){
	      $deleted = $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE mapid=%d AND markerimg in (".implode(",",$to_delete).")",$mapid));
	      if ($deleted != sizeof($to_delete)){
	      	return array('code' => 'ERROR','msg' => __('ERROR: delete of markers failed!','wp-design-maps-and-places'));
	      }
      }
      
      foreach($to_add as &$add){
      	$insresult = $wpdb->insert($table_name, array("mapid" => $mapid, "markerimg" => $add));
      	if ($insresult != 1){
      		return array('code' => 'ERROR','msg' => __('ERROR: insert of a marker failed!','wp-design-maps-and-places'));
      	}
      }
      
      return $not_removed;
   }
endif;

if ( !function_exists('wpdmp_get_markers_available_usage') ):
   function wpdmp_get_markers_available_usage($mapid)  {
      
      global $wpdb;
	
      $map_marker = $wpdb->prefix . "wpdmp_map_marker";
      $marker = $wpdb->prefix . "wpdmp_marker";
      
      $used_markers = $wpdb->get_results($wpdb->prepare("SELECT mm.markerimg, COUNT(m.id) as cnt FROM $map_marker mm LEFT OUTER JOIN $marker m ON mm.id=m.marker WHERE mm.mapid=%d GROUP BY mm.markerimg",$mapid), ARRAY_N);
        
      return $used_markers;
   }
endif;

if ( !function_exists('wpdmp_get_map_places') ):
   function wpdmp_get_map_places($mapid)  {
      
      global $wpdb;      
	
      $marker_table = $wpdb->prefix . "wpdmp_marker";   
      $map_marker = $wpdb->prefix . "wpdmp_map_marker";   
      $mrs = $wpdb->get_results($wpdb->prepare("SELECT m.*, mm.markerimg as markerimg FROM $map_marker mm JOIN $marker_table m ON mm.id=m.marker WHERE m.mapid = %d ORDER BY m.address",$mapid), ARRAY_A);
      //SELECT * FROM $table_name ", ARRAY_A);
      
      $table_name = $wpdb->prefix . "wpdmp_marker_descr";
      
      
      foreach($mrs as &$mr){
         //$markerid = $mr['id'];
         //$mr_descs = $wpdb->get_results("SELECT lang, descr FROM $table_name WHERE markerid = $markerid", OBJECT_K);
         $mr['descr'] = wpdmp_get_place_descr($mr['id']);
         wpdmp_convert_marker_att_id_to_url($mr);
      }
      
      return $mrs;
   }
endif;

if ( !function_exists('wpdmp_get_number_of_markers_for_lang') ):
   function wpdmp_get_number_of_markers_for_lang($lang)  {
      
      global $wpdb;      
	
      $table_name = $wpdb->prefix . "wpdmp_marker_descr";   
      $mrs = $wpdb->get_row($wpdb->prepare("SELECT count(*) FROM $table_name WHERE lang = %s",$lang), ARRAY_N);
      
      return $mrs[0];
   }
endif;

if ( !function_exists('wpdmp_get_marker') ):
   function wpdmp_get_marker($id)  {
     
      global $wpdb;
	
      $map_marker = $wpdb->prefix . "wpdmp_map_marker";   
      $marker_table = $wpdb->prefix . "wpdmp_marker";
      $marker = $wpdb->get_row($wpdb->prepare("SELECT m.*, mm.markerimg as markerimg FROM $map_marker mm JOIN $marker_table m ON mm.id=m.marker WHERE m.id=%d",$id), ARRAY_A);
            
      wpdmp_convert_marker_att_id_to_url($marker);
      
      $marker['descr'] = wpdmp_get_place_descr($marker['id']);
      
      return $marker;
   }
endif;

if ( !function_exists('wpdmp_get_place_descr') ):
	function wpdmp_get_place_descr($placeid) {
		global $wpdb;
		$table_name = $wpdb->prefix . "wpdmp_marker_descr";
		$mr_descs = $wpdb->get_results($wpdb->prepare("SELECT lang, descr FROM $table_name WHERE markerid = %d",$placeid), OBJECT_K);
		
		//fill all languages which missing in DB
		$supported_langs = get_option('wpdmp_langs');
		foreach ($supported_langs as $sl){
			if (!$mr_descs[$sl]){
				$missing_lang = new stdClass();
				$missing_lang->lang = $sl;
				$missing_lang->descr = '';
				$mr_descs[$sl] = $missing_lang;
			}else{
			   $mr_descs[$sl]->descr = stripslashes($mr_descs[$sl]->descr);
			}
		}
		
		return $mr_descs;
	}
endif;

if ( !function_exists('wpdmp_add_marker_callback') ):
	function wpdmp_add_marker_callback() {	
      global $wpdb;
	
      $table_name = $wpdb->prefix . "wpdmp_marker"; 
      $rows_affected = $wpdb->insert( $table_name, 
         array( 
			'mapid' => $_POST['mapid'],
			'address' => $_POST['address'], 
			'lat' => $_POST['lat'],
	      	'lng' => $_POST['lng'],
            'marker' => $_POST['marker']
	      ) );
	      
      $mid = $wpdb->insert_id;
       	  
      if ($mid != false){      
         $table_name = $wpdb->prefix . "wpdmp_marker_descr"; 
         
         $langs = explode('#%#',$_POST['desc']);
         
         foreach ($langs as $lang){            
            $x = explode('$%$',$lang);                 
            if (count($x)==2){ //exclude last empty part of $langs            
               $rows_affected = $wpdb->insert( $table_name,
                  array(   				
      				'markerid' => $mid,
      				'descr' => $x[1],
      				'lang' => $x[0]
               ));
            }
            if ($rows_affected != 1){
            	$marker_descr_error = array(
	           		'code' => 'ERROR',
	            	'msg' => __('FAILURE: marker description could not be updated!','wp-design-maps-and-places')	
            	);
               echo json_encode($marker_descr_error);
               die();
               exit;
            }
         }
      }else{
      	$add_marker_error = array(
      			'code' => 'ERROR',
      			'msg' => __('FAILURE: marker could not be updated!','wp-design-maps-and-places')
      	);
         echo json_encode($add_marker_error);
         die();
         exit;
      }  
		
		
      $new_marker = wpdmp_get_marker($mid);
      ob_start();
      wpdmp_marker_info($new_marker);
      $html = ob_get_contents();
      ob_end_clean();
      $new_marker['html'] = $html; 
						
      echo json_encode($new_marker);
	
      die();
      exit;
   }
endif;

if ( !function_exists('wpdmp_save_marker_callback') ):
	function wpdmp_save_marker_callback() {	
      global $wpdb;
		//$markers = get_option ('wpdmp-markers');
		
		$mid = intval($_POST['id']);
		//$map = pathinfo($_POST['map'], PATHINFO_FILENAME) . '.' . pathinfo($_POST['map'], PATHINFO_EXTENSION);
		
	   $table_name = $wpdb->prefix . "wpdmp_marker_descr";
	   $wpdb->delete( $table_name, array('markerid' => $mid));
	   
	   $langs = explode('#%#',$_POST['desc']);
	   foreach ($langs as $lang){            
            $x = explode('$%$',$lang);                 
            if (count($x)==2 &&  $x[1]!=""){ //exclude last empty part of $langs            
               $rows_affected = $wpdb->insert( $table_name,
                  array(
      				'markerid' => $mid,
      				'descr' => $x[1],
      				'lang' => $x[0]
               ));
            }
            if ($rows_affected != 1){
               echo 'error wpdmp_add_marker_callback';
               die();
               exit;
            }
      }
		
      $table_name = $wpdb->prefix . "wpdmp_marker";
      $rows_affected = $wpdb->update( $table_name,
               array('marker' => $_POST['marker']),      				
               array('id' => $mid));
		
        $new_marker = wpdmp_get_marker($mid);
		ob_start();		
		wpdmp_marker_info($new_marker);
		$html = ob_get_contents();
		ob_end_clean();
		$new_marker['html'] = $html; 
						
		echo json_encode($new_marker);
	
		die();
		exit;
	}
endif;

if ( !function_exists('wpdmp_remove_marker_callback') ):
	function wpdmp_remove_marker_callback() {		
      
      global $wpdb;
	
      $markerid = $_POST['id'];
      $table_name = $wpdb->prefix . "wpdmp_marker";   
      $rows_affected = $wpdb->delete( $table_name, array('id' => $markerid));
	   
      if ($rows_affected == 1){
         $table_name = $wpdb->prefix . "wpdmp_marker_descr";
         $rows_affected = $wpdb->delete( $table_name, array('markerid' => $markerid));
      }
      
      if ($rows_affected != false){
         echo 'removed-'.$markerid;
      }else{
         echo 'error wpdmp_remove_marker_callback';
      }
      die();
      exit;
	}
endif;

if ( !function_exists('wpdmp_save_popup_offset_callback') ):
	function wpdmp_save_popup_offset_callback() {
	   global $wpdb;
	
      $mapid = $_POST['mapid'];
      $offsetx = $_POST['offsetx'];
      $offsety = $_POST['offsety'];
      $table_name = $wpdb->prefix . "wpdmp_map";
      
      $rows_affected = $wpdb->update($table_name, array( 'popupoffsetx' => $offsetx, 'popupoffsety' => $offsety ), array( 'id' => $mapid ) );
      
      $popup_offset_save_success = array(
      		'code' => 'SUCCESS', 
      		'msg' => __('popup offset saved','wp-design-maps-and-places'));
      $popup_offset_save_error = array(
      		'code' => 'ERROR', 
      		'msg' => __('Error: offset could not be saved. Check that the values are numbers (e.g. 5, -10 etc.)','wp-design-maps-and-places'));
      
      if ($rows_affected != false){
         echo json_encode($popup_offset_save_success);
      }else{
         echo json_encode($popup_offset_save_error);
      }
      die();
      exit;
	}
endif;

if ( !function_exists('wpdmp_save_popup_location_callback') ):
function wpdmp_save_popup_location_callback() {
	global $wpdb;

	$mapid = $_POST['mapid'];
	$pop_location = $_POST['val'];	
	$table_name = $wpdb->prefix . "wpdmp_map";

	$rows_affected = $wpdb->update($table_name, array( 'popuplocation' => $pop_location), array( 'id' => $mapid ) );
	
	$popup_location_save_success = array(
      		'code' => 'SUCCESS', 
      		'msg' => __('popup location saved','wp-design-maps-and-places'));
	$popup_location_save_error = array(
			'code' => 'ERROR',
			'msg' => __('Error: popup location could not be saved.','wp-design-maps-and-places'));

	if ($rows_affected != false){
		echo json_encode($popup_location_save_success);
	}else{
		echo json_encode($popup_location_save_error);
	}
	die();
	exit;
}
endif;

if ( !function_exists('wpdmp_mark_free_hand_map_callback') ):
function wpdmp_mark_free_hand_map_callback() {
	global $wpdb;
	
	$mapid = $_POST['mapid'];	
	$table_name = $wpdb->prefix . "wpdmp_map";
	
	$rows_affected = $wpdb->update($table_name, array( 'type' => 'freehand'), array( 'id' => $mapid ) );
	
	$freehand_map_success = array(
      		'code' => 'SUCCESS', 
      		'msg' => __('map marked as free hand map','wp-design-maps-and-places'));
	$freehand_map_error = array(
			'code' => 'ERROR',
			'msg' => __('Error: the map could not be saved as "Free Hand Map"','wp-design-maps-and-places'));
	
	if ($rows_affected != false){
		echo json_encode($freehand_map_success);
	}else{
		echo json_encode($freehand_map_error);
	}
	die();
	exit;
	}
endif;
?>