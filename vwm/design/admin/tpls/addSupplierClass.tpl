	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
<div style="padding:7px;">
	<form method="post" action="admin.php?action={$request.action}&category=supplier{if $request.action neq "addItem"}&id={$request.id}{/if}" name="editingsup" id="editingsup">
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_u_top_size users_top" >
				<td class="users_u_top" width="27%" height="30" >
					<span >{if $request.action eq "addItem"}Adding for a new supplier{else}Editing supplier{/if}</span>
				</td>
				<td class="users_u_top_r" >
					&nbsp;
				</td>					
			</tr>

			<tr height="10px">
		
							<td class="border_users_l border_users_b" height="20">
								Supplier Description:
							</td>
							<td class="border_users_l border_users_b border_users_r">
							<div align="left" >	<input type='text' name='supplier_desc' value='{$data.supplier_desc}{$data.description}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.description eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}
						    {elseif $validStatus.description eq 'alredyExist'}
								<div style="width:220px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
							    <font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Entered name is alredy in use!</font></div>
							{/if}
						    {/if}
								
							</td>
					
						</tr>
						
						<tr height="10px">
		
							<td class="border_users_l border_users_b" height="20">
								Contact Person:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	<input type='text' name='contact' value='{$data.contact}'></div>
								
							{if $validStatus.summary eq 'false'}
							{if $validStatus.contact eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}						    
							{/if}
						    {/if}													
							</td>
					
						</tr>
						<tr height="10px">
		
							<td class="border_users_l border_users_b" height="20">
								Phone:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	<input type='text' name='phone' value='{$data.phone}'></div>
							
							{if $validStatus.summary eq 'false'}
							{if $validStatus.phone eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}						    
							{/if}
						    {/if}																
							</td>
					
						</tr>
						
						<tr height="10px">
		
							<td class="border_users_l border_users_b" height="20">
								Address:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >	<input type='text' name='address' value='{$data.address}'></div>
			
							{if $validStatus.summary eq 'false'}
							{if $validStatus.address eq 'failed'}
			     				{*ERROR*}					
								<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
								<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
							    {*/ERROR*}						    
							{/if}
						    {/if}																
							</td>
					
						</tr>
						
						<tr height="10px">
		
							<td class="border_users_l border_users_b" height="20">
								Country:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div align="left" >
									<select name="country" id="selectCountry">
			                            {section name=i loop=$country}
			                            	<option value='{$country[i].id}'> {$country[i].name}  </option>
			                            {/section}
			                        </select>			                        
								</div>																					
							</td>					
						</tr>	
{*duplicate SUPPLIER*}						
						<tr height="10px">

							<td class="border_users_l border_users_b" height="20">
								Similar Suppliers:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div id="supplierList" align="left" >	
									{foreach from=$SuppliersByOrigin item=supplier name=fooList}
										{if $smarty.foreach.fooList.index < $SuppliersByOrigin|@count-1}
											&nbsp;{$supplier.supplier},
										{else}
											&nbsp;{$supplier.supplier}
										{/if}	
									{/foreach}
								</div>		
								<div>							
									&nbsp;<a href="#" onclick="$('#supplierPopup').dialog('open');return false;">edit</a>
								</div>
								<div id="hiddenSuppliers">
									{foreach from=$SuppliersByOrigin item=supplierA key=k name=foo}
										<input type="hidden" name="supplier_{$smarty.foreach.foo.index}" value="{$supplierA.supplier_id}">
									{/foreach}
								</div>
							</td>					
						</tr>	

						
{*////////////////////////*}						
												
						<tr>
             				 <td height="20" class="users_u_bottom">
             	 				&nbsp;
                			 </td>
                			 <td height="20" class="users_u_bottom_r">
                 				&nbsp;
                 			</td>
           				</tr>	
			</table>
			<br>
			<div align="right">
				<input type='submit' name='save' class="button" value='Save'>
				<input type='button' name='cancel' class="button" value='Cancel' 
					{if $request.action=='edit'} onclick='location.href="admin.php?action=viewDetails&category=supplier&id={$request.id}"'{/if}
					{if $request.action=='addItem'} onclick='location.href="admin.php?action=browseCategory&category=tables&bookmark=supplier"'{/if}>
				<span style="padding-right:50">&nbsp;</span>
			</div>		
</form>
</div>	

{literal}
	<script type='text/javascript'>
		$(function(){			
			if ({/literal}'{$data.country_id}'{literal}!='')
			{
				$('#selectCountry option[value ={/literal}{$data.country_id}{literal}]').attr('selected',true);
			}
			else
			{
				$('#selectCountry option[value =215]').attr('selected',true);
			}
			
		});	
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

{*SELECT_INDUSTRY_TYPES_CLASS_POPUP*}	
<div id="supplierPopup" title="Choose companies" style="background-color:#e3e9f8; padding:25px; font-size:150%; text-align:center;display:none;">		
		 <table id="supplierListPP" width="750px" cellpadding="0" cellspacing="0" class="popup_table" align="center" >		
			<tr>			
				 <td class="control_list" colspan="2" style="border-bottom:0px solid #fff;padding-left:0px">						
					Select: 
					<a onclick="CheckAll(this)" name="allTypesClasses" class="id_company1" >All</a>									
				 	/
					<a onclick="unCheckAll(this)" name="allTypesClasses" class="id_company1">None</a>										
				</td>
			</tr>		
					
			<tr class="table_popup_rule">
				<td>
					Select
				</td>
				<td>
					Supplier Name
				</td>
			</tr>
			
			{foreach from=$supplierList item=supplier key=k}
				<tr>
					<td align="center" style="width:150px">				
						<input type="checkbox"  value="{$supplier.supplier_id}"
							   {foreach from=$SuppliersByOrigin item=supplierO key=j}
								   {if $supplier.supplier_id eq $supplierO.supplier_id} checked {/if}
							   {/foreach}
						/>
					</td>
					<td id="category_{$supplier.supplier_id}">
						{$supplier.supplier_desc}&nbsp;
					</td>
				</tr>	
				<tr>
					<td colspan="2"></td>
				</tr>
			{/foreach}
		</table>	
</div>