<?php
/**
 * 2d geometry polygon on a plane.
 *
 * @author palamarchuk_a
 */
class EGeoPolygon extends EGeo
{
	function __construct()
	{
		$this->type = 'Polygon';
	}

	public function set($coords)
	{
		if(!is_array($coords))
			throw new EGeoException('Invalid argument. Must be an array of EGeoCoords objects.');
		elseif(count($coords)<4)
			throw new EGeoException('Invalid argument. Must be an array of at least 4 EGeoCoords objects (3 vertexes).');
		else
		{
			$this->coordinates = array();
			foreach($coords as $coord)
			if($coord instanceof EGeoCoords)
				$this->coordinates[] = $coord;
			else
				throw new EGeoException('Invalid argument. Each element must be an instance of the EGeoCoords class.');
		}
	}

	/**
	 * Does not support embedded polygons.
	 * @todo preg_match has a problem
	 */
	public function loadFromWKT($wkt)
	{
		if(!preg_match_all('/^POLYGON\(\(([\d\s\,\.]*)\)\,?(\(([\d\s\,\.]*)\)\,?)*\)/i', $wkt, $data))
		{
			throw new GeometryException('Incorrect Geometry Type in WKT data for Polygon.');
		}
		$pairs = preg_split('/\,\s*/', $data[1][0]);
		foreach($pairs as $pair)
		{
			list($longitude, $latitude) = explode(' ', $pair);
			$this->coordinates[] = new EGeoCoords($longitude, $latitude);
		}
	}

	public function loadFromGeoJSON($geoJson)
	{
		$data = json_decode($geoJson, true);
		if(empty($data['type']) || !isset($data['coordinates'][0]) || $data['type'] != 'Polygon')
			throw new GeometryException('Invalid geometry type in GeoJson data for Polygon.');

		foreach($data['coordinates'][0] as $coords)
		{
			$this->coordinates[] = new EGeoCoords(round($coords[0], 5), round($coords[1], 5));
		}
	}

	public function exportAsWKT()
	{
		$coordPairs = array();
		foreach($this->coordinates as $coord)
		{
			$coordPairs[] = "{$coord->x} {$coord->y}";
		}
		$wkt = 'Polygon(('.implode(',', $coordPairs).'))';
		return $wkt;
	}

	public function exportAsGeoJson()
	{
		$coords = array();
		foreach($this->coordinates as $coords)
		{
			$coords[] = array($coords->x, $coords->y);
		}
		return json_encode(array(
				'type' => 'Polygon',
				'coordinates' => $coords
			));
	}
}
