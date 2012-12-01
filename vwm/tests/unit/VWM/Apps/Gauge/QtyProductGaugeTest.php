<?php

namespace VWM\Apps\Gauge;
use VWM\Framework\Test\DbTestCase;

class QtyProductGaugeTest extends DbTestCase {
	
	protected $fixtures = array(
		TB_UNITTYPE,
        TB_COMPANY,
        TB_FACILITY,
        TB_QTY_PRODUCT_GAGE
	);
	

	public function testQtyProductGageSave() {
		
        $qtyProductGage = new QtyProductGage($this->db);
        $qtyProductGage->setLimit("100");
        $qtyProductGage->setUnit_type("1");
        $qtyProductGage->setPeriod("0");
        $qtyProductGage->setFacility_id("3");
        
		$expectedId = 3;
		$result = $qtyProductGage->save();
		
		$this->assertEquals($expectedId, $result);	// last id
		
		$myTest = \Phactory::get(TB_QTY_PRODUCT_GAGE, array('facility_id'=>"3"));
		$this->assertTrue($myTest->id == '3');

		$sql = "SELECT * FROM " . TB_QTY_PRODUCT_GAGE . " WHERE id = {$expectedId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$qtyProductGageActual = new QtyProductGage($this->db);
		$qtyProductGageActual->initByArray($row);
		$qtyProductGageActual->setLastUpdateTime(date(MYSQL_DATETIME_FORMAT));
		$this->assertEquals($qtyProductGage, $qtyProductGageActual);
		
		// check UPDATE
		
		 $qtyProductGageUpdated = new QtyProductGage($this->db, '1');
		 $newLimit = "500";
		 $qtyProductGageUpdated->setLimit($newLimit);
		 $qtyProductGageUpdated->save();
		 $qtyProductGageUpdatedTest = \Phactory::get(TB_QTY_PRODUCT_GAGE, array('id'=>"1"));		
		 $this->assertTrue($qtyProductGageUpdatedTest->limit == $newLimit);		
	}

}

?>
