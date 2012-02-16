{*ajax-preloader*}
<div style="height:16px;text-align:center;">
	<div id="preloader" style="display:none">
		<img src='images/ajax-loader.gif'>
	</div>
</div>

{if $color eq "green"}
{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}
{literal}
<script>
									
$(document).ready(function() {
	$('#company').attr('value', $('#company_id > option:selected').attr('title'));
});
</script>
{/literal}
<div style="padding:7px;">
	{*if $parentCategory == 'facility'}
    <form method='POST' action='?action={$request.action}&category=inventory&id={$request.id}&facilityID={$request.facilityID}&tab={$inventory->getType()}'>    	
    {else $parentCategory == 'department'}
	<form method='POST' action='?action={$request.action}&category=inventory&id={$request.id}&departmentID={$request.departmentID}&tab={$inventory->getType()}'>
	{/if*}
	<form method='POST' id='formP' action='?action={$request.action}&category=products&id={$request.id}&supplierID={$request.supplierID}'>
        <table class="users" align="center" cellpadding="0" cellspacing="0">
            <tr class="users_u_top_size users_top">
                <td class="users_u_top" width="30%">
                    <span>{if $request.action eq "addItem"}Adding for a new client{else}Editing client{/if}</span>
                </td>
                <td class="users_u_top_r">
                </td>
            </tr>
			
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Product :
                </td>
                <td class="border_users_r border_users_b">

{$product.product_nr}									
                </td>
            </tr>				
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Price per gal:
                </td>
                <td class="border_users_r border_users_b">

					<input type='text' name="price" id="price" value='{$product.price}'> $
						{literal}
							<script type="text/javascript">
								$("#price").numeric();
								function check_price(){
									var amount = $("#price").val();
									var form = $("#formP");
									if(amount == "" && amount == 0 && amount == "0.00") {

										$("#error_price .error_text").text("Type price!");
										$("#error_price").css('display','inline');
										$("#price").focus();
										$("#price").select();
										return false;
									}else{
										form.submit();
									}
								}		
							</script>	
						{/literal}
                    <div class="error_price" id="error_price" style="display: none;">
                        <span id="" class="error_text">Error!</span>
                    </div>						
                </td>
            </tr>
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Unit Type :
                </td>
                <td class="border_users_r border_users_b">

{$unittype.description}	
                    <div align="left">

									<select name="selectUnittypeClass" id="selectUnittypeClass" onchange="getUnittypes(document.getElementById('selectUnittypeClass'))" >									 										
										{section name=j loop=$typeEx}
										{if 'USALiquid' eq $typeEx[j]}<option value='USALiquid' {if 'USALiquid' eq $unitTypeClass}selected="selected"{/if}>USA liquid</option>{/if}
										{if 'USADry' eq $typeEx[j]}<option value='USADry' {if 'USADry' eq $unitTypeClass}selected="selected"{/if}>USA dry</option>{/if}
										{if 'USAWght' eq $typeEx[j]}<option value='USAWght' {if 'USAWght' eq $unitTypeClass}selected="selected"{/if}>USA weight</option>{/if}										
										{if 'MetricVlm' eq $typeEx[j]}<option value='MetricVlm' {if 'MetricVlm' eq $unitTypeClass}selected="selected"{/if}>Metric volume</option>{/if}
										{if 'MetricWght' eq $typeEx[j]}<option value='MetricWght' {if 'MetricWght' eq $unitTypeClass}selected="selected"{/if}>Metric weight</option>{/if}		
										{/section}
							
									</select>&nbsp; 
					
									<select name="selectUnittype" id="selectUnittype" >	
										{section name=i loop=$unittype}	
											<option value='{$unittype[i].unittype_id}' {if $unittype[i].unittype_id eq $product.unittype}selected="selected"{/if}>{$unittype[i].description}</option>										
										{/section}

									</select>							
                    </div>
                </td>
            </tr>	

		
			
            <tr>
                <td class="users_u_bottom">
                </td>
                <td bgcolor="" height="20" class="users_u_bottom_r">
                </td>
            </tr>
        </table>
					
        <div align="right" class="margin7">
			<input type='button' class="button" value='Cancel' onclick="location.href='?action=browseCategory&category=sales&bookmark=products'">
			
      	
            <input type='button' class="button" value='Save' onclick='check_price();'>

			<input type="hidden" name="price_id" id="price_id"  value="{$product.price_id}"/>
                  
        </div>	
			 
    </form>
</div>
