<?php
	include('modules/mail/Mail.php');
	include('modules/mail/mime.php');
	
class EMail {

	var $mimeType = "text/plain; charset=iso-8859-1";

    function EMail($isHtml = false) {
		if ($isHtml){
			$mimeType = "text/html; charset=iso-8859-1";
		}
    }
    
    function sendMail($from, $to, $subject, $message) {
    	$headers = array(
			'From'=> $from,
			'Subject' => $subject,
			'Content-type' => $mimeType);
		
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