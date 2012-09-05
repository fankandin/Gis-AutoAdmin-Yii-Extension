<?php
/**
 * Initializing class for the Gis Extension for AutoAdmin.
 *
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
class AutoAdminEGis implements AutoAdminIExtension
{
	/**
	 * 
	 * @var int SRID of metric projection. Should be optimized for an area where your calculations are performed mostly.
	 * E.g.	SRID=26986 is the most accurate for Massachusetts, US.
	 *		SRID=28408 is the most accurate for the western part of Russia.
	 * For more information see @link http://www.epsg-registry.org/
	 */
	public static $srid = 4326;
	/**
	 *
	 * @var string Path to this extension by the Yii standart.
	 */
	public static $extPath = 'application.extensions.autoAdminEGis';
	/**
	 *
	 * @var string Path to assets dir.
	 */
	public static $assetPath;
	/**
	 *
	 * @var \EGeoIGisSQL An object of DB type specific class that implements EGeoIGisSQL interface.
	 */
	public static $sql;

	public static function init($initData=array())
	{
		if($initData)
		{
			foreach($initData as $param=>$value)
			{
				if(property_exists('AutoAdminEGis', $param))
					self::$$param = $value;
			}
		}
		Yii::import(self::$extPath.'.models.fields.*');
		Yii::import(self::$extPath.'.models.geo.*');
		self::$assetPath = Yii::app()->assetManager->publish(Yii::getPathOfAlias(self::$extPath.'.assets'));
		Yii::app()->clientScript->registerCssFile(self::$assetPath.'/css/gisfields.css');

		list($dbDriver, ) = explode(':', Yii::app()->db->connectionString);
		self::$sql = EGeoGisSql::get($dbDriver);
	}

	/**
	 * Little hack in order to get an opportunity to use translation messages in this extension independently.
	 * @param string $category Category name coinciding with a file name with messages.
	 * @return string Hack-string to force YII use the category.
	 */
	public static function tCategoryConvert($category)
	{
		return 'AutoAdmin.'.'../../../autoAdminEGis/messages/'.Yii::app()->getLanguage().'/'.$category;
	}
}
