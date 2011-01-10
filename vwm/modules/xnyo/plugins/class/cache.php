<?php

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/class/cache.php
 *              Cache Control Functions
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: cache.php 2 2004-07-06 19:49:11Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/class/cache.php $
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

class cache_plugin
{

	/**
	 * Fetch Cache
	 *
	 * Check for and fetch a cache'd file.
	**/
	public function _fetch_cache ()
	{
		global $xnyo_parent;

		// ok, first things first, last things last, you know the drill

		// no handler? we're screwed!
		if (empty($xnyo_parent->cache_handler))
		{
			$xnyo_parent->trigger_error('No cache handler set, but caching is on!', WARNING);
			return false;
		}

		// load it first, to be sure
		if (!$xnyo_parent->load_plugin($xnyo_parent->cache_handler, 'cache'))
		{
			$xnyo_parent->trigger_error('Unable to load configured cache handler '.$xnyo_parent->cache_handler, WARNING);
			return false;
		}

		// do the warez
		$method_name = "_cache_".$xnyo_parent->cache_handler."_handler";
		if ($xnyo_parent->$method_name->read())
			exit;
		return false;
	}


	/**
	 * Output Buffer Handler
	 *
	 * Cache the output
	**/
	public function output_buffer_handler ($buffer)
	{
	
		global $xnyo_parent, $input;
		
		// not caching? fine with me (and never cache debug output)
		if (!$xnyo_parent->cache || $xnyo_parent->debug)
			return $buffer;
	
		// no handler? we're screwed!
		if (empty($xnyo_parent->cache_handler))
		{
			$xnyo_parent->trigger_error('No caching handler set but caching is on', WARNING);
			return $buffer;
		}
	
		// load it first, to be sure
		if (!$xnyo_parent->load_plugin($xnyo_parent->cache_handler, 'cache'))
		{
			$xnyo_parent->trigger_error('Couldnt load configured cache handler.', WARNING);
			return $buffer;
		}
	
		// do the warez
		$method_name = "_cache_".$xnyo_parent->cache_handler."_handler";
		$xnyo_parent->$method_name->write($buffer);
	
		return $buffer;
	}
}
	