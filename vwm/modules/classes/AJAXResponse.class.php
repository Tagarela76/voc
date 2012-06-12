<?php

/**
 * Handles server/client communication via AJAX
 */

class AJAXResponse {

	/**
	 * Form validation flag
	 * @var boolean true if validation passed, false on failure
	 */
	private $success = true;

	/**
	 * Flash notification text
	 * @var string
	 */
	private $message = '';

	/**
	 * Array which contains errors for each form input
	 * @var array
	 */
	public $validationRes = array();

	/**
	 * Form data
	 * @var mixed
	 */
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


	/**
	 * Send AJAXResponse to client
	 */
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
