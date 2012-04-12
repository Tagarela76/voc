<?php

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/smarty/modifier.debug_xnyo_print_var.php
 *              Reformat a variable into text
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: modifier.debug_xnyo_print_var.php 2 2004-07-06 19:49:11Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/smarty/modifier.debug_xnyo_print_var.php $
 * Authors:     Robert Amos <bok[at]odynia.org>
 *              Andrew Wellington <proton[at]wiretapped.net>
 *              vort <vort[at]solutionstap.com>
 *
 * Copyright (c) 2001-2004 Robert Amos <bok[at]odynia.org>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
**/

function smarty_modifier_debug_xnyo_print_var($var)
{

	// array?
	if (is_array($var))
	{
		$output = '<b>array</b> (';
		if (count($var))
		{
			foreach ($var as $k => $v)
				if (is_int($k))
					$output .= '\''.$v.'\', ';
				else
					$output .= $k.' => \''.$v.'\', ';
			$output = substr($output, 0, -2);
		}
		return $output.')';
	}
	
	// object?
	if (is_object($var))
	{
		$output = '<b>object of class '.get_class($var).'</b>';
		return $output;
	}

	// bool?
	if (is_bool($var))
		if ($var)
			return '<b>TRUE</b>';
		else
			return '<b>FALSE</b>';

	// null?
	if (is_null($var))
		return '<b>NULL</b>';

	// empty?
	if ($var === '')
		return '<i>empty</i>';

	// clean up everything else
	$var = (string)$var;
	$var = htmlspecialchars($var);
	
	// borrowed from smarty's debug_print_var modifier..
	$var = strtr($var, array("\n"=>'<i>&#92;n</i>', "\r"=>'<i>&#92;r</i>', "\t"=>'<i>&#92;t</i>'));

	return $var;
}

?>
