/**
 *  Map Manager functions
 */

var file_frame_markers;
var file_frame;

function learn_map(){
	
	clearCoords(".ui-dialog #f_address",".ui-dialog #f_latitude",".ui-dialog #f_longitude");
	$jq('div.ui-dialog').remove();
	
	var $info = $jq("#modalCoordDialog");
	
    $info.dialog({                   
	        'dialogClass'   : 'wpdmp-dialog',           
	        'modal'         : true,
	        'autoOpen'      : false, 
	        'closeOnEscape' : false, 
	        //'position'		: { my: "right top" , at: "right top", of: "#col-left"}, 
	        'buttons'       : {
	            "Close": function() {
	            	$jq(this).dialog('close');            	
	            	//learn_map();
	            },
	            "Save": function() {
	            	if ($jq("#coordsFound").val()=="found"){            		
	            		save_ref_point($jq('#mapimage').attr('mapid'), 
	            					getImageWidth('mapimage'),
	            					getImageHeight('mapimage'),
	            					$jq('.ui-dialog #f_address').val(), 
	            					$jq('.ui-dialog #f_latitude').val(), 
	            					$jq('.ui-dialog #f_longitude').val(), 
	            					$jq('#refX').val(), 
	            					$jq('#refY').val());
	            	}else{
	            		displayPopupMsg(wpdmp_popup.alert_msg.msg,wpdmp_popup.alert_msg.title);
	            		return;
	            	}
	            	$jq(this).dialog('close');           
	            }
	        }
    });

    
    //attach "add ref point"-click    
    attach_click_mapoverlay($info);
}

function attach_click_mapoverlay($info){
	$jq("#mapoverlay").click(function(event) {
		
		var refs = get_refpoints_from_map();
		
		if (refs != null && refs.length > 1){
			displayPopupMsg(wpdmp_popup.remove_ref.msg,wpdmp_popup.remove_ref.title);
		}else{    	
	        event.preventDefault();
	    
	        //save relative (to image) mouse position
	        var posX = event.pageX - $jq('#mapimage').offset().left;
	        var posY = event.pageY - $jq('#mapimage').offset().top;
	
	        $jq('#refX').val(posX);
	        $jq('#refY').val(posY);
	        
	        if (get_maptype_from_map()!='freehand'){
	        	$info.dialog('open');
	        }else{
	        	event.preventDefault();
	        	
	        	$jq('#markerdesc').css('display','block');
	        	$jq('#addmarker').css('display','block');
	        	
		        //save relative (to image size) mouse position
		        var posX = (event.pageX - $jq('#mapimage').offset().left)/$jq('#mapimage').width();
		        var posY = (event.pageY - $jq('#mapimage').offset().top)/$jq('#mapimage').height();
		
		        $jq('#freeX').val(posX);
		        $jq('#freeY').val(posY);
	        	
	        }
		}
	}).children().click(function(e) {
		  return false;
	});
}

function save_ref_point(mapid,mapwidth,mapheight,address,lat,lng,x,y){
	
	var data = {
			action		: 'save_ref_point',			
			mapid		: mapid,
			mapwidth	: mapwidth,
			mapheight	: mapheight,
			address 	: address,
			lat 		: lat,
			lng 		: lng,	
			x			: x,
			y			: y,
			nt			: (new Date().getTime())
		};
	
	toggleProgressBar();
	
	//var mapid = $jq('#mapimage').attr('mapid');
	
	$jq.post(ajaxurl, data, function(response) {		
		if (response.indexOf('created')!=-1){			
			get_map_status(mapid);
			reload_map(mapid,'map',true,'backend_map_manager');
		}else{
			displayPopupMsg(wpdmp_popup.ref_fail.msg,wpdmp_popup.ref_fail.title);
		}
		
		toggleProgressBar();
	});
}

function save_ref_points_google(mapid,refpoints){
		
	
	var data = {
			action		: 'save_ref_points',			
			mapid		: mapid,
			refpoints	:JSON.stringify(refpoints),
			nt			: (new Date().getTime()),
			traditional :false
		};
	
	toggleProgressBar();
	
	$jq.post(ajaxurl, data, function(response) {		
		if (response.indexOf('created')!=-1){			
			get_map_status(mapid);
			reload_map(mapid,'map',true,'backend_map_manager');
		}else{
			displayPopupMsg(wpdmp_popup.ref_fail.msg,wpdmp_popup.ref_fail.title);
		}
		
		toggleProgressBar();
	});
}

