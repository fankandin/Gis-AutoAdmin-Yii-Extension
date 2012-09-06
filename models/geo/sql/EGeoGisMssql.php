<?php
/**
 * MsSql GIS driver.
 *
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
class EGeoGisMssql implements EGeoIGisSQL
{
	public function getSrid($field)
	{
		return "{$field}.STSrid";
	}

	public function asText($field)
	{
		return "{$field}.STAsText()";
	}

	public function geomFromText($wkt, $srid)
	{
		return "GEOGRAPHY::STGeomFromText('{$wkt}', {$srid})";
	}
}
