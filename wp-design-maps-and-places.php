<?php
/**
 * @package WP Design Maps & Places
 */
/*
Plugin Name: WP Design Maps & Places
Plugin URI: http://amazingweb.de/
Description: Put Places on your own Map image (not on the Google Map as other plugins) 
Version: 0.6.1
Author: alexanderherdt, amazingweb-gmbh
Author URI: http://amazingweb.de/
License: GPLv2 or later
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

define('WPDMP_VERSION', '0.6');
define('WPDMP_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('WPDMP_PLUGIN_DIR', dirname(__FILE__) );

if ( !function_exists('wpdmp_load_dependencies') ):
	function wpdmp_load_dependencies() {		
	    require_once (WPDMP_PLUGIN_DIR . '/wpdmp-db.php');	    
		require_once (WPDMP_PLUGIN_DIR . '/wpdmp-functions.php');
		require_once (WPDMP_PLUGIN_DIR . '/wpdmp-admin.php');
		require_once (WPDMP_PLUGIN_DIR . '/wpdmp-settings.php');
		require_once (WPDMP_PLUGIN_DIR . '/wpdmp-calibration.php');
		require_once (WPDMP_PLUGIN_DIR . '/wpdmp-install.php');
		require_once (WPDMP_PLUGIN_DIR . '/wpdmp-attach-file.php');
	}
endif;
wpdmp_load_dependencies();

register_activation_hook( __FILE__, 'wpdmp_update_check' );
//register_activation_hook( __FILE__, 'wpdmp_install_data' );

if ( !function_exists('wpdmp_update_check') ):
   function wpdmp_update_check($network_wide) {             
   		wpdmp_upgrade(WPDMP_VERSION, get_site_option( 'wpdmp_version' ),$network_wide);       
   }
endif;

if ( !function_exists('wpdmp_add_actions') ):
	function wpdmp_add_actions() {
	   
	   //for front end
	    add_action('wp_enqueue_scripts', 'wpdmp_ajaxurl', 0);
		add_action('wp_enqueue_scripts', 'wpdmp_front_script', 0);		
		//add_action('wp_footer', 'wpdmp_print_css_and_effects',10000);
		add_shortcode('wpdmp-map', 'wp_design_map_and_places');
	   
	   //for back end
		add_action('admin_menu', 'wpdmp_admin_script');
		add_action('admin_menu', 'wpdmp_plugin_menu');
		add_action('wp_ajax_add_map', 'wpdmp_add_map_callback');
		add_action('wp_ajax_delete_map', 'wpdmp_delete_map_callback');
		add_action('wp_ajax_remove_marker', 'wpdmp_remove_marker_callback');
		add_action('wp_ajax_add_marker', 'wpdmp_add_marker_callback');
		add_action('wp_ajax_save_ref_point', 'wpdmp_save_ref_point');
		add_action('wp_ajax_delete_ref_point', 'wpdmp_delete_ref_point');
		add_action('wp_ajax_reload_map', 'wpdmp_reload_map_callback');
		add_action('wp_ajax_get_map_url', 'wpdmp_get_map_url');
		add_action('wp_ajax_edit_marker','wpdmp_edit_marker_callback');
		add_action('wp_ajax_save_marker','wpdmp_save_marker_callback');
		add_action('wp_ajax_reload_site_list','wpdmp_reload_site_list_callback');
		add_action('wp_ajax_print_maps_available','wpdmp_print_maps_available_callback');
		add_action('wp_ajax_add_lang','wpdmp_add_lang_callback');
		add_action('wp_ajax_set_default_lang','wpdmp_set_default_lang_callback');
		add_action('wp_ajax_delete_lang','wpdmp_delete_lang_callback');
		add_action('wp_ajax_get_map_status','wpdmp_get_map_status_callback');
		add_action('wp_ajax_aw_link_changed','wpdmp_aw_link_changed_callback');
		add_action('wp_ajax_save_ref_points','wpdmp_save_ref_points_callback');
		add_action('wp_ajax_add_markers_to_map','wpdmp_add_markers_to_map_callback');
		add_action('wp_ajax_save_css_and_effects','wpdmp_save_css_and_effects_callback');
		add_action('wp_ajax_save_popup_offset','wpdmp_save_popup_offset_callback');
	}
endif;
wpdmp_add_actions();