function delete_ref_point(id){
	var mapid = $jq('#mapimage').attr('mapid');
	var data = {
			action		: 'delete_ref_point',			
			id			: id,			
			mapid		: mapid,
			nt			: (new Date().getTime())
		};
	
	toggleProgressBar();
	
	$jq.post(ajaxurl, data, function(response) {		
		if (response.indexOf('removed')!=-1){			
			get_map_status(mapid);
			reload_map($jq('#mapimage').attr('mapid'),'map',true,'backend_map_manager');			
		}else{
			displayPopupMsg(wpdmp_popup.ref_fail.msg,wpdmp_popup.ref_fail.title);
			toggleProgressBar();
		}
	});
}

function get_map_status(mapid){
	var data = {
			action		: 'get_map_status',
			mapid		: mapid,			
			nt			: (new Date().getTime())
		};
	
	$jq.post(ajaxurl, data, function(response) {
		$jq('#mapstatus-'+mapid).empty();
		$jq('#mapstatus-'+mapid).append(response);
	});
}

function print_maps_available(){

	var data = {
			action: 'print_maps_available',
			nt: (new Date().getTime())
		};
	
	$jq.post(ajaxurl, data, function(response) {
		$jq('#col-left').empty();
		$jq('#col-left').append(response);
		
		add_map_onchange_handler();
	});
}

function add_map_onchange_handler(){
	
	$jq('[id^=xoffset_]').change(function() {			
		$jq("#save_popup_offset_" + $jq(this).attr('mapid')).css('display','inline');
	});
	$jq('[id^=yoffset_]').change(function() {			
		$jq("#save_popup_offset_" + $jq(this).attr('mapid')).css('display','inline');
	});
	$jq('[id^=xoffset_]').keyup(function() {			
		$jq("#save_popup_offset_" + $jq(this).attr('mapid')).css('display','inline');
	});
	$jq('[id^=yoffset_]').keyup(function() {			
		$jq("#save_popup_offset_" + $jq(this).attr('mapid')).css('display','inline');
	});
	
	$jq('[id^=popup_loc_]').on('change', function() {			
		$jq("#save_popup_location_" + $jq(this).attr('mapid')).css('display','inline');
	});
}

function save_popup_offset(mapid,offsetx,offsety){
	
	if(isNaN(offsetx) || isNaN(offsety)) {
		displayPopupMsg(wpdmp_popup.popup_offset_validation_error.msg,wpdmp_popup.popup_offset_validation_error.title);
		return;
	}
	var data = {
			action		: 'save_popup_offset',			
			mapid		: mapid,
			offsetx		: offsetx,
			offsety		: offsety,
			nt			: (new Date().getTime())
		};
	
	toggleProgressBar();
	
	$jq.post(ajaxurl, data, function(response) {	
		var response_data = $jq.parseJSON(response);
		if (response_data.code != 'ERROR'){						
			reload_map(mapid,'map',true,'backend_map_manager');
		}else{
			displayPopupMsg(response_data.msg);
		}
		
		$jq("#save_popup_offset_" + mapid).css('display','none');		
		toggleProgressBar();
	});
}

function save_popup_location(mapid,val){
	
	var data = {
			action		: 'save_popup_location',			
			mapid		: mapid,
			val			: val,			
			nt			: (new Date().getTime())
		};
	
	toggleProgressBar();
	
	$jq.post(ajaxurl, data, function(response) {	
		var response_data = $jq.parseJSON(response);
		if (response_data.code != 'ERROR'){						
			reload_map(mapid,'map',true,'backend_map_manager');
		}else{
			displayPopupMsg(response_data.msg);
		}
		
		$jq("#save_popup_location_" + mapid).css('display','none');
		toggleProgressBar();
	});
}


