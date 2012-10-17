<?php

/**
 * Project:     Xnyo: Application Backend
 * File:        xnyo.class.php
 *              Main Xnyo Class
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: xnyo.class.php 66 2004-10-13 09:16:36Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/xnyo.class.php $
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

// set the constants first
// directory seperator (for compatibility with windows)
// this is mearly a shortcut to an existing constant
if (!defined('DIRSEP'))
	define('DIRSEP', DIRECTORY_SEPARATOR);
if (!defined('PATHSEP'))
	define('PATHSEP', PATH_SEPARATOR);

/**
 * Directory shortcuts
**/

// Xnyo's Directory
if (!defined('XNYODIR'))
{
	if (!defined('XNYO_DIR'))
	{
		define('XNYODIR', dirname(__FILE__).DIRSEP);
		define('XNYO_DIR', dirname(__FILE__).DIRSEP);
	} else
		define('XNYODIR', XNYO_DIR);
} elseif (!defined('XNYO_DIR'))
	define('XNYO_DIR', XNYODIR);

// main smarty dir
if (!defined('SMARTY_DIR'))
	define('SMARTY_DIR', XNYODIR.'smarty'.DIRSEP);

// main script dir
if (!defined('SCRIPT_DIR'))
	define('SCRIPT_DIR', dirname($_SERVER['SCRIPT_FILENAME']).DIRSEP);

/**
 * Simplified Error Constants
**/
define('ERROR', E_USER_ERROR);
define('WARNING', E_USER_WARNING);
define('NOTICE', E_USER_NOTICE);
define('CLIENT', 8192);
define('DEBUG', 16384);

/**
 * Our Auth Error responses.
**/
define('XNYO_AUTH_BLANK_USERNAME', 1);
define('XNYO_AUTH_BLANK_PASSWORD', 2);
define('XNYO_AUTH_NO_AUTH_TYPE', 3);
define('XNYO_AUTH_NO_PLUGIN', 4);
define('XNYO_AUTH_INVALID', 5);
define('XNYO_AUTH_UNAUTHORISED', 6);


// Pretty Defines
if (PHP_SAPI == 'cli')
{
	if (!defined('CLI')) define ('CLI', true);
	if (!defined('WEB')) define ('WEB', false);
} else
{
	if (!defined('CLI')) define ('CLI', false);
	if (!defined('WEB')) define ('WEB', true);
}

// set the include path to include XNYODIR
ini_set('include_path', ini_get('include_path').PATHSEP.XNYODIR.PATHSEP.SCRIPT_DIR);

// start session now to avoid it bitching about the cache firing before it does
if (WEB) session_start();

// start the class
class Xnyo
{
	/*******************************************************************
	* Begin Variable Configuration
	*
	* You should never change any of these defaults.
	* When creating your script set them there, like so:
	*	$xnyo = new Xnyo;
	*	$xnyo->var = 'value';
	*******************************************************************/

	/**
	 * Handlers
	 *
	 * These are the handlers for the config/cache/error systems.
	 * They need to be the name of a plugin in <plugin_dir>/<type>/.
	 *
	 * Inbuilt Configuration Handlers:
	 *	file		=> File. (var		value) pairs
	 *	xml		=> XML. Loaded up as a SimpleXML object.
	 *
	 * Inbuilt Cache Handlers:
	 *	file		=> File. (stored on the local filesystem)
	 *	database	=> SQL Database (your SQL db of choice)
	 *
	 * Inbuilt Error Handlers:
	 *	xnyo		=> Xnyo. (handles debug/storing info locally)
	**/
	
	// Configuration File Handler
	public $config_handler = 'xml';

	// Cache File Handler
	public $cache_handler = 'file';
	
	// Error Handler
	public $error_handler = 'xnyo';


	/**
	 * Construction/Destruction Functions
	 *
	 * Like PHP's constructors and destructors, Xnyo also has its own
	 * controls built in to allow functions to be called at opportune
	 * moments. The construction function is called at the end of
	 * the start() function, the destructor at the start of the output
	 * buffer handler.
	**/
	
	// constructor
	public $constructor;
	
	// destructor
	public $destructor;
	

	/**
	 * Load Controls
	 *
	 * Xnyo has many things built in that load automagically. But what if
	 * you don't need all that functionality? Simple, set these to false in
	 * your script and that feature won't load.
	**/

