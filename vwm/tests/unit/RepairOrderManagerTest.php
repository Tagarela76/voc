<?php

use VWM\Framework\Test as Testing;
use VWM\Apps\Process\ProcessTemplate;
use VWM\Hierarchy\Company;
use VWM\Hierarchy\Facility;

class RepairOrderManagerTest extends Testing\DbTestCase
{

	protected $fixtures = array(
		Company::TABLE_NAME,
        Facility::TABLE_NAME,
        TB_DEPARTMENT,
        ProcessTemplate::TABLE_NAME,
        TB_WORK_ORDER,
        TB_WO2DEPARTMENT
	);

    public function testGetDepartmentsByWo() {
		$repairOrderManager = new RepairOrderManager($this->db);
        $woId = 1;
        $departments = $repairOrderManager->getDepartmentsByWo($woId);
        $this->asserttrue(is_array($departments));
        $this->asserttrue(count($departments) == 3);
        $this->asserttrue(in_array($departments[0], array("3", "2")));
	}

	public function testSetDepartmentToWo() {
		$repairOrderManager = new RepairOrderManager($this->db);
        $woId = 1;
        $departmentId = 3;
        $repairOrderManager->setDepartmentToWo($woId, $departmentId);
        $departments = $repairOrderManager->getDepartmentsByWo($woId);
        $this->asserttrue(count($departments) == 4);
	}

    public function testUnSetDepartmentToWo() {
		$repairOrderManager = new RepairOrderManager($this->db);
        $woId = 1;
        $repairOrderManager->unSetDepartmentToWo($woId);
        $departments = $repairOrderManager->getDepartmentsByWo($woId);
        $this->asserttrue($departments == false);
	}

}