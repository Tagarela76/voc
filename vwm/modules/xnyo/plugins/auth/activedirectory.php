<?php

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/auth/activedirectory.php
 *              Authenticate against an Active Directory Domain
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: activedirectory.php 57 2004-09-12 10:30:17Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/auth/activedirectory.php $
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

class auth_activedirectory_plugin {

	public function login ($username, $password, $params)
	{
		// Active Directory Authentication
		global $xnyo_parent;

		// make config stuff
		if (empty($params['server']) || empty($params['domain']) || empty($params['basedn']))
		{
			$xnyo_parent->trigger_error('Call to Active Directory auth module failed, insufficient parameters to make connection. Server, domain and base DN are required.', ERROR);
			return false;
		}
		extract($params);
		
		// is our username already a domain?
		if (strpos($username, '@') === FALSE)
			$email = $username."@".$domain;
		else
			$email = $username;

		// open the connection
		if (XNYO_DEBUG) $xnyo_parent->debug('Connecting to LDAP Server <i>'.$server.'</i>.');
		$fp = ldap_connect($server);

		// if no connection, return false
		if (!$fp)
			return false;

		// bind the user
		if (XNYO_DEBUG) $xnyo_parent->debug('Binding as <i>'.$email.'</i>.');
		$bind = @ldap_bind($fp, $email, $password);

		// invalid username/password
		if (!$bind)
			return false;

		// setup search vars
		if (XNYO_DEBUG) $xnyo_parent->debug('Searching Active Directory for any groups we\'re in.');
		$search = "userPrincipalName=*".$username."*";
		$details['groups'] = $this->search($fp, $basedn, $search);

		// no groups, error
		if (!$details['groups'])
			return false;

		if (XNYO_DEBUG) $xnyo_parent->debug('Successfully logged into Active Directory.');
		return $details;
	}

	// Search the active directory and pull the group names
	private function search ($fp, $basedn, $search)
	{
		// Perform Search
		$sr = ldap_search($fp, $basedn, $search);

		// no matches, bad bad bad
		if (!ldap_count_entries($fp, $sr))
			return false;

		// get the info
		$info = ldap_get_entries($fp, $sr);

		// make it a bit cleaner
		$info = $info[0];

		// if its a Person category, set that as their full name
		if (preg_match('/CN=Person/i', $info['objectcategory'][0]))
			$loginname = $info['name'][0];

		// loop through and pull out all the groups.
		if (is_array($info['memberof']))
			foreach ($info['memberof'] as $key => $var)
				if (preg_match('/CN=([^,]*?),/i', $var, $m))
				{
					$groups[] = $m[1];
					$moregroups = $this->search($fp, $basedn, 'name=*'.$m[1].'*');
					if (is_array($moregroups))
						foreach ($moregroups as $v)
							$groups[] = $v;
				}

		// nice stuff.
		if (isset($loginname))
			$groups['loginname'] = $loginname;

		return $groups;

	}

}

/* vim: set expandtab: */

?>
