<?php

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/class/filter.php
 *              Input Filter Control Functions
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: filter.php 57 2004-09-12 10:30:17Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/class/filter.php $
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

class filter_plugin
{
	// storage container!
	private $storage;

	// Initialise and auto-filter
	public function init ()
	{
		global $xnyo_parent;

		// setup our cache
		$this->storage = new stdClass;

		// caching for input variables
		$this->storage->get_vars = $_GET;
		$this->storage->post_vars = $_POST;
		$this->storage->cookie_vars = $_COOKIE;

		// unset varibles
		$_GET = $_POST = $_COOKIE = $_REQUEST = array();

		// filter the get variables (if we have any)
		if (XNYO_DEBUG) $xnyo_parent->debug('Filtering GET Variables');
		if (count($xnyo_parent->get_vars))
			foreach ($this->storage->get_vars as $key => $var)
				// if they havent specified this variable then obviously we ignore it here
				if (!empty($xnyo_parent->get_vars[$key]))
					$this->filter_get_var($key, $xnyo_parent->get_vars[$key]);

		// do the same to the post variables
		if (XNYO_DEBUG) $xnyo_parent->debug('Filtering POST Variables');
		if (count($xnyo_parent->post_vars))
			foreach ($this->storage->post_vars as $key => $var)
				if (!empty($xnyo_parent->post_vars[$key]))
					$this->filter_post_var($key, $xnyo_parent->post_vars[$key]);
					
		// do the same to the post variables
		if (XNYO_DEBUG) $xnyo_parent->debug('Filtering Cookie Variables.');
		if (count($xnyo_parent->cookie_vars))
			foreach ($this->storage->cookie_vars as $key => $var)
				if (!empty($xnyo_parent->cookie_vars[$key]))
					$this->filter_cookie_var($key, $xnyo_parent->cookie_vars[$key]);
	}

	
	/**
	 * Filter GET var
	 *
	 * Allow a GET variable through the filter.
	**/
	public function filter_get_var ($name, $type=NULL)
	{
		global $xnyo_parent;

		// defaults, cant have an empty name, but set type to text
		if (empty($name))
			return false;
		if (empty($type))
			$type = 'text';

		// obviously if its not set we die..
		if (!isset($this->storage->get_vars[$name]))
			return false;
			
		$_GET[$name] = $this->_filter_var($this->storage->get_vars[$name], $type);

		// ok if the variable has been modified from its original state then we need to know!
		if (is_array($_GET[$name]))
		{
			foreach ($_GET[$name] as $k => $v)
				$xnyo_parent->input_modified['get'][$name][$k] = $_GET[$name][$k] != $v;
		} else
		{
			$xnyo_parent->input_modified['get'][$name] = $_GET[$name] != $this->storage->get_vars[$name];
		}
		
		// debug
		if (XNYO_DEBUG) $xnyo_parent->debug('Filtering GET variable <i>'.$name.'</i> ('.$type.'). '.($xnyo_parent->input_modified['get'][$name] ? 'Modified.' : 'Not modified.'));
		
		// add to the request variables
		if (empty($_REQUEST[$name]))
			$_REQUEST[$name] = $_GET[$name];

		// globals on?
		if ($xnyo_parent->global_vars && !isset($GLOBALS[$name]))
			$GLOBALS[$name] = $_GET[$name];

		return true;
	}

	/**
	 * Filter POST Var
	 *
	 * Allow a POST variable through
	**/
	public function filter_post_var ($name, $type=NULL)
	{
		global $xnyo_parent;

		// defaults, cant have an empty name, but set type to text
		if (empty($name))
			return false;
		if (empty($type))
			$type = 'text';

		// obviously if its not set we die..
		if (!isset($this->storage->post_vars[$name]))
			return false;
			
		$_POST[$name] = $this->_filter_var($this->storage->post_vars[$name], $type);

		// ok if the variable has been modified from its original state then we need to know!
		if (is_array($_POST[$name]))
		{
			foreach ($_POST[$name] as $k => $v)
				$xnyo_parent->input_modified['post'][$name][$k] = $_POST[$name][$k] != $v;
		} else
		{
			$xnyo_parent->input_modified['post'][$name] = $_POST[$name] != $this->storage->post_vars[$name];
		}

		// debug
		if (XNYO_DEBUG) $xnyo_parent->debug('Filtering POST variable <i>'.$name.'</i> ('.$type.'). '.($xnyo_parent->input_modified['post'][$name] ? 'Modified.' : 'Not modified.'));

		// add to the request variables
		if (empty($_REQUEST[$name]))
			$_REQUEST[$name] = $_POST[$name];

		// globals on?
		if ($xnyo_parent->global_vars && !isset($GLOBALS[$name]))
			$GLOBALS[$name] = $_POST[$name];

		return true;
	}

	/**
	 * Filter Cookie Var
	 *
	 * Allow a cookie through the filter
	**/
	public function filter_cookie_var ($name, $type=NULL)
	{
		global $xnyo_parent;

		// defaults, cant have an empty name, but set type to text
		if (empty($name))
			return false;
		if (empty($type))
			$type = 'text';

		// obviously if its not set we die..
		if (!isset($this->storage->cookie_vars[$name]))
			return false;
			
		$_COOKIE[$name] = $this->_filter_var($this->storage->cookie_vars[$name], $type);

		// ok if the variable has been modified from its original state then we need to know!
		if (is_array($_COOKIE[$name]))
		{
			foreach ($_COOKIE[$name] as $k => $v)
				$xnyo_parent->input_modified['cookie'][$name][$k] = $_COOKIE[$name][$k] != $v;
		} else
		{
			$xnyo_parent->input_modified['cookie'][$name] = $_COOKIE[$name] != $this->cookie->get_vars[$name];
		}

		// debug
		if (XNYO_DEBUG) $xnyo_parent->debug('Filtering Cookie variable <i>'.$name.'</i> ('.$type.'). '.($xnyo_parent->input_modified['cookie'][$name] ? 'Modified.' : 'Not modified.'));

		// add to the request variables
		if (empty($_REQUEST[$name]))
			$_REQUEST[$name] = $_POST[$name];

		// globals on?
		if ($xnyo_parent->global_vars && !isset($GLOBALS[$name]))
			$GLOBALS[$name] = $_COOKIE[$name];

		return true;
	}

	/**
	 * Filter Var
	 *
	 * Filter the actual variable
	**/
	private function _filter_var ($data, $type)
	{
		global $input;

		// deal with arrays
		if (is_array($type))
		{
			$array_type = $type[0];
			$type = '_array';
		}

		if (method_exists($input, $type))
		{
			// again our arrays
			if (!empty($array_type))
				$parsed = $input->$type($data, $array_type);
			else
				$parsed = $input->$type($data);
		}

		return $parsed;
	}
	
	/**
	 * Fetch!
	**/
	public function fetch ($type)
	{
		if (!is_object($this->storage))
			return array();

		$var = $type.'_vars';
		return (array)$this->storage->$var;
	}
}
	