	// Database Abstraction Layer
	public $load_database = true;

	// Session Control
	public $load_session = true;

	// Language
	public $set_language = true;

	// HTML Cache
	public $load_cache = true;

	// Input Parsing Functions
	public $load_input = true;

	// Variable Filter
	public $load_filter = true;
	
	// SQL Generation Classes (not loaded by default, but the database plugin will load
	// this if its needed)
	public $load_sql = false;

	// The Smarty Template Engine
	public $load_smarty = true;
	
	// Error Handling/Debug
	public $load_debug = true;


	/**
	 * Database Abstraction Layer
	 *
	 * Xnyo's Database Abstraction Layer provides a simple set of methods for accessing
	 * many different database types. Supported types include:
	 *	pgsql		=> PostgreSQL
	 *
	 * Planned Types:
	 *	sqlite		=> SQLite
	 *	mysql		=> MySQL
	 *	oracle		=> Oracle
	 *	mssql		=> MSSQL
	 *	msql		=> mSQL
	 *	sybase		=> Sybase
	**/

	// database type, set to one of the above options
	public $database_type = 'mysql';

	// database host, in most cases leave this blank for localhost/unix sockets
	public $db_host;

	// port, it will default to the normal for whichever database type you've picked
	public $db_port;

	// username for connecting to the database, if required
	public $db_user;

	// password, same as above.
	public $db_passwd;

	// persistent database connections, can be good in production, are death in development though
	// off by default
	public $use_persistent_db_conns = false;
	
	// whether to default to the <db>_insert()/<db>_update()/<db>_delete()/<db>_select() functions or gen
	// the sql and do it ourselves.
	public $use_db_insert = true;
	public $use_db_update = true;
	public $use_db_delete = true;
	public $use_db_select = true;

	/**
	 * Session/Authentication Controls
	 *
	 * Xnyo's inbuilt session/authentication controls allow for auto-management of any
	 * visitors. Simply load up the auth plugin and call $auth->login(username, password)
	 * it will take care of the rest.
	 *
	 * Current authention modules:
	 *	activedirectory	=> Active Directory (not used for a long while, needs testing)
	 *	sql		=> SQL Database (any SQL db. See plugins/dbspec/auth.php for details)
	**/

	// this is how long a logged in session may be idle before being kicked. for websites
	// the default is usually fine (its 1 day), for web applications you might want to drop it
	public $session_lifetime = 86400;

	// authentication modules!
	public $auth_type = 'sql';

	// extra parameters to pass to the authentication plugin
	public $auth_params = array();

	// when a user gets logged out, where do they go? Leave this blank for no where,
	// or set to a URL (relative or whatever) and they'll be redirected there.
	public $logout_redirect_url;

	// access level required to view this page (provided for backwards compat.)
	// use $access->set_acl() now
	public $access;


	/**
	 * Langauge
	 *
	 * Xnyo has the ability to determine what languages the incoming user (and overriding
	 * this via ?lang=bleh obviously), as well as requiring languages like they were an
	 * access control list. See the manual for detailed examples, but
	 * lists of valid languages are stored here. It will also store the results of
	 * these searches so you can use them to localize your work, by say mangling the smarty
	 * template dir to show based on language, etc.
	**/

	// valid languages, see the manual for help with this one
	public $languages;
	
	// default language
	public $default_language = 'en';

	// and where our language lives - this is set automagically if $xnyo->set_language == true
	public $language;

	/**
	 * HTML Cache
	 *
	 * Xnyo's inbuilt cache lets you store the contents of your generated HTML pages for set
	 * periods of time (ie a page that only recreates itself once a day). It also allows you
	 * to generate static html files to be stored somewhere else.
	**/

	// turn on the cache? defaults to off, should be turned on for production (and final testing!)
	public $cache = false;

	// date/time (as unix timestamp) that cache file should expire
	public $cache_expire;
	
	// Length of time a cache file should remain inaccessed before its expired
	public $cache_idle_time;

	// How many seconds a cache file should be valid for, ignored if $cache_expire != NULL
	public $cache_lifetime = 86400;

	// where to store cached data, depends on the cache handler, could be a directory, or a db table, etc
	public $cache_location = 'cache';

