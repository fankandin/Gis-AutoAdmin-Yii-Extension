<?php
/**
 * The Geometry Point field.
 *
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
class AAFieldGispoint extends AAField implements AAIField
{
	public $type='gispoint';

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
			foreach(array(1, 'lon', 'longitude') as $lonKey)
			{
				if(isset($this->defaultValue[$lonKey]))
				{
					$defaultValue['lon'] = $this->defaultValue[$lonKey];
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
			$this->value = new EGeoPoint();
			$this->value->setSrid($queryValue["{$this->name}_srid"]);
			$this->value->loadFromWKT($queryValue[$this->name]);
		}
	}

	public function printValue()
	{
		if($this->value)
		{
			$coords = $this->value->get();
			return "[{$coords->lon}; {$coords->lat}]";
		}
		else
			return null;
	}

	public function formInput(&$controller, $tagOptions=array())
	{
		Yii::app()->clientScript->registerScriptFile(AutoAdminEGis::$assetPath.'/js/gis-interface.js');
		ob_start();
		$inputName = $this->formInputName();
		echo CHtml::tag('b', array(), $this->label);
		if($this->isReadonly)
			$tagOptions['disabled'] = true;
		$tagOptions['pattern'] = '\-?[0-9]+(\.[0-9]+)?';

		$defaultCoords = $this->value ? $this->value->get() : null;
		$tagOptions['id'] = "{$inputName}[lon]";
		echo CHtml::label(Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'X (Lon)'), $tagOptions['id']);
		echo CHtml::textField($tagOptions['id'], ($defaultCoords ? $defaultCoords->x : $this->defaultValue['lon']), $tagOptions);
		$tagOptions['id'] = "{$inputName}[lat]";
		$tagOptions['tabindex']++;
		echo CHtml::label(Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'Y (Lat)'), $tagOptions['id']);
		echo CHtml::textField($tagOptions['id'], ($defaultCoords ? $defaultCoords->y : $this->defaultValue['lat']), $tagOptions);
		$tagOptions['id'] = "{$inputName}[srid]";
		unset($tagOptions['tabindex']);
		echo CHtml::label(Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'SRID'), $tagOptions['id']);
		echo CHtml::textField($tagOptions['id'], ($this->value ? $this->value->getSrid() : (!empty($this->options['srid']) ? $this->options['srid'] : EGeo::SRID_GPS)), $tagOptions);
		echo CHtml::tag('span', array(
				'class' => 'indmap',
				'id'	=> "{$inputName}_indmap",
				'title' => Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'Indicate on the map'),
				'onclick' => "window.open('".AutoAdminEGis::$assetPath."/html/map-".strtolower(substr($this->type, 3)).".html#'+this.id, 'w{$this->type}Map', 'width=750,height=600,scrollbars=0,toolbar=0,menubar=0,location=0,status=0,resizable=1');",
			), '');

		return ob_get_clean();
	}

	public function loadFromForm($formData)
	{
		if(!isset($formData[$this->name]['lat']) || !isset($formData[$this->name]['lon']) || $formData[$this->name]['lon'] === '' || $formData[$this->name]['lat'] === '')
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
		return ($value && $value->test());
	}

	public function modifySqlQuery()
	{
		return array('select' => array(
				new CDbExpression(AutoAdminEGis::$sql->asText("{$this->tableName}.{$this->name}")." AS {$this->name}"),
				new CDbExpression(AutoAdminEGis::$sql->getSrid("{$this->tableName}.{$this->name}")." AS {$this->name}_srid"),
			));
	}
}
