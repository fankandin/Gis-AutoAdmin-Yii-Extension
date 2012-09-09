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
		if($coords instanceof EGeoCoords)	//step-by-step addition
			$this->coordinates[] = $coords;
		elseif(is_array($coords) && count($coords)>=3)	//set at once
		{
			foreach($coords as $coord)
				$this->set($coord);
		}
		else
			throw new EGeoException('Invalid argument. Must be an array of EGeoCoords objects.');
	}

	/**
	 * Returns the coordinates (internally stored).
	 * @param bool $unclose Whether to return enclosed path or remove the last vertex which is equal to the first one.
	 */
	public function get($unclose=false)
	{
		return $unclose ? array_splice($this->coordinates, 0, -1): $this->coordinates;
	}

	public function test()
	{
		if(isset($this->coordinates) && count($this->coordinates) >= 4)
		{
			foreach($this->coordinates as $coord)
			{
				if(!($coord instanceof EGeoCoords && $this->testCoord($coord->x) && $this->testCoord($coord->y)))
					return false;
			}
			if($this->coordinates[0] == end($this->coordinates))
				return true;
		}
		return false;
	}

	/**
	 * Does not support embedded polygons.
	 * @todo preg_match seems having a problem.
	 */
	public function loadFromWKT($wkt)
	{
		if(!preg_match_all('/^POLYGON\(\(([\-\d\s\,\.]*)\)\,?(\(([\-\d\s\,\.]*)\)\,?)*\)/i', $wkt, $data))
		{
			throw new EGeoException('Incorrect Geometry Type in WKT data for Polygon.');
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
			throw new EGeoException('Invalid geometry type in GeoJson data for Polygon.');

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

	/**
	 * Adds the last vertex to the coordinates that is equal to the first one.
	 */
	public function closePath()
	{
		$this->set($this->coordinates[0]);
	}
}
