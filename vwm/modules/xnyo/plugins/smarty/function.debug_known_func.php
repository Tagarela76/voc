<?php

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/smarty/function.debug_known_func.php
 *              Convert a class::function to a name, if known
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: function.debug_known_func.php 17 2004-08-12 05:58:01Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/smarty/function.debug_known_func.php $
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

function smarty_function_debug_known_func($params, &$smarty)
{
	$known = array
	(
		/**
		 * Note that all these will move to the documentation side of things
		 * whenever that is written and will be loaded from there
		**/
		// Xnyo Methods
		'xnyo::load_plugin'	=> 'Xnyo: Load Plugin',
		'xnyo::start'		=> 'Xnyo: Start',
		'xnyo::load_smarty'	=> 'Xnyo: Load Smarty',
		'xnyo::load_database'	=> 'Xnyo: Load Database',
		'xnyo::load_session'	=> 'Xnyo: Load Session',
		'xnyo::load_cache'	=> 'Xnyo: Load Cache',
		'xnyo::load_filter'	=> 'Xnyo: Load Filter',
		'xnyo::load_sql'	=> 'Xnyo: Load SQL',
		'xnyo::load_debug'	=> 'Xnyo: Load Debug',
		'xnyo::parse_config'	=> 'Xnyo: Parse Config',
		'xnyo::trigger_error'	=> 'Xnyo: Trigger Error',
		'xnyo::debug_console'	=> 'Xnyo: Debug Console',
		'xnyo::dump_errors'	=> 'Xnyo: Dump Errors',
		'xnyo::output_buffer_handler' => 'Xnyo: OB Handler',
		'xnyo::_php_to_xnyo_error_parser' => 'Xnyo: PHP2Xnyo EP',

		// Access!
		'access_plugin::sess_check'	=> 'Access: Session Check',
		'access_plugin::logout'		=> 'Acesss: Logout',
		'access_plugin::check'		=> 'Access: Check',
		'access_plugin::location'	=> 'Acesss: Set Location',
		'access_plugin::subnet'		=> 'Access: Subnet',
		'access_plugin::user'		=> 'Access: Get Username',
		'access_plugin::set_lcl'	=> 'Access: Set LCL',
		'access_plugin::set_acl'	=> 'Access: Set ACL',

		// Authentication!
		'auth_plugin::login'	=> 'Auth: Login',

		// Cache
		'cache_plugin::_fetch_cache' => 'Cache: Fetch',
		'cache_plugin::output_buffer_handler'	=> 'Cache: OB Handler',

		// Filter Methods
		'filter_plugin::init'	=> 'Filter: Init',
		'filter_plugin::filter_get_var'	=> 'Filter: Get Var',
		'filter_plugin::filter_post_var' => 'Filter: Post Var',
		'filter_plugin::filter_cookie_var' => 'Filter: Cookie Var',
		'filter_plugin::_filter_var'	=> 'Filter: Filter Var',
		'filter_plugin::fetch'	=> 'Filter: Fetch Raw Vars',

		// Input Plugin
		'input_plugin::int'	=> 'Input: Integer',
		'input_plugin::number'	=> 'Input: Number String',
		'input_plugin::float'	=> 'Input: Float',
		'input_plugin::double'	=> 'Input: Float',
		'input_plugin::text'	=> 'Input: Text',
		'input_plugin::string'	=> 'Input: String',
		'input_plugin::safetext'	=> 'Input: Safe Text',
		'input_plugin::filename'	=> 'Input: Filename',
		'input_plugin::bool'	=> 'Input: Boolean',
		'input_plugin::boolean'	=> 'Input: Boolean',
		'input_plugin::username'	=> 'Input: Username',
		'input_plugin::password'	=> 'Input: Password',
		'input_plugin::email'	=> 'Input: Email',
		'input_plugin::shell'	=> 'Input: Shell',
		'input_plugin::alphanum'	=> 'Input: AlphaNumeric',
		'input_plugin::hex'	=> 'Input: Hex',
		'input_plugin::null'	=> 'Input: NULL',
		'input_plugin::_array'	=> 'Input: Array',
		'input_plugin::date'	=> 'Input: Date',
		'input_plugin::date_year'	=> 'Input: Year',
		'input_plugin::date_month'	=> 'Input: Month',
		'input_plugin::date_day'	=> 'Input: Day',
		'input_plugin::sqltext'	=> 'Input: SQL Text',
		'input_plugin::sqlbinary'	=> 'Input: SQL Binary',
		'input_plugin::unsqlbinary'	=> 'Input: unSQL Binary',
		'input_plugin::htmlsafe'	=> 'Input: HTML Safe',
		'input_plugin::htmlnlsafe'	=> 'Input: HTML \n Safe',
		'input_plugin::unhtmlsafe'	=> 'Input: unHTML Safe',
		'input_plugin::unhtmlnlsafe'	=> 'Input: unHTML \n Safe',
		'input_plugin::trimwhitespace'	=> 'Input: Trim Whitespace',

		// SQL Generation
		'sql_plugin::insert'	=> 'SQL: Insert',
		'sql_plugin::delete'	=> 'SQL: Delete',
		'sql_plugin::select'	=> 'SQL: Select',
		'sql_plugin::update'	=> 'SQL: Update',

		// DB Methods
		'db::_connect'	=> 'DB: Connect',
		'db::select_db'	=> 'DB: Select DB',
		'db::exec'	=> 'DB: Exec',
		'db::query'	=> 'DB: Exec',
		'db::fetch'	=> 'DB: Fetch',
		'db::fetch_all'	=> 'DB: Fetch All',
		'db::fetch_all_objects'	=> 'DB: Fetch All Objects',
		'db::fetch_all_array'	=> 'DB: Fetch All Array',
		'db::fetch_object'	=> 'DB: Fetch Object',
		'db::fetch_array'	=> 'DB: Fetch Array',
		'db::fetch_column'	=> 'DB: Fetch Column',
		'db::fetch_result'	=> 'DB: Fetch Result',
		'db::num_rows'	=> 'DB: Num Rows',
		'db::numrows'	=> 'DB: Num Rows',
		'db::affected_rows'	=> 'DB: Affected Rows',
		'db::get_database_name'	=> 'DB: Get DB Name',
		'db::copy'	=> 'DB: Copy',
		'db::insert'	=> 'DB: Insert',
		'db::update'	=> 'DB: Update',
		'db::delete'	=> 'DB: Delete',
		'db::select'	=> 'DB: Select',
		'db::spec'	=> 'DB: Load Spec',
		'db::go_back'	=> 'DB: Go Back',
		'db::sqltext'	=> 'DB: SQL Text',
		'db::sqlbinary'	=> 'DB: SQL Binary',
		'db::unsqlbinary'	=> 'DB: unSQL Binary',
		'db::get_error'	=> 'DB: Get Error',

	);

	if (!empty($params['class']))
		$params['func'] = $params['class'].'::'.$params['func'];
	$func = strtolower($params['func']);
	
	if (!empty($known[$func]))
		return '<td style="font-weight: bold;">'.$known[$func].'</td>';

	return '<td>'.$func.'</td>';
}

/* vim: set expandtab: */

?>
