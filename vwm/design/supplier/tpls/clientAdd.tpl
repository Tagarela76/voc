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
	<form method='POST' id='formA' action='?action={$request.action}&category=clients'>
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
                    Company :
                </td>
                <td class="border_users_r border_users_b">

								{*NICE PRODUCT LIST*}	
								<select name="company_id" id="company_id" class="addInventory" onchange="getCompany_name();" title>
									{*<option selected="selected" >Select Product</option>*}
									{if $companies}				

											{section name=i loop=$companies}

												<option title="{$companies[i].name}" value='{$companies[i].id}' {if $productsArr[i].disabled}disabled="disabled"{/if}> {$companies[i].name}</option>

											{/section}
																			
									{else}
										<option value='0'> no companies </option>
									{/if}
								</select>	
								{literal}
									<script>
									function getCompany_name(){
										$('#company').attr('value', $('#company_id > option:selected').attr('title'));

									}
									</script>
								{/literal}
								{if $request.error eq "exist"}<span style="color: red; font-size: 14px;">discount for this company already exist!</span>{/if}									
                </td>
            </tr>				
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20">
                    Discount :
                </td>
                <td class="border_users_r border_users_b">

					<input type='text' name="discount" id="discount" value=''>
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
			<input type='button' class="button" value='Cancel' onclick="location.href='?action=browseCategory&category=sales&bookmark=clients'">
			
      	
            <input type='button' class="button" value='Save' onclick='check_amount();'>
			<input type="hidden" name="company" id="company"  value=""/>
                  
        </div>	
			 
    </form>
</div>
