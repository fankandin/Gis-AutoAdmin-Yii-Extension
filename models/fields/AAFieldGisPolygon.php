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
		if(!isset($formData[$this->name]['lat']) || !isset($formData[$this->name]['lon']))
		{
			$this->value = null;
		}
		else
		{
			$this->value = new EGeoPoint();
			$this->value->set(new EGeoCoords($formData[$this->name]['lon'], $formData[$this->name]['lat']));
			if(!empty($formData[$this->name]['srid']))
				$this->value->setSrid($formData[$this->name]['srid']);
		}
	}

	public function valueForSql()
	{
		$value = parent::valueForSql();
		if(!($value instanceof CDbExpression))
		{
			if(!$value)
				$value = new CDbExpression("NULL");
			else
				$value = new CDbExpression(AutoAdminEGis::$sql->geomFromText($value->exportAsWKT(), $value->getSrid()));
		}
		return $value;
	}

	public function validateValue($value)
	{
		if(!parent::validateValue($value))
			return false;
		return true;
	}

	public function modifySqlQuery()
	{
		return array('select' => array(
				new CDbExpression(AutoAdminEGis::$sql->asText("{$this->tableName}.{$this->name}")." AS {$this->name}"),
				new CDbExpression(AutoAdminEGis::$sql->getSrid("{$this->tableName}.{$this->name}")." AS {$this->name}_srid"),
			));
	}
}
