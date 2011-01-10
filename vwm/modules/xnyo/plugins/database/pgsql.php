<?PHP

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/database/pgsql.php
 *              PostgreSQL Abstraction Layer
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: pgsql.php 57 2004-09-12 10:30:17Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/database/pgsql.php $
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

class db {

	// initial variables
	private $_connections = array();	// database connections
	private $_resources = array();	// misc resources
	private $_history = array();

	/**
	 * Connect!
	 *
	 * Take the given arguments and connect, simple eh?
	 * $args is an assoc. array with the following values: host, port, user, password
	**/
	private function _connect ($dbname, $args=array())
	{

		global $xnyo_parent;

		// check first
		if (empty($dbname))
			return false;

		// debug
		if (XNYO_DEBUG) $xnyo_parent->debug('Attempting to connect to PostgreSQL Server '.(empty($args['host']) ? '' : 'at '.$args['host']).' for database <i>'.$args['dbname'].'</i>.');

		// do we have a connection to this db open already?
		if (isset($this->_connections[$dbname]))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('Connection to <i>'.$dbname.'</i> cached.');
			$this->_connections[$dbname]['times']++;
			return $this->_connections[$dbname]['resource'];
		}

		// build up a connection string
		$args['dbname'] = $dbname;
		foreach ($args as $key => $var)
			$connstr .= $key.'='.$var.' ';

		// open the connection
		$before = $xnyo_parent->_getmicrotime();
		if ($xnyo_parent->use_persistent_db_conns)
			$conn = @pg_pconnect($connstr);
		else
			$conn = @pg_connect($connstr);
		$after = $xnyo_parent->_getmicrotime();

		// bad connection, set error
		if (!$conn)
		{
			$xnyo_parent->trigger_error('Unable to establish connection to PostgerSQL Server: '.$connect_string, WARNING);
			return false;
		}

		if (XNYO_DEBUG) $xnyo_parent->debug('Connected successfully to <i>'.$args['dbname'].'</i> in '.round($after - $before, 5).' seconds.');

		// set the resource id into the database for later use
		$this->_connections[$dbname] = array
		(
			'resource' => $conn,
			'times' => 1,
			'connect_time' => ($after - $before)
		);

