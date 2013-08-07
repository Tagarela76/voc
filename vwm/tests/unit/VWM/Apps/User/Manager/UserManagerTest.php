<?php

namespace VWM\Apps\User\Manager;
use VWM\Framework\Test\DbTestCase;

class UserManagerTest extends DbTestCase
{
    public function testUserManager()
    {
        $uManager = \VOCApp::getInstance()->getService('user');
        $this->assertTrue($uManager instanceof UserManager);
    }
    
    
}
?>
