/**
 * common functions for all views
 */

var $jq = jQuery.noConflict();

function toggleProgressBar(){
	if ($jq('#progressbar').css('display')=='none'){
		$jq('#progressbar').css('display','block');
	}else{
		$jq('#progressbar').css('display','none');
	}
}

function remove_marker(id){
	var data = {
			action: 'remove_marker',
			id: id,
			nt: (new Date().getTime())
		};
	
	toggleProgressBar();
	
	$jq.post(ajaxurl, data, function(response) {		
		if (response.indexOf('removed-'+id)!=-1){
			$jq('#markerprint'+id).fadeOut(600, function(){$jq('#markerprint'+id).detach();});
			$jq('#m_'+id).remove();
			$jq('.m_'+id+'_mo').remove();
		}
		
		toggleProgressBar();
	});
}

function save_marker(id){
	
	var desc = '';
	$jq('.descr_langs-'+id).each(function(){desc = desc + $jq(this).attr('lang')+'$%$'+$jq(this).val()+"#%#";});
	
	var data = {
			action		: 'save_marker',
			id			: id,
			desc 		: desc,
			marker		: $jq('input[name=marker-'+id+']:checked').val(),
			nt			: (new Date().getTime())
		};
	
	toggleProgressBar();
	
	$jq.post(ajaxurl, data, function(response) {
		var marker = JSON.parse(response);
		if (typeof marker !== 'undefined'){
			$jq('.m_'+id+'_mo').remove();
			$jq('#m_'+id).remove();
			set_marker(marker.id,marker.lat,marker.lng,marker.descr,marker.marker,marker.markerimg);
			add_effects();
		    			
			$jq('#markerprint'+id).replaceWith(marker.html);
			$jq('#tabs-'+id).tabs();
			$jq('#savemarker-'+id).css('display','none');
			add_onchange_handler(id);
			
			alert ("Marker successfully updated!");
		}else{
			alert ("FAILURE: marker could not be updated!");
		}
		
		toggleProgressBar();
	});
}

//function add_marker(mapid, address,lat,lng,desc,desc_en,marker){
function add_marker(){
	
	$jq('#m_new_temp').remove();
	$jq('.m_new_temp_mo').remove();
	
	var desc = '';
	$jq('.descr_langs-new').each(function(){desc = desc + $jq(this).attr('lang')+'$%$'+$jq(this).val()+"#%#";});//

	var data = {
			action  	: 'add_marker',
			mapid		: $jq('#mapimage').attr('mapid'),
			address 	: $jq('#f_address').val(),
			lat 		: $jq('#f_latitude').val(),
			lng 		: $jq('#f_longitude').val(),
			desc 		: desc,			
			marker		: $jq('input[name=marker-new]:checked').val(),
			nt			: (new Date().getTime())
		};
	
	toggleProgressBar();
	
	$jq.post(ajaxurl, data, function(response) {
		var marker = JSON.parse(response);
		if (typeof marker !== 'undefined'){
			set_marker(marker.id,marker.lat,marker.lng,marker.descr,marker.marker,marker.markerimg);
			add_effects();
			
			$jq( '#newmarkerform' )[0].reset();
			$jq('#markerdesc').css('display','none');
			$jq('#addmarker').css('display','none');			
			$jq('#markerslist').append(marker.html);			
			$jq('#tabs-'+marker.id).tabs();
			add_onchange_handler(marker.id);
			
			alert ("Marker successfully created!");
		}else{
			alert ("FAILURE: marker could not be created!");
		}
		toggleProgressBar();
	});
}

function reload_site_list(mapid){

	var data = {
			action: 'reload_site_list',
			mapid: mapid,			
			nt: (new Date().getTime())
		};
	
	$jq('#progressbar').css('display','block');
	
	$jq.post(ajaxurl, data, function(response) {
		
		$jq('#col-left').empty();
		$jq('#col-left').append(response);

		setTimeout(function(){$jq('.markertabcontainer').tabs()}, 1000);
		
		//display Save on edit
		add_onchange_handler(-1);
		
		$jq('#progressbar').css('display', 'none');
	});
}

