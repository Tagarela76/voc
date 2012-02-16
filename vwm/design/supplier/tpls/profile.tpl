<div style="padding:7px;">
	<form method='POST' id='formP' action='?action={$request.action}&category=profile'>
        <table class="users" align="center" cellpadding="0" cellspacing="0">
            <tr class="users_u_top_size users_top">
                <td class="users_u_top" width="30%">
                    <span>{if $request.action eq "edit"}Editing client discount{else} Editing your profile {/if}</span>
                </td>
                <td class="users_u_top_r">
                </td>
            </tr>
			
            <tr>
               <td class="border_users_r border_users_l border_users_b" height="20">
                    Email :
                </td>
                <td class="border_users_r border_users_b">

					<input type='text' name="email" id="email" value='{$supplier.email}'><span id='alert' style="color: red; font-size: 14px;"></span>
				
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
			{if $request.error eq "false"}<span style="color: red; font-size: 14px;"> Error!</span>{/if}	
			{literal}
			<script type="text/javascript">
			function mail (str) { return /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/.test(str); }
			function check(){	
				var form = $("#formP");
				var	str = "Type right E-mail!";
				var elem = $("#email").val()
				if (elem.length == 0 || !mail(elem) ){
					document.getElementById("alert").innerHTML = str;
				}else{
					form.submit();
				}
			}	 
			</script>
			{/literal}
            <input type='button' class="button" value='Save' onclick="check();">

                  
        </div>	
			 
    </form>
</div>
