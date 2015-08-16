
//var $jq = jQuery.noConflict();
var geocoder;
var map;
var overlay;
var status='';


function codeAddress(address,latFieldId, lngFieldId, callback) {
  var lat;
  var lng;
  geocoder = new google.maps.Geocoder();
  geocoder.geocode( { 'address': address}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
    	lat = results[0].geometry.location.lat();
    	lng = results[0].geometry.location.lng();
    	setCoords(lat, lng, latFieldId, lngFieldId);
    	$jq("#coordsFound").val("found");
    	
    	if (typeof callback === "function"){
    		callback();
    	}
    	
    }else{
    	displayPopupMsg(wpdmp_popup.check_addr.msg,wpdmp_popup.check_addr.title);
    }
  });  
  return status;
}

function setCoords(lat, lng, latFieldId, lngFieldId){

	$jq(latFieldId).val(lat);
	$jq(lngFieldId).val(lng);
	
	$jq('#markerdesc').css('display','block');
	$jq('#addmarker').css('display','block');		
}

function clearCoords(addressFieldId, latFieldId, lngFieldId){
	//clean lat and lng
	$jq("#coordsFound").val('');
	$jq(addressFieldId).val('');
	$jq(latFieldId).val('');
	$jq(lngFieldId).val('');
	$jq('#addmarker').css('display','none');
	$jq('#markerdesc').css('display','none');
}

function getCoords(addressFieldId, latFieldId, lngFieldId, add_temp_pl) {
	
	$jq(latFieldId).val('');
	$jq(lngFieldId).val('');
	
	var addr = $jq(addressFieldId).val();
	
	var callback;
	if (add_temp_pl){
		callback = function(){add_temp_place($jq(latFieldId).val(),$jq(lngFieldId).val(),$jq(addressFieldId).val());}; 
	}
	var status = codeAddress(addr, latFieldId, lngFieldId, callback);
	
	/*if (add_temp_pl){
		
	}*/
}