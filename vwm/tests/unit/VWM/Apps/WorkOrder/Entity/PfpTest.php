<?php

namespace VWM\Apps\WorkOrder\Entity;

use VWM\Framework\Test\DbTestCase;

class PfpTest extends DbTestCase
{

    public $fixtures = array(
        Pfp::TABLE_NAME,
    );

    public function testSave()
    {
        $pfp = new Pfp();
        $pfp->setDescription('Test 6 Pfp');
        $pfp->setWeightNumberSort(6);
        $pfp->setWeightLetterSort('Test ');
        $id = $pfp->save();
        $this->assertEquals(6, $id);

        $pfp->setDescription('Updated');
        $this->assertEquals(6, $pfp->save());
    }

    public function testSetSortColumns()
    {
        $pfp = new Pfp();
        $pfp->setDescription('Test/777/Pfp');
        $pfp->setSortColumns();
        $this->assertEquals($pfp->getWeightLetterSort(), 'Test/');
        $this->assertEquals($pfp->getWeightNumberSort(), '777');
    }
}
?>
