<?php
/**
 * Class of an object for storing of a pair of 2d-geometry coordinates.
 *
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
class EGeoCoords
{
	/**
	 *
	 * @var float X-axis coordinate.
	 */
	public $x;
	/**
	 *
	 * @var float Y-axis coordinate.
	 */
	public $y;
	/**
	 *
	 * @var float GPS Longitude coordinate. An alias for $this->x.
	 */
	public $lon;
	/**
	 *
	 * @var float GPS Latitude coordinate. An alias for $this->y.
	 */
	public $lat;

	public function __construct($x, $y)
	{
		$this->lon =& $this->x;
		$this->lat =& $this->y;
		$this->x = (float)$x;
		$this->y = (float)$y;
	}
}
