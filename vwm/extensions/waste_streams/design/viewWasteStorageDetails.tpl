{if $color eq "green"}
	{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
	{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
	{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

<br>

<table class="users" align="center" cellpadding="0" cellspacing="0">
	<tr class="users_u_top_size users_top_green">	
		<td class="users_u_top_green" >	
			<span>View waste storage details</span>	
		</td>	
		<td class="users_u_top_r_green">
			&nbsp;	
		</td>	
	</tr>
	<tr>
		<td height="20" class="border_users_l border_users_b border_users_r">Name</td>
		<td class="border_users_b border_users_r" align='left'>{$data->name}&nbsp;</td>
	</tr>
	<tr>
		<td height="20" class="border_users_l border_users_b border_users_r">Capacity</td>
		<td class="border_users_b border_users_r" align='left'>{$data->capacity_volume} {$volumeUnittype} {*/ {$data->capacity_weight} {$weightUnittype}</td>*}
	</tr>
	<tr>
		<td height="20" class="border_users_l border_users_b border_users_r">Density</td>
		<td class="border_users_b border_users_r" align='left'>{$data->density}({$data->density_type})&nbsp;</td>
	</tr>
	<tr>
		<td height="20" class="border_users_l border_users_b border_users_r">Max Period</td>
		<td class="border_users_b border_users_r" align='left'>{$data->max_period}&nbsp;</td>
	</tr>
	<tr>
		<td height="20" class="border_users_l border_users_b border_users_r">Suitability</td>
		<td class="border_users_b border_users_r" align='left'>{$suitability}&nbsp;</td>
	</tr>
	{if $data->active!='0'}
		<tr>
			<td height="20" class="border_users_l border_users_b border_users_r">Last use since</td>
			<td class="border_users_b border_users_r" align='left'>{$data->use_date} &nbsp;</td>
		</tr>
		<tr>
			<td height="20" class="border_users_l border_users_b border_users_r">Days left</td>
			<td class="border_users_b border_users_r" align='left'>{$data->days_left}&nbsp;</td>
		</tr>
		<tr>
			<td height="20" class="border_users_l border_users_b border_users_r">Current usage</td>
			<td class="border_users_b border_users_r" align='left'>{$data->current_usage}&nbsp;</td>
		</tr>
		<tr>
			<td height="20" class="border_users_l border_users_b border_users_r">Gauge  </td>
			<td class="border_users_b border_users_r" align='left'> {include file="tpls:waste_streams/design/indicator.tpl" value=$data->current_usage limit=$data->capacity_volume}&nbsp;</td>
		</tr>	
	{else}
		<tr>
			<td height="20" class="border_users_l border_users_b border_users_r">Delete date  </td>
			<td class="border_users_b border_users_r" align='left'> {$data->delete_date}&nbsp;</td>
		</tr>	
	{/if}
	{if $show.docs}
	<tr>
		<td height="20" class="border_users_l border_users_b border_users_r">Document</td>
		<td class="border_users_b border_users_r" align='left'>
			{if $doc eq '0'}
				<i>No document</i>
			{else}Post
				<div class="category_documents"><div class="category_link"><p><a href ="{$doc.link}" title = "{$doc.description}">{$doc.name}</a></p></div></div>
			{/if}
			&nbsp;
		</td>
	</tr>
	{/if}
	<tr>
	 	<td height="20" class="users_u_bottom">&nbsp;</td>
		<td height="20" class="users_u_bottom_r">&nbsp;</td>
	</tr>	
<table>	
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js"></script>
{literal}
<script type="text/javascript">
	$(document).ready(function()
	{
		date = new Date();
		var month = date.getMonth() + 1
		var day = date.getDate()
		var year = date.getFullYear()
		var dateString=month + "/" + day + "/" + year;
		if ((month/10)<1)
		{
			 dateString="0"+dateString;
		}
					
		$('#calendar').attr('value',dateString);
		$('#calendar').datepicker({ dateFormat: 'mm/dd/yy'});
		$('#calendar2').attr('value',dateString);
		$('#calendar2').datepicker({ dateFormat: 'mm/dd/yy'});
	});
</script>
{/literal}	
	