<?php

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/error/debug.php
 *              Display the Debug Console
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: debug.php 27 2004-08-21 22:24:34Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/error/debug.php $
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

class error_debug_plugin
{
	private $prev;

	public function display ()
	{
		global $xnyo_parent, $access, $smarty;

		// load up our display template..
		if (empty($xnyo_parent->debug_console_template))
		{
			$xnyo_parent->trigger_error('Debug console template left empty, cannot display the console.', ERROR);
			return NULL;
		}

		// check locations
		if (file_exists($xnyo_parent->debug_console_template))
			$template_file = $xnyo_parent->debug_console_template;
		elseif (file_exists(SCRIPT_DIR.$xnyo_parent->debug_console_template))
			$template_file = SCRIPT_DIR.$xnyo_parent->debug_console_template;
		elseif (file_exists(XNYO_DIR.$xnyo_parent->debug_console_template))
			$template_file = XNYO_DIR.$xnyo_parent->debug_console_template;
		else
		{
			$xnyo_parent->trigger_error('Unable to access display console template '.$xnyo_parent->debug_console_template, ERROR);
			return NULL;
		}

		// load in the template
		$template = file_get_contents($template_file);
		if (empty($template))
		{
			$xnyo_parent->trigger_error('Unable to read display console template '.$template_file, ERROR);
			return NULL;
		}

		// all assigned variables
		$smarty->assign('smarty_vars', (array)$smarty->get_template_vars());


		// clear any old consoles
		$_SESSION['_debug_console'] = NULL;

		// error messages
		$types = array
		(
			'client' => $this->fetch_error('client'),
			'client_tagged' => $this->fetch_error('client_tagged'),
			'errors' => $this->fetch_error('errors'),
			'warnings' => $this->fetch_error('warnings'),
			'notices' => $this->fetch_error('notices'),
			'strict' => $this->fetch_error('strict'),
			'unknown' => $this->fetch_error('unknown')
		);
		$debug = $this->fetch_error('debug');
		$smarty->assign('debug', $debug);
		$smarty->assign('errors', $types);

		// current microtime
		$smarty->assign('cur_time', $xnyo_parent->_getmicrotime());
		
		// the very first time something was logged into debug
		$first = end($debug);
		reset($debug);
		$smarty->assign('first_time', $first['timestamp']);
		
		// get variables
		$filtered = array
		(
			'get' => &$_GET,
			'post' => &$_POST,
			'cookie' => &$_COOKIE
		);
		$unfiltered = array
		(
			'get' => $this->fetch_vars('get'),
			'post' => $this->fetch_vars('post'),
			'cookie' => $this->fetch_vars('cookie')
		);
		
		// remove filtered vars from unfiltered ones :)
		foreach ($unfiltered as $key => $var)
			foreach ($var as $k => $v)
				if (isset($filtered[$key][$k]))
					unset($unfiltered[$key][$k]);

		// assign
		$smarty->assign_by_ref('filtered', $filtered);
		$smarty->assign_by_ref('unfiltered', $unfiltered);

		// location/language
		$smarty->assign('location', $xnyo_parent->location);

		// see if we can find a nicer name
		if (is_array($xnyo_parent->languages) && isset($xnyo_parent->languages[$xnyo_parent->language]) && is_string($xnyo_parent->languages[$xnyo_parent->language]))
			$smarty->assign('language', $xnyo_parent->languages[$xnyo_parent->language]);
		else
			$smarty->assign('language', $xnyo_parent->language);

		// are we displaying access?
		if (is_object($access))
		{
			$smarty->assign('display_access', true);
			$smarty->assign('logged_in', ($access->check('required') ? 'Yes' : 'No'));
		} else
			$smarty->assign('display_access', false);
		
		// simple data
		if (isset($xnyo_parent->user->username)) $smarty->assign('username', $xnyo_parent->user->username);
		if (isset($xnyo_parent->user->id)) $smarty->assign('userid', $xnyo_parent->user->id);
		if (isset($xnyo_parent->user->email)) $smarty->assign('useremail', $xnyo_parent->user->email);
		
		// all the loaded plugins
		$smarty->assign('loaded_plugins', $xnyo_parent->fetch_loaded_plugins());

		// open database connections
		$smarty->assign('open_db_connections', $this->fetch_database_connections());
		
		// open sessions
		$smarty->assign('sessions', $this->get_sessions());
		
		// xnyo configuration
		$smarty->assign('xnyo_conf', (array)$xnyo_parent);
		
		// smarty configuration
		$smarty->assign('smarty_conf', (array)$smarty);
		

		// well we've loaded all the required variables, eval the template..
		$smarty->_compile_source('evaluated template', $template, $compiled_template);
		$smarty->_eval('?>'.$compiled_template);
		$output = ob_get_contents();

		// actually grab what we're looking for now
		if (!preg_match('/<XNYO::START::OF::DEBUG::CONSOLE::TEMPLATE>(.*)<XNYO::END::OF::DEBUG::CONSOLE::TEMPLATE>/s', $output, $m))
		{
			$xnyo_parent->trigger_error('Unable to get console output, cannot display debug template.', ERROR);
			return NULL;
		}
		
		// store it
		$_SESSION['_debug_console'] = $m[1];

		// and print the console button
		return $this->print_console_button();
	}
	
