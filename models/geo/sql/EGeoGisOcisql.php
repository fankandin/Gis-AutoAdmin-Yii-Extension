<?php
/**
 * Oracle GIS driver.
 *
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
class EGeoGisOcisql implements EGeoIGisSql
{
	public function getSrid($field)
	{
		return "{$field}.sdo_srid";
	}

	public function asText($field)
	{
		return "AsText({$field})";
	}

	public function geomFromText($wkt, $srid)
	{
		return "GeomFromText('{$wkt}', {$srid})";
	}
}