		// return it so the calling function can use it
		return $conn;
	}
	
	/**
	 * Close
	 *
	 * Close a database connection
	**/
	public function close ($res)
	{
		global $xnyo_parent;

		// are we closing a resource?
		if (is_resource($res))
		{
			foreach ($this->_connections as $key => $var)
				if ($var['resource'] == $res)
					$name = $key;
		} elseif (isset($this->_connections[$res]))
		{
			$name = $res;
			$res = $this->_connections[$name]['resource'];
		}

		// close it!
		if (!is_resource($res))
		{
			$xnyo_parent->trigger_error('Unable to close unopened database connection <i>'.$name.'</i>: '.$res, NOTICE);
			return false;
		}
		
		// debug
		if (XNYO_DEBUG) $xnyo_parent->debug('Closing database connection to <i>'.$name.'</i>.');

		// marked closed
		$this->_connections[$name]['resource'] = (string)$res;
		$this->_connections[$name]['status'] = 'Closed';

		// close it
		return pg_close($res);
	}

	/**
	 * Select
	 *
	 * This function is charged with selecting the correct database, and issuing orders
	 * to connect if necessary.
	**/
	public function select_db ($dbname)
	{

		// check
		if (empty($dbname))
			return false;

		// mummy!
		global $xnyo_parent;

		// already using warez?
		if (isset($this->_connections[$dbname]))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('Setting connection to database <i>'.$dbname.'</i> as current, previous stored in history.');
			$this->_connections[$dbname]['times']++;
			$this->_resources['db_select'] = $this->_connections[$dbname]['resource'];
			$this->_history[] = $this->_resources['db_select'];
			return $this->_connections[$dbname]['resource'];
		}

		// set variable stuffs
		$args = array();
		if (!empty($xnyo_parent->db_host)) $args['host'] = $xnyo_parent->db_host;
		if (!empty($xnyo_parent->db_port)) $args['port'] = $xnyo_parent->db_port;
		if (!empty($xnyo_parent->db_user)) $args['user'] = $xnyo_parent->db_user;
		if (!empty($xnyo_parent->db_passwd)) $args['passwd'] = $xnyo_parent->db_passwd;

		// connect!
		if (!$this->_connect($dbname, $args))
			return false;

		// debug
		if (XNYO_DEBUG) $xnyo_parent->debug('Setting connection to database <i>'.$dbname.'</i> as current, previous stored in history.');

		// set this as the latest resource
		$this->_resources['db_select'] = $this->_connections[$dbname]['resource'];
		
		// add new history layer
		$this->_history[] = $this->_resources['db_select'];

		// done!
		return $this->_connections[$dbname];

	}


	/**
	 * Exec/Query
	 *
	 * Execute the given SQL against the server
	**/
	public function exec ($sql, $res=NULL)
	{
		global $xnyo_parent;

		// they better give us an SQL query or there'll be trouble!
		if (empty($sql))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('Empty SQL query passed, not doing anything.');
			return false;
		}

		// check magical juarez
		if (is_null($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resource given, using current.');
			$res = $this->_resources['db_select'];
		}
		if (empty($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resources found, giving up.');
			return false;
		}

		// exec the query i guess, not much else to do
		if (XNYO_DEBUG) $xnyo_parent->debug('Attempting to execute the following SQL query against '.$res.' ('.$this->get_database_name($res).') : '.$sql);
		$before = $xnyo_parent->_getmicrotime();
		$result = @pg_query($res, $sql);		
		$after = $xnyo_parent->_getmicrotime();

		// problems?
		if (!$result)
		{
			$xnyo_parent->trigger_error('SQL Query failed. PostgreSQL returned: '.$this->get_error().' (SQL: '.$sql.')', WARNING);
		} else {
			unset($this->error);
		}

		// debug
		if (XNYO_DEBUG) $xnyo_parent->debug('Executed successfully in '.round($after - $before, 5).' seconds, caching result resource');

		// more magic
		$this->_resources['db_exec'] = $result;

		// return the result index
		return new DatabaseResult($result);
	}

	public function query ($sql, $res=NULL)
	{
		// this is just a wrapper for above
		return $this->exec($sql, $res);
	}


	/**
	 * Fetch
	 *
	 * Fetch data from the result
	**/
	public function fetch ($row=NULL, $column=NULL, $res=NULL)
	{

		// do checking stuffs - they want a single result
		if (!is_null($row) && !is_null($column))
			return $this->fetch_result($row, $column, $res);

		// single row
		if (!is_null($row) && is_null($column))
			return $this->fetch_object($row, $res);

		// single column
		if (is_null($row) && !is_null($column))
			return $this->fetch_column($column, $res);

		// everything!
		return $this->fetch_all_objects($res);

		// wow that was hard, huh?
	}


	/**
	 * Fetch All
	 * 
	 * Fetch everything as the default type!
	**/
	public function fetch_all ($res=NULL)
	{
		return $this->fetch_all_objects($res);
	}


	/**
	 * Fetch All Objects
	 *
	 * Fetch the entire contents of the result as an array of objects.
	**/
	public function fetch_all_objects ($res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resource given, using current.');
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resources found, giving up.');
			return false;
		}

		// loop through all the rows, grabby grabby!
		$result = array();
		while ($row = @pg_fetch_object($res))
			$result[] = $row;

		// done
		return $result;
	}


	/**
	 * Fetch All Array
	 *
	 * Fetch the entire contents of the result as an array of objects
	**/
	public function fetch_all_array ($res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resource given, using current.');
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resources found, giving up.');
			return false;
		}

		// use the default function if it exists, quicker
		if (function_exists('pg_fetch_all'))
			return (array)@pg_fetch_all($res);

		// do it manually!
		$result = array();
		while ($row = @pg_fetch_assoc($res))
			$result[] = $row;
		
		// done
		return $result;
	}


	/**
	 * Fetch Object
	 *
	 * Fetch the specified row, or else the next row in a result set as an object
	**/
	public function fetch_object ($row=NULL, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resource given, using current.');
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resources found, giving up.');
			return false;
		}

		// reset the row if necessary
		if (!is_null($row))
			@pg_result_seek($res, $row);

		// and get it
		$result = @pg_fetch_object($res);
		
		// done
		return (!$result ? false : $result);
	}


	/**
	 * Fetch Array
	 *
	 * Fetch the specified row, or else the next row in a result set as an array
	**/
	public function fetch_array ($row=NULL, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resource given, using current.');
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resources found, giving up.');
			return false;
		}


		// select manual or auto
		if (!is_null($row))
			$result = @pg_fetch_assoc($res, $row);
		else
			$result = @pg_fetch_assoc($res);

		// check for NULL, happens sometimes
		if (is_null($result))
			$result = false;
		
		// done
		return (!$result ? false : $result);
	}


	/**
	 * Fetch Column
	 *
	 * Fetch a column out of the result set.
	**/
	public function fetch_column ($column, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resource given, using current.');
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resources found, giving up.');
			return false;
		}

		if (empty($column))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No column given, nothing I can do.');
			return false;
		}

		// reset the internal counter and punch it through
		@pg_result_seek($res, 0);
		$result = array();
		while ($array = @pg_fetch_assoc($res))
			$result[] = $array[$column];
		
		// done
		return $result;
	}


	/**
	 * Fetch Result
	 *
	 * Fetch a single cell of data, if you want lots of data, use the above functions
	 * this is slower than them, but faster if you only really want the one cell.
	**/
	public function fetch_result ($row, $column, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resource given, using current.');
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resources found, giving up.');
			return false;
		}

		if (empty($column) || !is_int($row)) 
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No column specified, or the row is not numeric');
			return false;
		}

		// ouch, how did i ever come up with this function? (hehe i ams funny eh?)
		$result = @pg_fetch_result($res, $row, $column);

		// done
		return (!$result ? false : $result);
	}
	

	/**
	 * Num Rows/NumRows
	 *
	 * Return the number of rows in a result set.
	**/
	public function num_rows ($res=NULL)
	{

		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resource given, using current.');
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resources found, giving up.');
			return false;
		}

		// I don't know how I do it, I'm just so amazing (and funny!)
		$result = @pg_num_rows($res);

		// done
		return (int)$result;
	}
	public function numrows ($res=NULL)
	{
		return $this->num_rows($res);
	}


	/**
	 * Affected Rows/AffectedRows
	 *
	 * Return the number of rows affected by an insert/update
	**/
	public function affected_rows ($res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resource given, using current.');
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resources found, giving up.');
			return false;
		}

		// I don't know how I do it, I'm just so amazing || I fear j00. (I fear me too)
		$result = @pg_affected_rows($res);

		// done
		return (int)$result;
	}


	/**
	 * Get Database Name
	 *
	 * Get the database name of the current or specified resource.
	**/
	public function get_database_name ($res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resource given, using current.');
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resources found, giving up.');
			return false;
		}

		// get it, thats it there is!
		return @pg_dbname($res);
	}

	/**
	 * Copy
	 *
	 * Copy data into a table ala the inbuilt COPY command
	**/
	public function copy ($table, $data, $fields=NULL, $delim=NULL, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resource given, using current.');
			$res = $this->_resources['db_select'];
		}
		if (empty($res))
		{
			// try for the spec!
			if (is_object($this->spec($table)) && is_resource($this->_resources['db_select']))
			{
				// got one!
				$res = $this->_resources['db_select'];

				// double check the table name
				if (!empty($this->table->_table) && $this->table->_table != $table)
					$table = $this->table->_table;
				if (XNYO_DEBUG) $xnyo_parent->debug('Connected to the database <i>'.$this->table->_database.'</i> for table <i>'.$table.'</i>.');
			} else
			{
				if (XNYO_DEBUG) $xnyo_parent->debug('No resources found, giving up.');
				return false;
			}
		}
		
		// debug
		if (XNYO_DEBUG) $xnyo_parent->debug('Attempting to Copy data to <i>'.$table.'</i>.');

		// tab tab!
		if (!is_null($fields))
		{
			if (is_array($fields))
				$fields = join(',', $fields);
			$sql = 'COPY '.$table.' ('.$fields.') FROM stdin';
		} else
			$sql = 'COPY '.$table.' FROM stdin';
		if (is_null($delim))
			$delim = "\t";
		else
			$sql .= ' WITH DELIMITER AS \''.$delim.'\'';

		// lets go! assume that the lines are delimited correctly
		// exec the starting line
		if (!$this->exec($sql, $res))
		{
			$xnyo_parent->trigger_error('Unable to copy data to table <i>'.$table.'</i>.', WARNING);
			return false;
		}

		// loop the data, go go go!
		foreach ($data as $key => $var)
		{
			if (is_array($var))
			{
				// take care of nulls
				foreach ($var as $k => $v)
					if (is_null($v))
						$var[$k] = '\N';

				// make a string
				$var = join($delim, $var);
			}
			if (!pg_put_line($res, $var))
			{
				$xnyo_parent->trigger_error('Error copying data on line '.$key.' to table <i>'.$table.'</i> : '.$var, WARNING);
				return false;
			}
		}
		
		// we survived! close it!
		@pg_put_line($res, '\.');
		@pg_end_copy($res);

		// right, all nicely done now?
		if (XNYO_DEBUG) $xnyo_parent->debug('Copy completed successfully.');
		return true;
	}


	/** 
	 * Insert
	 *
	 * Insert an array of data into the db!
	**/
	public function insert ($table, $data, $options=NULL, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resource given, using current.');
			$res = $this->_resources['db_select'];
		}
		if (empty($res))
		{
			// try for the spec!
			if (is_object($this->spec($table)) && is_resource($this->_resources['db_select']))
			{
				// got one!
				$res = $this->_resources['db_select'];

				// double check the table name
				if (!empty($this->table->_table) && $this->table->_table != $table)
					$table = $this->table->_table;
				if (XNYO_DEBUG) $xnyo_parent->debug('Connected to the database <i>'.$this->table->_database.'</i> for table <i>'.$table.'</i>.');
			} else
			{
				if (XNYO_DEBUG) $xnyo_parent->debug('No resources found, giving up.');
				return false;
			}
		}

		// this is mostly simple stuff, if we've got the function, use that
		if (function_exists('pg_insert') && $xnyo_parent->use_db_insert)
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('Inserting row into table <i>'.$table.'</i> via pg_insert().');
			if (!is_null($options))
				return pg_insert($res, $table, $data, $options);
			return pg_insert($res, $table, $data);
		}

		// that didnt work? check for the sql generation plugin and use that
		if (!is_object($xnyo_parent->sql) && !$xnyo_parent->load_sql())
		{
			$xnyo_parent->trigger_error('Unable to insert row into <i>'.$table.'</i>, no methods found.', WARNING);
			return false;
		}
		
		// go go!
		if (XNYO_DEBUG) $xnyo_parent->debug('Inserting row into table <i>'.$table.'</i> via the SQL Generation Plugin + $db->exec()');

		return $this->exec($xnyo_parent->sql->insert($table, $data), $res);
	}
	
	
	/** 
	 * Update
	 *
	 * Update an array of data in the db! Designed for SIMPLE updates only.
	**/
	public function update ($table, $data, $where=array(), $options=NULL, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resource given, using current.');
			$res = $this->_resources['db_select'];
		}
		if (empty($res))
		{
			// try for the spec!
			if (is_object($this->spec($table)) && is_resource($this->_resources['db_select']))
			{
				// got one!
				$res = $this->_resources['db_select'];

				// double check the table name
				if (!empty($this->table->_table) && $this->table->_table != $table)
					$table = $this->table->_table;
				if (XNYO_DEBUG) $xnyo_parent->debug('Connected to the database <i>'.$this->table->_database.'</i> for table <i>'.$table.'</i>.');
			} else
			{
				if (XNYO_DEBUG) $xnyo_parent->debug('No resources found, giving up.');
				return false;
			}
		}

		// this is mostly simple stuff, if we've got the function, use that
		if (function_exists('pg_update') && $xnyo_parent->use_db_update)
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('Updating row(s) in table <i>'.$table.'</i> via pg_update().');
			if (!is_null($options))
				return pg_update($res, $table, $data, $where, $options);
			return pg_update($res, $table, $data, $where);
		}

		// that didnt work? check for the sql generation plugin and use that
		if (!is_object($xnyo_parent->sql) && !$xnyo_parent->load_sql())
		{
			$xnyo_parent->trigger_error('Unable to update row(s) in <i>'.$table.'</i>, no methods found.', WARNING);
			return false;
		}
		
		// go go!
		if (XNYO_DEBUG) $xnyo_parent->debug('Updating row(s) in table <i>'.$table.'</i> via the SQL Generation Plugin + $db->exec()');
		return $this->exec($xnyo_parent->sql->update($table, $data, $where), $res);
	}


	/** 
	 * Delete
	 *
	 * Delete data out of the DB.  Designed for SIMPLE deletes only.
	**/
	public function delete ($table, $where=array(), $options=NULL, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resource given, using current.');
			$res = $this->_resources['db_select'];
		}
		if (empty($res))
		{
			// try for the spec!
			if (is_object($this->spec($table)) && is_resource($this->_resources['db_select']))
			{
				// got one!
				$res = $this->_resources['db_select'];

				// double check the table name
				if (!empty($this->table->_table) && $this->table->_table != $table)
					$table = $this->table->_table;
				if (XNYO_DEBUG) $xnyo_parent->debug('Connected to the database <i>'.$this->table->_database.'</i> for table <i>'.$table.'</i>.');
			} else
			{
				if (XNYO_DEBUG) $xnyo_parent->debug('No resources found, giving up.');
				return false;
			}
		}

		// this is mostly simple stuff, if we've got the function, use that
		if (function_exists('pg_delete') && $xnyo_parent->use_db_delete)
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('Deleting row(s) from table <i>'.$table.'</i> via pg_delete().');
			if (!is_null($options))
				return pg_delete($res, $table, $where, $options);
			return pg_delete($res, $table, $where);
		}

		// that didnt work? check for the sql generation plugin and use that
		if (!is_object($xnyo_parent->sql) && !$xnyo_parent->load_sql())
		{
			$xnyo_parent->trigger_error('Unable to delete row(s) from <i>'.$table.'</i>, no methods found.', WARNING);
			return false;
		}
		
		// go go!
		if (XNYO_DEBUG) $xnyo_parent->debug('Deleting row(s) from table <i>'.$table.'</i> via the SQL Generation Plugin + $db->exec()');
		return $this->exec($xnyo_parent->sql->delete($table, $where), $res);
	}
	

	/** 
	 * Select
	 *
	 * Select stuff from the database. Designed for SIMPLE selects only.
	**/
	public function select ($table, $where=array(), $options=NULL, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('No resource given, using current.');
			$res = $this->_resources['db_select'];
		}
		if (empty($res))
		{
			// try for the spec!
			if (is_object($this->spec($table)) && is_resource($this->_resources['db_select']))
			{
				// got one!
				$res = $this->_resources['db_select'];

				// double check the table name
				if (!empty($this->table->_table) && $this->table->_table != $table)
					$table = $this->table->_table;
				if (XNYO_DEBUG) $xnyo_parent->debug('Connected to the database <i>'.$this->table->_database.'</i> for table <i>'.$table.'</i>.');
			} else
			{
				if (XNYO_DEBUG) $xnyo_parent->debug('No resources found, giving up.');
				return false;
			}
		}

		// this is mostly simple stuff, if we've got the function, use that
		// also, pg_select appears not to accept empty arrays as conditions, fail over to the gen functions if so
		if (function_exists('pg_select') && $xnyo_parent->use_db_select && count($where))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('Selecting row(s) from table <i>'.$table.'</i> via pg_select().');
			if (!is_null($options))
				$result = pg_select($res, $table, $where, $options);
			else
				$result = pg_select($res, $table, (array)$where);
		} else
		{
			// that didnt work? check for the sql generation plugin and use that
			if (!is_object($xnyo_parent->sql) && !$xnyo_parent->load_sql())
			{
				$xnyo_parent->trigger_error('Unable to select row(s) from <i>'.$table.'</i>, no methods found.', WARNING);
				return false;
			}
			
			// go go!
			if (XNYO_DEBUG) $xnyo_parent->debug('Selecting row(s) from table <i>'.$table.'</i> via the SQL Generation Plugin + $db->exec()');
			if (!$this->exec($xnyo_parent->sql->select($table, $where)))
				return false;
			return $this->fetch_all_array();
			
		}
		
		// problems?
		if (!$result)
		{
			$xnyo_parent->trigger_error('pg_select() failed!. PostgreSQL returned: '.pg_last_error($res), WARNING);
		} else {
			unset($this->error);
			if (XNYO_DEBUG) $xnyo_parent->debug('pg_select() returned success! '.count($result).' results.');
		}

		// return the result index
		return $result;
	}


	/**
	 * Spec
	 *
	 * Load the database table specifications file and hand back the object. Auto-connect too if asked.
	**/
	public function spec ($table, $connect=true)
	{
		global $xnyo_parent;

		// load the plugin, etc etc
		if (XNYO_DEBUG) $xnyo_parent->debug('Loading up specification file for <i>'.$table.'</i>.');
		$spec = $xnyo_parent->load_plugin($table, 'dbspec');

		// connect to the database if we're asked to
		if ($connect && isset($spec->_database))
		{
			if (XNYO_DEBUG) $xnyo_parent->debug('Auto-connecting to <i>'.$spec->_database.'</i> for <i>'.$spec->_title.'</i> ('.$table.')');
			$this->select_db($spec->_database);
		}

		// convenience?
		$this->table = $spec;

		// done
		return $spec;
	}
	

	/**
	 * Go Back
	 *
	 * Reverse in the connection history.
	**/
	public function go_back ()
	{
		global $xnyo_parent;

		if (count($this->_history) <= 1)
			return true;

		// delete the current one
		array_pop($this->_history);
		
		// reset the currently used resource
		$this->_resources['db_select'] = end($this->_history);
		if (XNYO_DEBUG) $xnyo_parent->debug('Going back in time! Retrieving connection to '.$this->_resources['db_select'].' (<i>'.$this->get_database_name($this->_resources['db_select']).'</i>). Setting as current.');
	}
	

	/**
	 * SQL Text
	 *
	 * Use the inbuilt postgres function for safely escaping text.
	**/
	public function sqltext ($text)
	{
		return @pg_escape_string($text);
	}


	/**
	 * SQL Binary
	 *
	 * Convert binary data to bytea for safety insertions!
	**/
	public function sqlbinary ($text)
	{
		return @pg_escape_bytea($text);
	}
	

	/**
	 * UnSQL Binary
	 *
	 * Convert from postgres bytea data back to binary
	**/
	public function unsqlbinary ($text)
	{
		return @pg_unescape_bytea($text);
	}


	/**
	 * Get Error
	 *
	 * Get the last known error
	**/
	public function get_error ()
	{
	
		// just return it
		$this->error = @pg_last_error($this->_resource['db_select']);
		return $this->error;
	}
	
	/**
	 * Fetch Connections
	 *
	 * Fetch all the connections, and their status
	**/
	public function fetch_connections ()
	{
		// status messages..
		$statuses = array
		(
			0 => 'Ok',
			1 => 'Bad'
		);
		
		$conns = array();
		foreach ($this->_connections as $key => $var)
		{
			// get connection status
			if (empty($var['status']))
			{
				if (pg_connection_status($var['resource']) === 0)
				{
					if (pg_connection_busy($var['resource']))
						$var['status'] = 'Busy';
					else
						$var['status'] = 'Ok';
				} else
					$var['status'] = 'Bad';
			}

			// add the data
			$conns[] = array
			(
				'name' => $key,
				'resource' => (string)$var['resource'],
				'status' => $var['status'],
				'times' => $var['times'],
				'connect_time' => $var['connect_time']
			);
		}
		return $conns;
	}
			

}

