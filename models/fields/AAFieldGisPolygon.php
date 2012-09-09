<?php
/**
 * The Geometry Polygon field.
 *
 * This field does not accept a default value.
 * 
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
class AAFieldGisPolygon extends AAFieldGisLinestring
{
	public $type='GisPolygon';

	public function loadFromSql($queryValue)
	{
		if(isset($queryValue[$this->name]))
		{
			$this->value = new EGeoPolygon();
			$this->value->setSrid($queryValue["{$this->name}_srid"]);
			$this->value->loadFromWKT($queryValue[$this->name]);
		}
	}

	public function formInput(&$controller, $tagOptions=array())
	{
		$inputBlock = parent::formInput($controller, $tagOptions);
		$inputBlock .= CHtml::tag('div', array('class'=>'desc'), Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'The looping vertex which matches with the first one will be set automatically. Do not input it!'));
		return $inputBlock;
	}

	public function loadFromForm($formData)
	{
		$this->value = new EGeoPolygon();
		parent::loadFromForm($formData);
		if($this->value)
		{
			$this->value->closePath();
		}
	}
}
