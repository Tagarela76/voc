<?php

namespace VWM\Calendar;

use VWM\Framework\Test\DbTestCase;
use VWM\ManageColumns\DisplayColumnsSettings;

class DisplayColumnsSettingsTest extends DbTestCase {
	
	protected $fixtures = array(
		INDUSTRY_TYPE, TB_DISPLAY_COLUMNS_SETTINGS
	);
	
	public function testGetDisplayColumnsSettings() {
		$browseCategoryEntityName = "browse_category_mix";
		$displayColumnsSettings = new DisplayColumnsSettings($this->db, '1');

		$displayColumnsSettingsObj = $displayColumnsSettings->getDisplayColumnsSettings($browseCategoryEntityName);
		$this->assertTrue($displayColumnsSettingsObj instanceof DisplayColumnsSettings);
		$this->assertTrue($displayColumnsSettingsObj->getValue() == 'Mix ID,Product Name,Description,R/O Description,Contact,R/O VIN number,VOC,Creation Date');
	}

	public function testDisplayColumnsSettingsSave() {

		$displayColumnsSettings = new DisplayColumnsSettings($this->db, '3');
		$displayColumnsSettings->setBrowseCategoryEntityId('1');
		$displayColumnsSettings->setValue('Mix ID,Product Name');
		$result = $displayColumnsSettings->save();
		
		$expectedId = 3;
		
		$this->assertEquals($expectedId, $result);	// last id
		
		$myTest = \Phactory::get(TB_DISPLAY_COLUMNS_SETTINGS, array('browse_category_entity_id'=>"1", 'industry_type_id' =>'3'));
		$this->assertTrue($myTest->id == '3');

		$sql = "SELECT * FROM " . TB_DISPLAY_COLUMNS_SETTINGS . " WHERE id = {$expectedId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$displayColumnsSettingsActual = new DisplayColumnsSettings($this->db);
		$displayColumnsSettingsActual->initByArray($row);
		$displayColumnsSettingsActual->setLastUpdateTime(date(MYSQL_DATETIME_FORMAT));
		$this->assertEquals($displayColumnsSettings, $displayColumnsSettingsActual);
		
		// check UPDATE
		
		 $displayColumnsSettingsUpdated = new DisplayColumnsSettings($this->db);
		 $newValue = "[111]";
		 $displayColumnsSettingsUpdated->setId('1');
		 $displayColumnsSettingsUpdated->setValue($newValue);
		 $displayColumnsSettingsUpdated->save();
		 $displayColumnsSettingsUpdatedTest = \Phactory::get(TB_DISPLAY_COLUMNS_SETTINGS, array('id'=>"1"));		
		 $this->assertTrue($displayColumnsSettingsUpdatedTest->value == $newValue);
		
	}

}

?>
