<?PHP

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/class/auth.php
 *              Authentication Functions
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: auth.php 57 2004-09-12 10:30:17Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/class/auth.php $
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

class auth_plugin
{

	/**
	 * Login
	 * 
	 * Authenticate a new user
	**/
	function login($username, $password)
	{
		global $access, $xnyo_parent, $input;

		// debug
		if (XNYO_DEBUG) $xnyo_parent->debug('Authenticating user, running checks.');

		// Check for blank username
		if (empty($username))
		{
		
			// Drop warning into the logs, return error status to the user
			$xnyo_parent->trigger_error('Blank Username', NOTICE);
			$this->error = XNYO_AUTH_BLANK_USERNAME;
			return false;
		}
		
		// Check for blank password
		if (empty($password))
		{
			// Drop warning into the logs, return error status to the user
			$xnyo_parent->trigger_error('Blank Password', NOTICE);
			$this->error = XNYO_AUTH_BLANK_PASSWORD;
			return false;
		}
		
		// run security checking functions over the username
		if (XNYO_DEBUG) $xnyo_parent->debug('Running security over username/password.');
		$username = $input->username($username);
		
		// Run less tight security over the password as it may contain non alpha-numeric characters
		$password = $input->password($password);
		
		
		// include warez
		if (!isset($xnyo_parent->auth_type))
		{
			$xnyo_parent->trigger_error('No authentication type selected', WARNING);
			$this->error = XNYO_AUTH_NO_AUTH_TYPE;
			return false;
		}
		
		// load the fucking plugin, moron
		if (!$xnyo_parent->load_plugin($xnyo_parent->auth_type, 'auth'))
		{
			$xnyo_parent->trigger_error('Unable to load plugin for selected authentication type ('.$xnyo_parent->auth_type.')', WARNING);
			$this->error = XNYO_AUTH_NO_PLUGIN;
			return false;
		}

		// debug
		if (XNYO_DEBUG) $xnyo_parent->debug('Loaded auth plugin <i>'.$xnyo_parent->auth_type.'</i>, calling it now.');

		// auth the user
		$class = "_auth_".$xnyo_parent->auth_type."_handler";
		$details = $xnyo_parent->$class->login($username, $password, $xnyo_parent->auth_params);
		
		// invalid login if false
		if (!$details)
		{
			if (empty($this->error) && !is_array($this->error_data))
				$this->error = XNYO_AUTH_INVALID;
			return false;
		}
		
		// not in any groups, not authorised to use
		if (count($details['groups']) < 1)
		{
			$xnyo_parent->trigger_error('Unauthorised access attempted by '.$username, WARNING);
			$this->error = XNYO_AUTH_UNAUTHORISED;
			return false;
		}
		
		//echo $details;
		//print_r($details);
		
		$xnyo_parent->user = $details;

		// store the username and groups in the session variables
		$xnyo_parent->user['user'] = $username;
		$xnyo_parent->user['browser'] = $_SERVER['HTTP_USER_AGENT'];
		$xnyo_parent->user['expiry'] = time() + $xnyo_parent->session_lifetime;
		$xnyo_parent->user['subnet'] = $access->subnet();
		//print_r($xnyo_parent->user);
		// authenticated, return ok
		return true;
	}

}

?>