	// generator! if you're using the file cache handler and this is set, Xnyo will
	// attempt to dump the contents of the cache to this file, in effect generating
	// a static html page, or image, or whatever
	public $cache_filename;


	/**
	 * Variable Filer
	 *
	 * If you've loaded this, and enable the auto-filter below, xnyo will remove
	 * all variables from $_GET, $_POST, and $_COOKIE. You can populate these arrays
	 * below with name/type pairs beforehand, or call the filter functions to get them back
	 * after they've been security filtered.
	 * ie:	$xnyo->filter_get_var('id', 'int'); will force $_GET['id'] to be an integer.
	**/

	// automagically filter vars?
	public $filter_vars = true;

	// same as the php's register_global directive, except these get filtered.
	public $global_vars = false;

	// populate these prior to calling $xnyo->start()
	// note this is the old behaviour, use of $xnyo->filter_get_vars() at any time
	// is considered to be more convenient, these are kept for backwards compat.
	public $get_vars = array();
	public $post_vars = array();
	public $cookie_vars = array();

	// this will mirror the get/post/cookie arrays with series of true/false
	// if the input has been modified by the filter at all, its set to true
	public $input_modified = array();


	/**
	 * Smarty Template Engine
	 *
	 * We firmly believe in the usage of a template engine, so we include the best, Smarty.
	 * If you've allowed it to load, these control *where* it loads, and auto configures
	 * its controls and plants a special {$xnyo} variable.
	 *
	 * http://smarty.php.net/
	**/
	
	// what to call the smarty object. ie 'smarty' means it will load under $smarty;
	public $smarty_obj = 'smarty';

	// the {$xnyo} special var. contents under this array will be accessable in the script
	// suggestion: reference them :)
	public $smarty_auto_assign = array();
	
	// Smarty configuration options..
	// See the smarty manual http://smarty.php.net/manual/en/
	public $smarty_config = array ();
	
	// do we need to check for smarty version changes? if set to true, Xnyo will clean the compiled
	// templates and cache files if the smarty version is changed. This needs to be done when smarty
	// is upgraded, but you dont need to this on unless you change often.
	public $smarty_check_version = false;

	/**
	 * Debug Console
	 *
	 * Xnyo's Debug console, when allowed, means you can append ?debug=true to get a debug output
	 * of everything xnyo (or your scripts) are doing. Also, appending ?debug=notices will display
	 * all PHP notices, ?debug=warnings does PHP Warnings, you get the idea.
	**/

	// whether to dump debug information. This is set to false normally, you can set it manually, or
	// allow xnyo to do it for you with ?debug
	public $debug = false;
	
	// whether to allow xnyo to auto-turn on debug with the ?debug=true variable.
	// this is set to off by default for security reasons, leave it off unless you know you're using
	// it
	public $allow_debug = false;

	// the template to display the debug console
	public $debug_console_template = 'plugins/error/debug.tpl';
	
	// the GET/POST/COOKIE variable to fire off the debug console (if we're allowed)
	// eg: some_page.php?xnyo_debug=true
	public $debug_var = 'xnyo_debug';
	
	// log debug info to file? you'll still need to call the page with the debug log params
	public $debug_log = false;


	/**
	 * Form Generation
	**/
	
	// form locations
	public $form_dir = 'forms';

	// form template locations
	public $form_templates = array
	(
		'header' => 'forms/header.tpl',
		'section-header' => 'forms/section-header.tpl',
		'section-footer' => 'forms/section-footer.tpl',
		'item-header' => 'forms/item-header.tpl',
		'footer' => 'forms/footer.tpl'
	);	

	/**
	 * Plugin Directories
	 *
	 * This is where Xnyo will look for plugins, see the manual for detailed information.
	**/
	public $plugin_dirs = array ('plugins');


	/**
	 * Whitespace Trimmer
	 *
	 * This will trim the unnecessary whitespaces at the beginning and ends of linse in order
	 * to compress the filesize and help it load quicker. No newline characters are removed.
	**/
	public $trim_html = true;

	/**
	 * Logged in user information
	 *
	 * Shortcut to the logged in user information, if any.
	**/
	public $user;

	/*******************************************************************
	* End Variable Configuration
	*******************************************************************/


	/*******************************************************************
	* Begin Internal Variables
	*******************************************************************/
	
