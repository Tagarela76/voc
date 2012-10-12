<?php

namespace VWM\Framework;
use Symfony\Component\Validator\Validation;


abstract class Model {
	
	/**	 
	 * @var \db - xnyo databse
	 */
	protected $db;

	protected $modelName = "";
	
	protected $validationGroup;
		
	protected $last_update_time;	

	/**
	 * @var \Symfony\Component\Validator\Validator;
	 */
	private $validator;
	
	
	public function getLastUpdateTime() {
		return $this->last_update_time;
	}

	public function setLastUpdateTime($lastUpdateTime) {
		$this->last_update_time = $lastUpdateTime;
	}
	
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
		//	we prefer absolute path
		if(defined('site_path')) {
			$path = site_path . "modules" . DIRSEP . "resources" . DIRSEP . "validation" . DIRSEP;	
		} else {
			$path = "modules" . DIRSEP . "resources" . DIRSEP . "validation" . DIRSEP;	
		}
		
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
		
		if($this->validationGroup) {
			return $this->getValidator($modelName)->validate(
				$this, 
				array($this->validationGroup));
		} else {			
			return $this->getValidator($modelName)->validate($this);
		}
		
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
	
	/**
	 * Saves model to database	 
	 */
	public function save() {	
		$this->setLastUpdateTime(date(MYSQL_DATETIME_FORMAT));
		
		if($this->getId()) {
			return $this->_update();
		} else {
			return $this->_insert();
		}
	}
	
	/**
	 * Should be implemented by children
	 */
	protected function _insert() {
		throw new \Exception("Insert should be implemented by child");
	}
	
	/**
	 * Should be implemented by children
	 */
	protected function _update() {
		throw new \Exception("Update should be implemented by child");
	}
}
