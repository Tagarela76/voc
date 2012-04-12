<?php

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/smarty/compiler.display_form.php
 *              Display a form
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: compiler.display_form.php 9 2004-07-14 23:44:32Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/smarty/compiler.display_form.php $
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

function smarty_compiler_display_form($tag_attrs, &$compiler)
{
	$_params = $compiler->_parse_attrs($tag_attrs);
	
	if (!isset($_params['name'])) {
		$compiler->_syntax_error("display_form: missing 'name' parameter", E_USER_WARNING);
		return;
	}

	// whats this meant to be?
	$_params['name'] = str_replace('\'', '', $_params['name']);

	$output = 'global $xnyo_parent, $forms; '."\n";
	$output .= 'if (!is_object($forms) && !$xnyo_parent->load_plugin(\'forms\')) {'."\n";
	$output .= "\t".'$xnyo_parent->trigger_error(\'Unable to load forms plugin, cant display form.\', WARNING);'."\n";
	$output .= '} else {'."\n";
	$output .= "\t".'$forms->display(\''.$_params['name'].'\', $this);'."\n";
	$output .= "\t".'require_once($forms->get_compiled_filename());'."\n";
	$output .= '}'."\n";
	return $output;
}

/* vim: set expandtab: */

?>
