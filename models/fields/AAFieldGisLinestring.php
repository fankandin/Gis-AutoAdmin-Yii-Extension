<?php
/**
 * The Geometry Linestring field.
 * 
 * This field does not accept a default value.
 * 
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
class AAFieldGisLinestring extends AAField
{
	public $type='GisLinestring';

	public function completeOptions()
	{
		$this->defaultValue = null;
	}

	public function loadFromSql($queryValue)
	{
		if(isset($queryValue[$this->name]))
		{
			$this->value = new EGeoLinestring();
			$this->value->setSrid($queryValue["{$this->name}_srid"]);
			$this->value->loadFromWKT($queryValue[$this->name]);
		}
	}

	public function printValue()
	{
		if($this->value)
		{
			$coords = $this->value->get();
			$s = '';
			foreach($coords as $coord)
			{
				$s .= "[{$coord->lon}; {$coord->lat}], ";
			}
			if($s)
				$s = '('.rtrim($s, ', ').')';
			return $s;
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
		$tagOptions['pattern'] = '[0-9]+(\.[0-9]+)?';

		$defaultCoords = $this->value ? $this->value->get() : null;
		$n = count($defaultCoords);
		echo CHtml::openTag('div', array('class'=>'coords'));
		echo CHtml::openTag('ol');
		for($i=0; $i<$n; $i++)
		{
			echo CHtml::openTag('li', array('class'=>'coords-row', 'row-i'=>$i));
			$coords = $i+1<$n ? $defaultCoords[$i] : null;
			$tagOptions['id'] = "{$inputName}[lon][{$i}]";
			$tagOptions['tabindex']++;
			echo CHtml::label(Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'X (Longitude)'), $tagOptions['id']);
			echo CHtml::textField($tagOptions['id'], ($coords ? $coords->x : null), $tagOptions);
			$tagOptions['id'] = "{$inputName}[lat][{$i}]";
			$tagOptions['tabindex']++;
			echo CHtml::label(Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'Y (Latitude)'), $tagOptions['id']);
			echo CHtml::textField($tagOptions['id'], ($coords ? $coords->y : null), $tagOptions);
			echo CHtml::tag('span', array('class'=>'delrow', 'title'=>Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'Delete this row')), '');
			echo CHtml::closeTag('li');
		}
		echo CHtml::closeTag('ol');
		echo CHtml::tag('span', array('class'=>'newrow', 'title'=>Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'Add a new row')), '');
		echo CHtml::closeTag('div');
		echo CHtml::tag('div', array('class'=>'collapse'), '');

		$tagOptions['id'] = "{$inputName}[srid]";
		unset($tagOptions['tabindex']);
		echo CHtml::openTag('div', array('class'=>'coords-manage'));
		echo CHtml::label(Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'SRID'), $tagOptions['id']);
		echo CHtml::textField($tagOptions['id'], ($this->value ? $this->value->getSrid() : (!empty($this->options['srid']) ? $this->options['srid'] : EGeo::SRID_GPS)), $tagOptions);
		echo CHtml::tag('span', array(
				'class' => 'indmap',
				'id'	=> "{$inputName}_indmap",
				'title' => Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'Indicate on the map'),
				'onclick' => "window.open('".AutoAdminEGis::$assetPath."/html/map-".strtolower(substr($this->type, 3)).".html#'+this.id, 'w{$this->type}Map', 'width=700,height=600,scrollbars=0,toolbar=0,menubar=0,location=0,status=0,resizable=1');",
			), '');
		echo CHtml::closeTag('div');

		return ob_get_clean();
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
