function geocodePosition(pos)
{
	geocoder.geocode({latLng: pos},	function(responses) {
			if(responses && responses.length > 0)
				updateMarkerAddress(responses[0].formatted_address);
			else
				updateMarkerAddress('Cannot determine address at this location.');
		}
	);
}

function updateMarkerStatus(str)
{
	$('#markerStatus').html(str);
}

function updateMarkerPosition(latLng)
{
	//document.getElementById('info').innerHTML = [latLng.lat(), latLng.lng()].join(', ');
}

function updateMarkerAddress(str)
{
	$('#address').html(str);
}

function backupCoords()
{
	backupData = [$lon.val(), $lat.val()];
}

function undoCoords()
{
	$lon.val(backupData[0]);
	$lat.val(backupData[1]);
}

$(document).ready(function() {
	backupCoords()
	$('#undo').click(undoCoords);

	var center;
	var defCoords = false;
	if($srid.val() == '4326')
	{
		var lat = $lat.val();
		var lon = $lon.val();
		if(lat <= 360 || Math.abs(lat) <= 180 && lon <= 360 || Math.abs(lon) <= 180)
		{
			center = new google.maps.LatLng(lat, lon);
			defCoords = true;
		}
	}
	if(!center)
		center = new google.maps.LatLng(google.loader.ClientLocation.latitude, google.loader.ClientLocation.longitude);
	Map = new google.maps.Map(document.getElementById('map'), {
			center: center,
			zoom: 5,
			mapTypeId: google.maps.MapTypeId['TERRAIN']
		});
	initLatLngControl(Map);
	if(defCoords)
	{
		var pointOpts = {
			position: center,
			title: 'Point is here',
			draggable: true
		};
		var gMarker = new google.maps.Marker(pointOpts);
		gMarker.setMap(Map);
	}

	geocoder = new google.maps.Geocoder();
	//Add dragging event listeners.
	google.maps.event.addListener(gMarker, 'dragstart', function() {
		updateMarkerAddress('Dragging...');
	});
	google.maps.event.addListener(gMarker, 'drag', function() {
		updateMarkerStatus('Dragging...');
		updateMarkerPosition(gMarker.getPosition());
	});
	google.maps.event.addListener(gMarker, 'dragend', function() {
		updateMarkerStatus('Drag ended');
		var pos = gMarker.getPosition();
		geocodePosition(pos);
		$lat.val(pos.lat());
		$lon.val(pos.lng());
		$srid.val('4326');
	});
});
