{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
<div class="padd7">
	<form action="{$addUsageUrl}" method="post" id="addUsageForm">
    <table class="users" align="center" cellpadding="0" cellspacing="0">
        <tr class="users_top_yellowgreen users_u_top_size">
            <td class="users_u_top_yellowgreen" width="10%" height="30">
                <span>View details</span>
            </td>
            <td class="users_u_top_r_yellowgreen" width="300">
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Accessory NR :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$accessory.id}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Description :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$accessory.name}
                </div>
            </td>
        </tr>
        <tr>
            <td class="border_users_l border_users_b" height="20">
                Jobber Name :
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left">
                    &nbsp;{$accessory.jobber_name}
                </div>
            </td>
        </tr>		
		<tr class="users_u_top_size users_top_lightgray">
			<td colspan="2">Add Usage</td>
		</tr>		
		<tr>
			<td class="border_users_r border_users_b border_users_l">
				Date
			</td>
			<td class="border_users_r border_users_b">
				<input type="text" name="AccessoryUsage[date]" id="calendar1" class="calendarFocus"/>
				<span id="dateError" class="error_text" ></span>
			</td>
		</tr>
		<tr>
			<td class="border_users_r border_users_b border_users_l">
				Usage
			</td>
			<td class="border_users_r border_users_b">
				<input type="text" name="AccessoryUsage[usage]"/>
				<span id="usageError" class="error_text"></span>
			</td>
		</tr>
		<tr>
			<td class="border_users_r border_users_b border_users_l">
				&nbsp;
			</td>
			<td class="border_users_r border_users_b">
				<input id="btnSave" class="button" type="submit" value="Add Usage" name="save">
			</td>
		</tr>
        <tr>
            <td height="20" class="users_u_bottom">
            </td>
            <td height="20" class="users_u_bottom_r">
            </td>
        </tr>
    </table>
</form>
</div>	
	
				
<div class="padd7">
    <table class="users" align="center" cellpadding="0" cellspacing="0" id="usageHistoryTable">
		<thead>
        <tr class="users_top">
            <td class="users_u_top" colspan="2" width="37%" height="30">
                <span>Usage History</span>
            </td>
            <td class="users_u_top_r" width="300">
            </td>
        </tr>
		<tr class="users_u_top_size users_top_lightgray">
			<td>ID</td>
			<td>Date</td>
			<td>Usage</td>
		</tr>		
		</thead>
		<tbody>
			{if $accessoryUsages}
				{foreach from=$accessoryUsages item=accessoryUsage}
				<tr>
					<td class='border_users_b border_users_l' height='20'>{$accessoryUsage->id}</td>
					{assign var="date" value=$accessoryUsage->date}
					<td class='border_users_r border_users_b border_users_l' >{$date->format($smarty.const.DEFAULT_DATE_FORMAT)}</td>
					<td class='border_users_r border_users_b'>{$accessoryUsage->usage}</td>
				</tr>
				{/foreach}
			{/if}
		</tbody>
		<tfoot>
		<tr class="">
			<td class="users_u_bottom" colspan="2" height="20"> </td>
			<td class="users_u_bottom_r"> </td>
		</tr>
        </tfoot>
    </table>
</div>

				{literal}
				<script type="text/javascript">
					$(document).ready(function(){      
						//	set calendar
						$('#calendar1').datepicker({ dateFormat: '{/literal}{$dataChain->getFromTypeController('getFormatForCalendar')}{literal}'}); 												
							
						//	handle ajax form
						jQuery('#addUsageForm').submit(function(){
							jQuery.ajax({
								'async':false, 
								'type':'POST',
								'dataType':'json',
								'success':function(r) {
									$("#dateError").css('display', 'none');
									$("#usageError").css('display', 'none');
										
									if (r.success) {
										var row = "<tr>";
										row += "<td class='border_users_b border_users_l' height='20'>"+r.data.id+"</td>";
										row += "<td class='border_users_r border_users_b border_users_l'>"+r.data.date+"</td>";
										row += "<td class='border_users_r border_users_b'>"+r.data.usage+"</td>";
										row += "</tr>";
										$("#usageHistoryTable tbody").prepend(row);
									} else {										
										if (r.validation.hasOwnProperty('date')) {
											$("#dateError").text(r.validation.date);
											$("#dateError").css('display', 'inline');												
										}	
										
										if(r.validation.hasOwnProperty('usage')) {
											$("#usageError").text(r.validation.usage);
											$("#usageError").css('display', 'inline');												
										}
										
									}
									
								},
								'url':'{/literal}{$addUsageUrl}{literal}',
								'cache':false,
								'data':jQuery("#addUsageForm").serialize()
							});														
							return false;
						});
					});
				</script>
				{/literal}