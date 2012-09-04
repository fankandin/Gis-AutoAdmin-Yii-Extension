<?php
/**
 * Initializing class for the Gis Extension for AutoAdmin.
 *
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
class AutoAdminEGis implements AutoAdminIExtension
{
	public static $srid = 4326;
	public static $extPath = 'application.extensions.autoAdminEGis';
	public static $assetPath;

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
