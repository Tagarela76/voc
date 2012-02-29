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

<div style="padding:7px;">
	<form method='POST' action='?action={$request.action}&category=facility&id={$request.id}&bookmark=inventory&tab={$request.tab}'>
        <table class="users" align="center" cellpadding="0" cellspacing="0"	>
            <tr class="users_u_top_size users_top">
                <td class="users_u_top" width="30%">
                    <span>Editing settings</span>
				</td>
                <td class="users_u_top_r" >

				</td>				
            </tr>
{if ($access == 3 || $access == 0) && $cuser}
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20" colspan="2">
                    <input type="checkbox" id="companylvl" /> <label for="companylvl">Company Level </label>
                </td>

            </tr>
            <tr id="companylvlview" style="display: none;">
                <td class="border_users_r border_users_l border_users_b" height="20" colspan="2">
					 <div style="padding-left: 15px;" id="company">
					{foreach from=$cuser item=user}
						<input type="checkbox" id="{$user.user_id}" value="{$user.user_id}" name="cuser[]" {foreach from=$emails item=email}{if $email.user_id == $user.user_id}checked{/if}{/foreach}/> <label for="{$user.user_id}">{$user.username} ({$user.email})</label><br/>	
					{/foreach}	
					 </div>	                    
                </td>

            </tr>			
{literal}
<script>
	$("#companylvl").click(function () {
	$("#companylvlview").slideToggle("slow");
	});
		
function countCChecked() {
	var n = $("#company > input:checked").length;
	if (n > 0 ){
		$("#companylvlview").show("slow");
		$("#companylvl").attr("checked","checked");
	}else{
		$("#companylvlview").hide("slow");
		$("#companylvl").attr("checked","");
	}		
}
countCChecked();
$("#company > input").click(countCChecked);		
</script>
{/literal}				
{/if}

{if $access != 2 && $fuser}
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20" colspan="2">
                    <input type="checkbox" id="facilitylvl" onclick=""/> <label for="facilitylvl">Facility Level</label>
                </td>

            </tr>
            <tr id="facilitylvlview" style="display: none;">
                <td class="border_users_r border_users_l border_users_b" height="20" colspan="2">
					 <div style="padding-left: 15px;" id="facility">
					{foreach from=$fuser item=user}
						<input type="checkbox" id="{$user.user_id}" name="fuser[]" value="{$user.user_id}" {foreach from=$emails item=email}{if $email.user_id == $user.user_id}checked{/if}{/foreach}/> <label for="{$user.user_id}">{$user.username} ({$user.email})</label><br/> 	
					{/foreach}	
					 </div>	
                </td>

            </tr>			
{literal}
<script>
$("#facilitylvl").click(function () {
	$("#facilitylvlview").slideToggle("slow");
});
		
function countFChecked() {
	var n = $("#facility > input:checked").length;
	if (n > 0 ){
		$("#facilitylvlview").show("slow");
		$("#facilitylvl").attr("checked","checked");
	}else{
		$("#facilitylvlview").hide("slow");
		$("#facilitylvl").attr("checked","");
	}		
}
countFChecked();
$("#facility > input").click(countFChecked);		
</script>
{/literal}				
{/if}				

{if $duser}
            <tr>
                <td class="border_users_r border_users_l border_users_b" height="20" colspan="2">
                    <input type="checkbox" id="departmentlvl" /> <label for="departmentlvl">Department Level </label>
                </td>

            </tr>
            <tr id="departmentlvlview" style="display: none;">
                <td class="border_users_r border_users_l border_users_b" height="20" colspan="2">
					 <div style="padding-left: 15px;" id="department">
					{foreach from=$duser item=userarr}
						{foreach from=$userarr item=user}
							<input type="checkbox" id="{$user.user_id}" name="duser[]" value="{$user.user_id}" {foreach from=$emails item=email}{if $email.user_id == $user.user_id}checked{/if}{/foreach}/> <label for="{$user.user_id}">{$user.username} ({$user.email})</label><br/>	
						{/foreach}
					{/foreach}	
					 </div>	
                </td>

            </tr>			
{literal}
<script>
	$("#departmentlvl").click(function () {
	$("#departmentlvlview").slideToggle("slow");
	});
		
function countDChecked() {
	var n = $("#department > input:checked").length;
	if (n > 0 ){
		$("#departmentlvlview").show("slow");
		$("#departmentlvl").attr("checked","checked");
	}else{
		$("#departmentlvlview").hide("slow");
		$("#departmentlvl").attr("checked","");
	}		
}
countDChecked();
$("#department > input").click(countDChecked);		
</script>
{/literal}				
{/if}			
            <tr>
                <td class="users_u_bottom "bgcolor="" height="20">
                </td>
                <td class="users_u_bottom_r"bgcolor="" height="20">
                </td>				

            </tr>
        </table>
					
        <div align="right" class="margin7">

				<input type='button' class="button" value='Cancel' onclick="location.href='?action=browseCategory&category=facility&id={$request.id}&bookmark=inventory&tab=settings'">
			
      	
            <input type='submit' class="button" value='Save'>
			<input type='hidden' name="facilityID" value='{$request.id}'>
			<input type='hidden' name="companyID" value='{$companyID}'>

        </div>									
    </form>
</div>