/**
 * New Object Oriented Interface thingy!
**/
class XnyoDB extends db
{
	public function __construct ($table=NULL)
	{
		if (!is_null($table))
		{
			if (!is_object($spec = $this->spec($table)))
				$this->select_db($table);
		}
	}
}
		
/**
 * Database Iterator Classes
**/
class DatabaseIterator implements Iterator
{
	private $data = array();
	private $count = 0;
	private $key = 0;
	
	public function __construct ($data)
	{
		$this->data = $data;
		$this->count = count($this->data);
	}
	
	public function rewind ()
	{
		$this->key = 0;
	}
	
	public function valid ()
	{
		return $this->key < $this->count;
	}

	public function key ()
	{
		return $this->key;
	}
	
	public function current ()
	{
		return $this->data[$this->key];
	}
	
	public function next ()
	{
		$this->key++;
	}
}

class DatabaseResult implements IteratorAggregate
{
	private $result;
	private $data = array();
	private $num_rows;
	private $affected_rows;
	
	public function __construct ($result)
	{
		$this->result = $result;

		// get the number of rows/affected_rows
		$this->num_rows = (int)@pg_num_rows($this->result);
		$this->affected_rows = (int)@pg_affected_rows($this->result);
	}

	public function getIterator ()
	{
		$this->fetch_data();
		return new DatabaseIterator($this->data);
	}

