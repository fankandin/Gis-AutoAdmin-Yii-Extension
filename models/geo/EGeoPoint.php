<?php
/**
 * 2d-geometry point.
 *
 * @author palamarchuk_a
 */
class EGeoPoint extends EGeo
{
	function __construct()
	{
		$this->type = 'Point';
	}

	public function set($coords)
	{
		if($coords instanceof EGeoCoords)
			$this->coordinates = $coords;
		else
			throw new EGeoException('Invalid argument. Must be an instance of the EGeoCoords class.');
	}

	public function loadFromWKT($wkt)
	{
		if(false === ($this->coordinates = self::parseWKT($wkt)))
			throw new EGeoException('Invalid Geometry Type in WKT data for Point.');
	}

	public function loadFromGeoJson($geoJson)
	{
		$data = json_decode($geoJson, true);
		if(empty($data['type']) || !isset($data['coordinates']) || $data['type'] != 'Point')
			throw new EGeoException('Invalid geometry type in GeoJSON data for Point.');
		$this->coordinates = new EGeoCoords($data['coordinates'][0], $data['coordinates'][1]);
	}

	public function exportAsWKT()
	{
		return "Point({$this->coordinates->x} {$this->coordinates->y})";
	}

	public function exportAsGeoJson()
	{
		return json_encode(array(
				'type' => 'Point',
				'coordinates' => array((float)$this->coordinates->x, (float)$this->coordinates->y)
			));
	}

	public static function parseWKT($wkt)
	{
		if(!preg_match('/^POINT\((\d+\.?\d*) (\d+\.?\d*)\)$/i', $wkt, $matches))
			return false;
		return new EGeoCoords((float)$matches[1], (float)$matches[2]);
	}
}
