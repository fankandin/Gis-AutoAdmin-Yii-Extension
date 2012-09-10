$(document).ready(function() {
	var id = document.location.hash.substr(1);
	var geoData = jQuery.parseJSON(window.opener.$('#data-list td [id="'+ id +'"]').html());
	var srid = window.opener.$('#data-list td [id="'+ id +'_srid"]').html();
	if(srid == '4326')
	{
		var userCenter = false;
		switch(geoData.type)
		{
			case 'Point':
				var lon = geoData.coordinates[0];
				var lat = geoData.coordinates[1];
				userCenter = true;
				break;
			case 'LineString':
				var lon = geoData.coordinates[0][0];
				var lat = geoData.coordinates[0][1];
				userCenter = true;
				break;
			case 'Polygon':
				var lon = geoData.coordinates[0][0];
				var lat = geoData.coordinates[0][1];
				userCenter = true;
				break;
		}
		if(userCenter && testCoord(lat) && testCoord(lon))
		{
			initCenterPoint(lon, lat);
		}
		Map = new google.maps.Map(document.getElementById('map'), {
				center: centerPoint,
				zoom: 9,
				mapTypeId: google.maps.MapTypeId['TERRAIN']
			});
		initLatLngControl(Map);
		geocoder = new google.maps.Geocoder();

		var geoObj;
		switch(geoData.type)
		{
			case 'Point':
				var geoObj = new google.maps.Marker({
					position: new google.maps.LatLng(geoData.coordinates[1], geoData.coordinates[0]),
					title: 'Point is here',
					draggable: false
				});
				break;
			case 'LineString':
				var coords = [];
				for(var i=0; i<geoData.coordinates.length; i++)
				{
					coords.push(new google.maps.LatLng(geoData.coordinates[i][1], geoData.coordinates[i][0]));
				}
				var geoObj = new google.maps.Polyline({
					path: coords,
					strokeWeight: 2,
					editable: false
				});
				break;
			case 'Polygon':
				var coords = [];
				for(var i=0; i<geoData.coordinates.length; i++)
				{
					coords.push(new google.maps.LatLng(geoData.coordinates[i][1], geoData.coordinates[i][0]));
				}
				var geoObj = new google.maps.Polygon({
					paths: coords,
					strokeWeight: 2,
					editable: false
				});
				break;
		}
		if(geoObj)
			geoObj.setMap(Map);
	}
});