function delete_map_dialog(mapid){
	$jq(function() {
		$jq( "#dialog-confirm" ).dialog({
			 resizable: false,
			 height:200,
			 width:450,
			 draggable: false,
			 modal: true,
			 buttons: {	 
				 "Delete the map": function() {
					 	$jq( this ).dialog( "close" );
					 	delete_map(mapid); 					 	
				 },
				 Cancel: function() { $jq( this ).dialog( "close" ); }
			 }
		});
	});
}

function delete_map(mapid){
	
	var data = {
			action  	: 'delete_map',
			mapid		: mapid,			
			nt			: (new Date().getTime())
		};
	
	//alert ("add marker called!");
	
	toggleProgressBar();
	
	$jq.post(ajaxurl, data, function(response) {
		var response_data = $jq.parseJSON(response);
		if (response_data.code == 'ERROR'){
			displayPopupMsg(response_data.msg);
		}else{
			$jq('#map_'+mapid).parent().remove();
			//$jq('#mapinfo_'+mapid).remove();
		}
	});
	toggleProgressBar();
}

function add_map(attachmentid){
		
	var data = {
			action  	: 'add_map',
			attachmentid: attachmentid,			
			nt			: (new Date().getTime())
		};
	
	//alert ("add marker called!");
	
	toggleProgressBar();
	
	$jq.post(ajaxurl, data, function(response) {
		
		var response_data = $jq.parseJSON(response);
		if (response_data.code == 'ERROR'){
			displayPopupMsg(response_data.msg);
			toggleProgressBar();
			return;
		}
		
		reload_map(response,'map',true,'backend_map_manager');
		print_maps_available();
		
		toggleProgressBar();
	});
}

function selectMarkersFiles(event, mapid){
	   
	event.preventDefault();
	$jq('#curmapid').val(mapid);
	
	if ( file_frame_markers ) {
	   file_frame_markers.open();
	   return;
	}else{
    
	   // Create the media frame.
	   file_frame_markers = wp.media.frames.file_frame = wp.media({
		   title: $jq( this ).data( 'uploader_title' ),
		   button: {
			   text: $jq( this ).data( 'uploader_button_text' ),
		   },
		   multiple: true 
	   });
    
	   // When an image is selected, run a callback.
	   file_frame_markers.on( 'select', function() {
		   
		   var selection = file_frame_markers.state().get('selection');
		   var attachment_ids = selection.map( function( attachment ) {
		     attachment = attachment.toJSON();
		     return attachment.id;
		   }).join();
		   //output: 81,82,79
		   
		   add_markers_to_map($jq('#curmapid').val(),attachment_ids);
	   });
	   
	   file_frame_markers.on('open',function() {
           
		   $jq("img[mapid='"+$jq('#curmapid').val()+"'].markers_list").each(function(){
               attachment = wp.media.attachment($jq(this).attr('attid'));
               attachment.fetch();
               file_frame_markers.state().get('selection').add( attachment ? [ attachment ] : [] );
           });
		
       });
	    
	   // open the modal dialog
	   file_frame_markers.open();
	}
}

function add_markers_to_map(map,attachmentids){
	
	var data = {
			action  		: 'add_markers_to_map',
			mapid			: map,
			attachmentids	: attachmentids,			
			nt				: (new Date().getTime())
		};
	
	//alert ("add marker called!");
	
	toggleProgressBar();
	
	$jq.post(ajaxurl, data, function(response) {
		
		var response_data = $jq.parseJSON(response);
		if (response_data.code == 'ERROR'){
			displayPopupMsg(response_data.msg);
			toggleProgressBar();
			return;
		}
		
		$jq('#col-left').empty();
		$jq('#col-left').append(response_data);
		
		if ($jq('.ajaxmessage').length > 0){
			displayPopupMsg($jq('.ajaxmessage').text());
			$jq('.ajaxmessage').remove();
		}
		
		toggleProgressBar();
	});
}

function selectMapFile(event){

   event.preventDefault();

   if ( file_frame ) {
	   file_frame.open();
	   return;
   }

   file_frame = wp.media.frames.file_frame = wp.media({
	   title: $jq( this ).data( 'uploader_title' ),
	   button: {
		   text: $jq( this ).data( 'uploader_button_text' ),
	   },
	   multiple: false 
   });

   file_frame.on( 'select', function() {
	   attachment = file_frame.state().get('selection').first().toJSON();		   
	   add_map(attachment.id );
   });
    
   file_frame.open();
}

