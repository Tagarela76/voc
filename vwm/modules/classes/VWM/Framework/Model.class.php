<?php

namespace VWM\Framework;

use Symfony\Component\Validator\Validation;

/**
 * Model is the base class providing the common features needed by data model
 * objects.
 */
abstract class Model
{

    /**
     * @var \db - xnyo databse - DEPRECATED. Use service approuch
     * <pre>
     * \VOCApp::getInstanse()->getService('db');
     * </pre>
     */
    protected $db;

    /**
     * @var string representation of the model. Used by validation at this moment
     */
    protected $modelName = "";

    /**
     * @var string validationGroup is used to trigger different validation
     * scenarios. For example, validation rules are different for login and sign up
     */
    protected $validationGroup;

    /**
     * TODO: Use DateTime one day
     * @var string common field for major models
     */
    protected $last_update_time;

    /**
     * @var \Symfony\Component\Validator\Validator
     */
    private $validator;

    public function getLastUpdateTime()
    {
        return $this->last_update_time;
    }

    public function setLastUpdateTime($lastUpdateTime)
    {
        $this->last_update_time = $lastUpdateTime;
    }

    /**
     * TODO: get rid of this
     *
     * Overvrite get property if property is not exists or private.
     * @param mixed $name - property name. method call method
     * get_%property_name%, if method does not exists - return property value;
     */
    public function __get($name)
    {
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
     * TODO: get rid of this
     *
     * Overvrive set property. If property reload function set_%property_name%
     * exists - call it. Else - do nothing. Keep OOP =)
     * @param unknown_type $name - name of property
     * @param unknown_type $value - value to set
     */
    public function __set($name, $value)
    {
        /* Call setter only if setter exists */
        if (method_exists($this, "set_" . $name)) {
            $methodName = "set_" . $name;
            $this->$methodName($value);
        }
    }

    /**
     * @param string model name
     *
     * @return \Symfony\Component\Validator\Validator
     */
    protected function getValidator($name)
    {
        //	we prefer absolute path
        if (defined('site_path')) {
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
    public function validate($modelName = false)
    {
        if (!$modelName) {
            $modelName = $this->modelName;
        }

        if ($this->validationGroup) {
            return $this->getValidator($modelName)->validate(
                            $this, array($this->validationGroup));
        } else {
            return $this->getValidator($modelName)->validate($this);
        }
    }

    /**
     * Ini object by properties array in key=>value format
     *
     * @param array $array
     */
    public function initByArray($array)
    {
        foreach ($array as $key => $value) {
            try {
                $this->$key = $value;
            } catch (Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }
    }

    /**
     * @param string
     */
    public function setValidationGroup($validationGroup)
    {
        $this->validationGroup = $validationGroup;
    }

    /**
     * Convert model to JSON string
     *
     * At this moment method converts only scalar values.
     * THIS WILL NOT WORK WITH RELATIONS
     *
     * @return string
     */
    public function toJson()
    {
        $attributes = $this->getAttributes();
        
        return json_encode($attributes);
    }

    /**
     * Saves model to database
     *
     * @return int newly created recored ID
     */
    public function save()
    {
        $this->setLastUpdateTime(date(MYSQL_DATETIME_FORMAT));

        if ($this->getId()) {
            return $this->_update();
        } else {
            return $this->_insert();
        }
    }

    /**
     * TODO: make it abstract
     *
     * Should be implemented by children
     */
    protected function _insert()
    {
        throw new \Exception("Insert should be implemented by child");
    }

    /**
     * TODO: make it abstract
     *
     * Should be implemented by children
     */
    protected function _update()
    {
        throw new \Exception("Update should be implemented by child");
    }

    /**
     * Model properties that are readable by world
     *
     * @return array property => value
     */
    abstract protected function getAttributes();

}
