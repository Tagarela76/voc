<script type="text/javascript">
	var accessLevel='department';
	var logging= true;
</script>
{include file="tpls:tpls/pagination.tpl"}

	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue2" && $itemsCount == 0}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
<div class="padd7" align="center">		
<table class="users addCompany" cellpadding="0" cellspacing="0" align="center">
			<tr class="users_top_red users_u_top_size">
				
        		<td  class="users_u_top_red">
					<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
						<div style='width:100%;  color:white;'>
							Log ID
							{if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}
						</div>
					</a>
        		</td>
				<td>
					<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
						<div style='width:100%;  color:white;'>
							User ID
							{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}
						</div>
					</a>					
        			
        		</td>
				
				<td>
					<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==9}10{else}9{/if}"); $("#sortForm").submit();'>
						<div style='width:100%;  color:white;'>
							User Name
							{if $sort==9 || $sort==10}<img src="{if $sort==9}images/asc2.gif{/if}{if $sort==10}images/desc2.gif{/if}" alt=""/>{/if}
						</div>
					</a>					
        			
        		</td>				
				<td>
					<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==5}6{else}5{/if}"); $("#sortForm").submit();'>
						<div style='width:100%;  color:white;'>
							Action
							{if $sort==5 || $sort==6}<img src="{if $sort==5}images/asc2.gif{/if}{if $sort==6}images/desc2.gif{/if}" alt=""/>{/if}
						</div>
					</a>					
        			
        		</td>
				<td class="users_u_top_r_red">
					<a style='color:white;' onclick='$("#sort").attr("value","{if $sort==7}8{else}7{/if}"); $("#sortForm").submit();'>
						<div style='width:100%;  color:white;'>
							Date
							{if $sort==7 || $sort==8}<img src="{if $sort==7}images/asc2.gif{/if}{if $sort==8}images/desc2.gif{/if}" alt=""/>{/if}
						</div>
					</a>					
        			
        		</td>

    		</tr>
			{section loop=$logList name=i}
    		<tr class="border_users_b border_users_r">
        		<td  class="border_users_l">
				<a href="{$logList[i].url}" class="id_accessory1">
					<div>
						{$logList[i].log_id}
					</div>
				</a>					
        			
        		</td>
				<td>
				<a href="{$logList[i].url}" class="id_accessory1">
					<div>
						{$logList[i].user_id}
					</div>
				</a>					
				<td>
				<a href="{$logList[i].url}" class="id_accessory1">
					<div>
						{$logList[i].username}
					</div>
				</a>        			
        		</td>
				<td>
				<a href="{$logList[i].url}" class="">
					<div>
						{$logList[i].action}
					</div>
				</a>					

        		</td>
				<td>
				<a href="{$logList[i].url}" class="id_accessory1">
					<div>
						{$logList[i].date}
					</div>
				</a>					

        		</td>
    		</tr>
			{/section}
			{if $smarty.section.i.total ==0}
				<tr align = 'center'>						
					<td class="border_users_l border_users_b border_users_r" colspan='4'>
						No records
					</td>						
				</tr>
			{/if}
			<tr>
				<td height="20" class="users_u_bottom">&nbsp;</td>
				<td height="20" class="users_u_bottom_r" colspan="3">&nbsp;</td>
			</tr>
</table>
</div>
{include file="tpls:tpls/pagination.tpl"}