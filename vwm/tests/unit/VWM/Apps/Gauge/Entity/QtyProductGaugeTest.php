<?php

namespace VWM\Apps\Gauge\Entity;
use VWM\Framework\Test\DbTestCase;

class QtyProductGaugeTest extends DbTestCase {
	
	protected $fixtures = array(
		TB_UNITTYPE,
        TB_COMPANY,
        TB_FACILITY,
        QtyProductGauge::TABLE_NAME,
	);
	

	public function testQtyProductGaugeSave() {
		
        $qtyProductGauge = new QtyProductGauge($this->db);
        $qtyProductGauge->setLimit("100");
        $qtyProductGauge->setUnitType("1");
        $qtyProductGauge->setPeriod("0");
        $qtyProductGauge->setFacilityId("2");
        
		$expectedId = 2;
		$result = $qtyProductGauge->save();
		
		$this->assertEquals($expectedId, $result);	// last id
		
		$myTest = \Phactory::get(QtyProductGauge::TABLE_NAME, array('facility_id'=>"2"));
		$this->assertTrue($myTest->id == '2');

		$sql = "SELECT * FROM " . QtyProductGauge::TABLE_NAME . " WHERE id = {$expectedId}";
		$this->db->query($sql);
		$row = $this->db->fetch_array(0);
		$qtyProductGaugeActual = new QtyProductGauge($this->db);
		$qtyProductGaugeActual->initByArray($row);
		$qtyProductGaugeActual->setLastUpdateTime(date(MYSQL_DATETIME_FORMAT));
		$this->assertEquals($qtyProductGauge, $qtyProductGaugeActual);
		
		// check UPDATE
		
		 $qtyProductGaugeUpdated = new QtyProductGauge($this->db, '1');
		 $newLimit = "500";
		 $qtyProductGaugeUpdated->setLimit($newLimit);
		 $qtyProductGaugeUpdated->save();
		 $qtyProductGaugeUpdatedTest = \Phactory::get(QtyProductGauge::TABLE_NAME, array('id'=>"1"));
		 $this->assertTrue($qtyProductGaugeUpdatedTest->limit == $newLimit);		
	}

}

?>
