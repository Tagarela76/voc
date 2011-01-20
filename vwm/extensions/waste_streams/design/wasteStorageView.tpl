<!-- </form> -->
{if $color eq "green"}
	{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
	{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
	{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

<form name="wasteStorage" action = "?action=deleteItem&category=wastestorage&facilityID={$request.id}" method = "post">
<!-- Start Cute Table Head -->
<div align="center" class="control_panel_padd">
        										<div class="control_panel" class="logbg" align="left">
           											 <div class="control_panel_tl">
                										<div class="control_panel_tr">
                    										<div class="control_panel_bl">
                       											 <div class="control_panel_br">										
																	<div class="control_panel_center">
																		<div class="controlCategoriesList" style="display:table;width:100%;">
																			<div style="display:table;" class="floatleft">
<!-- End Cute Table Head -->
<div style='padding-left:20px;padding-top:10px;padding-bottom:10px;'>			
		{if $request.tab eq 'active'}	
			<input type='button' class='button' id='add' value='Add' onclick="location.href='?action=addItem&category=wastestorage&facilityID={$request.id}'"/>					
			<input type='submit' class='button' id='empty' name="empty" value='Empty'/>
			<input type='text' name='dateEmpty' id='calendar'>
			&nbsp;&nbsp;&nbsp;&nbsp;
		{/if}
		<input type='submit' class='button' {if $request.tab eq 'active'}name='delete' value='Delete'{else}name='restore' value='Restore'{/if}/>
		{if $request.tab eq 'active'}	
			<input type='text' name='dateDeleted' id='calendar2'>
		{/if}
</div>

<!-- Start Footer Cute table -->
</div>
								<div class="button_float_right">
								&nbsp;
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Footer Cute table -->

<br />

{*PAGINATION*}
	{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}

<table class="users" align="center" cellpadding="0" cellspacing="0">
	<tr class="users_u_top_size users_top_green">	
		<td class="users_u_top_green" colspan="{if $request.tab eq 'active'}6{else}5{/if}">	
			<span >Waste Storage</span>	
		</td>	
		<td class="users_u_top_r_green" {if $show.docs}colspan="2"{/if}>
			&nbsp;	
		</td>	
	</tr>	
	
	<tr class="users_u_top_size users_top_lightgray" >	
		<td width="60"><span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:black'>All</a>/<a style='color:black' onclick="unCheckAll(this)" >None</a></span></td>	
		<td>
			<a style='color:black;' onclick='$("#sort").attr("value","{if $sort==1}2{else}1{/if}"); $("#sortForm").submit();'>
		        <div style='width:100%;  color:black;'>						
		            Name 		
					{if $sort==1 || $sort==2}<img src="{if $sort==1}images/asc2.gif{/if}{if $sort==2}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>
			</a>	
		</td>	
		<td>
			<a style='color:black;' onclick='$("#sort").attr("value","{if $sort==3}4{else}3{/if}"); $("#sortForm").submit();'>
			    <div style='width:100%;  color:black;'>						
			    	Capacity 
					{if $sort==3 || $sort==4}<img src="{if $sort==3}images/asc2.gif{/if}{if $sort==4}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a> 
		</td>	
		<td>
			<a style='color:black;' onclick='$("#sort").attr("value","{if $sort==5}6{else}5{/if}"); $("#sortForm").submit();'>
		    	<div style='width:100%;  color:black;'>						
		    		Density
					{if $sort==5 || $sort==6}<img src="{if $sort==5}images/asc2.gif{/if}{if $sort==6}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a>  
		</td>
		<td>
			<a style='color:black;' onclick='$("#sort").attr("value","{if $sort==7}8{else}7{/if}"); $("#sortForm").submit();'>
		    	<div style='width:100%;  color:black;'>						
		    		Max Period
					{if $sort==7 || $sort==8}<img src="{if $sort==7}images/asc2.gif{/if}{if $sort==8}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a>  
		</td>	
		<td>
			<a style='color:black;' onclick='$("#sort").attr("value","{if $sort==9}10{else}9{/if}"); $("#sortForm").submit();'>
		    	<div style='width:100%;  color:black;'>						
		    		Suitability
					{if $sort==9 || $sort==10}<img src="{if $sort==9}images/asc2.gif{/if}{if $sort==10}images/desc2.gif{/if}" alt=""/>{/if}				
				</div>					
			</a> 
		</td>
		{if $request.tab eq 'active'}	
		<td>Gauge</td>	
		{/if}
		{if $show.docs}
			<td>Document</td>
		{/if}	
	</tr>
	
	{section name=i loop=$data}
		
		{*VARIABLE FOR ILLUMINATING WRONG ROWS*}
		{if (($data[i]->current_usage) >= ($data[i]->capacity_volume)) || ($data[i]->days_left>$data[i]->max_period)}
			{assign value="invalid" var=valid}
		{else}
			{assign value="valid" var=valid}
		{/if}
		{*/VARIABLE FOR ILLUMINATING WRONG ROWS*}
		
		<tr {if $valid  eq "valid"}
 				class="hov_company"
			{else}			
			 	class="us_red"			
			{/if}>
			<td class="border_users_l border_users_b border_users_r"> 
				{if $valid  eq "valid"}
 					<span class="ok">&nbsp;</span>
				{else}			
			 		<span class="error">&nbsp;</span>			
				{/if}
				<input type='checkbox' name='checkWastestorage[]' value='{$data[i]->storage_id}' />
			</td>			
			<td class="border_users_b border_users_r">
				<a {if $permissions.viewItem}href="?action=viewDetails&category=wastestorage&facilityID={$request.id}&id={$data[i]->storage_id}"{/if}>
					<div style="width:100%;">						
						{$data[i]->name}											
					</div>
				</a>
			</td>
			<td class="border_users_b border_users_r">
				<a {if $permissions.viewItem}href="?action=viewDetails&category=wastestorage&facilityID={$request.id}&id={$data[i]->storage_id}"{/if}>
					<div style="width:100%;">
						{$data[i]->capacity_volume}({$unittypeObj->getNameByID($data[i]->volume_unittype)}){* / {$data[i]->capacity_weight}({$unittypeObj->getNameByID($data[i]->weight_unittype)})*}
					</div>
				</a>
			</td>			
			<td class="border_users_b border_users_r">
				<a {if $permissions.viewItem}href="?action=viewDetails&category=wastestorage&facilityID={$request.id}&id={$data[i]->storage_id}"{/if}>
					<div style="width:100%;">
						{$data[i]->density}({$data[i]->density_type})
					</div>
				</a>
			</td>
			<td class="border_users_b border_users_r">
				<a {if $permissions.viewItem}href="?action=viewDetails&category=wastestorage&facilityID={$request.id}&id={$data[i]->storage_id}"{/if}>
					<div style="width:100%;">
						{$data[i]->max_period}
					</div>
				</a>
			</td>
			<td class="border_users_b border_users_r">
				<a {if $permissions.viewItem}href="?action=viewDetails&category=wastestorage&facilityID={$request.id}&id={$data[i]->storage_id}"{/if}>
					<div style="width:100%;">
						{$wasteStreamObj->getNameById($data[i]->suitability)}
					</div>
				</a>
			</td>
			{if $request.tab eq 'active'}	
			<td class="border_users_b border_users_r">
				<a {if $permissions.viewItem}href="?action=viewDetails&category=wastestorage&facilityID={$request.id}&id={$data[i]->storage_id}"{/if}>
					<div style="width:100%;">
						{include file="tpls:waste_streams/design/indicator.tpl" value=$data[i]->current_usage limit=$data[i]->capacity_volume} {$data[i]->current_usage}/{$data[i]->capacity_volume}
					</div>
				</a>
			</td>
			{/if}
			{if $show.docs}
				<td class="border_users_b border_users_r">
					{*<a {if $permissions.viewItem}href="?action=viewDetails&category=wastestorage&facilityID={$request.id}&id={$data[i]->storage_id}"{/if}>*}
						<div style="width:100%;">
						{if $data[i]->document_id eq '0' || $data[i]->document_id eq null}
							<a {if $permissions.viewItem}href="?action=viewDetails&category=wastestorage&facilityID={$request.id}&id={$data[i]->storage_id}"{/if}>
								<i>No document</i>
							</a>
						{else}
							{assign value=$data[i]->document_id var=doc_id}
							<div class="category_documents">
								<div class="category_link"><p><a href ="{$docs[$doc_id].link}" title = "{$docs[$doc_id].description}">{$docs[$doc_id].name}</a></p></div>
							</div>
						{/if}
						</div>
					{*</a>*}
				</td>
			{/if}
		</tr>
	{/section}			
</table>
<div align="center"><div class="users_bottom"><div class="users_u_bottom"><div class="users_u_bottom_r"></div></div></div></div>
{*PAGINATION*}
	{include file="tpls:tpls/pagination.tpl"}
{*/PAGINATION*}
		
</form>
<script type="text/javascript" src="modules/js/checkBoxes.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js"></script>
{literal}
<script type="text/javascript">
	$(document).ready(function()
	{
		date = new Date();
		var month = date.getMonth() + 1;
		var day = date.getDate();
		var year = date.getFullYear();
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