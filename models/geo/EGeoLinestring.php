<?php
/**
 * 2d geometry line on a plane; is defined be separate points with the linear interpolation between them.
 *
 * @author palamarchuk_a
 */
class EGeoLinestring extends EGeo
{
	function __construct()
	{
		$this->type = 'LineString';
	}

	public function set($coords)
	{
		if($coords instanceof EGeoCoords)	//step-by-step addition
			$this->coordinates[] = $coords;
		elseif(is_array($coords) && count($coords)>=2)	//set at once
		{
			foreach($coords as $coord)
				$this->set($coord);
		}
		else
			throw new EGeoException('Invalid argument. Must be an array of EGeoCoords objects.');
	}

	public function test()
	{
		if(isset($this->coordinates) && count($this->coordinates) >= 2)
		{
			foreach($this->coordinates as $coord)
			{
				if(!($coord instanceof EGeoCoords && $this->testCoord($coord->x) && $this->testCoord($coord->y)))
					return false;
			}
			return true;
		}
		return false;
	}

	public function loadFromWKT($wkt)
	{
		$t = str_ireplace('LINESTRING(', '', str_replace(')', '', $wkt));
		$pairs = explode(',', $t);
		if(!$pairs)
			throw new EGeoException('Invalid geometry type in WKT data for LineString.');
		foreach($pairs as $pair)
		{
			list($x, $y) = explode(' ', $pair);
			$this->coordinates[] = new EGeoCoords($x, $y);
		}
	}

	public function loadFromGeoJSON($geoJson)
	{
		$data = json_decode($geoJson, true);
		if(empty($data['type']) || !isset($data['coordinates']) || $data['type'] != 'LineString')
			throw new EGeoException('Invalid geometry type in GeoJSON data for LineString.');

		foreach($data['coordinates'] as $coords)
			$this->coordinates[] = new EGeoCoords($coords[0], $coords[1]);
	}

	public function exportAsWKT()
	{
		$coordPairs = array();
		foreach($this->coordinates as $coords)
		{
			$coordPairs[] = "{$coords->x} {$coords->y}";
		}
		$wkt = 'LineString('.implode(',', $coordPairs).')';
		return $wkt;
	}

	public function exportAsGeoJson()
	{
		$coords = array();
		foreach($this->coordinates as $coord)
		{
			$coords[] = array($coord->x, $coord->y);
		}
		return json_encode(array(
				'type' => 'LineString',
				'coordinates' => $coords
			));
	}

	/**
	 * Adds a new point to the geometry.
	 * @param \EGeoCoords $coords Coordinates of a point.
	 * @throws EGeoException
	 */
	public function addPoint($coords)
	{
		if(false === $coords || !($coords instanceof EGeoCoords))
			throw new EGeoException('Invalid point coordinates.');
		array_push($this->coordinates, $coords);
	}

	/**
	 * Splits MultiLineString passed in WKT format on separate LineString objects
	 * @param string $wkt MultiLineString in WKT
	 * @return array An array of EGeoLinestring objects.
	 */
	public static function splitMultiFromWKT($wkt)
	{
		if(preg_match('/^MULTILINESTRING\(\((.+?)\)\)$/i', $wkt, $matches))
		{
			$lines = array();
			$ar = explode('),(', $matches[1]);
			foreach($ar as $lineCoords)
			{
				$line = new GeometryLinestring();
				$line->loadFromWKT("LINESTRING({$lineCoords})");
				$lines[] = $line;
			}
			return $lines;
		}
		else
		{
			$line = new EGeoLinestring;
			$line->loadFromWKT($wkt);
			return array($line);
		}
	}
}
