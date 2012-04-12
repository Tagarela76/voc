<?php

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/smarty/modifier.debug_print_delay.php
 *              Take a number of seconds and make it into hh:ii:ss
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: modifier.debug_print_delay.php 2 2004-07-06 19:49:11Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/smarty/modifier.debug_print_delay.php $
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

function smarty_modifier_debug_print_delay($string)
{
	$delay = time() - $string;

	// number of hours
	if ($delay > 3600)
	{
		$hours = (int)($delay / 3600);
		$delay = (int)($delay % 3600);
	}
	
	// number of minutes
	if ($delay > 60)
	{
		$minutes = (int)($delay / 60);
		$delay = (int)($delay % 60);
	}

	// number of seconds
	$seconds = $delay;
	
	// format it nicely and go :)
	return sprintf('%02d:%02d:%02d ago', $hours, $minutes, $seconds);
}

?>
