<?php
/**
 * Numeric field
 *
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
class AAFieldGisPoint extends AAField implements AAIField
{
	public $type='GisPoint';

	public function completeOptions()
	{
		if(!empty($this->defaultValue))
		{
			if(!is_array($this->defaultValue) || count($this->defaultValue) > 2)
				throw new AAException(Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'The parameter "defaultValue" is set incorrectly for the field {fieldName}', array('{fieldName}'=>$this->name)));
			$defaultValue = array();
			foreach(array(0, 'lat', 'latitude') as $latKey)
			{
				if(isset($this->defaultValue[$latKey]))
				{
					$defaultValue['lat'] = $this->defaultValue[$latKey];
					break;
				}
			}
			foreach(array(1, 'lon', 'longitude') as $latKey)
			{
				if(isset($this->defaultValue[$latKey]))
				{
					$defaultValue['lon'] = $this->defaultValue[$latKey];
					break;
				}
			}
			if(!isset($defaultValue['lat']) || !isset($defaultValue['lon']))
				throw new AAException(Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'The parameter "defaultValue" is set incorrectly for the field {fieldName}', array('{fieldName}'=>$this->name)));
			$this->defaultValue = $defaultValue;
		}
	}

	public function loadFromSql($queryValue)
	{
		if(isset($queryValue[$this->name]))
		{
			$this->value = CJSON::decode($queryValue[$this->name]);
		}
	}

	public function formInput(&$controller, $tagOptions=array())
	{
		ob_start();
		$inputName = $this->formInputName();
		echo CHtml::tag('b', array(), $this->label);
		if($this->isReadonly)
			$tagOptions['disabled'] = true;
		$tagOptions['pattern'] = '[0-9]+(\.[0-9]+)?';

		echo CHtml::label(Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'Latitude'), "{$inputName}[lat]");
		$tagOptions['id'] = "{$inputName}[lat]";
		echo CHtml::textField($tagOptions['id'], (isset($this->value['coordinates'][1]) ? $this->value['coordinates'][1] : $this->defaultValue['lat']), $tagOptions);
		echo CHtml::label(Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'Longitude'), "{$inputName}[lon]");
		$tagOptions['id'] = "{$inputName}[lon]";
		echo CHtml::textField($tagOptions['id'], (isset($this->value['coordinates'][0]) ? $this->value['coordinates'][0] : $this->defaultValue['lon']), $tagOptions);
		echo CHtml::tag('span', array('class'=>'indmap', 'title'=>Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'Indicate on the map')));

		return ob_get_clean();
	}

	public function printValue()
	{
		if($this->value)
		{
			return sprintf("[%f; %f]", $this->value['coordinates'][1], $this->value['coordinates'][0]);
		}
		else
			return null;
	}

	public function loadFromForm($formData)
	{
		if(!isset($formData[$this->name]['lat']) || !isset($formData[$this->name]['lon']))
		{
			$this->value = null;
		}
		else
		{
			$this->value = array(
				'type' => 'Point',
				'coordinates' => array((double)$formData[$this->name]['lon'], (double)$formData[$this->name]['lat'])
			);
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
				$value = new CDbExpression("ST_SetSRID(ST_GeomFromGeoJSON('".CJSON::encode($value)."'), ".AutoAdminEGis::GEO_SRID.")");
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
		return array('select' => array(new CDbExpression("ST_AsGeoJson({$this->tableName}.{$this->name}) AS {$this->name}")));
	}
}
