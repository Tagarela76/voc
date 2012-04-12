<?php

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/smarty/block.error.php
 *              Display error block if there are errors
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: block.error.php 2 2004-07-06 19:49:11Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/smarty/block.error.php $
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

/*
 * Smarty plugin - Xnyo: Application Backend
 * -------------------------------------------------------------
 * Type:      block function
 * Name:      error
 * Purpose:   display errors, if any
 * Arguments: id - (optional) id of the client error to check for
 * -------------------------------------------------------------
 */
function smarty_block_error($args, $content, $smarty)
{

	global $xnyo_parent;
	if (!is_null($content)) {

		// split it, for our else situation
		$content = explode('<::XNYO::ERROR::FUNCTION::SPLIT>', $content);

		// now, if we've been given an arg..
		if (!empty($args['id']))
		{
			// singular
			$data = $xnyo_parent->dump_errors(CLIENT, $args['id']);
			if (!empty($data))
				echo $content[0];
			elseif (!empty($content[1]))
				echo $content[1];
		} else
		{
			$dump = $xnyo_parent->dump_errors(CLIENT);
			if (count($dump))
				echo $content[0];
			elseif (!empty($content[1]))
				echo $content[1];
		}
	}

}

/* vim: set expandtab: */

?>
