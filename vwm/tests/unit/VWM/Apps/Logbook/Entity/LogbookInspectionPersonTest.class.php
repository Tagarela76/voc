<?php
use VWM\Framework\Test as Testing;
use VWM\Apps\Logbook\Entity\LogbookInspectionPerson;
use VWM\Framework\Model;

class LogbookInspectionPersonTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        LogbookInspectionPerson::TABLE_NAME
    );
    public function testSave()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $inspectionPerson = new LogbookInspectionPerson();
        $inspectionPerson->setFacilityId('1');
        $inspectionPerson->setName('Tagarela');
        $id = $inspectionPerson->save();

        $sql = 'SELECT * FROM inspection_persons WHERE id = ' . $id;
        $db->query($sql);
        $result = $db->fetch_all_array();
        $this->assertEquals($inspectionPerson->getFacilityId(), $result[0]['facility_id']);
        $this->assertEquals($inspectionPerson->getName(), $result[0]['name']);
        
        //UPDATE
        $newFacilityId = 2;
        $newName = 'NewTagarela';
        $inspectionPerson->setFacilityId($newFacilityId);
        $inspectionPerson->setName($newName);
        $inspectionPerson->save();
        $this->assertEquals($inspectionPerson->getFacilityId(), $newFacilityId);
        $this->assertEquals($inspectionPerson->getName(), $newName);
    }

}
?>