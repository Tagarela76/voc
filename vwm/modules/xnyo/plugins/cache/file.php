<?PHP

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/cache/file.php
 *              Store cache'd data on the filesystem.
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: file.php 67 2004-10-13 09:17:15Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/cache/file.php $
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

class cache_file_plugin
{
	private $searches;

	/**
	 * Constructor
	 *
	 * Set search dirs
	**/
	public function __construct ()
	{
		$this->searches = array
		(
			'',
			SCRIPT_DIR,
			XNYO_DIR
		);
	}

	/**
	 * Make Seed
	 *
	 * Make a CRC32 checksum of the QUERY_STRING
	**/
	private function make_seed ()
	{

		// make seed
		$seed = abs(crc32($_SERVER['QUERY_STRING']));

		// die.
		return $seed;
	}

	/**
	 * Write
	 *
	 * Write out a cache file
	**/
	function write ($buffer) {

		global $xnyo_parent;

		// well first things first, get the seed we need
		$seed = $this->make_seed();

		/**
		 * make sure the location is a directory
		**/
		$dir = $xnyo_parent->cache_location;
		if (substr($dir, -1) != DIRSEP)
			$dir .= DIRSEP;

		/**
		 * Search dirs for the file
		**/
		$found = false;
		foreach ($this->searches as $var)
			if (is_dir($var.$dir) && is_writable($var.$dir))
			{
				$dir = $var.$dir;
				$found = true;
				break;
			}

		if (!$found)
		{
			$xnyo_parent->trigger_error('Cache directory '.$dir.' doesnt exist or is not writable, please check your configuration.', WARNING);
			return false;
		}

		if (empty($xnyo_parent->cache_filename))
		{
			// no specified filename, try to load the default
			if (!empty($_SESSION['language']))
			{
				$dir .= $_SESSION['language'].DIRSEP;
				if (!is_dir($dir))
					mkdir($dir, 0700);
			}

			// logged in?
			if (!empty($_SESSION['auth']['username']))
			{
				$dir .= $_SESSION['auth']['username'].DIRSEP;
				if (!is_dir($dir))
					mkdir($dir, 0700);
			}

			$page = str_replace('/', '_', substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], '.')));

			// our filename
			$file = $page.'.'.$seed.'.html';

			// proper caching, headers are good
			$headers = true;
		} else
		{
			// use what we are given
			$file = $xnyo_parent->cache_filename;

			// we dont want headers
			$headers = false;
		}

		// write the cache i guess
		$fp = fopen($dir.$file, 'w');

		// calculate file expire time
		if (!is_null($xnyo_parent->cache_expire))
			$expire = $xnyo_parent->cache_expire;
		elseif (!is_null($xnyo_parent->cache_idle_time))
			$idle = $xnyo_parent->cache_idle_time;
		elseif ($xnyo_parent->cache_lifetime === 0)
			// dont bother caching if we have a file that has no life!
			return true;
		else
			$expire = time() + $xnyo_parent->cache_lifetime;

		// output headers and the file
		if ($headers)
			fputs ($fp, (isset($idle) ? 'idle: '.$idle."\n\n" : 'expire: '.$expire."\n\n"));
		fputs ($fp, $buffer);

		fclose($fp);

		// update the access time because we can
		touch ($dir.$file);

		// output last modified headers
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

		// finished, hard eh?
		return true;
	}

	/**
	 * Read
	 *
	 * Read and output a cache file
	**/
	function read ()
	{
		global $xnyo_parent;

		// make our seed
		$seed = $this->make_seed();

		// the location in this case is a directory, check it
		$dir = $xnyo_parent->cache_location;
		if (substr($dir, -1) != DIRSEP)
			$dir .= DIRSEP;

		// set the  filename
		$page = str_replace('/', '_', substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], '.')));
		$file = $page.'.'.$seed.'.html';

		if (!empty($xnyo_parent->language))
			$dir .= $xnyo_parent->language.DIRSEP;
		elseif (!empty($xnyo_parent->default_language))
			$dir .= $xnyo_parent->default_language.DIRSEP;

		if (!empty($_SESSION['auth']['username']))
			$dir .= $_SESSION['auth']['username'].DIRSEP;


                // try XNYODIR first, SCRIPT_DIR second
                // cant write to the dir? uh oh!
		$found = false;
		foreach ($this->searches as $var)
			if (file_exists($var.$dir.$file) && is_readable($var.$dir.$file))
			{
				$dir = $var.$dir;
				$found = true;
				break;
			}

		if (!$found)
			return false;

		// check if our executing file is more recent than the cache
		if (filemtime($_SERVER['SCRIPT_FILENAME']) > filemtime($dir.$file))
		{
			unlink ($dir.$file);
			return false;
		}
		
		// get the file access time..
		$access = NULL;
		$access = fileatime($dir.$file);

		// go!
		$fp = fopen($dir.$file, 'r');

		// get config data
		while (!feof($fp))
		{
			$header = fgets($fp);

			if ($header == "\n" || $header == "\r\n")
				break;

			$header = explode(': ', chop($header));

			// store data
			$config[$header[0]] = $header[1];

		}

		// check to see if the time now is past the expiration date
		if (!empty($config['expire']) && $config['expire'] !== 0 && time() > $config['expire'])
		{

			// close that file pointer, annoying thing
			fclose($fp);

			// delete the cache file!
			unlink($dir.$file);

			return false;
		}
		
		// idle timeout?
		if (!empty($config['idle']) && !is_null($access) && ($config['idle'] + $access) > time())
		{
			fclose($fp);
			unlink($dir.$file);
			return false;
		}

		// has this changed since last time?
		if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']))
		{
			$modsince = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
			if ($modsince >= filemtime($dir.$file))
			{
				header('HTTP/1.0 304 Not Modified');
				exit();
			}
		}

		// send a modified timestamp
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

		// well all checks worked? fire away?
		// get the rest of the file i guess!
		while (!feof($fp))
			$buffer .= fgets($fp);

		// done
		fclose($fp);

		// content-length juarez first
		if (!ini_get('zlib.output_compression'))
			header('Content-Length: '.strlen($buffer));

		// go!
		echo $buffer;

		// and we're done
		return true;
	}

}

?>
