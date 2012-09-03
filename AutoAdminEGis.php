<?php
/**
 * Initializing class for the Gis Extension for AutoAdmin.
 *
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
class AutoAdminEGis implements AutoAdminIExtension
{
	const GEO_SRID = 4326;
	public static $assetPath;

	public static function init()
	{
		Yii::import('application.extensions.autoAdminEGis.models.fields.*');
		Yii::import('');
		self::$assetPath = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.extensions.autoAdminEGis.assets'));
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
