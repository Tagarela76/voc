<?php

/*
 * Smarty plugin - Xnyo: Application Backend
 * -------------------------------------------------------------
 * Type:      function
 * Name:      date
 * Purpose:   format a date using the date() function
 * Arguments: format    - how to format the date, as per the date() page in 
 *                        the manual
 *            timestamp - strtotime parsable timestamp 
 * -------------------------------------------------------------
 */
function smarty_function_date($args)
{
	// extract the arguments into the local variable scope
	extract($args);

	// specify our default format
	if (empty($format)) $format = "l, jS F Y";

	// set the date, using the timestamp if its given
	if (!empty($timestamp) && strtotime($timestamp) != -1)
		$timestamp = strtotime($timestamp);

	$date = empty($timestamp) ? date($format) : date($format, $timestamp);

	// display the date
	echo $date;

}

/* vim: set expandtab: */

?>
