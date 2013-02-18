<?php

namespace VWM\Framework;

/**
 * Manager class
 */
abstract class Manager
{
    /**
     * Attributes which can filter find results
     *
     * @var array
     */
    protected $criteria = array();

    /**
     * Get attributes which can filter find results. You can get value by
     * specified key
     *
     * @param string $key of criteria
     *
     * @return mixed array of keys with values or value of specified key
     */
    public function getCriteria($key = false)
    {
        if ($key !== false && !array_key_exists($key, $this->criteria)) {
            throw new \InvalidArgumentException('Key "'.$key.'" does not exist');
        }

        return ($key === false) ? $this->criteria : $this->criteria[$key];
    }

    /**
     * Set criteria's key
     *
     * @param string $key
     * @param string|array $value
     *
     * @throws \InvalidArgumentException if $key does not exist
     */
    public function setCriteria($key, $value)
    {
        if (!array_key_exists($key, $this->criteria)) {
            throw new \InvalidArgumentException('Key "'.$key.'" does not exist');
        }

        $this->criteria[$key] = $value;
    }

    public function findById()
    {
        throw new Exception('This method should be implemented by child');
    }

    public function findAll()
    {
        throw new Exception('This method should be implemented by child');
    }
}
