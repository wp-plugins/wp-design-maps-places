<?php
/**
 * @package WP Design Maps & Places
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

$markers = array();

if ( !function_exists('wpdmp_plugin_menu') ):
	function wpdmp_plugin_menu() {
		//$hook = 
		add_menu_page(__('Maps & Places','wp-design-maps-and-places'), __('Maps & Places','wp-design-maps-and-places'), 'publish_posts', 'wpdmp_admin', 'wpdmp_admin_view');
		add_submenu_page( 'wpdmp_admin', __('WP Design Maps & Places - Map manager','wp-design-maps-and-places'), __('Map manager','wp-design-maps-and-places'), 'publish_posts', 'wpdmp_map_manager', 'wpdmp_map_manager_view');      		
		add_submenu_page( 'wpdmp_admin', __('WP Design Maps & Places - Settings','wp-design-maps-and-places'), __('Settings','wp-design-maps-and-places'), 'publish_posts', 'wpdmp_settings', 'wpdmp_settings_view');
		//add_action('admin_print_styles-'.$hook, 'wpdmp_admin_script');			
	}
endif;

if ( !function_exists('wpdmp_admin_script') ):
	function wpdmp_admin_script() {		            
		wp_enqueue_style('wpdmp-styles', WPDMP_PLUGIN_URL . 'css/wpdmp.css', false, WPDMP_VERSION, "screen");
		wp_enqueue_script('json2' );
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_style ('wp-jquery-ui-dialog');
		//wp_enqueue_style ('jquery-ui-tabs');
		wp_enqueue_script('wpdmp-coords', WPDMP_PLUGIN_URL . 'js/wpdmp-coords.js', array(), false, true);
		wp_register_script('imagesloaded', WPDMP_PLUGIN_URL . 'imagesloaded-master/jquery.imagesloaded.min.js', array(), false, false);
		wp_enqueue_script('wpdmp_map_manager', WPDMP_PLUGIN_URL . 'js/wpdmp-map-manager.js',array('wpdmp-common'), false, false);
		wp_enqueue_script('wpdmp-common', WPDMP_PLUGIN_URL . 'js/wpdmp-common.js',array('jquery-ui-dialog','imagesloaded','wpdmp-coords'), false, false);
		//needed for tabs...
		wp_enqueue_style('wpdmp-tabs-styles', WPDMP_PLUGIN_URL . "css/ui-tabs.css", false, WPDMP_VERSION, "screen");
		
		//for multilanguage support in javascript messages
		wpdmp_localize_scripts();
		
		if ($_GET['page'] != 'wpdmp_settings' && strpos($_GET['page'],'wpdmp_')===0){
			
			wp_enqueue_script('google-map-api', 'http://maps.google.com/maps/api/js?sensor=false', array(), false, false);
			wp_enqueue_script('wpdmp-js3', WPDMP_PLUGIN_URL . 'js/MathLib.min.js', array(), false, true);
			wp_enqueue_media();        
		} 
		if ($_GET['page']=='wpdmp_settings'){
			wp_enqueue_script('wpdmp-settings', WPDMP_PLUGIN_URL . 'js/wpdmp-map-settings.js',array('wpdmp-common'), false, false);		
		
		}else if ($_GET['page']=='wpdmp_map_manager'){
			wp_enqueue_script('jquery-ui-droppable');
			wp_enqueue_script('jquery-ui-slider');
		}
}
endif;

//add_action('wp_head', 'wpdmp_front_script');
if ( !function_exists('wpdmp_front_script') ):
	function wpdmp_front_script() {
	    wp_enqueue_script ('jquery');	
	    //wp_enqueue_script('jq-ui','code.jquery.com/ui/1.10.4/jquery-ui.js');
	    wp_enqueue_script( 'json2' );
		wp_enqueue_style('wpdmp-front-styles', WPDMP_PLUGIN_URL . 'css/wpdmp.css', false, WPDMP_VERSION, "screen");	
		wp_register_script('imagesloaded', WPDMP_PLUGIN_URL . 'imagesloaded-master/jquery.imagesloaded.min.js', true);
		wp_enqueue_script('wpdmp-front-js3', WPDMP_PLUGIN_URL . 'js/MathLib.min.js', array(), false, false);
		wp_enqueue_script('wpdmp-front-js1', WPDMP_PLUGIN_URL . 'js/wpdmp-common.js', array('imagesloaded'), false, false);
}
endif;

if ( !function_exists('wpdmp_ajaxurl') ):
function wpdmp_ajaxurl() {
   ?>
		<script type="text/javascript">
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
		</script>
   <?php
}
endif;

if ( !function_exists('wpdmp_admin_view') ):
	function wpdmp_admin_view() {
		wpdmp_print_popup_dialog();
		wpdmp_print_marker_manager_view();		
		?>
		<script type="text/javascript">						
			reload_map(jQuery('#mappath option:selected').val(),'map',true,'backend_marker_manager');						
		</script>
<?php 
		wpdmp_print_css_and_effects();
	}
endif;

if ( !function_exists('wpdmp_map_manager_view') ):
	function wpdmp_map_manager_view() {
		wpdmp_print_popup_dialog();
	   //$mapid = $_GET['mapid'];
		$maps = wpdmp_get_maps();
	   //load first found map as default
	   if ($mapid == ''){  			   
	      $map = $maps[0];
	      $mapid = $maps[0]['id'];
	   }else{
	      $map = wpdmp_get_map($mapid);            			   
	   }
	   $map_name = $map['map'];
	   $map_path = $map['map'];//print_map_url($map_name);
	   ?>
	
	<div class="wrap">
	
	<div id="col-right">
		<h1><?php _e('Preview map','wp-design-maps-and-places'); ?></h1>
	<?php 
	   wpdmp_print_map_manager($mapid,'backend_map_manager');
	   ?>
	   </div>
	   <div id="col-left" style="width:33%">
			<?php 
			wpdmp_print_maps_available($maps);
			?>
		</div>	
	</div>
		
	<div id="progressbar" style="background: url(<?php echo WPDMP_PLUGIN_URL . 'images/spin.gif';?>) center center no-repeat #fff"/></div>
<?php 
	wpdmp_print_css_and_effects();
}
endif;

/**
 * front view to be called by php code
 * e.g.: 
 * 	 wp_design_map_and_places_front(4, "en");
 * or if you use qTranslate (multilanguage) Plugin:
 *   wp_design_map_and_places_front(4, qtrans_getLanguage());
 */
