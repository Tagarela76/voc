<?php

namespace VWM\Apps\Process\ProcessTemplate;

use VWM\Apps\WorkOrder\Entity\WorkOrder;
use VWM\Framework\Test\DbTestCase;
use VWM\Apps\Process\ProcessTemplate;
use VWM\Apps\Process\ProcessInstance;
use VWM\Apps\Process\StepInstance;
use VWM\Hierarchy\Facility;
use VWM\Hierarchy\Company;


class ProcessInstanceTest extends DbTestCase
{

    const TB_STEP = 'step_instance';
    const TB_TEMPLATE_STEP = 'step_template';
    const TB_PROCESS = 'process_instance';

    public $fixtures = array(
        Company::TABLE_NAME,
        Facility::TABLE_NAME,
        ProcessTemplate::TABLE_NAME,
        WorkOrder::TABLE_NAME,
        self::TB_PROCESS,
        self::TB_STEP,
        self::TB_TEMPLATE_STEP
    );

    public function testSave()
    {
        $facilityId = 100;
        $workOrderId = 1;
        $process = new ProcessInstance($this->db);
        $process->setFacilityId($facilityId);
        $process->setName('newTestProcess');
        $process->setWorkOrderId($workOrderId);
        $processID = $process->save();

        $sql = "SELECT * FROM " . self::TB_PROCESS . " " .
                "WHERE id=" . $processID;
        $this->db->query($sql);
        $result = $this->db->fetch_all_array();
        $this->assertEquals($process->getId(), $result[0]['id']);
        $this->assertEquals($process->getName(), $result[0]['name']);
        $this->assertEquals($process->getFacilityId(), $result[0]['facility_id']);
        $this->assertEquals($process->getWorkOrderId(), $result[0]['work_order_id']);

        //test Update
        $newFacilityId = 200;
        $newWorkOrderId = 2;
        $process->setFacilityId($newFacilityId);
        $process->setWorkOrderId($newWorkOrderId);
        $process->setName('newName');
        $process->save();

        $sql = "SELECT * FROM " . self::TB_PROCESS . " " .
                "WHERE id=" . $processID;
        $this->db->query($sql);
        $result = $this->db->fetch_all_array();

        $this->assertEquals($process->getId(), $result[0]['id']);
        $this->assertEquals($process->getName(), $result[0]['name']);
        $this->assertEquals($process->getFacilityId(), $result[0]['facility_id']);
        $this->assertEquals($process->getWorkOrderId(), $result[0]['work_order_id']);
    }

    public function testGetSteps()
    {
        $woProcessId = 2;
        $process = new ProcessInstance($this->db, $woProcessId);
        $steps = $process->getSteps();

        $sql = "SELECT * FROM " . self::TB_STEP .
                " WHERE process_id = " . $woProcessId;
        $this->db->query($sql);

        $result = $this->db->fetch_all_array();

        $count = count($result);

        $this->assertEquals($count, count($steps));

        for ($i = 0; $i < $count; $i++) {
            $this->assertEquals($steps[$i]->getNumber(), $result[$i]['number']);
            $this->assertEquals($steps[$i]->getProcessId(), $result[$i]['process_id']);
            $this->assertEquals($steps[$i]->getId(), $result[$i]['id']);
        }
        //$this->assertEquals($process->getWorkOrderId(), $row['work_order_id']);
    }

    public function testGetStepsCreatingByUser(){
        $processTemplateId = 2;
        $processTemplate = new ProcessTemplate($this->db);
        $processTemplate->setId($processTemplateId);
        $processTemplate->load();
        $stepsTemplate = $processTemplate->getSteps();
        $this->assertEquals(count($stepsTemplate), 3);
        
        $processInstanceId = 2;
        $processInstance = new ProcessInstance($this->db);
        $processInstance->setId($processInstanceId);
        $processInstanceCreatingByUser = $processInstance->getStepsCreatingByUser(count($stepsTemplate));
        $this->assertTrue(is_null($processInstanceCreatingByUser));
        $this->assertEquals(count($processInstanceCreatingByUser), 0);
        //create new StepInstance
        $newStepNumber = count($processInstanceCreatingByUser)+count($stepsTemplate)+1;
        $stepInstance = new \VWM\Apps\Process\StepInstance($this->db);
        $stepInstance->setProcessId($processInstanceId);
        $stepInstance->setDescription('someDescription');
        $stepInstance->setNumber($newStepNumber);
        $stepInstance->save();
        
        $processInstanceCreatingByUser = $processInstance->getStepsCreatingByUser(count($stepsTemplate));
        $this->assertEquals(count($processInstanceCreatingByUser), 1);
        $this->assertEquals($stepInstance->getNumber(), 4);
    }
}
?>
