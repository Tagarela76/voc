
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
{*PAGINATION*}
		{include file="tpls:tpls/pagination.tpl"}
	{*/PAGINATION*}
	<table  class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
		<thead>	
		<tr  class="users_u_top_size users_top_blue">
			<td  class="users_u_top_blue"  width="5%" ><span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span></td>
			<td  class="">ID Number</td>
			<td  class="">Company</td>
			<td  class="">Contact</td>
			<td  class="">Phone</td>
			<td  class="">Cell/mobile phone</td>
			<td  class="">Email</td>
			<td  class="">Website</td>
			<td  class="">Mailing address</td>
			<td  class="">Fax</td>
<!--			<td  class="">Title</td> -->
			
			<td>Industry</td>
			<td>Country</td>
			<td>State</td>
			<td  class="">City</td>
			<td>Zip Code</td>
			<!--  <td>Affiliations</td>
			
			<td  class="">Government Agencies</td>-->
			<td  class="users_u_top_r_blue">Comments</td>				
		</tr>
		</thead>
		
		<tbody>
{if $itemsCount > 0}						 

{*BEGIN LIST*}				
{section name=i loop=$contacts}	
	<tr class="hov_company">
			<td class="border_users_l border_users_b">
				<input type="checkbox"  value="{$contacts[i]->id}" name="item_{$smarty.section.i.index}" onclick="return CheckCB(this);">
			</td>
			
			<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$contacts[i]->id}</div ></a>
			</td>
			
            <td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$contacts[i]->company}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$contacts[i]->contact}{if $contacts[i]->title}, {$contacts[i]->title}{/if}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$contacts[i]->phone}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$contacts[i]->cellphone}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$contacts[i]->email}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$contacts[i]->website}</div ></a>
			</td>			
			
			<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$contacts[i]->mail}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$contacts[i]->fax}</div ></a>
			</td>
<!--			
			<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}"><div style="width:100%;">{$contacts[i]->title}</div ></a>
			</td>
-->			
			<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$contacts[i]->industry}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$contacts[i]->country_name}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$contacts[i]->state_name}</div ></a>
			</td>
			
			<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$contacts[i]->city}</div ></a>
			</td>	
			
			<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}{if $page }&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$contacts[i]->zip_code}</div ></a>
			</td>
			
			<!--<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}"><div style="width:100%;">{$contacts[i]->affiliations}</div ></a>
			</td>
			
			
			
			<td class="border_users_b border_users_l" >
				<a href="{$contacts[i]->viewDetailsUrl}"><div style="width:100%;">{$contacts[i]->government_agencies}</div ></a>
			</td>-->
			
			
			
			<td class="border_users_b border_users_l border_users_r" >
				<a href="{$contacts[i]->viewDetailsUrl}{if $page}&page={$page}{/if}{if $request.subBookmark}&subBookmark={$request.subBookmark}{/if}"><div style="width:100%;">{$contacts[i]->getCommentsHTML()}</div ></a>
			</td>				
	</tr>
{/section}		 
{section name=i loop=$category}						
						
		 <tr class="hov_company">
			<td class="border_users_l border_users_b">
				<input type="checkbox"  value="{$category[i].apmethod_id}" name="item_{$smarty.section.i.index}" onclick="return CheckCB(this);">
			</td>
            <td class="border_users_b border_users_l" >
				<a href="{$category[i].url}"><div style="width:100%;">{$category[i].apmethod_id}</div ></a>
			</td>
            <td class="border_users_b border_users_l border_users_r">
				<a href="{$category[i].url}"><div style="width:100%;">{$category[i].description}</div ></a>
			</td>				
		</tr>
{/section}								
{*END LIST*}

{else}

{*BEGIN	EMPTY LIST*}
		<tr class="">
		    <td  class="border_users_l border_users_r" style='text-align:center; vertical-align:middle;' colspan="14" >No Contacts</td>
		</tr>
{*END	EMPTY LIST*}

{/if}
		</tbody>
		
		<tfoot>
		<tr>
			  <td class="users_u_bottom"></td>
        	  <td colspan="15" height="30" class="users_u_bottom_r"></td>
		</tr>	
		</tfoot>		
	</table>
	{*PAGINATION*}
		{include file="tpls:tpls/pagination.tpl"}
	{*/PAGINATION*}
</div>


</form>