var PolygonCreator;

function backupCoords()
{
	backupData = [];
	for(var i=0; i<$lon.length; i++)
	{
		backupData[i] = [$lon[i].value, $lat[i].value];
	}
}

function undoCoords()
{
	for(var i=0; i<$lon.length; i++)
	{
		$lon[i].value = backupData[i][0];
		$lat[i].value = backupData[i][1];
	}
}

function removeFigure()
{
	console.dir(PolygonCreator)
	//creator.destroy();
}

$(document).ready(function() {
	backupCoords()
	$('#undo').click(undoCoords);
	$('#remove').click(removeFigure);

	var center;
	if($srid.val() == '4326')
	{
		if($lat.length && $lon.length)
		{
			var lat = $lat.first().val();
			var lon = $lon.first().val();
			if(lat <= 360 || Math.abs(lat) <= 180 && lon <= 360 || Math.abs(lon) <= 180)
			{
				center = new google.maps.LatLng(lat, lon);
			}
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

	PolygonCreator = new PolygonCreator(Map);
	PolygonCreator.showData();
	PolygonCreator.showColor();
	PolygonCreator.setOnDrawFinish(function() {console.info(PolygonCreator.showData())});
});
