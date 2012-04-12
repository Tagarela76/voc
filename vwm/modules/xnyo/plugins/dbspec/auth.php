<?php

class dbspec_auth {

//	var $_database = "voc";
	var $_database = DB_NAME;

//	if (SYSTEM == "VPS") {
//		 $_title = "vps_user";
//	} else {
//		$_title = "user";
//	}

	var $_title = USERS_TABLE;
	
	var $username = "accessname";
	var $password = "password";
	var $groups = "accesslevel_id";


}

?>
