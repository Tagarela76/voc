<?php
	include('modules/mail/Mail.php');
	include('modules/mail/mime.php');
	
class EMail {

    function EMail() {
    }
    
    function sendMail($from, $to, $subject, $message) {
    	$headers = array(
			'From'=> $from,
			'Subject' => $subject);
		
		$mime = new Mail_Mime("\n");
		$mime->setTxtBody($message);
		$mime->setHtmlBody("");
		
		$mimeBody = $mime->get();
		$mimeHeaders = $mime->headers($headers);
		
		$mail = &Mail::factory('mail');
		$mail->send($to, $mimeHeaders, $mimeBody);
    }
}
?>