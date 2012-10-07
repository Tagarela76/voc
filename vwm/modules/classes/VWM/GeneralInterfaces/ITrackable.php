<?php

namespace VWM\GeneralInterfaces;


interface ITrackable {
	public function increaseQty($byQty = 1);
	public function decreaseQty($byQty = 1);
}

?>
