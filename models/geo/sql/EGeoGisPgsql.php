<?php
/**
 * PostgreSQL (PostGIS) GIS driver.
 *
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
class EGeoGisPgsql implements EGeoIGisSql
{
	public function getSrid($field)
	{
		return "ST_SRID({$field})";
	}

	public function asText($field)
	{
		return "ST_AsText({$field})";
	}

	public function geomFromText($wkt, $srid)
	{
		return "ST_GeomFromText('{$wkt}', {$srid})";
	}
}
