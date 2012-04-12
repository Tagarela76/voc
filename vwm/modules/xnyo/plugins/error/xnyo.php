<?php

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/error/xnyo.php
 *              Standard Xnyo Error Class
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: xnyo.php 34 2004-09-01 07:04:11Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/error/xnyo.php $
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


class error_xnyo_plugin
{
	private $errors;
	public $debug_console;

	// Contents of the debug variable, its passed to the dump function
	public $_debug_dump_val;


	// simple function that takes the error and does what we tell it to
	public function error ($msg, $level, $extra=NULL)
	{
		global $xnyo_parent;

		// lets build up the data array
		// null null?
		$bt = debug_backtrace();
		if (!is_null($extra) && !empty($extra['file']))
			$data = array
			(
				'body' => $msg,
				'level' => $level,
				'timestamp' => $xnyo_parent->_getmicrotime(),
				'class' => (isset($bt[3]) && isset($bt[3]['class']) ? $bt[3]['class'] : ''),
				'function' => (isset($bt[3]) ? $bt[3]['function'] : ''),
				'file' => $extra['file'],
				'line' => $extra['line']
			);
		else
			$data = array
			(
				'body' => $msg,
				'level' => $level,
				'tag' => $extra,
				'timestamp' => $xnyo_parent->_getmicrotime(),
				'class' => (isset($bt[2]['class']) ? $bt[2]['class'] : ''),
				'function' => $bt[2]['function'],
				'file' => $bt[1]['file'],
				'line' => $bt[1]['line']
			);
		// what do we do eh?
		switch ($level):
			case 0:
				// suppresssed!
				return true;

			// the php default error types
			case E_USER_ERROR:
				$this->errors->errors[] = $data;
				error_log(date('[D M d H:i:s Y] ').'PHP Error: '.$msg.' in '.$data['file'].' on line '.$data['line']);
				return true;

			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
				$this->errors->warnings[] = $data;
				error_log(date('[D M d H:i:s Y] ').'PHP Warning: '.$msg.' in '.$data['file'].' on line '.$data['line']);
				return true;

			case E_STRICT:
				$this->errors->strict[] = $data;
				error_log(date('[D M d H:i:s Y] ').'PHP Strict Error: '.$msg.' in '.$data['file'].' on line '.$data['line']);
				return true;

			case E_NOTICE:
			case E_USER_NOTICE:
				$this->errors->notices[] = $data;
				return true;

			case CLIENT:
				if (!empty($extra))
					// tagged error message
					$this->errors->client_tagged[$extra] = $data;
				else
					$this->errors->client[] = $data;
				$xnyo_parent->error_found = true;
				return true;

			case DEBUG:
				if ($xnyo_parent->debug)
				{
					// log the debug stuff?
					if ($xnyo_parent->debug_log)
						error_log(date('[D M d H:i:s Y] ').'Xnyo Debug: '.$msg.' in '.$data['file'].' on line '.$data['line']);

					// output the debug stuff ? CLI..
					if (CLI && $xnyo_parent->debug_output)
						echo date('[D M d H:i:s Y] ').'Xnyo Debug: '.$msg.' in '.$data['file'].' on line '.$data['line']."\n";

					// lets build up the data array
					$this->errors->debug[] = $data;
				}
				return true;
			default:
				$this->errors->unknown[] = $data;
				error_log(date('[D M d H:i:s Y] ').'PHP Unknown Error: '.$msg.' in '.$data['file'].' on line '.$data['line']);
				return true;
		endswitch;
	}
	
	
	/**
	 * Setup the error cache
	**/
	public function setup_error_cache ()
	{
		// create variables, is all
		$this->errors = new stdClass;
		$this->errors->debug = array();
		$this->errors->client = array();
		$this->errors->client_tagged = array();
		$this->errors->errors = array();
		$this->errors->warnings = array();
		$this->errors->notices = array();
		$this->errors->strict = array();
		$this->errors->unknown = array();
	}
	
	/**
	 * Simple fetch!
	**/
	public function fetch ($type)
	{
		return (array)$this->errors->$type;
	}
	
	public function dump_errors ($level=CLIENT, $tag=NULL)
	{
		// what error type are they trying to dump from me?
		switch ($level):
			case CLIENT:
			case 'client':
				// tagged ones?
				if (!is_null($tag))
					return (isset($this->errors->client_tagged[$tag]['body']) ? $this->errors->client_tagged[$tag]['body'] : NULL);
				$_errors = array();
				foreach ($this->errors->client as $var)
					$_errors[] = $var['body'];
				return $_errors;

			case ERROR:
			case 'errors':
				if (CLI)
					return $this->_prettify_cli_errors($this->errors->errors);
				return $this->_prettify_web_errors($this->errors->errors, 'errors');

			case 'warnings':
				if (CLI)
					return $this->_prettify_cli_errors($this->errors->warnings);
				return $this->_prettify_web_errors($this->errors->warnings, 'warnings');

			case WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
				if (CLI)
					return $this->_prettify_cli_errors($this->errors->warnings, $level);
				return $this->_prettify_web_errors($this->errors->warnings, 'warnings', $level);

			case 'notices':
				if (CLI)
					return $this->_prettify_cli_errors($this->errors->notices);
				return $this->_prettify_web_errors($this->errors->notices, 'notices');

			case 'E_NOTICE':
			case 'NOTICE':
				if (CLI)
					return $this->_prettify_cli_errors($this->errors->notices, $level);
				return $this->_prettify_web_errors($this->errors->notices, 'notices', $level);
			
			case 'E_STRICT':
			case 'strict':
				if (CLI)
					return $this->_prettify_cli_errors($this->errors->strict);
				return $this->_prettify_web_errors($this->errors->strict, 'strict');

			case 'unknown':
				if (CLI)
					return $this->_prettify_cli_errors($this->errors->unknown, $level);
				return $this->_prettify_web_errors($this->errors->unknown, 'unknown', $level);

			case DEBUG:
			case 'debug':
			default:
				if (CLI)
					return $this->_prettify_cli_errors($this->errors->debug);
				return $this->_prettify_web_errors($this->errors->debug, 'debug');
		endswitch;
	}
	
	// prettify CLI errors
	private function _prettify_cli_errors($data, $level=NULL)
	{
		$output = '['.date('r', (is_array($data[0]) ? $data[0]['timestamp'] : time())).'] Starting log output.'."\n";
		$prev = $data[0]['timestamp'];
		foreach ($data as $var)
		{
			if (!is_null($level) && $level != $var['level'])
				continue;

			$output .= '[+'.round(($var['timestamp'] - $prev), 5).' secs] ('.$var['file'].':'.$var['line'].') '.$var['body']."\n";
			$prev = $var['timestamp'];
		}		
		return $output;
	}
				
	// prettify WEB errors
	private function _prettify_web_errors($data, $type, $level=NULL)
	{
		global $xnyo_parent;
		$data = array_reverse($data);
		if (!is_object($this->debug_console))
			$this->debug_console = $xnyo_parent->load_plugin('debug', 'error', true);
		return $this->debug_console->dump_errors($data, $type, $level);
	}
}