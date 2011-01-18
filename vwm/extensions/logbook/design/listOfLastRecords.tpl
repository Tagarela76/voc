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
		<td class="users_u_top_brown" colspan="2">
			<span >List of last records at logbook</span>
		</td>
		<td class="users_u_top_r_brown">
			&nbsp;
		</td>
	</tr>
	
	<tr class="users_u_top_size users_top_lightgray" >
		<td width="60"><span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:black'>All</a>/<a style='color:black' onclick="unCheckAll(this)" >None</a></span></td>			
		<td width="100">
			<a style='color:black;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:black;'>						
                	Date          
					{if $sort==1 || $sort==2}<img  src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" />	{/if}							
				</div>				
			</a>  
		</td>
		<td>
			<a style='color:black;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:black;'>						
                	Type 		
					{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}"/>{/if}				
				</div>					
			</a>  
		</td>			
	</tr>
	{section name=i loop=$actionList}
		<tr>
			<td class="border_users_l border_users_b border_users_r"><input type="checkbox" name="checkLogbook[]" value="{$actionList[i]->id}"></td>			
			<td class="border_users_b border_users_r">
				<a {if $permissions.viewItem}href="?action=viewDetails&category=logbook&facilityID={$request.id}&id={$actionList[i]->id}"{/if}>
					<div style="width:100%;">
						{$actionList[i]->date}
					</div>
				</a>
			</td>
					
			<td class="border_users_b border_users_r">
				<a {if $permissions.viewItem}href="?action=viewDetails&category=logbook&facilityID={$request.id}&id={$actionList[i]->id}"{/if}>
					<div style="width:100%;">
						{$actionList[i]->type}
					</div>
				</a>
			</td>			
		</tr>
	{/section}
	
	{if $smarty.section.i.total ==0}
		<tr align = 'center'>						
			<td class="border_users_l border_users_b border_users_r" colspan='3'>
				No records in logbook
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