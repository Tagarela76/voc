<?php

namespace VWM\Apps\Gauge\Entity;
use VWM\Framework\Test\DbTestCase;

class QtyProductGaugeTest extends DbTestCase {

	protected $fixtures = array(
		TB_UNITTYPE,
        TB_COMPANY,
        TB_FACILITY,
        TB_DEPARTMENT,
        QtyProductGauge::TABLE_NAME,
	);


	public function testSave() {

        $qtyProductGauge = new QtyProductGauge($this->db);
        $qtyProductGauge->setLimit("100");
        $qtyProductGauge->setUnitType("1");
        $qtyProductGauge->setPeriod("0");
        $qtyProductGauge->setFacilityId("2");

		$expectedId = 5;
		$result = $qtyProductGauge->save();

		$this->assertEquals($expectedId, $result);	// last id

		$myTest = \Phactory::get(QtyProductGauge::TABLE_NAME, array('facility_id'=>"2"));
		$this->assertEquals(4, $myTest->id);

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
		 $qtyProductGaugeUpdatedTest = \Phactory::get(QtyProductGauge::TABLE_NAME,
                 array('id'=>$qtyProductGaugeUpdated->getId()));
		 $this->assertEquals($newLimit, $qtyProductGaugeUpdatedTest->limit);
	}

    public function testGetCurrentUsage()
    {

    }

}

?>
