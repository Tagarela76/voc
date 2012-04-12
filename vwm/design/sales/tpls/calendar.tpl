<!-- javascript pop-up -->
{literal}
     <script language="JavaScript">
            <!--
            function MM_openBrWindow(theURL,winName,features) { //v2.0
              window.open(theURL,winName,features);
	    }
	    //-->
	</script>
{/literal}	

{*JQUERY POPUP SETTINGS*}
<link href="modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/external/jquery.bgiframe-2.1.1.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.mouse.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.draggable.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.resizable.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.dialog.js"></script>
{*END OF SETTINGS*}

<div class="padd7" align="center">
{php}

//include ($_SERVER['DOCUMENT_ROOT'].'/voc_src/vwm/extensions/calendar/calendar.php');

/*
 <iframe src="/voc_src/vwm/extensions/calendar/calendar.php"  width="700" height="650" align="center" frameborder="0">
    Ваш браузер не поддерживает плавающие фреймы!
 </iframe>
*/
{/php}

<table cellspacing=0 class='calendar'>
	<tr>
		{section loop=$week name=i }
			<th>{$week[i]}</th>	
		{/section}	
	</tr>
	<tr>
		{section loop=$firstday name=i start=1}
			<td class='padding'>&nbsp;</td>
		{/section}	
		
		{assign var=a value=0}
			{section loop=$nr name=i start=1}
				<td {if $smarty.section.i.index == $day } class='today' {elseif $events[i]}class='date_has_event'{/if} onclick="addEvent({$smarty.section.i.index},{$month},{$year}); return false;" valign=top><b>{$smarty.section.i.index}</b>
	<!-- onclick="MM_openBrWindow('extensions/calendar/calendar.php?op=eventform&day={$smarty.section.i.index}&month={$month}&year={$year}','Calendar','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=400,height=400')"  -->
					
					{if $events[i]}
						<div class=events><ul>
	

					{foreach from=$events[i] item=event}
						<li>
							<span class='title'>
								<a onclick="MM_openBrWindow('extensions/calendar/cal_popup.php?op=view&id={$event.id}','Calendar','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width={$popupeventwidth},height={$popupeventheight}')">{$event.title|stripslashes}</a>

							</span>
							<span class="desc">{$event.description|stripslashes}</span>
						</li>
					{/foreach}

					</ul></div>
				{/if}			
				</td>
				{assign var=a value=$a+1}
			{if (($smarty.section.i.index == (8-$firstday)) or ($smarty.section.i.index == (15-$firstday)) or ($smarty.section.i.index == (22-$firstday)) or ($smarty.section.i.index == (29-$firstday)) or ($smarty.section.i.index == (36 - $firstday)))}
			</tr><tr>
			{assign var=a value=0}	
			{/if}			
		{/section}
		
        {if $a != 0}
			{assign var=last value=7-$a}
				{section loop=$last name=i start=2}
					<td class='padding'>&nbsp;</td>
				{/section}
		{/if}		
	</tr>
	
	
	<tr>
		{section loop=$week name=i }
			<th>{$week[i]}</th>	
		{/section}	
	</tr>	
</table>
</div>
	
{*SELECT_JOBBER_POPUP*}
<div id="addEvent" title="Add Event" style="display:none;">
					<div style="overflow:auto;height:400px;">
                    <table width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center" id="popup_table_jobber">

                        <tr>
                            <td colspan=""  style="padding:0px;border-bottom:0px;">
							Event Title : 	
                            </td>
                            <td colspan=""  style="padding:0px;border-bottom:0px;">
								<input type="text" name="title" id="title" />
                            </td>							
                        </tr>
						
                        <tr>
                            <td colspan=""  style="padding:0px;border-bottom:0px;">
							Event Description : 	
                            </td>
                            <td colspan=""  style="padding:0px;border-bottom:0px;">
								<textarea name="description" id="description" cols="50" rows="7"></textarea>
                            </td>							
                        </tr>	
						
                        <tr>
                            <td colspan=""  style="padding:0px;border-bottom:0px;">
							Email : 	
                            </td>
                            <td colspan=""  style="padding:0px;border-bottom:0px;">
								<input type="text" name="email" id="email" />
                            </td>							
                        </tr>	
                        <tr>
                            <td colspan=""  style="padding:0px;border-bottom:0px;">
							URL : 	
                            </td>
                            <td colspan=""  style="padding:0px;border-bottom:0px;">
								<input type="text" name="url" id="url" />
                            </td>							
                        </tr>
						
                        <tr>
                            <td colspan=""  style="padding:0px;border-bottom:0px;">
							Choose Category : 	
                            </td>
                            <td colspan=""  style="padding:0px;border-bottom:0px;">
								<select name="category" id="category" >
								{foreach from=$categoryList item=category}
									<option name="{$category.cat_id}" >{$category.cat_name}</option>
								{/foreach}
                            </td>							
                        </tr>						



    </div>

					
</div>
{*END OF POPUP*}	