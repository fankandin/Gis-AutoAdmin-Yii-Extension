var Map;
var geocoder;
var backupData;
var centerPoint;
google.load('maps', '3', {'other_params': 'sensor=false&libraries=drawing'});

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
		var $tds = $('#curpos td');
		$tds.first().html(latLng ? latLng.lat().toFixed(6) : '');
		$tds.last().html(latLng ? latLng.lng().toFixed(6) : '');
	};
	var obj = new LatLngControl(map);
	//google.maps.event.addListener(Map, 'mouseover', function(mEvent) {latLngControl.set('visible', true);});
	google.maps.event.addListener(map, 'mouseout', function(mEvent) {obj.updatePosition(null);});
	google.maps.event.addListener(map, 'mousemove', function(mEvent) {obj.updatePosition(mEvent.latLng);});
}

function initCenterPoint(lat, lon)
{
	centerPoint = new google.maps.LatLng(lat, lon);
}

function testCoord(coord)
{
	return (coord != '' && (coord <= 360 || Math.abs(coord) <= 180));
}

$(document).ready(function() {
	if(google.loader.ClientLocation)
		initCenterPoint(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);
	else if (navigator.geolocation.getCurrentPosition(function(position) {
			initCenterPoint(position.coords.latitude, position.coords.longitude);
		}));
	else
		initCenterPoint(50.95, 6.966667);
});