if ( !function_exists('wp_design_map_and_places_front') ):
function wp_design_map_and_places_front($mapid, $lang) {
	 
	ob_start();

	wpdmp_print_map_b( $mapid, false, $lang, true);
	?>
		<script type="text/javascript">
			reload_map('<?php echo $map;?>','map-front',false,'front');	
		</script>
		<?php      
		wpdmp_print_css_and_effects();
      	$out = ob_get_contents();
      	ob_end_clean();
      	echo  $out;
	}
endif;

/**
 * front view triggered by hook [wpdmp-map ...]
 */
if ( !function_exists('wp_design_map_and_places') ):
	function wp_design_map_and_places($attr, $content = null, $code = null) {
		   
		$attrs = shortcode_atts(array('id' => '','lang' => ''), $attr);
		   
		ob_start();
      
      	wpdmp_print_map_b( $attrs['id'], false, $attrs['lang'], true);
      	?>
		<script type="text/javascript">
			reload_map('<?php echo $map;?>','map-front',false,'front');	
		</script>
		<?php      
		wpdmp_print_css_and_effects();
      	$out = ob_get_contents();
      	ob_end_clean();
      	return  $out;
	}
endif;

if ( !function_exists('wpdmp_get_map_url') ):
	function wpdmp_get_map_url() {	
	   echo WPDMP_PLUGIN_URL.'images/maps/'.$_POST['filename'];
	   die();
	}
endif;

if ( !function_exists('wpdmp_show_message') ):
	function wpdmp_show_message($message, $errormsg = false)
	{
		if (!isset($message) || $message == '') {
			return;
		}
		echo '<div id="message" class="updated fade"><p><strong>'.$message.'</strong></p></div>';
	}
endif;

if(!function_exists('wpdmp_localize_scripts')):
function wpdmp_localize_scripts() {
	$user_messages = array(
			'marker_success' => array(
					'msg'=>__('Marker successfully updated!','wp-design-maps-and-places'),
					'title'=>__('Success','wp-design-maps-and-places')
					),
			'marker_failure' => array(
					'msg'=>__('FAILURE: marker could not be updated!','wp-design-maps-and-places'),
					'title'=>__('Error','wp-design-maps-and-places')
					),
			'add_two_ref' => array(
					'msg'=>__('0 reference points are defined. You need to define 2 points before a Place can be added!','wp-design-maps-and-places'),
					'title'=>__('Add 2 reference','wp-design-maps-and-places')
					),
			'add_one_ref' => array(
					'msg'=>__('1 reference point is defined. You need to define 2 points before a Place can be added!','wp-design-maps-and-places'),
					'title'=>__('Add 1 more reference','wp-design-maps-and-places')
					),
			'remove_ref' => array(
					'msg'=>__('You can define only 2 reference points. To define a new one, remove one of the existing.','wp-design-maps-and-places'),
					'title'=>__('Remove one reference','wp-design-maps-and-places')
					),
			'check_addr' => array(
					'msg'=>__('Please check the address, Google returns: ','wp-design-maps-and-places'),
					'title'=>__('Error','wp-design-maps-and-places')
					),
			'alert_msg' => array(
					'msg'=>__('The location not found or the \'Get Coordinates\' button not pressed','wp-design-maps-and-places'),
					'title'=>__('Error','wp-design-maps-and-places')
					),
			'ref_fail' => array(
					'msg'=>__('FAILURE: Reference point could not be created!','wp-design-maps-and-places'),
					'title'=>__('Error','wp-design-maps-and-places')
					),
			'add_lang_fail' => array(
					'msg' =>__('The language code should be maximum of 2 letters. E.g. for English it could be "en".','wp-design-maps-and-places'),
					'title' =>__('Failure!','wp-design-maps-and-places')
					),	
			'popup_offset_validation_error' => array(
					'msg' => __('Please input numbers(e.g. 5, -10 etc)','wp-design-maps-and-places'),
					'title' => __('Invalid','wp-design-maps-and-places')
					)
	);
	wp_localize_script('wpdmp-common','wpdmp_popup',$user_messages);
	wp_localize_script('wpdmp-coords','wpdmp_popup',$user_messages);
	wp_localize_script('wpdmp_map_manager','wpdmp_popup',$user_messages);
	wp_localize_script('wpdmp-settings','wpdmp_popup',$user_messages);
}
endif;

/*if ( !function_exists('wpdmp_admin_head') ):
	function wpdmp_admin_head($message, $errormsg = false)
	{
		if (!isset($message) || $message == '') {
			return;
		}
		echo '<div id="message" class="updated fade"><p><strong>'.$message.'</strong></p></div>';
	}
endif;*/