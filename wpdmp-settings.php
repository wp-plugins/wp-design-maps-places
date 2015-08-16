<?php

if ( !function_exists('wpdmp_settings_view') ):
	function wpdmp_settings_view() {
		wpdmp_print_popup_dialog();
		wpdmp_settings_view_inner();
	?>
	<div id="progressbar" style="background: url(<?php echo WPDMP_PLUGIN_URL . 'images/spin.gif';?>) center center no-repeat #fff"/></div>
	<div id="dialog-confirm" title='<?php _e('Remove the language including ALL descriptions in this language?','wp-design-maps-and-places'); ?>'>
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><?php _e('The language and all description in this language will be permanently deleted and cannot be recovered. Are you sure?','wp-design-maps-and-places'); ?></p>
	</div>
<?php }
endif;

if ( !function_exists('wpdmp_settings_view_inner') ):
	function wpdmp_settings_view_inner() {
		$css = stripslashes(get_option('wpdmp_css'));
		$effects = stripslashes(get_option('wpdmp_effects'));
	   ?>
	   <div id='wpdmp_settings'>
			<div id="col-right"  class="col-right-settings">
         		<fieldset class="wpdmp-fieldset">
         			<legend><?php _e('Please Donate','wp-design-maps-and-places'); ?></legend>
         			<p><?php _e("If you like this plugin, here's how you can say 'Thank You'",'wp-design-maps-and-places'); ?></p>
         			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="LPEEQUZ5MUECL">
						<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
					</form>
         			
         		</fieldset>
         		<fieldset class="wpdmp-fieldset">
         			<legend><?php _e('Appearance','wp-design-maps-and-places'); ?></legend>
         			
         			<p><?php _e('Map & Places styles (CSS)','wp-design-maps-and-places'); ?></p>
         			<textarea cols="100" rows="15" value="" id="wpdmp-css" autocomplete="off" autocorrect="off"><?php echo $css; ?></textarea>
         			
         			<p><?php _e('Effects (JavaScript)','wp-design-maps-and-places'); ?></p>
         			<textarea cols="100" rows="15" value="" id="wpdmp-effects" autocomplete="off" autocorrect="off"><?php echo $effects; ?></textarea>
         			
         			<input type='button' class='button-primary wpdmp-setting-input' tabindex='10' 
                	 		value='<?php _e(' SAVE CSS AND EFFECTS ','wp-design-maps-and-places'); ?>' id='savecss' name='savecss' 
                	 		onclick="save_css_and_effects($jq('#wpdmp-css').val(),$jq('#wpdmp-effects').val());"/>
         		</fieldset>
			</div>
	
			<div id="col-left" style="width:33%">
				<fieldset class="wpdmp-fieldset">
					<legend><?php _e('Languages available','wp-design-maps-and-places'); ?></legend>
					<p><?php _e('Define the codes of languages you need. The codes are only used for map shortcut. Use only 2 letters to create a code. E.g. for English it could be "en".','wp-design-maps-and-places'); ?></p>  
            		<?php 
            		$langs = get_option( 'wpdmp_langs');
            		if (count($langs)<1 || $langs==false){
            		   $langs=array('en');
            		   add_option('wpdmp_langs',$langs);
            		}		 
            		$default_lang = get_option( 'wpdmp_default_lang');
            		
            		?>
            		<ol id="langs">
            		<?php foreach ($langs as $lang){?>
            		 	<li><span class="wpdmp-setting-input"><?php echo $lang;
            		 		 if ($default_lang == $lang){?></span>
            		 		 	<?php $markers_count = wpdmp_get_number_of_markers_for_lang($lang);
            		 		 	printf(__('Default Language (used by %d markers)','wp-design-maps-and-places'),$markers_count);
            		 		  }else{
            		 		  	$num_of_marker_for_lang = wpdmp_get_number_of_markers_for_lang($lang);?>
            		 		 	<a href="#" onclick="delete_lang_dialog('<?php echo $lang;?>');"><?php printf(__('Remove language (used by %d markers)','wp-design-maps-and-places'),$num_of_marker_for_lang);?></a>
            		 	<?php }?>
            		 	</li>
            		<?php }?>
            		</ol>
            		
            		<p class="submit">
            			<input type='text' size='20' class='wpdmp-setting-input' id='addlang_edit'/>
                	 	<input type='button' class='button-primary wpdmp-setting-input' tabindex='1' 
                	 		value='<?php _e(' ADD NEW LANGUAGE ','wp-design-maps-and-places'); ?>'
                	 		id='addlang' name='addlang' disabled="1"
                	 		onclick="add_lang($jq('#addlang_edit').val());"/>
            		</p>
				</fieldset>
		
		
            	<fieldset class="wpdmp-fieldset">
            		<legend><?php _e('Default Language','wp-design-maps-and-places'); ?></legend>
            		<p><?php _e("The default language is used if a marker's description is not available for a requested language",'wp-design-maps-and-places'); ?></p>
            		<select id="default_lang" name="wpdmp_default_lang" size="1" class="wpdmp-setting-input">
            			<?php foreach ($langs as $lang){?>
            			<option <?php if ($default_lang == $lang){echo "selected='1'";}?>><?php echo $lang;?></option>
            			<?php }?>
            		</select>
            		<script type="text/javascript">
            		$jq("#addlang_edit").change(function() {
            			if ($jq(this).val().length > 0){
            				$jq("#addlang").prop('disabled', false);
            			}else{
            				$jq("#addlang").prop('disabled', true);
            			}
            		});
            		$jq("#addlang_edit").keyup(function() {
            			if ($jq(this).val().length > 0){
            				$jq("#addlang").prop('disabled', false);
            			}else{
            				$jq("#addlang").prop('disabled', true);
            			}
            		});
            		$jq("#default_lang").change(function () {
            			$jq( "#default_lang option:selected" ).each(function() {
            				set_default_lang($jq( this ).text());
            			});
            		});
            		</script>
				</fieldset>
		
         		<fieldset class="wpdmp-fieldset">
         			<legend><?php _e('Link to our Homepage','wp-design-maps-and-places'); ?></legend>
         			<p><?php _e('If you like the plugin please support us by the link to our homepage from the map image (right lower corner)','wp-design-maps-and-places'); ?></p>
         			<input type="radio" name="aw_link" value="yes" <?php if (get_option('wpdmp_link')=='yes'){echo 'checked="checked"';}?>><?php _e('Yes, I allow to set the link','wp-design-maps-and-places'); ?></input><br/>
         			<input type="radio" name="aw_link" value="no" <?php if (get_option('wpdmp_link')!='yes'){echo 'checked="checked"';}?>><?php _e('No, no link please','wp-design-maps-and-places'); ?></input>
         			<script type="text/javascript">
         				$jq("input[name='aw_link']:radio").change(function() {aw_link_changed($jq(this).val());});
         			</script>
         		</fieldset>
         		
         		<?php do_action("wpdmp_settings_after_col_left")?>
         		
			</div>
	</div>	
	<?php }
