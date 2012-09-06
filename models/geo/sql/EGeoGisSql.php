<?php
/**
 * Description of EGeoGisSql
 *
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
class EGeoGisSql
{
	public static function get($dbDriver)
	{
		if(in_array($dbDriver, array('mssql', 'sqlsrv', 'dblib')))
			return new EGeoGisMssql();
		elseif($dbDriver == 'pgsql')
			return new EGeoGisPgsql();
		elseif($dbDriver == 'oci')
			return new EGeoGisOcisql();
		else
			return new EGeoGisMysql();
	}
}
