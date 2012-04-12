<XNYO::START::OF::DEBUG::CONSOLE::TEMPLATE>
{**
 * Project:     Xnyo: Application Backend
 * File:        plugins/error/debug.tpl
 *              Debug Console Template
 *
 * Website:	http://xnyo.odynia.org/
 * Manual:	http://xnyo.odynia.org/manual/
 *
 * Version:     3.0-dev
 * SVN Id:      $Id: debug.tpl 20 2004-08-12 06:02:04Z bok $
 * SVN URL:     $HeadURL: http://svn.odynia.org/xnyo/trunk/plugins/error/debug.tpl $
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
**}
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title>Xnyo Debug Console</title>
	<style>
		body
		{ldelim}
			margin:			20px;
			background-color:	#FFFFFF;
			font-family:		verdana, arial, sans-serif;
		{rdelim}
		a:link
		{ldelim}
			text-decoration:	none;
			background-color:	transparent;
			color:			#005CB3;
		{rdelim}
		a:visited,
		a:active
		{ldelim}
			text-decoration:	none;
			background-color:	transparent;
			color:			#005CB3;
		{rdelim}
		a:hover,
		{ldelim}
			text-decoration:	none;
			color:			red;
			background-color:	transparent;
		{rdelim}
		td
		{ldelim}
			font-size:		11px;
		{rdelim}
		/* Page Headers */
		h1
		{ldelim}
			font-size:		26px;
			font-weight:		bold;	
			letter-spacing:		-1px;
			background-color:	transparent;
			padding:		0px;
			margin:			0px;
			margin-bottom:		10px;
			color:			#3200B3;
		{rdelim}
		h2
		{ldelim}
			font-size:		16px;
			background-color:	transparent;
			font-weight:		bold;
			color:			#3200B3;
		{rdelim}
		h3
		{ldelim}
			font-size:		12px;
			background-color:	transparent;
		 	font-weight:		bold;
			color:			#3200B3;
		{rdelim}
		h4
		{ldelim}
			font-size:		12px;
			background-color:	transparent;
		{rdelim}
		.indent
		{ldelim}
			padding-left:		20px;
		{rdelim}
		/* List Tables */
	        #list th,
	        #data th
	        {ldelim}
			font-weight:		bold;
			font-size:		12px;
			text-align:		left;
			margin:			0px;
			padding:		0px;
			padding-left:		5px;
			border:			0px;
			border-bottom:		2px solid #235CDB;
	        {rdelim}
		#list td
		{ldelim}
			border-right:		1px solid #FFFFFF;
			padding:		0px 5px;
			border-bottom:		1px solid #DDDDDD;
		{rdelim}
		/* For alternating table rows */
		tr.line1
		{ldelim}
			line-height:		16px;
			color:			#222222;
			background:		#FFFFFF;
		{rdelim}
		tr.line1err
		{ldelim}
			line-height:		16px;
			color:			#FF2222;
			background-color:	#FFFFFF;
			border-bottom:		1px solid #FFBBBB;
		{rdelim}
		tr.line2
		{ldelim}
			line-height:		16px;
			color:			#222222;
			background:		#FAFAFA;
		{rdelim}
		tr.line2err
		{ldelim}
			line-height:		16px;
			color:			#FF2222;
			background-color:	#FFEEEE;
			border-bottom:		1px solid #FFBBBB;
		{rdelim}
		/** Table/Form layout stuff **/
		.flabel
		{ldelim}
			padding:		7px 4px 7px 4px;
			font-weight:		bold;
			border-top:		1px solid #FFFFFF;
			border-bottom:		1px solid #EEEEEE;
			width:			150px;
			text-weight:		bold;
		{rdelim}
		.flabelerr
		{ldelim}
			padding:		7px 4px 7px 4px;
			font-weight:		bold;
			width:			150px;
			text-weight:		bold;
			color:			#FF0000;
			border-top:		1px solid #FFFFFF;
		{rdelim}
		.flabelerrline
		{ldelim}
			border-bottom:		1px solid #FFBBBB;
			width:			150px;
		{rdelim}
		.fentry
		{ldelim}
			padding:		7px 4px 7px 4px;
			background-color:	#FAFAFA;
			border-top:		1px solid #FFFFFF;
			border-bottom:		1px solid #EEEEEE;
			border-right:		1px solid #EEEEEE;
		{rdelim}
		.fentryerr
		{ldelim}
			padding:		7px 4px 7px 4px;
			background-color:	#FFFAFA;
			border-top:		1px solid #FFFFFF;
		{rdelim}
		.fentryerrline
		{ldelim}
			border-bottom:		1px solid #FFBBBB;
			background-color:	#FFFAFA;
			text-align:		right;
			color:			#FF0000;
			font-size:		10px;
		{rdelim}
		.fsublabel
		{ldelim}
			padding:		7px 4px 7px 4px;
			font-weight:		bold;
			width:			150px;
		{rdelim}
		.fsubentry
		{ldelim}
			padding:		7px 4px 7px 4px;
			background-color:	#FAFAFA;
		{rdelim}
		.fbody
		{ldelim}
			padding:		5px;
			border:			1px solid rgb(213, 213, 213);
		{rdelim}
	</style>
