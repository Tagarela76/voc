<?PHP

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/class/access.php
 *              Access/Session Control Functions
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: access.php 59 2004-09-12 11:27:52Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/class/access.php $
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

class access_plugin
{
	// plugin description
	public $_xnyo_plugin_desc = 'Xnyo Access Plugin';


	/**
	 * Session Check
	 *
	 * Check current session to ensure their session hasn't someway become invalid.
	**/
	function sess_check ()
	{

		global $xnyo_parent;

		// store the IP Address in the session varz
		if (XNYO_DEBUG) $xnyo_parent->debug('Storing IP/Browser/Time.');
		$_SESSION['_ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['_browser'] = $_SERVER['HTTP_USER_AGENT'];
		$_SESSION['_last_activity'] = time();

		// if we're logged in, do auth stuff
		if (!empty($xnyo->user))
		{
			// there really isnt much to do in here
			// check that we havent expired
			if (XNYO_DEBUG) $xnyo_parent->debug('Checking session expiry.');
			if ($_SESSION['auth']['expiry'] !== 0)
			{
				if (time() > $_SESSION['auth']['expiry'])
				{
					$time = time() - $_SESSION['auth']['expiry'];
					$errmsg = "Session time expired ($time seconds). Logging out user (" . $xnyo_parent->user->username . ")";
					$xnyo_parent->trigger_error($errmsg, NOTICE);
					$this->logout ('expired');
				} else {
					// update session times
					$_SESSION['auth']["expiry"] = time() + $xnyo_parent->session_lifetime;
				}
			}

			// check their subnet is the same
			if (XNYO_DEBUG) $xnyo_parent->debug('Checking session subnet.');
			if ($_SESSION['auth']['subnet'] != $this->subnet())
			{
				$errmsg = "Session moved subnet (" . $_SESSION['auth']['subnet'] . " => " . $this->subnet() . "). Logging out user (" . $xnyo_parent->user->username . ")";
				$xnyo_parent->trigger_error($errmsg, NOTICE);
				$this->logout('moved_subnet');
			}
	
			// check browser is the same
			if (XNYO_DEBUG) $xnyo_parent->debug('Checking session Browser.');
			if ($_SESSION['auth']['browser'] != $_SERVER['HTTP_USER_AGENT'])
			{
				$errmsg = "User changed browsers (" . $_SESSION['auth']['browser'] . " => " . $_SERVER['HTTP_USER_AGENT'] . "). Logging out user (" . $xnyo_parent->user->username . ")";
				$xnyo_parent->trigger_error($errmsg, NOTICE);
				$this->logout('changed_browser');
			}
		
		}

		// access checking is the only thing left i guess
		// if theres no access level specified, just let them through
		if (!empty($xnyo_parent->access) && !$this->check($xnyo_parent->access) && !$this->set_acl($xnyo_parent->access))
			return false;

		// we're all done i guess
		return true;
	}

	/**
	 * Logout
	 * 
	 * Logout the current user
	**/
	function logout ($reason=NULL)
	{

		// first destroy their user data
		global $xnyo_parent;
		if (XNYO_DEBUG) $xnyo_parent->debug('Logging out current user'.(empty($xnyo_parent->user->username) ? '.' : ' ('.$xnyo_parent->user->username.')'));

		if (!is_null($reason))
			$_SESSION['_logout_reason'] = $reason;

		session_unregister('auth');
	}

	/**
	 * Check
	 * 
	 * Check whether a user is logged in or not
	**/
	function check ($groups=NULL)
	{
		//header ('Location: index_alpha.php');
		//echo //"hello";
		global $xnyo_parent;

		// no groups? bleh, guess they can go in
		if (is_null($groups) || empty($groups))
			return true;

		// a string? split it into the array
		if (!is_array($groups))
			$groups = explode(",", preg_replace('/\s/', '', $groups));

		// If not allowed to be logged in
		if (in_array('none', $groups))
			if (!empty($xnyo_parent->user->username))
				return false;
			else
				return true;
		
		// guess we have to be logged in then hey
		//if (empty($xnyo_parent->user->user)) {	//	fuck				
		if (empty($xnyo_parent->user['user'])) {		
			return false;
		}
		
		//print_r($groups);
		//echo "HELLO<br>";
		// required to be logged in, and they are
		if (in_array('required', $groups) || in_array('all', $groups))
			return true;

		// ok, cycle the list
		foreach ($groups as $group) {

			// if its their username, fire away
			if (strtoupper($group) == strtoupper($_SESSION['auth']['user']))
				return true;

			if (is_array($_SESSION['auth']['groups']))
			{
				// make the group into a regexp
				$group = preg_replace("/\*/", ".*?", $group);
				$group = preg_replace("/([\@\(\)\|\[\]])/", "\\\\\\1", $group);

				// see if our regexp matches a current group
				foreach ($_SESSION['auth']['groups'] as $var)
					if (preg_match("/$group/i", $var))
						return true;

			}
		}

		// guess they arent allowed in hey
		return false;

	}


	/**
	 * Subnet
	 *
	 * Create a Class C subnet for the given IP (xxx.xxx.xxx.0/24)
	**/
	function subnet ($ip=NULL, $subnet=NULL)
	{

		// assign defaults
		if (is_null($ip))
			$ip = $_SERVER['REMOTE_ADDR'];

		// default to REMOTE_ADDR
		if (!is_null($subnet))
		{
			$explodeip = explode('.', $ip);
			$subnetip = explode('.', substr($subnet, 0, strpos($subnet, '/')));
			$subnetmask = substr($subnet, strpos($subnet, '/') + 1);
			if ($subnetmask == 32)
			{
				if (substr($subnet, 0, strpos($subnet, '/')) == $ip)
				{
					return true;
				} else {
					return false;
				}
			} elseif ($subnetmask < 32 && $subnetmask >= 24)
			{
				$start = $subnetip[3];
				$check = 32;
				$top = 254;
				$checkip = $explodeip[3];
			} elseif ($subnetmask < 24 && $subnetmask >= 16)
			{
				$start = $subnetip[2];
				$check = 24;
				$top = 255;
				$checkip = $explodeip[2];
			} elseif ($subnetmask < 16 && $subnetmask >= 8)
			{
				$start = $subnetip[1];
				$check = 16;
				$top = 255;
				$checkip = $explodeip[1];
			} elseif ($subnetmask < 8)
			{
				$start = $subnetip[0];
				$check = 8;
				$top = 254;
				$checkip = $explodeip[0];
			}
			$end = $start + pow(2, ($check - $subnetmask));
			if ($end > $top) $end = $top;
			if ($checkip >= $start && $checkip <= $end)
				return true;
			else
				return false;
			
		} else {				
				
			// wow hard
			$subnet = substr($ip, 0, strrpos($ip, '.')).".0/24";
	
			return $subnet;
		}
	}

	/**
	 * Set LCL
	 * 
	 * Set a Language Control List
	**/
	function set_lcl ($langs)
	{
		global $xnyo_parent;
		if (XNYO_DEBUG) $xnyo_parent->debug('Setting new Language Control List for this page to '.$langs);

		// a string? split it into the array
		if (!is_array($langs))
			$langs = explode(",", preg_replace('/\s/', '', $langs));

		// current language in the list?
		if (!in_array($langs, $xnyo_parent->language))
		{
			$xnyo_parent->trigger_error('User ('.$xnyo_parent->user->username.') not allowed to view page, wrong language: currently <i>'.$xnyo_parent->language.'</i>, required <i>'.join(',', $langs).'</i>', NOTICE);
			if (!empty($xnyo_parent->language_redirect_url))
				header('Location: '.$xnyo_parent->language_redirect_url);
			exit('Not allowed to view this page. Wrong Language');
		}
	}

	/**
	 * Set ACL
	 * 
	 * Set an Access Control List for the current page
	**/
	function set_acl ($acl)
	{
		global $xnyo_parent;
		if (XNYO_DEBUG) $xnyo_parent->debug('Setting new Access Control List for this page to '.$acl);
		if (!$this->check($acl))
		{
			// tailor error messages to cater for logged in status
			if (empty($xnyo_parent->user->username))
				$errmsg = "Unauthenticated user";
			else
				$errmsg = "User (".$xnyo_parent->user->username.")";
			$errmsg .= " attempted to access page requiring the following access: ".$acl.' Session: '.var_export($_SESSION['auth'], true);

			// trigger the error
			$xnyo_parent->trigger_error($errmsg, NOTICE);

			$this->logout('access_denied');
			if (!empty($xnyo_parent->logout_redirect_url))
				header('Location: '.$xnyo_parent->logout_redirect_url);
			exit('Not allowed to view this page. Access Denied.');
		}
	}
}
