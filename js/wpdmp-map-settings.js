/**
 *  Settings functions
 */

var $jq = jQuery.noConflict();

function add_lang(lang){
	
	var data = {
			action  	: 'add_lang',
			lang		: lang,			
			nt			: (new Date().getTime())
		};
	
	toggleProgressBar();
	
	$jq.post(ajaxurl, data, function(response) {
		if (response.indexOf('Error')!=-1){
			alert(response);
			toggleProgressBar();
			return;
		}else{
			$jq('#langs').append(response);
			$jq('#default_lang').append("<option>"+lang+"</option>");
			$jq('#addlang_edit').val('');
		}
		toggleProgressBar();
	});	
}

function set_default_lang(lang){
		
	var data = {
			action  	: 'set_default_lang',
			lang		: lang,			
			nt			: (new Date().getTime())
		};
	
	toggleProgressBar();	
	
	$jq.post(ajaxurl, data, function(response) {
		$jq('#wpdmp_settings').replaceWith(response);
		toggleProgressBar();
	});	
}

function delete_lang_dialog(lang){
	
	$jq(function() {
		$jq( "#dialog-confirm" ).dialog({
			 resizable: false,
			 height:185,
			 width:450,
			 draggable: false,
			 modal: true,
			 buttons: {	 
				 "Delete the language": function() {
					 	$jq( this ).dialog( "close" );
					 	delete_lang(lang); 					 	
				 },
				 Cancel: function() { $jq( this ).dialog( "close" ); }
			 }
		});
	});
}

function delete_lang(lang){
	var data = {
			action  	: 'delete_lang',
			lang		: lang,			
			nt			: (new Date().getTime())
		};
	
	toggleProgressBar();	
	
	$jq.post(ajaxurl, data, function(response) {
		$jq('#wpdmp_settings').replaceWith(response);
		toggleProgressBar();
	});	
}

function aw_link_changed(link){
	
	var data = {
			action  	: 'aw_link_changed',
			link		: link,			
			nt			: (new Date().getTime())
		};
	
	$jq.post(ajaxurl, data, function(response) {});	
}

function save_css_and_effects(css,effects){
	
	var data = {
			action  	: 'save_css_and_effects',
			css			: css,
			effects		: effects,
			nt			: (new Date().getTime())
		};
	
	toggleProgressBar();
	
	$jq.post(ajaxurl, data, function(response) {
		if (response.indexOf('Error')!=-1){
			alert(response);
			toggleProgressBar();
			return;
		}else{
			arr = response.split("#ENDCSS#");
			$jq('#wpdmp-css').val(arr[0]);
			$jq('#wpdmp-effects').val(arr[1]);
		}
		toggleProgressBar();
	});	
}
