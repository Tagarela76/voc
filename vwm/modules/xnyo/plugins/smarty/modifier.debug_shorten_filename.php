<?php

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/smarty/modifier.debug_shorten_filename.php
 *              Shorten a filename so it doesnt look so clunky :)
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: modifier.debug_shorten_filename.php 2 2004-07-06 19:49:11Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/smarty/modifier.debug_shorten_filename.php $
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

function smarty_modifier_debug_shorten_filename($file)
{
	$file = str_replace(XNYO_DIR, 'XNYO_DIR'.DIRSEP, $file);
	$file = str_replace(SCRIPT_DIR, 'SCRIPT_DIR'.DIRSEP, $file);
	$file = str_replace(SMARTY_DIR, 'SMARTY_DIR'.DIRSEP, $file);
	$file = str_replace(ROOT_DIR, 'ROOT_DIR'.DIRSEP, $file);

	return $file;
}

?>