function reload_map(mapid,id,learn,mode){

	var data = {
			action: 'reload_map',
			mapid: mapid,
			mode: mode,
			nt: (new Date().getTime())
		};
	
	if (id == 'map-front'){	
		$jq('#mapimage').imagesLoaded(function( $images, $proper, $broken ){
			$jq('#mapprogressbar').css('display','block');
			
			var w = getImageWidth('mapimage');
			var h = getImageHeight('mapimage');
			
			$jq('#mapprogressbar')
				.css('top', '-' + $jq('#mapimage').height() + 'px')
				.width(w)
				.height(h);
			
			var offset = $jq('#mapimage').offset();
			var offset2 = $jq('#mapoverlay').offset();
			
			$jq('#mapoverlay').css('top', '0px').width(w).height(h);
			$jq('#' + id).height($jq('#' + id).height() - h - $jq('#mapprogressbar').height());
			
			align_markers();
			add_effects();
			$jq('#mapprogressbar').css('display','none');
		});
		
	}else{
		
		$jq('#progressbar').css('display','block');
		
		$jq.post(ajaxurl, data, function(response) {
			
			$jq('#map').empty();
			$jq('#map').append(response);			
						
			if (mode == 'backend_map_manager' || mode == 'backend_marker_manager'){
				$jq('#mapimage').imagesLoaded(function( $images, $proper, $broken ){				
					$jq('#mapoverlay')
						//.css('top', '-' + ($jq('#mapimage').height()+4) + 'px')
						.css('top', '0px')
						.width($jq('#mapimage').width())
						.height($jq('#mapimage').height());
							
					$jq('#' + id).height($jq('#mapimage').height()+$jq('#mapimage').offset().top);
				
					align_markers();
					align_refpoints();
					add_effects();
					
				});
	
				
				if (mode == 'backend_marker_manager'){
					reload_site_list(mapid);
				}
			} 
			if (mode == 'backend_map_manager_google'){
				wpdmp_calibration_init();
				init_goolge_calibrator();
	      		//google.maps.event.addDomListener(window, 'load', wpdmp_calibration_init);
			}
			if (mode == 'backend_map_manager'){
				if (learn){
					learn_map();
				}
				add_map_onchange_handler();
			}
			$jq('#progressbar').css('display', 'none');
			
		});
	}
}

function wpdmp_calibration_init() {
	var mapOptions = {
			center: new google.maps.LatLng(20, -7),
			zoom: 2};
	map = new google.maps.Map(document.getElementById("google-map-canvas"),mapOptions);
	
	overlay = new google.maps.OverlayView();
	overlay.draw = function() {};
	overlay.setMap(map);
	
	$jq( '#map_draggable_container' ).draggable();
}

function getImageWidth(id){
	return document.getElementById(id).clientWidth;
}

function getImageHeight(id){
	return document.getElementById(id).clientHeight;
}

//TBD: not used
function set_markers (json_markers, front){
 var markers = JSON.parse(json_markers);
 var i=0;

 while (typeof markers[i]!== 'undefined'){
    set_marker(markers[i].id,markers[i].lat,markers[i].lng,markers[i].desc,markers[i].marker,markers[i].markerimg)

    i++;
 }   
}

function get_refpoints_from_map(){
	var refstmp = $jq('#mapimage').attr('refpoints');
	var refs = null;
	
	if (refstmp != null && refstmp != ''){
		refs = JSON.parse('['+refstmp+']');
	}
	
	return refs;		
}

/*
* correctScale parameter is used in front-mode: if the image is scaled, the coordinates must be corrected.
* It can be used also in backend, but no use case as of now. Backend is "under control" (front end - up to user)  
*/
function align_markers(){
	var refs = get_refpoints_from_map();
	
	if (refs == null){
		alert('0 reference points are defined. You need to define 2 points before a Place can be added!');
		return;
	}
	
	refpoint1 = refs[0];
	refpoint2 = refs[1];
	
	if (typeof refpoint1 === "undefined" || typeof refpoint2 === "undefined"){
		alert('One reference point is defined. You need to define 2 points before a Place can be added!');
		return;
	}
	
	//correct scale of current map to size of the refpoints' map 
	dw1 = getImageWidth('mapimage') / refpoint1.mapwidth;
	dw2 = getImageWidth('mapimage') / refpoint2.mapwidth;
	dh1 = getImageHeight('mapimage') / refpoint1.mapheight;
	dh2 = getImageHeight('mapimage') / refpoint2.mapheight;
	
	var pxlng = Math.abs(refpoint1.x - refpoint2.x)/Math.abs(refpoint1.lng - refpoint2.lng);
	
	$jq('.ctrl').each(function() {
	
		var lat = $jq(this).attr('lat');
		var lng = $jq(this).attr('lng');
		var dy = getMarkerYForLatMerkator(lat, refpoint1, refpoint2, dh1, dh2);  
		var dx = ((refpoint1.x + (lng - refpoint1.lng)*pxlng)*dw1 + (refpoint2.x + (lng - refpoint2.lng)*pxlng)*dw2)/2;
		
	   
		//positioning of the marker's image to center
		imh = $jq(this).height()/2;
		imw = $jq(this).width()/2;
		
		$jq(this).css('top',(dy-imh)+'px').css('left',(dx-imw)+'px').css('display','block');

		//positioning of the popup 
		var offsetx = parseInt($jq('#mapimage').attr('popupoffsetx'))+dx;
		var offsety = parseInt($jq('#mapimage').attr('popupoffsety'))+dy;
		$jq('.m_'+$jq(this).attr('mid')+'_mo').css('top',offsety+'px').css('left',offsetx+'px');
	});
}

