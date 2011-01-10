<?PHP

/**
 * Project:     Xnyo: Application Backend
 * File:        plugins/config/file.php
 *              Configuration File Parser
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: file.php 2 2004-07-06 19:49:11Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/config/file.php $
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

class config_file_plugin
{

	/**
	 * Method:			parse
	 * Description:	Parse Configuration Files
	 * Arguments:		string	- file name
	 * Returns:			an array of the config file (empty if error)
	**/
	function parse($file=NULL)
	{

		global $xnyo_parent;

		// check first
		if (is_null($file))
		{
			$xnyo_parent->trigger_error('No configuration file specified.', WARNING);
			return array();
		}

		// check it exists and we can read it
		if (!file_exists($file) || !is_readable($file))
		{
			$xnyo_parent->trigger_error('File <i>'.$file.'</i> not found or isnt readable.', WARNING);
			return array();
		}

		// well what else is there to do now but start it off?
		$fp = fopen($file, "r");

		// keep a line counter for debugging and errors
		$line = 0;

		// for dealing with variables in config values for later		
		if (is_object($GLOBALS['smarty']))
			$vars = $GLOBALS['smarty']->get_template_vars();

		// set the return up..
		$return = new stdClass;

		// loop the file
		while (!feof($fp))
		{

			// read line in
			$linebuf = chop(fgets($fp, 1024));

			// deal with comments
			if (preg_match("/^[^a-zA-Z0-9_]/", $linebuf))
			{
				$line++;
				continue;
			}

			// split!!
			if (!preg_match("/^([\S]+?)\s+(.*?)$/", $linebuf, $m))
			{
				$line++;
				continue; // invalid line ey
			}

			// allow for variables in the files
			if (preg_match_all('/\{\$([^}]*?)}/', $m[2], $variables, PREG_SET_ORDER))
			{
				foreach ($variables as $v)
				{
					if (!empty($vars[$v[1]]))
					{
						$match[] = $v[0];
						$replace[] = $vars[$v[1]];
					} elseif (!empty($GLOBALS[$v[1]]))
					{
						// only other source, global vars i guess
						$match[] = $v[0];
						$replace[] = $GLOBALS[$v[1]];
					} else
					{
						$match[] = $v[0];
						$replace[] = str_replace('$', '\$', $v[0]);
					}
				}
			}

			if (is_array($match))
				$evaluated = str_replace($match, $replace, $m[2]);
			else
				$evaluated = $m[2];

			// there seriously has to be a better way of doing this
			$m[1] = str_replace('.', '->', $m[1]);
			$varline = 'return->'.$m[1];

			// double check
			$evaluated = str_replace('\'', '\\\'', $evaluated);
			eval('$'.$varline.' = \''.$evaluated.'\';');
		}

		fclose($fp);
		$line++;

		// phew
		return $return;
	}
}

?>