	public function num_rows ()
	{
		return $this->num_rows;
	}
	public function numRows ()
	{
		return $this->num_rows;
	}

	public function affected_rows ()
	{
		return $this->affected_rows;
	}
	public function affectedRows ()
	{
		return $this->affected_rows;
	}

	public function get_resource ()
	{
		return $this->result;
	}
	public function getResource ()
	{
		return $this->result;
	}

	public function success ()
	{
		return ($this->num_rows || $this->affected_rows);
	}
	
	private function fetch_data ()
	{
		if (count($this->data))
			return true;

		// fetch all the stuff into the cache
		if ($this->num_rows > 0)
			while ($row = @pg_fetch_object($this->result))
				$this->data[] = $row;
	}

	public function fetch ($row=NULL, $column=NULL)
	{

		// do checking stuffs - they want a single result
		if (!is_null($row) && !is_null($column))
			return $this->fetch_result($row, $column);

		// single row
		if (!is_null($row) && is_null($column))
			return $this->fetch_object($row);

		// single column
		if (is_null($row) && !is_null($column))
			return $this->fetch_column($column);

		// everything!
		return $this->fetch_object();

		// wow that was hard, huh?
	}

	public function fetch_object ($row=NULL)
	{
		$this->fetch_data();
		if (is_null($row))
			return $this->data;
		if (!isset($this->data[$row]))
			return false;
		return $this->data[$row];
	}
	public function fetchObject($row=NULL)
	{
		return $this->fetch_object($row);
	}

	public function fetch_array ($row=NULL)
	{
		$this->fetch_data();
		if (is_null($row))
		{
			foreach ($this->data as $key => $var)
				$return[$key] = (array)$var;
			return $return;
		}
		if (!isset($this->data[$row]))
			return false;
		return (array)$this->data[$row];
	}
	public function fetchArray ($row=NULL)
	{
		return $this->fetch_array($row);
	}

	public function fetch_row ($row=NULL)
	{
		return $this->fetch_object($row);
	}
	public function fetchRow ($row=NULL)
	{
		return $this->fetch_object($row);
	}

	public function fetch_column ($col)
	{
		$this->fetch_data();
		foreach ($this->data as $var)
			$return = $var->$col;
		return $return;
	}
	public function fetchColumn ($col)
	{
		return $this->fetch_column($col);
	}
	
	public function fetch_result ($row, $col)
	{
		$this->fetch_data();
		if (!isset($this->data[$row]) || !isset($this->data[$row]->$col))
			return false;
		return $this->data[$row]->$col;
	}
	public function fetchResult ($row, $col)
	{
		return $this->fetch_result($row, $col);
	}
}

?>