function align_refpoints(){
	
	$jq('.ref').each(function() {
	
		var x = $jq(this).attr('x');
		var y = $jq(this).attr('y');		
		$jq(this).css('top',(y-8)+'px').css('left',(x-8)+'px').css('display','block');
	
		$jq('.'+$jq(this).attr('id')+'_mo').css('top',(y-5)+'px').css('left',x+'px');
	});
}

//add place after coords are got from google 
function add_temp_place(lat,lng,descr){
	
	var id = 'new_temp';
	$jq('.m_'+id+'_mo').remove();
	$jq('#m_'+id).remove();
	set_marker(id,lat,lng,descr,0,$jq('#refpointimg').val());
	
	//TBD print hidden el with ref point marker image 
}

function set_marker(id,lat,lng,desc,marker,markerimg){
 
	var refs = get_refpoints_from_map();
	
	if (refs == null){
		alert('0 reference points are defined. You need to define 2 points before a Place can be added!');
		return;
	}
	
	refpoint1 = refs[0];
	refpoint2 = refs[1];
	
	if (typeof refpoint1 === "undefined" || typeof refpoint2 === "undefined"){
		alert('1 reference point is defined. You need to define 2 points before a Place can be added!');
		return;
	}
	
	var pxlng = Math.abs(refpoint1.x - refpoint2.x)/Math.abs(refpoint1.lng - refpoint2.lng);
	//var pxlat = Math.abs(refpoint1.y - refpoint2.y)/Math.abs(refpoint1.lat - refpoint2.lat);
 
	$jq('#mapoverlay').prepend('<img class="ctrl" id="m_'+id+'" src="'+markerimg+'"/>');   
 	
	var dy = getMarkerYForLatMerkator(lat, refpoint1, refpoint2,1,1); 
		//((refpoint1.y + (refpoint1.lat - lat)*pxlat) + (refpoint2.y + (refpoint2.lat - lat)*pxlat))/2;
	var dx = ((refpoint1.x + (lng - refpoint1.lng)*pxlng) + (refpoint2.x + (lng - refpoint2.lng)*pxlng))/2;
 
	//var mw = $jq('#m_'+id).css('width');
	//var mh = $jq('#m_'+id).css('height');
	var imh = $jq('#m_'+id).height()/2;
	var imw = $jq('#m_'+id).width()/2;
	
	$jq('#m_'+id).css('top',(dy-imh)+'px').css('left',(dx-imw)+'px');
	
	var cur_desc = ''; 
	try{
		cur_desc = eval('desc.'+$jq('#mapimage').attr('cur_lang')+'.descr');
	}catch(err){
		//do nothing, happens in case of adding of a temp place
	}
	
	var offsetx = parseInt($jq('#mapimage').attr('popupoffsetx'))+dx;
	var offsety = parseInt($jq('#mapimage').attr('popupoffsety'))+dy;
	$jq('#mapoverlay').append('<div class="m_'+id+'_mo mappopupwrap" style="top:'+offsety+'px;left:'+offsetx+'px;"><div class="mappopup" id="#m_'+id+'_mo">'+cur_desc+'</div></div>');
}

