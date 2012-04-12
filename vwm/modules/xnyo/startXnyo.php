<?php
//	echo "Start begin<br>";
	require_once ('modules/xnyo/xnyo.class.php');
	
	$xnyo = new Xnyo();
	
	$xnyo->auth_type='sql';
	$xnyo->database_type = DB_TYPE;
	$xnyo->db_host = DB_HOST;
	$xnyo->db_user = DB_USER;
	$xnyo->db_passwd = DB_PASS;
	$xnyo->filter_vars=false;
	$xnyo->start();
?>
