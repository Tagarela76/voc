<?php

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/auth/sql.php
 *              Authenticate against a table in a SQL database.
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: sql.php 57 2004-09-12 10:30:17Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/auth/sql.php $
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

class auth_sql_plugin
{

	public function login($username, $password, $params=array())
	{
		// SQL Authentication
		global $db, $xnyo_parent;

		// md5 hash the password so it matches the password in the database
		if (!$params['already_md5'])
			$password = md5($password);
		
		// use specified juarez
		if (!empty($params['table']))
			$table = $db->spec($params['table']);
		else
			$table = $db->spec ('auth');

		// debug
		if (XNYO_DEBUG) $xnyo_parent->debug('Authenticating against a SQL database as <i>'.$username.'</i>, using table <i>'.$table->_title.'</i>.');
		
		// get any matching username
		$sql = "SELECT * FROM $table->_title WHERE $table->username = '$username' AND $table->password = '$password'";
		
		// run query
		$db->exec($sql);
		
		// if no matches, invalid login
		if (!$db->num_rows()) 
			return false;
		
		// get stuffs
		$details = $db->fetch_array (0);
		
		// store data into the array
		if (XNYO_DEBUG) $xnyo_parent->trigger_error('Expanding group list (if found)');
		$details['groups'] = explode(',', $details['groups']);
		
		// remove any references to the password field
		unset($details['password']);
		return $details;
	}
}

?>