function add_effects(){
	
	$jq('.ctrl').on('mouseenter', function(event){
			$jq('.mappopupwrap').hide();
			$jq('.'+event.target.id+'_mo').fadeIn('fast');});
		
	/* do via class(!) on dynamic elements*/   
	//$jq('.ctrl').on('mouseenter', function(event){$jq('.'+event.target.id+'_mo').fadeIn('fast');});	
	$jq('.mappopup').on('mouseleave', function(event){$jq('.mappopupwrap').fadeOut('slow');});
	
	$jq('.ref').on('mouseenter', function(event){
			$jq('.mappopupwrap').hide();
			$jq('.'+event.target.id+'_mo').fadeIn('fast');
	});
	
	//try{
		add_custom_effects();
	/*}catch(e){
		alert('The custom effects could not be loaded.');
	}*/
	
	/* do via class(!) on dynamic elements*/   
	//$jq('.ref').on('mouseenter', function(event){$jq('.'+event.target.id+'_mo').fadeIn('fast');});   
	//$jq('.mappopupwrap').on('mouseleave', function(event){$jq('.mappopupwrap').fadeOut('slow');});

}
/*
function add_effects(){
	$jq('#mapoverlay[mapid="2"] .ctrl').on('mouseenter', function(event){
		$jq(this).clearQueue();
		$jq(this).stop();
		var mrk = $jq(this);
        $jq('.'+mrk.attr('id')+'_mo').clearQueue();
        $jq('.'+mrk.attr('id')+'_mo').stop();
		$jq(this).attr("markerentered", "1");
		checkZoom(this);
		});
	
    $jq('#mapoverlay[mapid="2"] .mappopup').on('mouseenter', function(event){
       
        var mrid = $jq(this).parent().attr('mrid');
        
        $jq(this).clearQueue();
        $jq(this).stop();
        $jq('#m_'+mrid).clearQueue();
        $jq('#m_'+mrid).stop();

        $jq('#m_'+mrid).attr("popupentered", "1");
        checkZoom($jq('#m_'+mrid));});
   
    $jq('#mapoverlay[mapid="2"] .ctrl').on('mouseleave', function(event){
    	$jq(this).clearQueue();
    	$jq(this).stop();
		var mrk = $jq(this);
        $jq('.'+mrk.attr('id')+'_mo').clearQueue();
        $jq('.'+mrk.attr('id')+'_mo').stop();
    	$jq(this).attr("markerentered", "0");
		checkZoom(this); });
    
    $jq('#mapoverlay[mapid="2"] .mappopup').on('mouseleave', function(event){
       
    	var mrid = $jq(this).parent().attr('mrid');
    	
    	$jq(this).clearQueue();
        $jq(this).stop();
        $jq('#m_'+mrid).clearQueue();
        $jq('#m_'+mrid).stop();

        $jq('#m_'+mrid).attr("popupentered", "0");
        checkZoom($jq('#m_'+mrid));});
}

function checkZoom(el){
	if($jq(el).attr("popupentered")==1 || $jq(el).attr("markerentered")==1){
		marker_zoom(el,true);
	}else{
		marker_zoom(el,false);
	}
}*/

function marker_zoom(el,zoomin){
     if (typeof $jq( el ).attr("inittop")==="undefined"){
          $jq( el ).attr("inittop", $jq( el ).position().top);
          $jq( el ).attr("initleft", $jq( el ).position().left);
     }
     if (zoomin){
         $jq( el ).animate( {width: "50px", height: "50px", top: $jq( el ).attr("inittop")-12+"px", left: $jq( el ).attr("initleft")-12+"px"} );
         
         $jq('.mappopupwrap').hide();
         $jq('.'+$jq(el).attr('id')+'_mo').fadeIn('fast');
     }else{
    	 $jq('.mappopupwrap').hide();
         $jq( el ).animate( {width: "25px", height: "25px" ,top: $jq( el ).attr("inittop")+"px", left: $jq( el ).attr("initleft")+"px"} );
     }
}

function add_onchange_handler(id){
	if (id!=-1){
		$jq('[id^=f_text-'+id+']').change(function() {
			var mid =  $jq(this).attr('id').split('-');
			$jq("#savemarker-" + mid[1]).css('display','block');
		});
		$jq('[id^=f_text-'+id+']').keyup(function() {
			var mid =  $jq(this).attr('id').split('-');
			$jq("#savemarker-" + mid[1]).css('display','block');
		});
		
		$jq( "input[name='marker-"+id+"']" ).change(function() {
			$jq("#save" + $jq(this).attr('name')).css('display','block');
		});
	}else{
		$jq( "[class^=descr_langs]" ).change(function() {
			var mid =  $jq(this).attr('id').split('-');
			$jq("#savemarker-" + mid[1]).css('display','block');
		});
		$jq( "[class^=descr_langs]" ).keyup(function() {
			var mid =  $jq(this).attr('id').split('-');
			$jq("#savemarker-" + mid[1]).css('display','block');
		});
		
		$jq( "input[type='radio']" ).change(function() {
			$jq("#save" + $jq(this).attr('name')).css('display','block');
		});
	}
}