	// already loaded plugins
	private $_loaded_plugins = array();

	// object storage!
	public $sql;
	public $cache_plugin;
	public $filter_plugin;
	public $error_plugin;
	
	// version
	public $version = '3.0-dev';

	/*******************************************************************
	* End Internal Variables
	*******************************************************************/

	/**
	 * Constructor
	 *
	 * Sets the internal "always there" variable for xnyo plugins' use
	**/
	public function __construct ()
	{
		// set global $xnyo_parent variable
		$GLOBALS['xnyo_parent'] =& $this;
	}


	/**
	 * Xnyo Start Function
	 *
	 * Initialise Xnyo! Run! Go Baby Go!
	**/
	public function start ()
	{
		// load error handling
		if ($this->load_debug)
			$this->load_debug();
		elseif (!defined('XNYO_DEBUG'))
			define('XNYO_DEBUG', false);

		// reset our Content-Type if we're on teh web
		if (WEB)
		{
			if (XNYO_DEBUG) $this->debug('Forcing Content-Type header to UTF-8');
			header('Content-Type: text/html; charset=UTF-8');
		}

		// set language! this is needed for the cache
		if ($this->set_language)
			$this->set_language();
		
		// loading cache
		if ($this->load_cache)
			$this->load_cache();

		// start the output buffer (no caching for CLI)
		if (WEB)
		{
			if (XNYO_DEBUG) $this->debug('Starting the output buffer.');
			ob_start(array(&$this, 'output_buffer_handler'));
		}

		// load database
		if ($this->load_database)
		{
			$this->load_database();
			}

		// and input
		if ($this->load_input)
			$this->load_plugin('input');

		// and access!
		if ($this->load_session)
			$this->load_session();

		// and filter!
		if ($this->load_filter)
			$this->load_filter();
			
		// SQL plugin
		if ($this->load_sql)
			$this->load_sql();

		// load smarty
		if (WEB && $this->load_smarty)
			$this->load_smarty();
		
		// run the construction functions, if we have specified one
		if (!empty($this->constructor))
		{
			if (array($this->constructor) && method_exists($this->constructor[0], $this->constructor[1]))
			{
				if (XNYO_DEBUG) $this->debug('Running constructor function $object->'.$this->constructor[1]);
				$func = $this->constructor[1];
				$this->constructor[0]->$func();
			} elseif (array($this->constructor) && class_exists($this->constructor[0]) && function_exists($this->constructor[0].'::'.$this->constructor[1]))
			{
				if (XNYO_DEBUG) $this->debug('Running constructor function '.$this->constructor[0].'::'.$this->construtor[1]);
				$func = $this->constructor[0].'::'.$this->constructor[1];
				$func();
			} elseif (function_exists($this->constructor))
			{
				if (XNYO_DEBUG) $this->debug('Running constructor function '.$this->constructor);
				$func = $this->constructor;
				$func();
			}
		}

	}


	/**
	 * Load Database
	 *
	 * Load up the database and pass over any config items
	**/
	public function load_database ()
	{
		$this->load_plugin($this->database_type, 'database');
		}

	/**
	 * Load Session
	 *
	 * Load up the session controls/authentication system.
	**/
	public function load_session ()
	{
		global $access;
		$this->load_plugin('access');
		
		// repin session information locally :)
		$this->user =& $_SESSION['auth'];

		// do auth type checking (not for CLI!@#!)
		if (WEB && !$access->sess_check())
		{
			header("Location: ".$this->logout_redirect_url);
			exit;
		}
	}


	/**
	 * Load Cache
	 *
	 * Load the cache related gizmos and such
	**/	
	public function load_cache ()
	{
		$this->cache_plugin = $this->load_plugin('cache', 'class', true);

		// fetched cache'd copy if we have one
		if ($this->cache && WEB)
			if ($this->cache_plugin->_fetch_cache() !== false)
				exit;

		if (XNYO_DEBUG) $this->debug('Not using cached copy.');
	}


	/**
	 * Load Filter
	 *
	 * If you're letting it, this will load all necessary methods for filtering your input
	**/
	public function load_filter ()
	{
		// load the filter
		$this->filter_plugin = $this->load_plugin('filter', 'class', true);

		// Parse our variables
		if (WEB && $this->filter_vars)
			$this->filter_plugin->init();
	}
	
	
	/**
	 * Load SQL
	 *
	 * Load the SQL Generation Plugin
	**/
	public function load_sql ()
	{
		$this->sql = $this->load_plugin('sql', 'class', true);
		if (is_object($this->sql))
			return true;
		return false;
	}