function position_google_map(address){
	
	geocoder = new google.maps.Geocoder(address);
	geocoder.geocode( { 'address': address}, function(results, status) {
	    if (status == google.maps.GeocoderStatus.OK) {
	    	map.panTo(new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng()));
	    	map.fitBounds(results[0].geometry.bounds)
	    }else{
	    	//toggleProgressBar();
	    	displayPopupMsg(wpdmp_popup.check_addr.msg,wpdmp_popup.check_addr.title);
	    }
	  });	
}

function init_goolge_calibrator(){
	//display map for calibration
	$jq('#map_draggable_container').css('display', 'block');	
	
	var rel = $jq('#map_draggable').attr('maph')/$jq('#map_draggable').attr('mapw');
	
	$jq('#mapimage').imagesLoaded(function( $images, $proper, $broken ){
		if (rel>0.9){			
			$jq('#map_draggable_container').css('height',$jq('#google-map-canvas').height());
			$jq('#map_draggable').css('height',$jq('#google-map-canvas').height());
			$jq('#map_draggable_container').css('width',$jq('#map_draggable').width());
			$jq('#map_draggable_container').css('left', ($jq('#google-map-canvas').width() - $jq('#google-map-canvas').height()/rel)/2);
		}else{
			$jq('#map_draggable_container').css('width',$jq('#google-map-canvas').width()-200);
			$jq('#map_draggable').css('width',$jq('#google-map-canvas').width()-200);
			$jq('#map_draggable_container').css('left','100px');
			$jq('#map_draggable_container').css('top', ($jq('#google-map-canvas').height() - $jq('#map_draggable_container').height())/2);
		}
	
		init_sliders();
	});
}

function init_sliders(){
	
	var mh = $jq( "#map_draggable" ).height();
	var mw = $jq( "#map_draggable" ).width();
	$jq('#keepratio_check').css('outline-color', 'red');
	$jq('#keepratio_check').css('outline-style', 'solid');
	$jq('#keepratio_check').css('outline-width', 'thin');
	
	$jq( "#vslider" ).slider({
		value: mh,
		range: "max",
		min: 0,
		max: mh*2-100,
		step: 3,
		animate: "fast",
		orientation: "vertical",
		slide: function( event, ui ) {
			var previousVal = parseInt($jq(this).data("value"));  
			$jq( "#map_draggable_container" ).css( "height", mh*2 - ui.value);
			$jq( "#map_draggable" ).css( "height", mh*2 - ui.value);
			if ($jq('#keepratio_check').prop('checked')){
				$jq("#hslider").slider("value", mw*(mh*2 - ui.value)/mh);
				$jq( "#map_draggable_container" ).css( "width", $jq("#hslider").slider("value"));
				$jq( "#map_draggable" ).css( "width", $jq("#hslider").slider("value"));
			}
		}
	});
	$jq( "#hslider" ).slider({		
		value: mw,
		range: "max",
		min: 100,
		max: mw*2,
		step: 3,
		animate: "fast",
		slide: function( event, ui ) {
			$jq( "#map_draggable_container" ).css( "width", ui.value);
			$jq( "#map_draggable" ).css( "width", ui.value);
			if ($jq('#keepratio_check').prop('checked')){
				$jq("#vslider").slider("value", mh*2 - mh*ui.value/mw);
				$jq( "#map_draggable_container" ).css( "height", mh*2 - $jq("#vslider").slider("value"));
				$jq( "#map_draggable" ).css( "height", mh*2 - $jq("#vslider").slider("value"));
			}
		}
	});
}

function mark_free_hand_map(mapid){
	var data = {
			action  	: 'mark_free_hand_map',
			mapid		: mapid,			
			nt			: (new Date().getTime())
		};
	
	//alert ("add marker called!");
	
	toggleProgressBar();
	
	$jq.post(ajaxurl, data, function(response) {
		
		var response_data = $jq.parseJSON(response);
		
		if (response_data.code == 'ERROR'){
			displayPopupMsg(response_data.msg);
			toggleProgressBar();
			return;
		}
		else{			
			get_map_status(mapid);
			reload_map(mapid,'map',true,'backend_map_manager');
		}
		
		toggleProgressBar();
	});
}