function getPxForLatLng() {

	var yt = $jq( "#map_draggable_container" ).css("top");
	yt = yt.substring(0,yt.indexOf('px'));
	var xt = $jq( "#map_draggable_container" ).css("left");
	xt = parseInt(xt.substring(0,xt.indexOf('px')));
	
	var point1=new google.maps.Point(parseInt(xt),parseInt(yt));
	var point2=new google.maps.Point(parseInt(xt)+$jq( "#map_draggable_container" ).width(),parseInt(yt)+$jq( "#map_draggable_container" ).height());
	
	var location1=overlay.getProjection().fromContainerPixelToLatLng(point1); 
	var location2=overlay.getProjection().fromContainerPixelToLatLng(point2);
		
	var refpoint1 = {
			"mapid": 		$jq("#map_draggable").attr("mapid"),
			"mapwidth": 	$jq("#map_draggable").attr("mapw"),
			"mapheight": 	$jq("#map_draggable").attr("maph"),
			"address":	"",
			"lat":		location1.lat(),
			"lng":		location1.lng(),
			"x":			1,
			"y":			1
	};
	
	var refpoint2 = {
			"mapid": 		$jq("#map_draggable").attr("mapid"),
			"mapwidth": 	$jq("#map_draggable").attr("mapw"),
			"mapheight": 	$jq("#map_draggable").attr("maph"),
			"address":	"",
			"lat":		location2.lat(),
			"lng":		location2.lng(),
			"x": 			$jq("#map_draggable").attr("mapw"),
			"y":			$jq("#map_draggable").attr("maph") 
	};
			
	save_ref_points_google($jq("#map_draggable").attr("mapid"),{1:refpoint1,2:refpoint2});
	
	//alert (location1.lat()+ "-" + location1.lng()+" .... "+ location2.lat()+ "-" + location2.lng());
};

//using Mercator-Projektion
function getMarkerYForLatMerkator(lat, refpoint1, refpoint2, dh1, dh2) {
	
/*	lat = 50.07;
	lng = 8.41;
	
	refpoint1 = {
			lat: 54.5159131,
			lng: 13.633191600000032,
			x:	 352,
			y:   46
	};
	refpoint2 = {
			lat: 47.6169191,
			lng: 7.670924799999966,
			x:	 76,
			y:   556
	};
	var mapheight = 600;
	*/
	
	//rotate map
	var point1y = refpoint1.mapheight * dh1 - refpoint1.y * dh1; 
	var point2y = refpoint2.mapheight * dh2 - refpoint2.y * dh2;
	
	var my = MathLib.arsinh(Math.tan(lat* Math.PI / 180)); //for 54 -> 1.3
	
	var p1y = MathLib.arsinh(Math.tan(refpoint1.lat* Math.PI / 180));   
	var p2y = MathLib.arsinh(Math.tan(refpoint2.lat* Math.PI / 180)); 
	
	//align to pix
	var pixPerY = Math.abs((point1y - point2y)/(p1y - p2y));
	
	var absp1y = p1y * pixPerY;
	var absmy  = my  * pixPerY;		
	var diff = absp1y - point1y;
	
	var y = absmy - diff;
	
	y  = refpoint1.mapheight * dh2 -y;
		
	return y;
	
	//alert (location1.lat()+ "-" + location1.lng()+" .... "+ location2.lat()+ "-" + location2.lng());
};

//use only ref points without any projection type
function getMarkerYForLatLinear(lat, refpoint1, refpoint2, dh1, dh2) {
	var pxlat = Math.abs(refpoint1.y - refpoint2.y)/Math.abs(refpoint1.lat - refpoint2.lat);
	return ((refpoint1.y + (refpoint1.lat - lat)*pxlat)*dh1 + (refpoint2.y + (refpoint2.lat - lat)*pxlat)*dh2)/2;
}