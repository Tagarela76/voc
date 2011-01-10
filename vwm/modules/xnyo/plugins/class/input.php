<?PHP

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/class/input.php
 *              Input Checking Functions
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: input.php 17 2004-08-12 05:58:01Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/class/input.php $
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

class Input_Plugin {

	/**
	 * Integers
	**/
	function int ($text) {
		$text = $this->number($text);
		$text = intval($text);
		return $text;
	}
	function number ($text) {
		$text = preg_replace("/[^\-0-9]/", '', $text);
		return $text;
	}

	/**
	 * Floating point numbers
	**/
	function float ($int) {
		$int = preg_replace("/[^\-0-9.e]/", '', $int);
		settype ($int, 'float');
		return $int;
	}
	function double ($int) {
		return $this->float($int);
	}

	/**
	 * Strings
	**/
	function text ($text) {
		// just remove certain control characters
                // atleast someone is on the ball -- vort@st :)
                $text = preg_replace('/[\x00-\x07\x16-\x1F]/', '', $text);
                settype($text, 'string');
		return $text;
	}
	function string ($text) {
		return $this->text($text);
	}

	/**
	 * "Safe" strings (alphabet, numbers, underscores and hyphens)
	**/
	function safetext ($text) {
		$text = preg_replace("/[^a-z0-9_-]/i", "", $text);
		return $text;
	}

	/**
	 * Filenames
	**/
	function filename ($path) {

		 // nor do we allow directory transversals
		 $path = preg_replace("/\.\./", "", $path);
		
		 // clean up nicely
		 $path = preg_replace("/\/\//", "", $path);
		
		 // we dont allow leading slashes
		 $path = preg_replace("/^\//", "", $path);

		 // all done
		 return $path;
	}

	/**
	 * Booleans
	**/
	function bool ($text) {
		// smart checking for booleans
		$text = strtolower($text);
		if ($text === 1 || $text == 'true' || $text === true || $text == 't' || $text == 'yes' || $text == 'y' || $text == 'on')
			return true;

		// nup?
		return false;
	}
	function boolean ($text) {
		// wrapper
		return $this->bool($text);
	}

	/**
	 * Usernames (alphanumeric, underscores, hyphens, and full stops)
	**/
	function username ($text) {
		$text = preg_replace("/[^a-zA-Z0-9_\-.@]/", "", $text);
		return $text;
	}

	/**
	 * Passwords
	**/
	function password ($text) {
		$text = preg_replace("/[^a-zA-Z0-9_\-\`\[\];\'\/\\.,~!@#$%\^&*\(\)=+|\}\{:\"<>?]/", "", $text);
		return $text;
	}

	/**
	 * Email Addresses
	**/
	function email ($text) {
		
		if (!preg_match('/^([^@]+?)\@(.+?)$/', $text, $m))
			return '';

		// if we dont have a getmxrr function, just return the text, because they passed the basic test
		if (!function_exists('getmxrr'))
			return $text;
			
		// ok attempt to lookup the MX record for the given email addresses
		// if none found then the address is invalid isnt it.
		if (getmxrr($m[2], $r) || gethostbyname($m[2]))
			return $text;

		return '';
	}
	
	/**
	 * Shell script safe
	**/
	function shell ($text) {
		$text = $this->text($text);
		$text = escapeshellcmd($text);
		return $text;
	}

	/**
	 * Alphanumberic
	**/
	function alphanum ($text) {
		$text = preg_replace("/[^a-z0-9]/i", "", $text);
		return $text;
	}

	/**
	 * Hex (A-F, 0-9)
	**/
	function hex ($text) {
		$text = preg_replace("/[^a-f0-9]/i", "", $text);
		return $text;
	}

	/**
	 * NULL
	**/
	function null ($text) {
		settype($text, 'null');
	}

	/**
	 * Arrays
	**/
	function _array ($text, $type) {
		if (!is_array($text))
			$text = array($text);

		foreach ($text as $key => $var) {
			if (is_array($type)) {
				$newtype = $type[0];
				$newtext[$key] = $this->_array($var, $newtype);
			} elseif (method_exists($this, $type)) {
				$newtext[$key] = $this->$type($var);
			}
		}

		settype($newtext, 'array');
		return $newtext;
	}