	/**
	 * Load Smarty
	 *
	 * Load the Smarty Template Engine
	**/
	public function load_smarty ()
	{
		if (XNYO_DEBUG) $this->debug('Loading Smarty.');
		include_once SMARTY_DIR.'Smarty.class.php';
		$GLOBALS[$this->smarty_obj] = new Smarty;
		if (XNYO_DEBUG) $this->debug('Smarty Loaded.');

		// default path for debug template
		$GLOBALS[$this->smarty_obj]->debug_tpl = 'file:'.SMARTY_DIR.'templates'.DIRSEP.'debug.tpl';

		// load our smarty configuration over it's..
		if (is_array($this->smarty_config) && count($this->smarty_config) > 0)
			foreach ($this->smarty_config as $key => $var)
			{
				if (XNYO_DEBUG) $this->debug('Replacing Smarty Variable: '.$key);
				$GLOBALS[$this->smarty_obj]->$key = $var;
			}

		// set plugin directories for smarty
		if (XNYO_DEBUG) $this->debug('Translating plugin dirs for smarty.');
		foreach ($this->plugin_dirs as $dir)
			$smarty_dirs[] = $dir.DIRSEP.'smarty';
		$GLOBALS[$this->smarty_obj]->plugins_dir = $smarty_dirs;

		if (XNYO_DEBUG) $this->debug('Assigning magic {$xnyo} variable.');
		if (count($this->smarty_auto_assign))
			$GLOBALS[$this->smarty_obj]->assign_by_ref('xnyo', $this->smarty_auto_assign);

		// clear out the plugin dirs if we've changed versions
		if ($this->smarty_version_check)
		{
			$file = $GLOBALS[$this->smarty_obj]->compile_dir.DIRSEP.'.version-'.$GLOBALS[$this->smarty_obj]->_version.'.tpl';
			if (!file_exists($file))
			{
				$GLOBALS[$this->smarty_obj]->clear_all_cache();
				$GLOBALS[$this->smarty_obj]->clear_compiled_tpl();
			}
			touch($file);
		}
	}


	/**
	 * Load Debug
	 *
	 * Load Debug related controls and the console
	**/
	public function load_debug ()
	{
		// load up the error handler, its always used
		if (empty($this->error_handler) || !$this->load_plugin($this->error_handler, 'error'))
			return false;

		// before we parse all that stuff! turn on debugging if so asked
		$this->error_plugin->setup_error_cache();
		if ($this->allow_debug)
		{
			if (WEB && !empty($_REQUEST[$this->debug_var]))
			{
				if ($_REQUEST[$this->debug_var] === 'console')
				{
					// reload the constructor
					$this->constructor = array(&$this, '_open_debug_console');

					// no caching for the console!
					$this->cache = false;
				} else
					$this->debug = true;
				$this->debug('Turning on debugging output.');
			} elseif (CLI && in_array('--'.$this->debug_var.'_log', (array)$_SERVER['argv']))
			{
				$this->debug = true;
				$this->debug_log = true;
				unset($_SERVER['argv'][array_search('--'.$this->debug_var.'_log', (array)$_SERVER['argv'])]);
			} elseif (CLI && in_array('--'.$this->debug_var.'_output', (array)$_SERVER['argv']))
			{
				$this->debug = true;
				$this->debug_output = true;
				unset($_SERVER['argv'][array_search('--'.$this->debug_var.'_output', (array)$_SERVER['argv'])]);
			}

		}

		// set the constant
		if (!defined('XNYO_DEBUG'))
			define('XNYO_DEBUG', $this->debug);

		// so we need to load up a new error handler eh?
		if (!empty($this->error_handler))
			//set_error_handler('_php_to_xnyo_error_parser', ini_get('error_reporting'));
			set_error_handler(array(&$this, '_php_to_xnyo_error_parser'), E_ALL);
	}

