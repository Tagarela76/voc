<?php
error_reporting(0);
include("constants.php");
$mail_result= 'Thanks you! Your information has been sent to our administrator.<br>You will be redirected to the previous page in 3 seconds.';
if ($_REQUEST['first_name']!='' && $_REQUEST['last_name']!='') {
	$mail_text.= 'First Name: ' . $_REQUEST['first_name'] . '<br />';
	$mail_text.= 'Middle Initial: ' . $_REQUEST['middle_initial'] . '<br />';
	$mail_text.= 'Last Name: ' . $_REQUEST['last_name'] . '<br />';
	$mail_text.= 'First Address: ' . $_REQUEST['address1'] . '<br />';
	if ($_REQUEST['address2'] != '') {
		$mail_text.= 'Second Address: ' . $_REQUEST['address2'] . '<br />';
	}
	
	$mail_text.= 'City: ' . $_REQUEST['city'] . '<br />';
	$mail_text.= 'Zip Code: ' . $_REQUEST['zip'] . '<br />';
	$mail_text.= 'Phone Number: ' . $_REQUEST['phone_number'] . '<br />';
	$mail_text.= 'Email address: ' . $_REQUEST['email_address'] . '<br />';
	$mail_text.= 'He is a: ' . $_REQUEST['i_am_a'] . '<br />';
	$mail_text.= 'Position Interested In: ' . $_REQUEST['position'] . '<br />';
	$mail_text.= 'Are You CTP Sertified? ' . $_REQUEST['ctp_sertified'] . '<br />';
	$mail_text.= 'If you are a physician, are you licensed in the State of California? ' . $_REQUEST['fis_licensed'] . '<br />';
	$mail_text.= 'Are you presently working as an insurance examiner? ' . $_REQUEST['ins_examiner'] . '<br />';
	$mail_text.= 'Are you certified to do EKG\'s? ' . $_REQUEST['ekg_sertified'] . '<br />';
	$mail_text.= 'Do you have experience working in health fairs? ' . $_REQUEST['hf_experience'] . '<br />';
	$mail_text.= 'Do you have over 500 blood draws? ' . $_REQUEST['blood_draws'];
	$mail_topic = TOPIC;
	$mail_target = EMAIL;
	$headers.= 'MIME-Version: 1.0' . "\r\n";
	$headers.= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	mail($mail_target, $mail_topic, $mail_text, $headers);
}

echo '<html><head><meta http-equiv="refresh" content="3; url=index-6.html"><title>mail result</title></head><body>' . $mail_result . '</body></html>';

?>