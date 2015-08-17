<?php

if ( !function_exists('wpdmp_print_google_calibrator') ):
	function wpdmp_print_google_calibrator($mapid)  {
	   $map = wpdmp_get_map($mapid);
	   ?>
		<p><?php _e('Type the location on your map to position the Google Map, e.g. Germany or Munich or Black Sea:','wp-design-maps-and-places'); ?></p>
		<input type="text" maxlength="2048" tabindex='1' value="" name="f_googleaddress" id="f_googleaddress" title="Address" class="" autocomplete="off" autocorrect="off" />
		<input type='button' onclick='position_google_map($jq("#f_googleaddress").val());' tabindex='2' value='<?php _e(' Pane Google Map to ','wp-design-maps-and-places'); ?>' id='positionmap' name='positionmap' />
		
		<div id="google-map-canvas"></div>
		
		<div id="map_draggable_container">
			<div id="vslider"></div>
			<div id="hslider"></div>
			<div id="keepratio"><input id="keepratio_check" type="checkbox" name="keepratio" value="keepratio" checked="checked"></input></div>
			<div id="map_calibrated_ok"><input id="map_calibrated_ok_button" onclick='getPxForLatLng();' type="button" value='<?php _e('Save','wp-design-maps-and-places'); ?>'></input></div>
			<img id="map_draggable" mapid="<?php echo $map['id']; ?>" style="" mapw="<?php echo $map['mapwidth'];?>" maph="<?php echo $map['mapheight'];?>" src="<?php echo $map['map']; ?>"/>			
		</div>
<?php 
	}
endif;
?>