</head>
<body>
	<h1>Xnyo Debug Console</h1>
	
	<h2>Debug Log</h2>
	<table id="list" style="width: 100%;" cellspacing=0>
	<tr>
		<th style="width: 100px;">Time taken</th>
		<th>Message</th>
		<th style="width: 150px;">Function</td>
		<th style="width: 200px;">Filename</td>
		<th style="width: 50px;">Line</td>
	</tr>
	<tr class="line1">
		<td>now</td>
		<td style="font-weight: bold;" colspan="4">Total Script Execution Time: {math equation="round(x - y, 5)" x=$cur_time y=$first_time} seconds</td>
	</tr>
	{foreach from=$debug key="k" item="v"}
	<tr class="{cycle name=$key values="line1,line2}">
		<td nowrap="true">{if !empty($v.next)}{math equation="round((x - y), 5)" x=$v.timestamp y=$v.next} secs{/if}</td>
		<td style="font-weight: bold; max-width: 500px;">{$v.body}</td>
		{debug_known_func class=$v.class func=$v.function}
		<td>{$v.file|debug_shorten_filename}</td>
		<td>{$v.line}</td>
	</tr>
	{/foreach}
	</table>
	<br />

	{foreach from=$errors key="key" item="var"}
		{if count($var)}
		<h2>Displaying errors of type: {$key|ucfirst}</h2>
		<table id="list" style="width: 100%;" cellspacing=0>
		<tr>
			<th style="width: 100px;">Time</th>
			<th>Message</th>
			<th style="width: 150px;">Function</td>
			<th style="width: 200px;">Filename</td>
			<th style="width: 50px;">Line</td>
		</tr>
		{foreach from=$var key="k" item="v"}
		<tr class="{cycle name=$key values="line1,line2}">
			<td nowrap="true">{date format="H:i:s" timestamp=$v.timestamp}{if !empty($v.microseconds)}{$v.microseconds}{/if}</td>
			<td style="font-weight: bold; max-width: 500px;">{$v.body}</td>
			{debug_known_func class=$v.class func=$v.function}
			<td>{$v.file|debug_shorten_filename}</td>
			<td>{$v.line}</td>
		</tr>
		{/foreach}
		</table>
		<br />
		{/if}
	{/foreach}

	<h2>Input Variables</h2>
	<div class="indent" style="font-size: 10px;">Variables marked in <font style="color: #FF0000;">red</font> were not allowed through the input filter.</div>
	<table style="width: 100%;" cellspacing=0>
	<tr>
		<td style="border-bottom: 0px; text-align: center;"><h3>$_GET</h3></td>
		<td>&nbsp;</td>
		<td style="border-bottom: 0px; text-align: center;"><h3>$_POST</h3></td>
		<td>&nbsp;</td>
		<td style="border-bottom: 0px; text-align: center;"><h3>$_COOKIE</h3></td>
	</tr>
	<tr>
		<td style="width: 350px; vertical-align: top;">
			<table id="list" style="width: 350px;" cellspacing=0>
			<tr>
				<th style="width: 100px;">Name</th>
				<th style="width: 250px;">Value</th>
			</tr>
			{if !count($filtered.get) && !count($unfiltered.get)}
				<tr class="line1">
					<td colspan="2" style="font-style: italic;">None Found.</td>
				</tr>
			{/if}
			{foreach from=$filtered.get key="key" item="var"}
				<tr class="{cycle name="get" values="line1,line2"}">
					<td style="width: 100px;">{$key|truncate:"15"}</td>
					<td style="width: 250px; overflow: hidden; whitespace: nowrap;" nowrap="true">=&gt; {$var|@debug_xnyo_print_var|truncate:"40":"...":true}</td>
				</tr>
			{/foreach}
			{foreach from=$unfiltered.get key="key" item="var"}
				<tr class="{cycle name="get" values="line1,line2"}err">
					<td style="width: 100px;">{$key|truncate:"15"}</td>
					<td style="width: 250px; overflow: hidden; whitespace: nowrap;" nowrap="true">=&gt; {$var|@debug_xnyo_print_var|truncate:"40":"...":true}</td>
				</tr>
			{/foreach}
			</table>
		</td>
		<td>&nbsp;</td>
		<td style="width: 350px; vertical-align: top;">
			<table id="list" style="width: 100%;" cellspacing=0>
			<tr>
				<th style="width: 100px;">Name</th>
				<th style="width: 250px;">Value</th>
			</tr>
			{if !count($filtered.post) && !count($unfiltered.post)}
				<tr class="line1">
					<td colspan="2" style="font-style: italic;">None Found.</td>
				</tr>
			{/if}
			{foreach from=$filtered.post key="key" item="var"}
				<tr class="{cycle name="post" values="line1,line2"}">
					<td style="width: 100px;">{$key|truncate:"15"}</td>
					<td style="width: 250px; overflow: hidden; whitespace: nowrap;" nowrap="true">=&gt; {$var|@debug_xnyo_print_var|truncate:"40":"...":true}</td>
				</tr>
			{/foreach}
			{foreach from=$unfiltered.post key="key" item="var"}
				<tr class="{cycle name="post" values="line1,line2"}err">
					<td style="width: 100px;">{$key|truncate:"15"}</td>
					<td style="width: 250px; overflow: hidden; whitespace: nowrap;" nowrap="true">=&gt; {$var|@debug_xnyo_print_var|truncate:"40":"...":true}</td>
				</tr>
			{/foreach}
			</table>
		</td>
		<td>&nbsp;</td>
		<td style="width: 350px; vertical-align: top;">
			<table id="list" style="width: 100%;" cellspacing=0>
			<tr>
				<th style="width: 100px;">Name</th>
				<th style="width: 250px;">Value</th>
			</tr>
			{if !count($filtered.cookie) && !count($unfiltered.cookie)}
				<tr class="line1">
					<td colspan="2" style="font-style: italic;">None Found.</td>
				</tr>
			{/if}
			{foreach from=$filtered.cookie key="key" item="var"}
				<tr class="{cycle name="cookie" values="line1,line2"}">
					<td style="width: 100px;">{$key|truncate:"15"}</td>
					<td style="width: 250px; overflow: hidden; whitespace: nowrap;" nowrap="true">=&gt; {$var|@debug_xnyo_print_var|truncate:"40":"...":true}</td>
				</tr>
			{/foreach}
			{foreach from=$unfiltered.cookie key="key" item="var"}
				<tr class="{cycle name="cookie" values="line1,line2"}err">
					<td style="width: 100px;">{$key|truncate:"15"}</td>
					<td style="width: 250px; overflow: hidden; whitespace: nowrap;" nowrap="true">=&gt; {$var|@debug_xnyo_print_var|truncate:"40":"...":true}</td>
				</tr>
			{/foreach}
			</table>
		</td>
	</tr>
	</table>

	<table style="width: 100%;" cellspacing=0>
	<tr>
		<td style="vertical-align: top; width: 500px;">
			<h2 style="text-align: center;">User Configuration</h2>
			<table id="data" style="width: 500px;" cellspacing=0>
			<tr>
				<th style="border-bottom: 2px;">&nbsp;</th>
				<th style="border-bottom: 2px;">&nbsp;</th>
			</tr>
			<tr>
				<td class="flabel">Location</td>
				<td class="fentry">{$location|default:"None"}</td>
			</tr>
			<tr>
				<td class="flabel">Language</td>
				<td class="fentry">{$language|default:"English"}</td>
			</tr>

			{if $display_access}
			<tr>
				<td class="flabel">Logged In</td>
				<td class="fentry">{$logged_in}</td>
			</tr>
			{/if}
			{if !empty($username)}
			<tr>
				<td class="flabel">Username</td>
				<td class="fentry">{$username}</td>
			</tr>
			{/if}
			{if !empty($userid)}
			<tr>
				<td class="flabel">User ID</td>
				<td class="fentry">{$userid}</td>
			</tr>
			{/if}
			{if !empty($useremail)}
			<tr>
				<td class="flabel">Email Address</td>
				<td class="fentry">{$useremail}</td>
			</tr>
			{/if}
			</table>
			<br />
			<br />

			{if count($userdata)}
				<h2 style="text-align: center;">User Variables ($xnyo->user)</h2>
				<table id="list" style="width: 500px;" cellspacing=0>
				<tr>
					<th style="width: 100px;">Name</th>
					<th>Value</th>
				</tr>
				{foreach from=$userdata key="key" item="var"}
				<tr class="{cycle name="userdata" values="line1,line2"}">
					<td style="width: 100px;">{$key|truncate:"20":"...":true}</td>
					<td style="whitespace: nowrap;" nowrap="true">=&gt {$var|@debug_xnyo_print_var|truncate:"60":"...":true}</td>
				</tr>
				{/foreach}
				</table>
				<br />
				<br />
			{/if}
	
			<h2 style="text-align: center;">Loaded Plugins</h2>
			<table id="list" style="width: 500px;" cellspacing=0>
			<tr>
				<th style="width: 50px;">Name</th>
				<th>Description</th>
				<th style="width: 100px;">Type</th>
				<th style="width: 100px;">Class</th>
			</tr>
			{foreach from=$loaded_plugins key="key" item="var"}
				{foreach from=$var key="k" item="v"}
				<tr class="{cycle name="loaded_plugins" values="line1,line2"}">
					<td style="width: 50px;">{$k|truncate:"20":"...":true}</td>
					<td>{debug_xnyo_plugin_descr|truncate:"25":"...":"1" class=$v}</td>
					<td style="width: 100px;">{$key|truncate:"20":"...":true}</td>
					<td style="whitespace: nowrap;" nowrap="true">=&gt {$v|@debug_xnyo_print_var|truncate:"20":"...":true}</td>
				</tr>
				{/foreach}
			{/foreach}
			</table>
			<br />
			<br />
	
			<h2 style="text-align: center;">Xnyo Configuration</h2>
			<table id="list" style="width: 500px;" cellspacing=0>
			<tr>
				<th style="width: 100px;">Name</th>
				<th>Class</th>
			</tr>
			{foreach from=$xnyo_conf key="key" item="var"}
			<tr class="{cycle name="xnyo" values="line1,line2"}">
				<td style="width: 100px;">{$key|truncate:"20":"...":true}</td>
				<td style="whitespace: nowrap;" nowrap="true">=&gt {$var|@debug_xnyo_print_var|truncate:"60":"...":true}</td>
			</tr>
			{/foreach}
			</table>
			<br />
			<br />
	
			<h2 style="text-align: center;">Server Variables ($_SERVER)</h2>
			<table id="list" style="width: 500px;" cellspacing=0>
			<tr>
				<th style="width: 100px;">Name</th>
				<th>Value</th>
			</tr>
			{foreach from=$smarty.server key="key" item="var"}
			<tr class="{cycle name="server" values="line1,line2"}">
				<td style="width: 100px;">{$key|truncate:"20":"...":true}</td>
				<td style="whitespace: nowrap;" nowrap="true">=&gt {$var|@debug_xnyo_print_var|truncate:"50":"...":true}</td>
			</tr>
			{/foreach}
			</table>
		</td>
		<td>&nbsp;</td>
		<td style="vertical-align: top; width: 500px;">
			<h2 style="text-align: center;">Session Variables ($_SESSION)</h2>
			<table id="list" style="width: 500px;" cellspacing=0>
			<tr>
				<th style="width: 100px;">Name</th>
				<th>Value</th>
			</tr>
			{foreach from=$smarty.session key="key" item="var"}
			<tr class="{cycle name="session" values="line1,line2"}">
				<td style="width: 100px;">{$key|truncate:"20":"...":true}</td>
				<td style="whitespace: nowrap;" nowrap="true">=&gt {$var|@debug_xnyo_print_var|truncate:"60":"...":true}</td>
			</tr>
			{/foreach}
			</table>
			<br />
			<br />

			{if count($open_db_connections)}
				<h2 style="text-align: center;">Open Database Connections</h2>
				<table id="list" style="width: 500px;" cellspacing=0>
				<tr>
					<th style="width: 100px;">Database Name</th>
					<th>Status</th>
					<th style="width: 100px;"># Times</th>
					<th style="width: 100px;">Connect Time</th>
					<th>Resource</th>
				</tr>
				{foreach from=$open_db_connections key="key" item="var"}
				<tr class="{cycle name="database" values="{line1,line2}"}">
					<td style="width: 100px;">{$var.name}</td>
					<td>{$var.status}</td>
					<td>{$var.times}</td>
					<td>{$var.connect_time|truncate:"6":"":true} secs</td>
					<td>{$var.resource}</td>
				</tr>
				{/foreach}
				</table>		
				<br />
				<br />
			{/if}
			
			<h2 style="text-align: center;">Current Sessions</h2>
			<table id="list" style="width: 500px;" cellspacing=0>
			<tr>
				<th style="width: 100px;">IP</th>
				<th style="width: 100px;">Last Active</th>
				<th>Browser</th>
			</tr>
			{foreach from=$sessions key="key" item="var"}
			<tr class="{cycle name="sessions" values="{line1,line2}"}">
				<td style="width: 100px;">{$var.ip}</td>
				<td style="width: 100px;">{$var.last_activity|debug_print_delay}</td>
				<td style="whitespace: nowrap;" nowrap="true">{$var.browser|truncate:"40":"...":true}</td>
			</tr>
			{/foreach}
			</table>		
			<br />
			<br />
	
			<h2 style="text-align: center;">Smarty Configuration</h2>
			<table id="list" style="width: 500px;" cellspacing=0>
			<tr>
				<th style="width: 100px;">Name</th>
				<th>Value</th>
			</tr>
			{foreach from=$smarty_conf key="key" item="var"}
			<tr class="{cycle name="smarty" values="line1,line2"}">
				<td style="width: 100px;">{$key|truncate:"20":"...":true}</td>
				<td style="whitespace: nowrap;" nowrap="true">=&gt {$var|@debug_xnyo_print_var|truncate:"60":"...":true}</td>
			</tr>
			{/foreach}
			</table>
			<br />
			<br />
	
			<h2 style="text-align: center;">Assigned Smarty Variables</h2>
			<table id="list" style="width: 500px;" cellspacing=0>
			<tr>
				<th style="width: 100px;">Name</th>
				<th>Value</th>
			</tr>
			{foreach from=$smarty_vars key="key" item="var"}
			<tr class="{cycle name="smarty" values="line1,line2"}">
				<td style="width: 100px;">{$key|truncate:"20":"...":true}</td>
				<td style="whitespace: nowrap;" nowrap="true">=&gt {$var|@debug_xnyo_print_var|truncate:"60":"...":true}</td>
			</tr>
			{/foreach}
			</table>
			<br />
			<br />
	
			{if count($smarty.env)}
				<h2 style="text-align: center;">Environment Variables ($_ENV)</h2>
				<table id="list" style="width: 500px;" cellspacing=0>
				<tr>
					<th style="width: 100px;">Name</th>
					<th>Value</th>
				</tr>
				{foreach from=$smarty.env key="key" item="var"}
				<tr class="{cycle name="env" values="line1,line2"}">
					<td style="width: 100px;">{$key|truncate:"20":"...":true}</td>
					<td style="whitespace: nowrap;" nowrap="true">=&gt {$var|@debug_xnyo_print_var|truncate:"60":"...":true}</td>
				</tr>
				{/foreach}
				</table>
				<br />
				<br />
			{/if}
			{if count($files)}
				<h2 style="text-align: center;">Uploaded Files</h2>
				<table id="list" style="width: 500px;" cellspacing=0>
				<tr>
					<th style="width: 100px;">Name</th>
					<th>Value</th>
				</tr>
				{foreach from=$files item="var"}
					{foreach from=$smarty_conf key="key" item="var" name="files"}
					<tr class="{cycle name="files" values="line1,line2"}"{if $smarty.foreach.files.last} style="border-bottom: 2px solid #235CDB;"{/if}>
						<td style="width: 100px;">{$key|truncate:"20":"...":true}</td>
						<td style="whitespace: nowrap;" nowrap="true">{$var|@debug_xnyo_print_var|truncate:"60":"...":true}</td>
					</tr>
					{/foreach}
				{/foreach}
				</table>
			{/if}
		</td>
	</tr>
	</table>
	<br />
	<br />
	<br />
</body>
</html>
<XNYO::END::OF::DEBUG::CONSOLE::TEMPLATE>