	public function output_console ()
	{
		// no console? bugger..
		if (empty($_SESSION['_debug_console']))
			die('Unable to open debug console, exiting.');

		// output it
		echo $_SESSION['_debug_console'];
	}
	
	private function print_console_button ()
	{
		global $xnyo_parent;
		$output = '<style>
			#console_button
			{
				position:		fixed;
				_position:		absolute;
				top:			5px;
				right:			5px;
				height:			20px;
				padding:		5px;
				padding-top:		4px;
				text-align:		center;
				vertical-align:		middle;
				background-color:	#FF0000;
				border:			2px solid #000000;
			}
			#console_button a
			{
				color:			#FFFFFF;
				font-weight:		bold;
				font-family:		verdana, arial, sans-serif;
				font-size:		11px;
				text-decoration:	none;
			}				
			</style>';
		$output .= '<div id="console_button"><a href="'.$_SERVER['PHP_SELF'].'?'.$xnyo_parent->debug_var.'=console" target="xnyo_debug_console">Open Console</div>';
		return $output;
	}

	private function get_sessions ()
	{
		// open up all the sessions
		$dir = ini_get('session.save_path');
		if (empty($dir))
			$dir = '/tmp';
		$dp = opendir($dir);
		while ($file = readdir($dp))
		{
			if (substr($file, 0, 5) != 'sess_' || !@filesize($dir.DIRSEP.$file) || !is_readable($dir.DIRSEP.$file))
				continue;

			// got one!
			$data = file_get_contents($dir.DIRSEP.$file);
			$data = explode('|', $data);
			foreach ($data as $v)
			{
				$pos = strlen($v) - strpos(strrev($v), ';');
				$pos2 = strlen($v) - strpos(strrev($v), '}');
				if ($pos2 != strlen($v) && $pos2 > $pos)
				        $pos = $pos2;
				$value = substr($v, 0, $pos);
				$fields[$name] = unserialize($value);
				$name = substr($v, $pos);
				$pos = $pos2 = NULL;
			}
	                
	                // is this us?
	                if ('sess_'.session_id() == $file)
	                	$fields['_ip'] = '<b>You</b>';
	                
	                $sessions[] = array('ip' => $fields['_ip'], 'last_activity' => $fields['_last_activity'], 'browser' => $fields['_browser']);
		}
		closedir($dp);
		return $sessions;
	}
	
	private function fetch_database_connections ()
	{
		global $db;
		if (!is_object($db))
			return array();
		return $db->fetch_connections();
	}

	private function fetch_error($type)
	{
		global $xnyo_parent;
		$errors = (array)$xnyo_parent->error_plugin->fetch($type);
		foreach ($errors as $key => $var)
		{
			if (!empty($prev))
				$errors[$key]['next'] = $prev;
			$prev = $var['timestamp'];

			// client tagged
			if (!empty($var['tag']))
				$errors[$key]['body'] = '('.$var['tag'].') '.$var['body'];
				
			// microseconds, damn thing
			$errors[$key]['microseconds'] = substr($var['timestamp'], strpos($var['timestamp'], '.'), 5);
		}
		
		return array_reverse($errors);
	}
	
	private function fetch_vars ($type)
	{
		global $xnyo_parent;
		
		if (!is_object($xnyo_parent->filter_plugin))
			return array();

		return $xnyo_parent->filter_plugin->fetch($type);
	}	

}
