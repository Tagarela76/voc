<?php

namespace VWM\Framework;
use Symfony\Component\Validator\Validation;


abstract class Model {
	
	/**	 
	 * @var db - xnyo databse
	 */
	protected $db;

	protected $modelName = "";
	
	protected $validationGroup = "Default";

	/** 	 
	 * @var \Symfony\Component\Validator\Validator;
	 */
	private $validator;

	/**
	 * 
	 * Overvrite get property if property is not exists or private.
	 * @param unknown_type $name - property name. method call method 
	 * get_%property_name%, if method does not exists - return property value; 
	 */
	public function __get($name) {
		if (method_exists($this, "get_" . $name)) {
			$methodName = "get_" . $name;
			$res = $this->$methodName();
			return $res;
		} else if (property_exists($this, $name)) {
			return $this->$name;
		} else {
			return false;
		}
	}

	/**
	 * 
	 * Overvrive set property. If property reload function set_%property_name% 
	 * exists - call it. Else - do nothing. Keep OOP =)
	 * @param unknown_type $name - name of property
	 * @param unknown_type $value - value to set
	 */
	public function __set($name, $value) {
		/* Call setter only if setter exists */
		if (method_exists($this, "set_" . $name)) {
			$methodName = "set_" . $name;
			$this->$methodName($value);
		}		
	}

	/** 	 	 
	 * @param string model name
	 * @return \Symfony\Component\Validator\Validator
	 */
	protected function getValidator($name) {
		$path = "modules" . DIRSEP . "resources" . DIRSEP . "validation" . DIRSEP;
		if ($this->validator === null) {
			$this->validator = Validation::createValidatorBuilder()
					->addYamlMapping($path . $name . '.yml')
					->getValidator();
		}

		return $this->validator;
	}

	/** 	 
	 * @return Symfony\Component\Validator\ConstraintViolationList
	 */
	public function validate($modelName = false) {
		if (!$modelName) {
			$modelName = $this->modelName;
		}
		return $this->getValidator($modelName)->validate($this, array($this->validationGroup));
	}
	
	
	/**
	 * Ini object by properties array in key=>value format
	 * @param array $array
	 */
	public function initByArray($array) {
		foreach ($array as $key => $value) {
			try {
				$this->$key = $value;
			} catch (Exception $e) {
				$this->errors[] = $e->getMessage();
			}
		}
	}
	
	/**
	 * 
	 * @param type string
	 */
	public function setValidationGroup($validationGroup) {
		$this->validationGroup = $validationGroup;
	}

}
