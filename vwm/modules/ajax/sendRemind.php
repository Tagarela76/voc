<?php
	chdir('../..');

	require('config/constants.php');
	require_once ('modules/xnyo/xnyo.class.php');

	$site_path = getcwd().DIRECTORY_SEPARATOR;
	define ('site_path', $site_path);

	//	Include Class Autoloader
	require $site_path.'../vendor/autoload.php';

	$xnyo = new Xnyo();
	$xnyo->database_type	= DB_TYPE;
	$xnyo->db_host 			= DB_HOST;
	$xnyo->db_user			= DB_USER;
	$xnyo->db_passwd		= DB_PASS;
	$xnyo->start();

	$db->select_db(DB_NAME);
	$email = new EMail();
	$facility = new Facility($db);

	$currentTime = new DateTime('now');

	$reminderManager = new ReminderManager($db);
	$reminderList = $reminderManager->getReminders();
	foreach ($reminderList as $reminder) {
		$remind = new Reminder($db, $reminder->id, $email);
		// we should use this only for test Democompany (id = 125)
		$facilityDetails = $facility->getFacilityDetails($remind->facility_id);
		$companyId = $facilityDetails["company_id"];
		if ($companyId == 125) {
			$remindersTime = new DateTime();
			$remindersTime->setDate(date("Y", $remind->date), date("m", $remind->date), date("d", $remind->date));

			$interval = date_diff($currentTime, $remindersTime);
			if ($interval->days == 0) {
				$remind->sendRemind();
			}
		}
	}



?>