endif;

if ( !function_exists('wpdmp_add_lang_callback') ):
	function wpdmp_add_lang_callback() {
		
		$new_lang = $_POST['lang'];
		$langs = get_option( 'wpdmp_langs');
		$language_exists_error = array('code' => 'ERROR' , 'msg' => '');
		
		foreach($langs as $lang){
			if ($lang == $new_lang){
				$language_exists_error['msg'] = sprintf(__("Error: the language '%s' already exists!",'wp-design-maps-and-places'),$new_lang);
				echo json_encode($language_exists_error);
				die();
				exit;
			}
		}
		
		array_push($langs, $new_lang);
		update_option('wpdmp_langs', $langs);
		
		ob_start();
      	?>
      	<li>
      		<span class="wpdmp-setting-input"><?php echo $new_lang;?>
		 	<a href="#" onclick="delete_lang_dialog('<?php echo $new_lang;?>');"><?php _e('Remove language (used by 0 markers)','wp-design-maps-and-places'); ?></a>		
		</li>
      	<?php 
      	$content = ob_get_contents();
      	ob_end_clean();
      	echo json_encode($content);
      	die();
      	exit;
		
?>
<?php }
endif;

if ( !function_exists('wpdmp_set_default_lang_callback') ):
	function wpdmp_set_default_lang_callback() {
		
		$def_lang = $_POST['lang'];
		update_option( 'wpdmp_default_lang',$def_lang );
		
		ob_start();
      	wpdmp_settings_view_inner(); 
      	$content = ob_get_contents();
      	ob_end_clean();
      	echo $content;
      	die();
      	exit;
		
?>
<?php }
endif;

if ( !function_exists('wpdmp_delete_lang_callback') ):
	function wpdmp_delete_lang_callback() {
		
		$del_lang = $_POST['lang'];
		
		$langs = get_option('wpdmp_langs');
		
		$new_langs = array_diff($langs, array($del_lang));
		
		/*foreach ($langs as $lang){
		   if ($lang == $del_lang){
		      unset($lang);
		   }
		}*/		
		
		update_option( 'wpdmp_langs',$new_langs );
				
      	ob_start();
      	wpdmp_settings_view_inner(); 
      	$content = ob_get_contents();
      	ob_end_clean();
      	echo $content;
      	die();
      	exit;
		
?>
<?php }
endif;

if ( !function_exists('wpdmp_aw_link_changed_callback') ):
	function wpdmp_aw_link_changed_callback() {
		
		$link = $_POST['link'];		
				
		update_option('wpdmp_link', $link);
				
      	_e('link_updated','wp-design-maps-and-places');
      	die();
      	exit;
	}
endif;

if ( !function_exists('wpdmp_save_css_and_effects_callback') ):
	function wpdmp_save_css_and_effects_callback() {
		update_option('wpdmp_css', $_POST['css']);
		update_option('wpdmp_effects', $_POST['effects']);
		
		echo stripslashes(get_option('wpdmp_css') . '#ENDCSS#' . get_option('wpdmp_effects'));
      	die();
      	exit;
	}
endif;