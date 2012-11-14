<?php

namespace VWM\Apps\Sales\Entity;

use VWM\Framework\Test\DbTestCase;
use VWM\Apps\Sales\Manager\SalesContactsManager;

class SalesContactTest extends DbTestCase {

	public $fixtures = array(
		TB_USER,
		TB_BOOKMARKS_TYPE,
		'contacts',
		'contacts2type',
		MeetingWithContact::TABLE_NAME,
	);

	public function testGetMeetings() {
		$manager = new SalesContactsManager($this->db);
		$contact = $manager->getSalesContact(1);
		$meetings = $contact->getMeetings();
		$this->assertCount(1, $meetings);
		$this->assertInstanceOf('VWM\Apps\Sales\Entity\MeetingWithContact',
				$meetings[0]);

		$meetingExpected = new MeetingWithContact($this->db, 1);
		$this->assertEquals($meetingExpected, $meetings[0]);

		$contactWithoutMeetings = $manager->getSalesContact(2);
		$meetings = $contactWithoutMeetings->getMeetings();
		$this->assertCount(0, $meetings);
	}
}

?>
