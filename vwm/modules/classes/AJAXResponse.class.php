<?php

class AJAXResponse {
	
	private $success = true;
	private $message = '';
	public $validationRes = array();
	
	public $data;
	
	public function __construct() {		
	}	
	
	
	public function setSuccess($success) {
		if (is_bool($success)) {
			$this->success = $success;
		} else {
			return false;
		}
	}
	
	public function setMessage($message) {
		if (is_string($message)) {
			$this->message = $message;
		} else {
			return false;
		}		
	}
	
	
	public function isSuccess() {
		return $this->success;
	}
	
	public function getMessage() {
		return $this->message;
	}
	
	
	public function response() {
		$response = array(
			'success' => $this->isSuccess(),
			'message' => $this->getMessage(),
			'validation'	=> $this->validationRes,
			'data'	=> $this->data
		);
		echo json_encode($response);
	}
		
}

?>
