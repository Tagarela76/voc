<?php

namespace VWM\Framework\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Custom validator for VOC WEB MANAGER dates
 * 
 * Each company may have it's own date format
 */
class Date extends Constraint {	
	public $message = 'Date format is wrong';
}


class DateValidator extends ConstraintValidator {
	// validate class will be places here
}

?>
