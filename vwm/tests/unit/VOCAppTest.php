<?php

use VWM\Framework\Test\TestCase;

class VOCAppTest extends TestCase
{
    public function testAddSharedService()
    {
        $pfpService = array(
            'name' => 'pfp',
            'callback'=> function($c) {
                return new \VWM\Apps\WorkOrder\Manager\PfpManager();
            },
        );
        $inspectionTypeService = array(
            'name' => 'inspectionType',
            'callback'=> function($c) {
                return new \VWM\Apps\Logbook\Manager\InspectionTypeManager();
            },
        );
        VOCApp::getInstance()->addSharedService($pfpService['name'], $pfpService['callback']);
        VOCApp::getInstance()->addSharedService($inspectionTypeService['name'], $inspectionTypeService['callback']);

        $pfpManager = VOCApp::getInstance()->getService($pfpService['name']);
        $this->assertInstanceOf('\VWM\Apps\WorkOrder\Manager\PfpManager', $pfpManager);

        $inspectionTypeManager = VOCApp::getInstance()->getService($inspectionTypeService['name']);
        $this->assertInstanceOf('\VWM\Apps\Logbook\Manager\InspectionTypeManager', $inspectionTypeManager);
    }
}