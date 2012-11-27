{if $message}
<table cellspacing="0" cellpadding="0" width="100%" height="37px">
    <tr>
        <td bgcolor="white" valign="bottom">
            {if $color eq "green"}
            {include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
            {/if}
            {if $color eq "orange"}
            {include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
            {/if}
            {if $color eq "blue"}
            {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
            {/if}
        </td>
    </tr>
</table>
{/if}
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
<table class="users" height="200" cellspacing="0" cellpadding="0" align="center">
    <tr class="users_top" height="27px">
    <td class="users_u_top" width="10%">
        <span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span>
    </td>
    {foreach from=$mixColumn4Display item=mixTitle key=index}
        <td class="{if ($index+2) > $columnCount}users_u_top_r{/if}" width="{$widths.$index}">
            <a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
                <div style='width:100%;  color:white;'>
                    {$mixTitle|escape}
                </div>
            </a>
        </td>
    {/foreach}
	</tr>
	{if $mixFormatObjList|@is_array and $mixFormatObjList|@count > 0}
		{foreach from=$mixFormatObjList item=mix}
			<!-- Begin Highlighting -->
			<tr {if $mix.valid  eq "valid"}
					class="hov_company"
				{else}
					{if $mix.valid  eq "invalid"}
					 class="us_red"
					{else}
					class="us_orange"
					{/if}
				{/if}
			height="10px">
			<!-- End Highlighting -->
			<td class="border_users_b border_users_l" >
				{if $mix.valid eq "valid"}
					<span class="ok">&nbsp;</span>
				{else}
					{if $mix.valid eq "invalid"}
					<span class="error">&nbsp;</span>
					{else}
					<span class="warning">&nbsp;</span>
					{/if}
				{/if}
				<input type="checkbox" value="{$mix.mix_id}" name="id[]">
			</td>
			{foreach from=$mix.mixObject item=mixValue key=columnName}
				<td class="border_users_b border_users_r" >
					<a href="{$mix.url}" class="id_company1">
						<div style="width:100%;">
							{if $columnName eq "add_job"}
								{$mixValue} &nbsp;
							{else}
								{$mixValue|escape}
							{/if}
						</div>
					</a>
				</td>
			{/foreach}				
		{/foreach}		
		<tr>
			<td colspan="{$columnCount+1}" class="border_users_l border_users_r">
				&nbsp;
			</td>
		</tr>
		{*END LIST*}
	{else}
		{*BEGIN	EMPTY LIST*}
		<tr class="">
			<td colspan="{$columnCount+1}"class="border_users_l border_users_r" align="center">
				No mixes in the department
			</td>
		</tr>
		{*END	EMPTY LIST*}
	{/if}
    <tr>
        <td class="users_u_bottom" colspan="{$columnCount}" height="15">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table>
{*PAGINATION*}
{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
</form>	{*close form that was opened at controlInsideDepartment.tpl*}
