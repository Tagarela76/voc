<?php

namespace VWM\Framework;

use VWM\Framework\Test\TestCase;
use VWM\Apps\WorkOrder\Entity\Pfp;

class ModelTest extends TestCase
{

    public function testToJson()
    {
        $stub = $this->getMockForAbstractClass('VWM\Framework\Model');
        $getAttributesOutput = array(
            'id'        => 5,
            'name'      => 'test'
        );
        $stub->expects($this->any())
                ->method('getAttributes')
                ->will($this->returnValue($getAttributesOutput));

        $expectedJson = json_encode($getAttributesOutput);
        $this->assertEquals($expectedJson, $stub->toJson());

    }
}