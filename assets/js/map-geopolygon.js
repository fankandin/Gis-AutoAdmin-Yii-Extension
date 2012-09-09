var drawingManager;
var drawnFigure;
var isDefaultFigure = false;

function updateOpenerCoords(coords)
{
	var $coords = $srid.parents('.item').find('.coords');
	window.opener.EGisRemoveCoordsRow($coords, -1);
	for(var i=0; i<coords.length; i++)
		window.opener.EGisAddCoordsRow($coords, coords[i][0], coords[i][1]);
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
	updateOpenerCoords(backupData);
}

function figureOnDraw(figure)
{
	if(drawnFigure)
		removeFigure();	//for previously drawn.
	drawingManager.setOptions({drawingMode: null});
	drawnFigure = figure;
	if(drawnFigure.getPath)
	{
		google.maps.event.addListener(drawnFigure.getPath(), 'set_at', updateFigure);
		google.maps.event.addListener(drawnFigure.getPath(), 'insert_at', updateFigure);
	}
	else if(drawnFigure.getBounds)
		google.maps.event.addListener(drawnFigure, 'bounds_changed', updateFigure);
	if(!isDefaultFigure)
		updateFigure();
}

function updateFigure()
{
	var coords = [];
	if(drawnFigure.getPath)
	{
		var vertexes = drawnFigure.getPath().getArray();
		for(var i=0; i<vertexes.length; i++)
		{
			coords.push([vertexes[i].lng(), vertexes[i].lat()]);
		}
	}
	else if(drawnFigure.getBounds)
	{
		var ne = drawnFigure.getBounds().getNorthEast();
		var sw = drawnFigure.getBounds().getSouthWest();
		coords.push([sw.lng(), ne.lat()]);
		coords.push([ne.lng(), ne.lat()]);
		coords.push([ne.lng(), sw.lat()]);
		coords.push([sw.lng(), sw.lat()]);
	}
	if(coords)
		updateOpenerCoords(coords);
}

function removeFigure()
{
	if(drawnFigure)
	{
		drawnFigure.setMap(null);
		var $coords = $srid.parents('.item').find('.coords');
		window.opener.EGisRemoveCoordsRow($coords, -1);
	}
	drawingManager.setOptions({drawingMode: google.maps.drawing.OverlayType.POLYGON});
}

$(document).ready(function() {
	backupCoords()
	$('#undo').click(undoCoords);
	$('#remove').click(removeFigure);

	if($srid.val() == '4326' && $lat.length && $lon.length)
	{
		var lat = $lat.first().val();
		var lon = $lon.first().val();
		if(testCoord(lat) && testCoord(lon))
		{
			initCenterPoint(lon, lat);
		}
	}
	Map = new google.maps.Map(document.getElementById('map'), {
			center: centerPoint,
			zoom: 12,
			mapTypeId: google.maps.MapTypeId['TERRAIN']
		});
	initLatLngControl(Map);

	drawingManager = new google.maps.drawing.DrawingManager({
		drawingMode: google.maps.drawing.OverlayType.POLYGON,
		drawingControlOptions: {
			//position: google.maps.ControlPosition.TOP_CENTER,
			drawingModes: [
				google.maps.drawing.OverlayType.POLYGON,
				google.maps.drawing.OverlayType.RECTANGLE
			]
		},	
		polygonOptions: {
			strokeWeight: 2,
			editable: true
		},
		rectangleOptions: {
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
		if(coords.length)
		{
			coords.push(new google.maps.LatLng($lat[0].value, $lon[0].value));
			var figure = new google.maps.Polygon({
				paths: coords,
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
