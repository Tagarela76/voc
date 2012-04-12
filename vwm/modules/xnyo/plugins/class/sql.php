<?php

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/class/sql.php
 *              SQL Generation Functions
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: sql.php 57 2004-09-12 10:30:17Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/class/sql.php $
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

class sql_plugin
{

	/**
	 * Generate a nicely done insert query
	**/
	public function insert ($table, $data)
	{
		global $xnyo_parent, $input;
		
		// data is expected to be in the form of row -> result
		if (XNYO_DEBUG) $xnyo_parent->debug('Preparing to iterate over the insert data, number of rows is '.count($data).'.');
		foreach ($data as $key => $var)
		{
			if (empty($key))
				continue;

			$keys[] = $key;
			if (is_numeric($var))
			        if (preg_match('/[.eE]/', $var))
			                $vars[] = floatval($var);
			        else
			                $vars[] = intval($var);
			elseif (empty($var))
			        $vars[] = 'NULL';
			else
			        $vars[] = "'".$input->sqltext($var)."'";
		}
		
		// should be simple enough
		$sql = 'INSERT INTO '.$table.' ('.join(', ', $keys).') VALUES ('.join(', ', $vars).')';
		if (XNYO_DEBUG) $xnyo_parent->debug('SQL Insert query generated. SQL to be executed is: '.$sql);
		
		// kill off excess vars
		unset($table, $data, $key, $var, $keys, $vars);
		
		return $sql;
	}

	/**
	 * Generate a nicely done update query
	**/
	public function update ($table, $data, $where=array())
	{
		global $xnyo_parent, $input;
		
		// data is expected to be in the form of row -> result
		if (XNYO_DEBUG) $xnyo_parent->debug('Preparing to iterate over the update data, number of rows is '.count($data).', number of conditions to match is '.count($where).'.');
		foreach ($data as $key => $var)
		{
			if (empty($key))
				continue;

			if (is_numeric($var))
			        if (preg_match('/[.eE]/', $var))
			                $var= floatval($var);
			        else
			                $var = intval($var);
			elseif (empty($var))
			        $var = 'NULL';
			else
			        $var = "'".$input->sqltext($var)."'";

			// done
			$set[] = $key.' = '.$var;
			
		}
		
		// check for assoc array on $where
		foreach ((array)array_keys($where) as $var)
			if (!is_numeric($var))
			{
				// fix fix
				foreach ($where as $k => $v)
					$_where[] = $k.' = \''.$input->sqltext($v).'\'';
				$where = $_where;
				break;
			}

		// should be simple enough
		$sql = 'UPDATE '.$table.' SET '.join(', ', $set);
		if (!empty($where))
			$sql .= ' WHERE '.join(' AND ', $where);
		if (XNYO_DEBUG) $xnyo_parent->debug('SQL Update query generated. SQL to be returned is: '.$sql);
		
		// kill off excess vars
		unset ($data, $key, $var, $set, $where, $_where, $table, $k, $v);
		
		return $sql;
	}
	

	/**
	 * Generate a nicely done delete query
	**/
	public function delete ($table, $where=array())
	{
		global $xnyo_parent, $input;
		
		// data is expected to be in the form of row -> result
		if (XNYO_DEBUG) $xnyo_parent->debug('Preparing to iterate over the delete data, number of conditions to match is '.count($where).'.');
		
		// check for assoc array on $where
		foreach ((array)array_keys($where) as $var)
			if (!is_numeric($var))
			{
				// fix fix
				foreach ($where as $k => $v)
					$_where[] = $k.' = \''.$input->sqltext($v).'\'';
				$where = $_where;
				break;
			}

		// should be simple enough
		$sql = 'DELETE FROM '.$table;
		if (!empty($where))
			$sql .= ' WHERE '.join(' AND ', $where);
		if (XNYO_DEBUG) $xnyo_parent->debug('SQL Delete query generated. SQL to be returned is: '.$sql);
		
		// kill off excess vars
		unset($table, $where, $_where, $var, $k, $v);
		
		return $sql;
	}
	
	
	/**
	 * Generate a nicely done select query
	**/
	public function select ($table, $where=array())
	{
		global $xnyo_parent, $input;
		
		// data is expected to be in the form of row -> result
		if (XNYO_DEBUG) $xnyo_parent->debug('Preparing to iterate over the select data, number of conditions to match is '.count($where).'.');
		
		// check for assoc array on $where
		foreach ((array)array_keys($where) as $var)
			if (!is_numeric($var))
			{
				// fix fix
				foreach ($where as $k => $v)
					$_where[] = $k.' = \''.$input->sqltext($v).'\'';
				$where = $_where;
				break;
			}

		// should be simple enough
		$sql = 'SELECT * FROM '.$table;
		if (!empty($where))
			$sql .= ' WHERE '.join(' AND ', $where);
		if (XNYO_DEBUG) $xnyo_parent->debug('SQL Select query generated. SQL to be returned is: '.$sql);
		
		// kill off excess vars
		unset($table, $where, $_where, $k, $v, $var);

		return $sql;
	}

}
	