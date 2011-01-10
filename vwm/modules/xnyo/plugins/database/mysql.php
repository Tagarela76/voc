<?PHP

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/database/mysql.php
 *              MySQL Abstraction Layer
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: pgsql.php 23 2004-08-12 09:14:57Z bok $
 * SVN URL:     $HeadURL: http://svn.lexx.odynia.org/xnyo/branches/php4/plugins/database/pgsql.php $
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
	var $_connections = array();	// database connections
	var $_resources = array();	// misc resources
	var $_history = array();

	/**
	 * Connect!
	 *
	 * Take the given arguments and connect, simple eh?
	 * $args is an assoc. array with the following values: host, port, user, password
	**/
	function _connect ($dbname, $args=array())
	{

		global $xnyo_parent;

		// check first
		if (empty($dbname))
			return false;

		// debug
		$xnyo_parent->trigger_error('Attempting to connect to MySQL Server '.(empty($args['host']) ? '' : 'at '.$args['host']).' for database <i>'.$args['dbname'].'</i>.', DEBUG);

		// do we have a connection to this db open already?
		if (isset($this->_connections[$dbname]))
		{
			$xnyo_parent->trigger_error('Connection to <i>'.$dbname.'</i> cached.', DEBUG);
			$this->_connections[$dbname]['times']++;
			return $this->_connections[$dbname]['resource'];
		}

		// open the connection
		$before = $xnyo_parent->_getmicrotime();
		if ($xnyo_parent->use_persistent_db_conns)
			$conn = @mysql_pconnect($args['host'], $args['user'], $args['passwd']);
		else
			$conn = @mysql_connect($args['host'], $args['user'], $args['passwd']);
		$after = $xnyo_parent->_getmicrotime();

		// bad connection, set error
		if (!$conn)
		{
			$xnyo_parent->trigger_error('Unable to establish connection to MySQL Server: '.$args['host'], WARNING);
			return false;
		}
		
		// select the db
		if (!@mysql_select_db($dbname, $conn))
		{
			$xnyo_parent->trigger_error('Unable to change to database <i>'.$dbname.'</i>.', WARNING);
			return false;
		}

		$xnyo_parent->trigger_error('Connected successfully to <i>'.$dbname.'</i> in '.round($after - $before, 5).' seconds.', DEBUG);

		// set the resource id into the database for later use
		$this->_connections[$dbname] = array
		(
			'resource' => $conn,
			'times' => 1,
			'connect_time' => ($after - $before)
		);

		//	forse xnyo to use utf
		//	@author Denis
		mysql_query('SET NAMES utf8');
		
		// return it so the calling function can use it		
		return $conn;
	}
	
	/**
	 * Close
	 *
	 * Close a database connection
	**/
	function close ($res)
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
		$xnyo_parent->trigger_error('Closing database connection to <i>'.$name.'</i>.', DEBUG);

		// marked closed
		$this->_connections[$name]['resource'] = (string)$res;
		$this->_connections[$name]['status'] = 'Closed';

		// close it
		return @mysql_close($res);
	}

	/**
	 * Select
	 *
	 * This function is charged with selecting the correct database, and issuing orders
	 * to connect if necessary.
	**/
	function select_db ($dbname)
	{

		// check
		if (empty($dbname))
			return false;

		// mummy!
		global $xnyo_parent;

		// already using warez?
		if (isset($this->_connections[$dbname]))
		{
			$xnyo_parent->trigger_error('Setting connection to database <i>'.$dbname.'</i> as current, previous stored in history.', DEBUG);
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
		$xnyo_parent->trigger_error('Setting connection to database <i>'.$dbname.'</i> as current, previous stored in history.', DEBUG);

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
	function exec ($sql, $res=NULL)
	{
		global $xnyo_parent;

		// they better give us an SQL query or there'll be trouble!
		if (empty($sql))
		{
			$xnyo_parent->trigger_error('Empty SQL query passed, not doing anything.', DEBUG);
			return false;
		}

		// check magical juarez
		if (is_null($res))
		{
			$xnyo_parent->trigger_error('No resource given, using current.', DEBUG);
			$res = $this->_resources['db_select'];
		}
		if (empty($res))
		{
			$xnyo_parent->trigger_error('No resources found, giving up.', DEBUG);
			return false;
		}

		// exec the query i guess, not much else to do
		$xnyo_parent->trigger_error('Attempting to execute the following SQL query against '.$res.' ('.$this->get_database_name($res).') : '.$sql, DEBUG);
		$before = $xnyo_parent->_getmicrotime();
		$result = @mysql_query($sql, $res);		
		$after = $xnyo_parent->_getmicrotime();

		// problems?
		if (!$result)
		{
			//	Trace SQL errors
			if (TRACE_MYSQL === 'on') {
				$content = "(".date('m-d-Y H:i:s').") SQL Query failed. MySQL returned: ".$this->get_error()." (SQL: ".$sql.")". WARNING."\n";
				error_log($content, 3, DIR_PATH_LOGS."mysqlErrors.log");
			}
			$xnyo_parent->trigger_error('SQL Query failed. MySQL returned: '.$this->get_error().' (SQL: '.$sql.')', WARNING);
		} else {
			unset($this->error);
		}

		// debug
		$xnyo_parent->trigger_error('Executed successfully in '.round($after - $before, 5).' seconds, caching result resource', DEBUG);

		// more magic
		$this->_resources['db_exec'] = $result;

		// return the result index
		return $result;
	}

	function query ($sql, $res=NULL)
	{	
		//	Trace sql queries
		if (TRACE_MYSQL === 'on') {
			$content = "(".date('m-d-Y H:i:s').") ".$sql."\n";
			error_log($content, 3, DIR_PATH_LOGS."mysql_".date('mdY').".log");
		}
	
		// this is just a wrapper for above
		return $this->exec($sql, $res);
	}


	/**
	 * Fetch
	 *
	 * Fetch data from the result
	**/
	function fetch ($row=NULL, $column=NULL, $res=NULL)
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
	function fetch_all ($res=NULL)
	{
		return $this->fetch_all_objects($res);
	}


	/**
	 * Fetch All Objects
	 *
	 * Fetch the entire contents of the result as an array of objects.
	**/
	function fetch_all_objects ($res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			$xnyo_parent->trigger_error('No resource given, using current.', DEBUG);
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			$xnyo_parent->trigger_error('No resources found, giving up.', DEBUG);
			return false;
		}

		// loop through all the rows, grabby grabby!
		$result = array();
		while ($row = @mysql_fetch_object($res))
			$result[] = $row;

		// done
		return $result;
	}


	/**
	 * Fetch All Array
	 *
	 * Fetch the entire contents of the result as an array of objects
	**/
	function fetch_all_array ($res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			$xnyo_parent->trigger_error('No resource given, using current.', DEBUG);
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			$xnyo_parent->trigger_error('No resources found, giving up.', DEBUG);
			return false;
		}

		// do it manually!
		$result = array();
		while ($row = @mysql_fetch_assoc($res))
			$result[] = $row;
		
		// done
		return $result;
	}


	/**
	 * Fetch Object
	 *
	 * Fetch the specified row, or else the next row in a result set as an object
	**/
	function fetch_object ($row=NULL, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			$xnyo_parent->trigger_error('No resource given, using current.', DEBUG);
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			$xnyo_parent->trigger_error('No resources found, giving up.', DEBUG);
			return false;
		}

		// reset the row if necessary
		if (!is_null($row))
			@mysql_data_seek($res, $row);

		// and get it
		$result = @mysql_fetch_object($res);
		
		// done
		return (!$result ? false : $result);
	}


	/**
	 * Fetch Array
	 *
	 * Fetch the specified row, or else the next row in a result set as an array
	**/
	function fetch_array ($row=NULL, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			$xnyo_parent->trigger_error('No resource given, using current.', DEBUG);
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			$xnyo_parent->trigger_error('No resources found, giving up.', DEBUG);
			return false;
		}


		// select manual or auto
		if (is_null($row))
		{
			@mysql_data_seek($res, $row);
		}
		else
		{
			$result = @mysql_fetch_assoc($res);
		}

		// check for NULL, happens sometimes
		if (is_null($result))
		{
			$result = false;
		}
		
		
		// done
		return (!$result ? false : $result);
	}


	/**
	 * Fetch Column
	 *
	 * Fetch a column out of the result set.
	**/
	function fetch_column ($column, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			$xnyo_parent->trigger_error('No resource given, using current.', DEBUG);
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			$xnyo_parent->trigger_error('No resources found, giving up.', DEBUG);
			return false;
		}

		if (empty($column))
		{
			$xnyo_parent->trigger_error('No column given, nothing I can do.', DEBUG);
			return false;
		}

		// reset the internal counter and punch it through
		@mysql_data_seek($res, 0);
		$result = array();
		while ($array = @mysql_fetch_assoc($res))
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
	function fetch_result ($row, $column, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			$xnyo_parent->trigger_error('No resource given, using current.', DEBUG);
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			$xnyo_parent->trigger_error('No resources found, giving up.', DEBUG);
			return false;
		}

		if (empty($column) || !is_int($row)) 
		{
			$xnyo_parent->trigger_error('No column specified, or the row is not numeric', DEBUG);
			return false;
		}

		// ouch, how did i ever come up with this function? (hehe i ams funny eh?)
		$result = @mysql_result($res, $row, $column);

		// done
		return (!$result ? false : $result);
	}
	

	/**
	 * Num Rows/NumRows
	 *
	 * Return the number of rows in a result set.
	**/
	function num_rows ($res=NULL)
	{

		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			$xnyo_parent->trigger_error('No resource given, using current.', DEBUG);
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			$xnyo_parent->trigger_error('No resources found, giving up.', DEBUG);
			return false;
		}

		// I don't know how I do it, I'm just so amazing (and funny!)
		$result = @mysql_num_rows($res);

		// done
		return (int)$result;
	}
	function numrows ($res=NULL)
	{
		return $this->num_rows($res);
	}


	/**
	 * Affected Rows/AffectedRows
	 *
	 * Return the number of rows affected by an insert/update
	**/
	function affected_rows ($res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			$xnyo_parent->trigger_error('No resource given, using current.', DEBUG);
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			$xnyo_parent->trigger_error('No resources found, giving up.', DEBUG);
			return false;
		}

		// I don't know how I do it, I'm just so amazing || I fear j00. (I fear me too)
		$result = @mysql_affected_rows();

		// done
		return (int)$result;
	}


	/**
	 * Get Database Name
	 *
	 * Get the database name of the current or specified resource.
	**/
	function get_database_name ($res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			$xnyo_parent->trigger_error('No resource given, using current.', DEBUG);
			$res = $this->_resources['db_exec'];
		}
		if (empty($res))
		{
			$xnyo_parent->trigger_error('No resources found, giving up.', DEBUG);
			return false;
		}

		// get it, thats it there is!
		if (in_array($res, $this->_connections))
			return $this->_connections[array_search($res, $this->_connections)];
	}

	/**
	 * Copy
	 *
	 * Copy data into a table ala the inbuilt COPY command
	**/
	function copy ($table, $data, $fields=NULL, $delim=NULL, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			$xnyo_parent->trigger_error('No resource given, using current.', DEBUG);
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
				$xnyo_parent->trigger_error('Connected to the database <i>'.$this->table->_database.'</i> for table <i>'.$table.'</i>.', DEUBG);
			} else
			{
				$xnyo_parent->trigger_error('No resources found, giving up.', DEBUG);
				return false;
			}
		}
		
		// debug
		$xnyo_parent->trigger_error('Attempting to Copy data to <i>'.$table.'</i>.', DEBUG);

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
		$xnyo_parent->trigger_error('Copy completed successfully.', DEBUG);
		return true;
	}


	/** 
	 * Insert
	 *
	 * Insert an array of data into the db!
	**/
	function insert ($table, $data, $options=NULL, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			$xnyo_parent->trigger_error('No resource given, using current.', DEBUG);
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
				$xnyo_parent->trigger_error('Connected to the database <i>'.$this->table->_database.'</i> for table <i>'.$table.'</i>.', DEUBG);
			} else
			{
				$xnyo_parent->trigger_error('No resources found, giving up.', DEBUG);
				return false;
			}
		}

		// use the sql generation stuff
		if (!is_object($xnyo_parent->sql) && !$xnyo_parent->load_sql())
		{
			$xnyo_parent->trigger_error('Unable to insert row into <i>'.$table.'</i>, no methods found.', WARNING);
			return false;
		}
		
		// go go!
		$xnyo_parent->trigger_error('Inserting row into table <i>'.$table.'</i> via the SQL Generation Plugin + $db->exec()', DEBUG);

		return $this->exec($xnyo_parent->sql->insert($table, $data), $res);
	}
	
	
	/** 
	 * Update
	 *
	 * Update an array of data in the db! Designed for SIMPLE updates only.
	**/
	function update ($table, $data, $where=array(), $options=NULL, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			$xnyo_parent->trigger_error('No resource given, using current.', DEBUG);
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
				$xnyo_parent->trigger_error('Connected to the database <i>'.$this->table->_database.'</i> for table <i>'.$table.'</i>.', DEBUG);
			} else
			{
				$xnyo_parent->trigger_error('No resources found, giving up.', DEBUG);
				return false;
			}
		}

		// use the sql generation stuff
		if (!is_object($xnyo_parent->sql) && !$xnyo_parent->load_sql())
		{
			$xnyo_parent->trigger_error('Unable to update row(s) in <i>'.$table.'</i>, no methods found.', WARNING);
			return false;
		}
		
		// go go!
		$xnyo_parent->trigger_error('Updating row(s) in table <i>'.$table.'</i> via the SQL Generation Plugin + $db->exec()', DEBUG);
		return $this->exec($xnyo_parent->sql->update($table, $data, $where), $res);
	}


	/** 
	 * Delete
	 *
	 * Delete data out of the DB.  Designed for SIMPLE deletes only.
	**/
	function delete ($table, $where=array(), $options=NULL, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			$xnyo_parent->trigger_error('No resource given, using current.', DEBUG);
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
				$xnyo_parent->trigger_error('Connected to the database <i>'.$this->table->_database.'</i> for table <i>'.$table.'</i>.', DEUBG);
			} else
			{
				$xnyo_parent->trigger_error('No resources found, giving up.', DEBUG);
				return false;
			}
		}

		// use the sql generation stuff
		if (!is_object($xnyo_parent->sql) && !$xnyo_parent->load_sql())
		{
			$xnyo_parent->trigger_error('Unable to delete row(s) from <i>'.$table.'</i>, no methods found.', WARNING);
			return false;
		}
		
		// go go!
		$xnyo_parent->trigger_error('Deleting row(s) from table <i>'.$table.'</i> via the SQL Generation Plugin + $db->exec()', DEBUG);
		return $this->exec($xnyo_parent->sql->delete($table, $where), $res);
	}
	

	/** 
	 * Select
	 *
	 * Select stuff from the database. Designed for SIMPLE selects only.
	**/
	function select ($table, $where=array(), $options=NULL, $res=NULL)
	{
		global $xnyo_parent;

		// checks
		if (is_null($res))
		{
			$xnyo_parent->trigger_error('No resource given, using current.', DEBUG);
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
				$xnyo_parent->trigger_error('Connected to the database <i>'.$this->table->_database.'</i> for table <i>'.$table.'</i>.', DEUBG);
			} else
			{
				$xnyo_parent->trigger_error('No resources found, giving up.', DEBUG);
				return false;
			}
		}

		// use the sql generation stuff
		if (!is_object($xnyo_parent->sql) && !$xnyo_parent->load_sql())
		{
			$xnyo_parent->trigger_error('Unable to select row(s) from <i>'.$table.'</i>, no methods found.', WARNING);
			return false;
		}
		
		// go go!
		$xnyo_parent->trigger_error('Selecting row(s) from table <i>'.$table.'</i> via the SQL Generation Plugin + $db->exec()', DEBUG);
		if (!$this->exec($xnyo_parent->sql->select($table, $where)))
			return false;
		return $this->fetch_all_array();
			
		
	}


	/**
	 * Spec
	 *
	 * Load the database table specifications file and hand back the object. Auto-connect too if asked.
	**/
	function spec ($table, $connect=true)
	{
		global $xnyo_parent;

		// load the plugin, etc etc
		$xnyo_parent->trigger_error('Loading up specification file for <i>'.$table.'</i>.', DEBUG);
		$spec = $xnyo_parent->load_plugin($table, 'dbspec');

		// connect to the database if we're asked to
		if ($connect && isset($spec->_database))
		{
			$xnyo_parent->trigger_error('Auto-connecting to <i>'.$spec->_database.'</i> for <i>'.$spec->_title.'</i> ('.$table.')', DEBUG);
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
	function go_back ()
	{
		global $xnyo_parent;

		if (count($this->_history) <= 1)
			return true;

		// delete the current one
		array_pop($this->_history);
		
		// reset the currently used resource
		$this->_resources['db_select'] = end($this->_history);
		$xnyo_parent->trigger_error('Going back in time! Retrieving connection to '.$this->_resources['db_select'].' (<i>'.$this->get_database_name($this->_resources['db_select']).'</i>). Setting as current.', DEBUG);
	}
	

	/**
	 * SQL Text
	 *
	 * Use the inbuilt postgres function for safely escaping text.
	**/
	function sqltext ($text)
	{
		return @mysql_escape_string($text);
	}


	/**
	 * Get Error
	 *
	 * Get the last known error
	**/
	function get_error ()
	{
	
		// just return it
		$this->error = @mysql_error($this->_resource['db_select']);
		return $this->error;
	}
	
	/**
	 * Fetch Connections
	 *
	 * Fetch all the connections, and their status
	**/
	function fetch_connections ()
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
			//	if (pg_connection_status($var['resource']) === 0)
			//	{
			//		if (pg_connection_busy($var['resource']))
			//			$var['status'] = 'Busy';
			//		else
			//			$var['status'] = 'Ok';
			//	} else
			//		$var['status'] = 'Bad';
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
	
	public function beginTransaction()
	{
		$this->query("START TRANSACTION;");
	}	
	
	public function commitTransaction()
	{
		$this->query("COMMIT;");
	}
	
	public function rollbackTransaction()
	{
		$this->query("ROLLBACK;");
	}	
	
	public function getLastInsertedID()
	{
		$this->query("SELECT LAST_INSERT_ID() id");
		$id = $this->fetch(0)->id;
		return $id;
	}	

}

?>
