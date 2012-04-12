<?PHP

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/cache/database.php
 *              Store cache'd data in a SQL database.
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: database.php 24 2004-08-12 09:16:34Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/cache/database.php $
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

class cache_database_plugin
{
	// unique seed for this page
	private $seed;

	// where we are
	private $page;
	
	/**
	 * Constructor
	 *
	 * Stuff set for read/write
	**/
	public function __construct ()
	{
		$this->seed = crc32($_SERVER['QUERY_STRING']);
		$this->page = $_SERVER['SCRIPT_FILENAME'];
	}

	/**
	 * Write
	 *
	 * Write a cache "file" into the database
	**/
	public function write ($buffer)
	{
		global $xnyo_parent, $db, $input;

		// go the table
		$table = $db->spec('cache');

		// clear out any existing ones first
		$sql = 'DELETE FROM '.$table->_title.' WHERE '.$table->page.' = \''.$this->page.'\' AND '.$table->seed.' = \''.$this->seed.'\' AND '.$table->location.' = \''.$xnyo_parent->location.'\' AND '.$table->user.' = \''.$xnyo_parent->user->id.'\'';
		$db->exec($sql);

		// make expire time
		if (!is_null($xnyo_parent->cache_expire))
			$expire = $xnyo_parent->cache_expire;
		elseif ($xnyo_parent->cache_lifetime != 0)
			$expire = time() + $xnyo_parent->cache_lifetime;
		else
			// dont cache things that have no life, duh
			return false;

		$insert = array
		(
			$table->page => $this->page,
			$table->location => $xnyo_parent->location,
			$table->user => $xnyo_parent->user->id,
			$table->seed => $this->seed,
			$table->time => $expire,
			$table->content => $input->sqltext($buffer)
		);
		$db->insert($table->_title, $insert);

		if (!$db->affected_rows())
		{
			$xnyo_parent->trigger_error('Unable to write to cache', WARNING);
			return false;
		}

		// finished, hard eh?
		return true;
	}

	/**
	 * Read
	 *
	 * Read and output a cache "file"
	**/
	public function read ()
	{

		global $xnyo_parent, $db, $input;

		$xnyo_parent->load_plugin($xnyo_parent->database_type, 'database');
		$xnyo_parent->load_plugin('input');

		// ok we need to find our lovely cache files
		$table = $db->spec('cache');

		$sql = 'SELECT * FROM '.$table->_title.' WHERE '.$table->page.' = \''.$this->page.'\' AND '.$table->seed.' = \''.$seed.'\'';
		if (!empty($xnyo_parent->location))
			$sql .= ' AND '.$table->location.' = \''.$xnyo_parent->location.'\'';
		if (!empty($xnyo_parent->user->id))
			$sql .= ' AND $table->user = \''.$xnyo_parent->user->id.'\'';
		$db->exec($sql);

		if (!$db->num_rows())
			return false;

		$data = $db->fetch_object(0);


		// check to see if our script is newer than this
		if (($time = filemtime($_SERVER['SCRIPT_FILENAME'])) > $data->time)
		{
			$this->delete();
			return false;
		}

		// do expiry checking
		if (time() > $data->time)
		{
			$this->delete();
			return false;
		}

		// Content-Length juarez
		$content = $input->unsqltext($data->content);
		header("Content-Length: ".strlen($content));
		echo $content;

		// and we're done
		return true;
	}

	/**
	 * Delete
	 *
	 * Delete a cached "file"
	**/
	public function delete ($page=NULL, $location=NULL, $user=NULL, $seed=NULL, $expired=NULL)
	{
		global $xnyo_parent, $db;

		$table = $db->spec('cache');

		// delete that stuff first i guess
		if (is_array($params))
		{
			$sql = 'DELETE FROM '.$table->_title.' WHERE ';

			// ok what are we looking forward to deleting?
			if (!is_null($page))
				$sql .= $table->page.' = \''.$page.'\' AND ';
			if (!is_null($location))
				$sql .= $table->location.' = \''.$location.'\' AND ';
			if (!is_null($user))
				$sql .= $table->user.' = \''.$user.'\' AND ';
			if (!is_null($seed))
				$sql .= $table->seed.' = \''.$seed.'\' AND ';

			$db->exec(substr($sql, 0, -5));
		} else
		{
			// delete cache data for current page i guess
			$sql = 'DELETE FROM '.$table->_title.' WHERE '.$table->page.' = \''.$this->page.'\' AND '.$table->location.' = \''.$xnyo_parent->location.'\' AND '.$table->user.' = \''.$xnyo_parent->user->id.'\' AND '.$table->seed.' = \''.$this->seed().'\'';
			$db->exec($sql);
		}

		return true;
	}

	/**
	 * Delete Expired
	 *
	 * Delete all expired pages
	**/
	public function delete_expired ()
	{
		global $db;
		
		$table = $db->spec('cache');
		$sql = 'DELETE FROM '.$table->_title.' WHERE '.$table->time.' + '.$xnyo_parent->cache_lifetime.' > '.time();
		$db->exec($sql);
		return true;
	}

}

?>