	/**
	 * Dates (any strtotime() parseable date)
	 * Note: this returns a UNIX timestamp
	**/
	function date($date) {
		$time = strtotime($date);
		if ($time !== -1)
			return $time;

		// if its already an int
		$time = $this->int($date);
		if ($time > 0)
			return $time;

		// we dont have anything at all really do we?
		// return _now_
		return time();

	}

	/**
	 * Years (1970 - 2999)
	**/
	function date_year ($year) {
		// go for teh epoch
		$year = $this->int($year);

		// invalid?
		if ($year < 1970 || $year > 2999) 
			return date('Y');

		// valid
		return $year;
	}

	/**
	 * Months (1 - 12)
	**/
	function date_month ($month) {
		// fear int
		$month = $this->int($month);

		// invalid
		if ($month < 1 || $month > 12)
			return date('m');

		// valid
		return $month;
	}

	/**
	 * Days (1 - 13)
	**/
	function date_day ($day) {
		// dude s3kur3
		$day = $this->int($day);

		// invalid
		if ($day < 1 || $day > 31)
			return date('d');

		// valid
		return $day;
	}

	/**
	 * SQL safe text, strip everything we dont need
	**/
	function sqltext ($text) {
		
		global $db;
		// is there a db function for this specified?
		if (method_exists($db, 'sqltext'))
			return db::sqltext($text);

		// quote quotes
		// remove all quoted ones first
		$text = str_replace("\'", "'", $text);
		$text = str_replace("'", "\'", $text);
		return $text;
	}
	
	/**
	 * SQL safe binary, strip everything we dont need
	**/
	function sqlbinary ($text) {
		
		global $db;
		
		// is there a db function for this specified?
		if (method_exists($db, 'sqlbinary'))
			return db_plugin::sqlbinary($text);

		// nothing else we can do
		return $text;
	}
	/**
	 * SQL safe binary, strip everything we dont need (undo!)
	**/
	function unsqlbinary ($text) {
		
		global $db;
		
		// is there a db function for this specified?
		if (method_exists($db, 'unsqlbinary'))
			return db_plugin::unsqlbinary($text);

		// nothing else we can do
		return $text;
	}

	/**
	 * HTML, so that people can't use cross-site scripting
	**/
	function htmlsafe ($text) {
		$text = htmlentities($text, ENT_QUOTES);
		return $text;
	}

	/**
	 * HTML as above + nl2br()
	**/
	function htmlnlsafe ($text) {
		$text = htmlentities($text, ENT_QUOTES);
		$text = preg_replace("/\n/", "<br />\n", $text);
		return $text;
	}

	/**
	 * Undo the htmlsafe() function above
	**/
	function unhtmlsafe ($text) {
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
		return strtr ($text, $trans_tbl);
	}

	/**
	 * Undo the htmlnlsafe() function above
	**/
	function unhtmlnlsafe ($text) {
		$text = preg_replace("/<br \/>/i", '', $text);
		$text = $this->unhtmlsafe();
	}
	
	/**
	 * Trim White Space
	 *
	 * Trim the leading and trailing whitespaces (as long as they're outside certain blocks.. and outside tags
	**/
	function trimwhitespace($text) {
	
		$_blocks_match = "script|pre|style|textarea";
	
		// Pull out the blocks
		preg_match_all("!<($_blocks_match)[^>]+>.*?</($_blocks_match)>!is", $text, $match);
		$_blocks = $match[0];
		$text = preg_replace("!<($_blocks_match)[^>]+>.*?</($_blocks_match)>!is", '@@@XNYO:TRIM@@@', $text);
	
		// clean up all whitespaces from the start of a line to the start of a HTML tag
		$text = preg_replace('/^[\s]+?</m', '<', $text);
	
		// remove multiple lines/empty lines
		$text = preg_replace('/>[\s\t\n]{2,}?</m', ">\n<", $text);
	
		// replace blocks
		foreach($_blocks as $curr_block)
			$text = preg_replace("!@@@XNYO:TRIM@@@!",$curr_block,$text,1);
	
		return $text; 
	}
}


?>
