<?php

namespace VWM\Import;

abstract class EntityBuilder {

	/**	 
	 * @var \db
	 */
	protected $db;

	/**
	 * @var Mapper
	 */
	protected $mapper;
	
	function __construct(\db $db, Mapper $mapper) {
		$this->db = $db;
		$this->mapper = $mapper;
	}

}

?>
