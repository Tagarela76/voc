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
	<form method='POST' id='formA' action='?action={$request.action}&category=clients{if $client.discount_id}&discountID={$client.discount_id}{/if}&facilityID={$request.facilityID}{if $request.productID}&productID={$request.productID}{/if}{if $request.jobberID}&jobberID={$request.jobberID}{/if}{if $request.supplierID}&supplierID={$request.supplierID}{/if}'>
        <table class="users" align="center" cellpadding="0" cellspacing="0">
            <tr class="users_u_top_size users_top">
                <td class="users_u_top" width="30%">
                    <span>{if $request.action eq "edit"}Editing client discount{else}Editing client discount for separate product{/if}</span>
                </td>
                <td class="users_u_top_r">
                </td>
            </tr>
			
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Company :
                </td>
                <td class="border_users_r border_users_b">
					{$client.name} > {$client.fname}								
                </td>
            </tr>				
{if $request.action == 'editPDiscount'}				

            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Product Name :
                </td>
                <td class="border_users_r border_users_b">
					{$client.product_nr}						
                </td>
            </tr>
	
{/if}
            <tr>
               <td class="border_users_r border_users_l border_users_b" height="20">
                    Discount :
                </td>
                <td class="border_users_r border_users_b">

					<input type='text' name="discount" id="discount" value='{$client.discount}'> %
						{literal}
							<script type="text/javascript">
								$("#discount").numeric();
								function check_amount(){
									var amount = $("#discount").val();
									var form = $("#formA");
									if(amount == "" && amount == 0) {

										$("#error_discount .error_text").text("Type discount!");
										$("#error_discount").css('display','inline');
										$("#discount").focus();
										$("#discount").select();
										return false;
									}else{
										form.submit();
									}
								}		
							</script>	
						{/literal}
                    <div class="error_discount" id="error_discount" style="display: none;">
                        <span id="" class="error_text">Error!</span>
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
			
			<input type='button' class="button" value='Cancel' onclick="location.href='?action=viewDetails&category=clients&facilityID={$client.facility_id}{if $request.jobberID}&jobberID={$request.jobberID}{/if}{if $request.supplierID}&supplierID={$request.supplierID}{/if}'">
			
      	
            <input type='button' class="button" value='Save' onclick='check_amount();'>
			<input type="hidden" name="discount_id" id="discount_id"  value="{$client.discount_id}"/>
			<input type="hidden" name="facilityID" id="facilityID"  value="{$client.facility_id}"/>
			<input type="hidden" name="companyID" id="companyID"  value="{$client.company_id}"/>
			<input type="hidden" name="supplier_id" id="supplier_id"  value="{$client.supplier_id}"/>
			<input type="hidden" name="jobber_id" id="supplier_id"  value="{$request.jobber_id}"/>
			<input type="hidden" name="product_id" id="product_id"  value="{if $client.product_id}{$client.product_id}{/if}" />

                  
        </div>	
			 
    </form>
</div>
