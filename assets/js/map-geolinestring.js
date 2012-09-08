var drawingManager;
var drawnFigure;
var isDefaultFigure = false;

function updateOpenerCoords(coords)
{
	//console.dir(coords)
	$srid.val('4326');
}

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

function figureOnDraw(figure)
{
	if(drawnFigure)
		removeFigure();	//for previously drawn.
	drawingManager.setOptions({drawingMode: null});
	drawnFigure = figure;
	google.maps.event.addListener(drawnFigure.getPath(), 'set_at', updateFigure);
	google.maps.event.addListener(drawnFigure.getPath(), 'insert_at', updateFigure);
	if(!isDefaultFigure)
		updateFigure();
}

function updateFigure()
{
	var coords = [];
	var vertexes = drawnFigure.getPath().getArray();
	for(var i=0; i<vertexes.length; i++)
	{
		coords.push([vertexes[i].lng(), vertexes[i].lat()]);
	}
	if(coords)
		updateOpenerCoords(coords);
}

function removeFigure()
{
	if(drawnFigure)
		drawnFigure.setMap(null);
}

$(document).ready(function() {
	backupCoords()
	$('#undo').click(undoCoords);
	$('#remove').click(removeFigure);

	if($srid.val() == '4326' && $lat.length && $lon.length)
	{
		var lat = $lat.first().val();
		var lon = $lon.first().val();
		if(lat <= 360 || Math.abs(lat) <= 180 && lon <= 360 || Math.abs(lon) <= 180)
		{
			initCenterPoint(lat, lon);
		}
	}
	Map = new google.maps.Map(document.getElementById('map'), {
			center: centerPoint,
			zoom: 12,
			mapTypeId: google.maps.MapTypeId['TERRAIN']
		});
	initLatLngControl(Map);

	drawingManager = new google.maps.drawing.DrawingManager({
		drawingMode: google.maps.drawing.OverlayType.POLYLINE,
		drawingControlOptions: {
			drawingModes: [
				google.maps.drawing.OverlayType.POLYLINE
			]
		},	
		polylineOptions: {
			strokeWeight: 2,
			editable: true
		}
	});
	drawingManager.setMap(Map);

	if($srid.val() == '4326' && $lat.length && $lon.length)
	{
		var coords = [];
		for(var i=0; i<$lat.length; i++)
		{
			if($lon[i] && testCoord($lat[i].value) && testCoord($lon[i].value))
			{
				coords.push(new google.maps.LatLng($lat[i].value, $lon[i].value));
			}
		}
		if(coords)
		{
			var figure = new google.maps.Polyline({
				path: coords,
				strokeWeight: 2,
				editable: true
			});
			figure.setMap(Map);
			isDefaultFigure = true;
			figureOnDraw(figure);
			isDefaultFigure = false;
		}
	}

	google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
		figureOnDraw(event.overlay);
	});
});
