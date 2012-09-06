var Map;
var geocoder;
var backupData;
google.load('maps', '3', {'other_params': 'sensor=false'});

var $inputs = window.opener.$('[id="'+ document.location.hash.substr(1) +'"]').parents('.item').find('input');
var $srid = $inputs.filter('[name$="[srid]"]');
var $lat = $inputs.filter('[name*="[lat]"]');
var $lon = $inputs.filter('[name*="[lon]"]');

function initLatLngControl(map)
{
	function LatLngControl(map)
	{
		this.ANCHOR_OFFSET_ = new google.maps.Point(8, 8);
		this.setMap(map);
	}
	LatLngControl.prototype = new google.maps.OverlayView();
	LatLngControl.prototype.draw = function() {};
	LatLngControl.prototype.updatePosition = function(latLng) {
		$('#latlng-control').html(latLng ? 'Lat: '+ latLng.lat().toFixed(5) +'<br/>Lon: '+ latLng.lng().toFixed(5) : '');
	};
	var obj = new LatLngControl(map);
	//google.maps.event.addListener(Map, 'mouseover', function(mEvent) {latLngControl.set('visible', true);});
	google.maps.event.addListener(map, 'mouseout', function(mEvent) {obj.updatePosition(null);});
	google.maps.event.addListener(map, 'mousemove', function(mEvent) {obj.updatePosition(mEvent.latLng);});
}

$(document).ready(function() {
});