	/**
	 * Set Language
	 *
	 * Determine the language preferences of the user.
	**/
	public function set_language ()
	{
		// repoint a few things so they work nicely
		if (!isset($_SESSION['language']))
			$_SESSION['language'] = '';
		$this->language = &$_SESSION['language'];

		// if theres no languages specified, dont bother
		if (!is_array($this->languages) || !count($this->languages))
		{
			if (empty($this->language))
			{
				if (XNYO_DEBUG) $this->debug('No languages configured, setting to default.');
				$this->language = $this->default_language;
			}
			return true;
		}

		// check the request variables for a language variable
		if (isset($_REQUEST['lang']) && (isset($this->languages[$_REQUEST['lang']]) || in_array($_REQUEST['lang'], $this->languages)))
		{
			if (XNYO_DEBUG) $this->debug('Found a language in $_REQUEST, setting language to <i>'.$_REQUEST['lang'].'</i>.');
			$this->language = $_REQUEST['lang'];
		} elseif (empty($this->language) && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $key => $var)
			{
				$var = ltrim(rtrim($var));
				if (strpos($var, ';') !== false)
					$var = substr($var, 0, strpos($var, ';'));

				if (isset($this->languages[$var]) || in_array($var, $this->languages))
				{
					if (XNYO_DEBUG) $this->trigger_error('Matched a browser-specified language to configured languages, setting language to <i>'.$var.'</i>.');
					$this->language = $var;
					return true;
				}
			}
		} elseif (empty($this->language))
		{
			// could not find a language
			if (XNYO_DEBUG) $this->debug('No language found from tried methods, setting to default.');
			$this->language = $this->default_language;
		}
	}


	/**
	 * Load Plugin
	 *
	 * Load a Plugin of any type.
	**/
	public function load_plugin($plugin, $type=NULL, $return=FALSE)
	{

		// set default type
		if (is_null($type))
			$type = "class";

		// do we have this plugin in the return list?
		if (isset($this->_return_plugins[$type][$plugin]))
			return $this->_return_plugins[$type][$plugin];

		// return true if we've already loaded this plugin
		if (isset($this->_loaded_plugins[$type][$plugin]) && !empty($this->_loaded_plugins[$type][$plugin]))
			return true;

		// no plugin name?
		if (empty($plugin))
		{
			$this->trigger_error("Empty plugin name specified.", NOTICE);
			return false;
		}

		if ($this->include_plugin($plugin, $type))
		{
			// our action variables
			$global = FALSE;

			//if (XNYO_DEBUG)
				//$this->debug('Loading up <i>'.$plugin.'</i> plugin (type: '.$type.')');

			// start the magic
			switch ($type)
			{
				case 'class':
					$class = $plugin."_plugin";
					$objname = strtolower($plugin);
					$global = TRUE;
					break;
				case 'database':
					$class = "db";
					$objname = "db";
					$global = TRUE;
					break;
				case 'auth':
					$class = "auth_".$plugin."_plugin";
					$objname = "_auth_".strtolower($plugin)."_handler";
					break;
				case 'config':
					$class = "config_".$plugin."_plugin";
					$objname = "_config_".strtolower($plugin)."_handler";
					break;
				case 'cache':
					$class = "cache_".$plugin."_plugin";
					$objname = "_cache_".strtolower($plugin)."_handler";
					break;
				case 'error':
					$class = "error_".$plugin."_plugin";
					$objname = "error_plugin";
					break;
				case 'dbspec':
					$class = "dbspec_".$plugin;
					$return = TRUE;
					break;
				case 'function':
					$this->_loaded_plugins[$type][$plugin] = $plugin;
					return true;
				default:
					$this->trigger_error("Invalid plugin type: $type (plugin: $plugin)", WARNING);
					return false;
			}

			// check
			if (!class_exists($class))
			{
				// try for a class named $plugin?
				if (!class_exists($plugin))
				{
					$this->trigger_error('Invalid plugin: '.$plugin.' ('.$type.'), class '.$class.' not found', WARNING);
					return false;
				} else
					$class = $plugin;
			}

			// loaded successfully
			$this->_loaded_plugins[$type][$plugin] = $class;

			// Make the object i guess
			if ($return)
			{
				if (XNYO_DEBUG) $this->debug('Returning plugin <i>'.$plugin.'</i>');
				$this->_return_plugins[$type][$plugin] = new $class;				
				return $this->_return_plugins[$type][$plugin];
			} elseif ($global)
			{
				if (XNYO_DEBUG) $this->debug('Moving plugin <i>'.$plugin.'</i> to the global variable $'.$objname);
				$GLOBALS[$objname] = new $class;				
			} else
			{
				//if (XNYO_DEBUG) $this->debug('Setting plugin <i>'.$plugin.'</i> to $xnyo->'.$objname);
				$this->$objname = new $class;
			}			
			// finished teh juarez
			return true;
		}

		return false;
	}
	
	/**
	 * Load Plugin
	 *
	 * Load a Plugin of any type.
	**/
	public function include_plugin($plugin, $type=NULL)
	{

		// set default type
		if (is_null($type))
			$type = 'class';

		// have we already included this plugin?
		if (isset($this->_included_plugins[$type][$plugin]) && !empty($this->_included_plugins[$type][$plugin]))
			return true;

		// no plugin name?
		if (empty($plugin))
		{
			$this->trigger_error('Empty plugin name specified.', NOTICE);
			return false;
		}

		// check for the plugin file
		foreach ($this->plugin_dirs as $dir)
		{

			// check the path
			$dir = preg_replace('/\.\./', '', $dir.DIRSEP.$type);

			// clean up nicely
			$dir = preg_replace('/\/\//', '', $dir).DIRSEP;

			// try it with XNYODIR prepended if its not in teh include_path..
			if (!file_exists($dir.$plugin.'.php') && file_exists(XNYODIR.$dir.$plugin.'.php'))
				$dir = XNYODIR.$dir;

			// load the plugin if we have it
			if (file_exists($dir.$plugin.'.php'))
			{
				include_once($dir.$plugin.'.php');
				$this->_included_plugins[$type][$plugin] = true;
				return true;
			}
		}

		// could not be found..
		$this->trigger_error('Unable to find plugin: $plugin ($type)', WARNING);
		return false;
	}

	/**
	 * Fetch Loaded Plugins
	 *
	 * Return a list of all the plugins loaded..
	**/
	public function fetch_loaded_plugins ()
	{
		return $this->_loaded_plugins;
	}

	
	/**
	 * Parse Config
	 *
	 * Parse a configuration file using the specified config handler.
	**/
	public function parse_config ($var=NULL)
	{

		if (is_null($var))
		{
			$this->trigger_error('Failed to parse_config for NULL config variable', NOTICE);
			return false;
		}
		
		// no handler? we're screwed!
		if (empty($this->config_handler))
		{
			$this->trigger_error('Failed to parse_config for '.$var.' because there is no configuration handler specified', NOTICE);
			return false;
		}
		
		// load it first, to be sure
		if (!$this->load_plugin($this->config_handler, 'config'))
		{
			$this->trigger_error('Unable to load configuration handler '.$this->config_handler, NOTICE);
			return false;
		}

		// do the warez
		$obj_name = "_config_".$this->config_handler."_handler";

		// check to make sure the config handler is valid
		if (!is_object($this->$obj_name))
		{
			$this->trigger_error('Invalid configuration handler '.$this->config_handler.'. Unable to access object $this->'.$obj_name.'->parse', NOTICE);
			return false;
		}
		
		$config = $this->$obj_name->parse($var);
		
		if (XNYO_DEBUG) $this->debug('Processed configuration <i>'.$var.'</i>');

		return $config;

	}


	/**
	 * Filter Wrappers
	 *
	 * The following three functions are filter wrappers to the filter plugin
	**/
	public function filter_get_var ($name, $type=NULL)
	{
		return $this->filter_plugin->filter_get_var($name, $type);
	}
	public function filter_post_var($name, $type=NULL)
	{
		return $this->filter_plugin->filter_post_var($name, $type);
	}
	public function filter_cookie_var($name, $type=NULL)
	{
		return $this->filter_plugin->filter_cookie_var($name, $type);
	}
	
	/**
	 * Pavid Server.
	 *
	 * Checks for an incoming Pavid function call and acts appropriately.
	**/
	public function pavid ()
	{
		global $pavid_server;

		$this->load_plugin('pavid_server');
		return $pavid_server->process();
	}

	
	/**
	 * Trigger Error
	 *
	 * Trigger an error, be it PHP based, Client or Debug
	**/
	public function trigger_error ($errmsg, $type=WARNING, $extra_data=NULL)
	{
		// performance hack.
		if ($type == DEBUG && !XNYO_DEBUG)
			return true;
		
		// easy enough
		// no error handler? dont bother
		if (!is_object($this->error_plugin))
			return false;

		// load the plugin
		// run the handler function
		$this->error_plugin->error($errmsg, $type, $extra_data);
		
		// done
		return true;
	}
	
	/**
	 * Debug
	 *
	 * Archive debug message.
	**/
	public function debug ($errmsg, $extra_data=NULL)
	{
		// performance hack.
		//if (!XNYO_DEBUG)
			//return true;
		
		// easy enough
		// no error handler? dont bother
		if (!is_object($this->error_plugin))
			return false;

		// load the plugin
		// run the handler function
		$this->error_plugin->error($errmsg, DEBUG, $extra_data);
		
		// done
		return true;
	}
	
	/**
	 * Debug Console
	 *
	 * Load the debug console and let it go pretty.
	**/
	public function debug_console ()
	{
		// we've been asked to load the console! guess we should..
		if (!is_object($this->error_plugin))
			return false;

		// load load
		if (!is_object($this->error_plugin->debug_console))
			$this->error_plugin->debug_console = $this->load_plugin('debug', 'error', true);
		
		// and display!
		return $this->error_plugin->debug_console->display();
	}
	

	/**
	 * Dump Errors
	 *
	 * Dump errors of the specified level and tag. Mostly a wrapper for the error plugin
	**/
	public function dump_errors($level=CLIENT, $tag=NULl)
	{
		if (!is_object($this->error_plugin))
			return false;
		return $this->error_plugin->dump_errors($level, $tag);
	}


	/**
	 * Output Buffer Handler
	 *
	 * This is the output buffer callback function used above to feed the cache system +
	 * allow the debug console and destructor functions to work.
	**/
	public function output_buffer_handler (&$buffer)
	{
		global $input;

		// destruction functions
		if (!empty($this->destructor) && function_exists($this->destructor))
		{
			$func = $this->destructor;
	
			// call the destructor with the output as the argument
			$func($buffer);
		}
		
		// wtf? dont know how this can error, but i guess if the input plugin is not loaded, load it
		if (!is_object($input))
			$this->load_plugin('input');
	
		if ($this->trim_html && is_object($input))
			$buffer = $input->trimwhitespace($buffer);
	
		// are we debugging? if so we can dump the existing data and do that
		if (XNYO_DEBUG)
		{
			// open the debug console
			$buffer .= $this->debug_console();
		}
	
		// output a content-length header, we never modify the data, so do it
		// as long as zlib.output_compression isnt on..
		if (!ini_get('zlib.output_compression'))
			header('Content-Length: '.strlen($buffer));

		// are we dumping debug?
		if ($this->debug)
			return $buffer;
			
		// doing cache stuff?
		if ($this->cache && $this->load_cache)
			return $this->cache_plugin->output_buffer_handler($buffer);
			
		// bugger eh?
		return $buffer;
	}


	/**
	 * PHP to Xnyo Error Parser
	 *
	 * This function gets set as the PHP error handler, so all PHP errors will come through here.
	 * It's mearly a wrapper for the Xnyo Error Handler.
	**/
	public function _php_to_xnyo_error_parser ($level, $msg, $file=NULL, $line=NULL)
	{
		// convert it over!
		xnyo::trigger_error($msg, $level, array('file' => $file, 'line' => $line));
		return true;
	}
	
	
	/**
	 * Pop open the display console!
	**/
	static function _open_debug_console ()
	{
		global $xnyo_parent;

		// we've been asked to load the console! guess we should..
		if (!is_object($xnyo_parent->error_plugin))
			return false;

		// load load
		if (!is_object($xnyo_parent->error_plugin->debug_console))
			$xnyo_parent->error_plugin->debug_console = $xnyo_parent->load_plugin('debug', 'error', true);
		
		// and display!
		$xnyo_parent->error_plugin->debug_console->output_console();
		exit;
	}

	/**
	 * Get Microtime
	 *
	 * This function will return the current time as seconds.milliseconds
	**/
	public function _getmicrotime()
	{
		return array_sum(explode(' ', microtime()));
	}
	

} // end the class

?>
