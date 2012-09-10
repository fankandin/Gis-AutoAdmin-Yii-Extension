<?php
/**
 * The Geometry Linestring field.
 * 
 * This field does not accept a default value.
 * 
 * @author Alexander Palamarchuk <a@palamarchuk.info>
 */
class AAFieldGislinestring extends AAField
{
	public $type='gislinestring';

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
		static $i = 0;
		if($this->value)
		{
			$coords = $this->value->get();
			$id = (string)($this->name).$i++;
			$result = CHtml::link('', '', array(
					'class'	=> 'clicon',
					'title'	=> 'View on map',
					'onclick' => "window.open('".AutoAdminEGis::$assetPath."/html/map-view.html#".$id."', 'w{$this->type}Map', 'width=750,height=600,scrollbars=0,toolbar=0,menubar=0,location=0,status=0,resizable=1');",
				));
			$result .= CHtml::tag('div', array('id'=>$id, 'class'=>'hdata'), $this->value->exportAsGeoJson());
			$result .= CHtml::tag('div', array('id'=>$id.'_srid', 'class'=>'hdata'), $this->value->getSrid());
			if(!isset($this->options['options']['showCoords']) || $this->options['options']['showCoords'])
			{
				$s = '';
				foreach($coords as $coord)
				{
					$s .= "[{$coord->lon}; {$coord->lat}], ";
				}
				if($s)
					$s = '('.rtrim($s, ', ').')';
				$result .= CHtml::tag('small', array(), $s);
			}

			return $result;
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

		$defaultCoords = $this->value ? $this->value->get(true) : null;
		$n = count($defaultCoords);
		echo CHtml::openTag('div', array('class'=>'coords'));
		echo CHtml::openTag('ol');
		for($i=0; $i<=$n; $i++)
		{
			echo CHtml::openTag('li', array('class'=>'coords-row', 'row-i'=>$i));
			$coords = $i<$n ? $defaultCoords[$i] : null;
			$tagOptions['id'] = "{$inputName}[lon][{$i}]";
			$tagOptions['tabindex']++;
			echo CHtml::label(Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'X (Lon)'), $tagOptions['id']);
			echo CHtml::textField($tagOptions['id'], ($coords ? $coords->x : null), $tagOptions);
			$tagOptions['id'] = "{$inputName}[lat][{$i}]";
			$tagOptions['tabindex']++;
			echo CHtml::label(Yii::t(AutoAdminEGis::tCategoryConvert('gisFields'), 'Y (Lat)'), $tagOptions['id']);
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
				'onclick' => "window.open('".AutoAdminEGis::$assetPath."/html/map-".strtolower(substr($this->type, 3)).".html#'+this.id, 'w{$this->type}Map', 'width=750,height=600,scrollbars=0,toolbar=0,menubar=0,location=0,status=0,resizable=1');",
			), '');
		echo CHtml::closeTag('div');

		return ob_get_clean();
	}

	public function loadFromForm($formData)
	{
		if(!isset($formData[$this->name]['lon']) || !isset($formData[$this->name]['lat']) || !is_array($formData[$this->name]['lon']) || !is_array($formData[$this->name]['lat']))
		{
			$this->value = null;
		}
		else
		{
			if(!$this->value)	//We should have an opportunity to override GeoObject type in childs.
				$this->value = new EGeoLinestring();
			foreach($formData[$this->name]['lon'] as $i=>$lon)
			{
				if(!isset($formData[$this->name]['lat'][$i]))
					continue;
				$lat = $formData[$this->name]['lat'][$i];
				if($lon !== '' && $lat !== '' && is_numeric($lon) && is_numeric($lat))
					$this->value->set(new EGeoCoords($lon, $lat));
			}
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
