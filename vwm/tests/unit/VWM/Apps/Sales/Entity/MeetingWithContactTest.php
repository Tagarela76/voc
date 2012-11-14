<?php

namespace VWM\Apps\Sales\Entity;

use VWM\Framework\Test\DbTestCase;
use VWM\Framework\Utils\DateTime;

class MeetingWithContactTest extends DbTestCase {

	public $fixtures = array(
		TB_USER, 
		'contacts',
		MeetingWithContact::TABLE_NAME,
	);

	public function testSave() {

		$datetime = new DateTime('2012-12-25 15:00');
		$meeting = new MeetingWithContact($this->db);
		$meeting->setContactId('1');
		$meeting->setUserId('5');
		$meeting->setMeetingDate((string)$datetime->getTimestamp());
		$meeting->setNotes('Greate meeting');

		$r = $meeting->save();
		$this->assertEquals(2, $r);

		$fetchedMeeting = new MeetingWithContact($this->db, $r);
		$this->assertEquals($meeting, $fetchedMeeting);

		// update
		$meeting->setNotes('Meeting failed');
		$meeting->save();
		$fetchedAfterUpdateMeeting = new MeetingWithContact($this->db, $meeting->getId());
		$this->assertEquals($meeting, $fetchedAfterUpdateMeeting);
	}
}

?>
