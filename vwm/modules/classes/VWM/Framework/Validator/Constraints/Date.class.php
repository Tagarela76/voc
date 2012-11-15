<?php

namespace VWM\Framework\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use VWM\Framework\Utils\DateTime;

/**
 * Custom validator for VOC WEB MANAGER dates
 * 
 * Each company may have it's own date format
 */
class Date extends Constraint {	
	public $message = 'Date format is wrong. Use this instead "%dateformat%"';		
}


class DateValidator extends ConstraintValidator {
	
	/**	 
	 * We are getting current company's date format and compare it with date 
	 * format submitted by form
	 * @param string $value - date string submitted from the form
	 * @param \Symfony\Component\Validator\Constraint $constraint
	 */
	public function isValid($value, Constraint $constraint) {		
		$format = \VOCApp::get_instance()->getDateFormat();		
		if(!DateTime::createFromFormat($format, $value)) {
			$this->setMessage($constraint->message, 
					array('%dateformat%'=>$format));
			
			return false;
		}
		
		return true;
	}
}

?>
