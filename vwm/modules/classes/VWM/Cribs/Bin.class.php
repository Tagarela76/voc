<?php

namespace VWM\Cribs;

use VWM\Framework\Model;

class Bin extends Model {
	
	protected $id;
	protected $crib_id;
	protected $number;
	protected $size;
	protected $type;
	protected $capacity;
	
	/**
	 * Crib to whom this bin assigned
	 * @var Crib
	 */
	protected $crib;
	
	
}

?>
