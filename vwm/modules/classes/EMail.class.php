<?php
	include(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'modules/mail/Mail.php');
	include(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'modules/mail/mime.php');
	
class EMail {

	var $mimeType = "text/plain; charset=iso-8859-1";
    
    function EMail($isHtml = false) {
		if ($isHtml){
			$this->mimeType = "text/html; charset=iso-8859-1";
		}
    }
    
    function sendMail($from, $to, $subject, $message) {
    	$headers = array(
			'From'=> $from,
			'Subject' => $subject,
			'Content-type' => $this->mimeType);
		
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