{if $color eq "green"}
	{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
	{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
	{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

{*PAGINATION*}
	{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}

<table class="users" align="center" cellpadding="0" cellspacing="0">
	<tr class="users_u_top_size users_top_brown">
		<td class="users_u_top_brown" colspan="10">
			<span >List of last records at regulation updates</span>
		</td>
		<td class="users_u_top_r_brown">
			&nbsp;
		</td>
	</tr>
	
	<tr class="users_u_top_size users_top_lightgray" >
		<td width="60"><span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:black'>All</a>/<a style='color:black' onclick="unCheckAll(this)" >None</a></span></td>			
		<td>
			RIN
		</td>
		<td>
			Title
		</td>	
		<td>
			Stage
		</td>	
		<td>
			Significant
		</td>	
		<td>
			Date Recieved
		</td>
		<td>
			Date Completed
		</td>
		<td>
			Decision
		</td>
		<td>
			Legal Deadline
		</td>
		<td>
			Reg Agency Code
		</td>
		<td>
			Reg Agency Acronym
		</td>
	</tr>
	
	{foreach from=$data item=act}
	{assign var=rin value=$act->rin}
	{assign var=facilityID value=$request.id}
	{assign var=url value="?tab=review&action=markReaded&category=regupdate&mark=$rin&facilityID=$facilityID"} <!-- target="_blank" -->
	{if !$act->readed}
	{assign var=boldStyle value=" style='font-weight:bold;'"}
	{else}
	{assign var=boldStyle value=""}
	{/if}
	{assign var=link value=" href='$url' $boldStyle target='_blank'"}
		<tr>
			<td class="border_users_l border_users_b border_users_r"><input type="checkbox" name="mark[]" value="{$actionList[i]->id}"></td>			
			<td class="border_users_b border_users_r">
					<div style="width:100%;">
					<a {$link}>
						{$act->rin}
					</a>
					</div>
			</td>
					
			<td class="border_users_b border_users_r">
					<div style="width:100%; ">
					<a {$link}>
						{$act->title}
					</a>
					</div>
			</td>	
			
			<td class="border_users_b border_users_r">
					<div style="width:100%;">
					<a {$link}>
						{$act->stage}
					</a>
					</div>
			</td>	
			<td class="border_users_b border_users_r">
					<div style="width:100%;">
					<a {$link}>
						{$act->significant}
					</a>
					</div>
			</td>	
			<td class="border_users_b border_users_r">
					<div style="width:100%;">
					<a {$link}>
						{$act->date_received}
					</a>
					</div>
			</td>
			<td class="border_users_b border_users_r">
					<div style="width:100%;">
					{if $act->date_completed and $act->date_completed != "0000-00-00"}
					<a {$link}>
						{$act->date_completed}
					</a>
					{else}
						-
					{/if}
					
					</div>
			</td>
			<td class="border_users_b border_users_r">
					<div style="width:100%;">
					{if $act->decision}
					<a {$link}>
						{$act->decision}
					</a>
					{else}
						-
					{/if}
					</div>
			</td>
			<td class="border_users_b border_users_r">
					<div style="width:100%;">
					<a {$link}>
						{$act->legal_deadline}
					</a>
					</div>
			</td>
			<td class="border_users_b border_users_r">
					<div style="width:100%;">
					<a {$link}>
						{$act->reg_agency->code}
					</a>
					</div>
			</td>
			<td class="border_users_b border_users_r">
					<div style="width:100%;">
					<a {$link}>
						{$act->reg_agency->acronym}
					</a>
					</div>
			</td>
		</tr>
	{/foreach}
	
	{if $smarty.section.i.total ==0}
		<tr align = 'center'>						
			<td class="border_users_l border_users_b border_users_r" colspan='12'>
				No records in regulation updates
			</td>						
		</tr>
	{/if}
</table>
<div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
{*PAGINATION*}
	{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}

</form>{*/FORM was opened in controlChildCategoriesList.tpl*}

<script type="text/javascript" src="modules/js/checkBoxes.js"></script>