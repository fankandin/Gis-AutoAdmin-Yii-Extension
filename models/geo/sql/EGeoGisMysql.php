<?php
/**
 * Mysql GIS driver.
 *
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
class EGeoGisMysql implements EGeoIGisSql
{
	public function getSrid($field)
	{
		return "SRID({$field})";
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
