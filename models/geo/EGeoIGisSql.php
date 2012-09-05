<?php
/**
 *
 * Interface for GIS SQL drivers.
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
interface EGeoIGisSQL
{
	/**
	 * Generates a specific function for getting SRID of a geometry/geography field.
	 * @param string $field Field name.
	 */
	public function getSrid($field);

	/**
	 * Generates a specific function for getting a geometry/geography field in Well-Known text format (WKT).
	 * @param string $field Field name.
	 */
	public function asText($field);

	/**
	 * Generates a specific function for gettinga geometry/geography type from Well-known text format (WKT).
	 * @param string $field Field name.
	 */
	public function geomFromText($wkt, $srid);
}
