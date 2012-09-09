<?php
/**
 * The EGeo abstract class. Defines the general view of a plain geometry object.
 *
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
abstract class EGeo
{
	/**
	 * 
	 * SRID of metric projection. Should be optimized for an area where your calculations are performed mostly.
	 * E.g.	SRID=26986 is the most accurate for Massachusetts, US.
	 *		SRID=28408 is the most accurate for the western part of Russia.
	 * For more information see @link http://www.epsg-registry.org/
	 */
	const SRID_METRIC = 28408;
	/**
	 * 
	 * SRID of the WGS84 projection. Widely used with GPS devices, Google Maps etc. May be changed only if you need a specific coordinate system for describing geo objects.
	 */
	const SRID_GPS = 4326;

	/**
	 *
	 * @var An SRID which coordinates are defined in.
	 */
	protected $srid = self::SRID_GPS;
	/**
	 *
	 * @var Internally defined (by a class) storage for coordinates.
	 */
	protected $coordinates;
	/**
	 *
	 * @var string Geometry type;
	 */
	protected $type;
	/**
	 *
	 * @var array Unconditioned object options.
	 */
	protected $options = array();

	/**
	 * At lest should set $this->type property.
	 */
	abstract public function __construct();

	/**
	 * Gets the type of a geometry object (which inherites this abstract class).
	 * @return string The string constant of a geometry type.
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Sets the coordinates directly, specifically for a geometry type.
	 * @param mixed $coords An object of EGeoCoords class or an array of such objects.
	 */
	abstract public function set($coords);

	/**
	 * Returns the coordinates (internally stored).
	 * @param mixed $params Any specific param for child overloadings.
	 */
	public function get($params=null)
	{
		return $this->coordinates;
	}

	/**
	 * Manipulates with $this->options property.
	 * @param string $param Property name.
	 * @param mixed $value Property value.
	 */
	public function __set($param, $value)
	{
		$this->options[$param] = $value;
	}

	/**
	 * Manipulates with $this->options property.
	 * @param string $param Property name.
	 * @return mixed A $this->options property value.
	 */
	public function __get($param)
	{
		return isset($this->options[$param]) ? $this->options[$param] : null;
			
	}

	/**
	 * Sets the SRID of an object.
	 * @param int $srid SRID.
	 */
	public function setSrid($srid)
	{
		$this->srid = (int)$srid;
	}

	/**
	 * Gets the SRID of an object.
	 * @return int SRID of an object.
	 */
	public function getSrid()
	{
		return $this->srid;
	}

	/**
	 * Checks whether the object is completed with sufficient and correct coordinates.
	 * @return bool Whether the object is completed with sufficient and correct coordinates.
	 */
	abstract public function test();

	/**
	 * Checks whether the coordinate is a correct one in current $this->srid projection.
	 * The function returns TRUE in any occasions if it doesn't know an SRID.
	 * @param int|float $coord Numeric coordinate.
	 * @return bool Whether the coordinate is a correct one in current $this->srid projection.
	 * @todo Tests for more SRIDs.
	 */
	public function testCoord($coord)
	{
		switch($this->srid)
		{
			case 4326:
				return ($coord != '' && ($coord <= 360 || abs($coord) <= 180));
			default:
				return true;
		}
	}

	/**
	 * Loads a geometry from Well-known text (WKT) format.
	 * @param string $wkt WKT-representation of a gemetry.
	 * @throws EGeoException
	 */
	abstract public function loadFromWKT($wkt);

	/**
	 * Loads a geometry from GeoJson text (GeoJson) format.
	 * @param string $geoJson GeoJson-representation of a gemetry.
	 * @throws EGeoException
	 */
	abstract public function loadFromGeoJSON($geoJson);

	/**
	 * Exports internally presented coordinates as WKT.
	 * @return string Coordinates in WKT format.
	 */
	abstract public function exportAsWKT();

	/**
	 * Exports internally presented coordinates as GeoJson.
	 * @return string Coordinates in GeoJson format.
	 */
	abstract public function exportAsGeoJson();
}

/**
 *  Specific Geometry exceptions.
 */
class EGeoException extends Exception {}