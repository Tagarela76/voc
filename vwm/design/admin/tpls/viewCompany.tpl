{if $color eq "green"}
    {include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}

{if $color eq "orange"}
    {include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}

{if $color eq "blue"}
    {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

<form method="POST" action='admin.php?action=edit&category=issue&id={$request.id}'>			  
    <table class="users" cellpadding="0" cellspacing="0" align="center">
        <tr class="users_top_yellowgreen users_u_top_size" >
            <td class="users_u_top_yellowgreen" width="15%">View details</td>
            <td class="users_u_top_r_yellowgreen" ></td>				
        </tr>					

        <tr height="20px">
            <td class="border_users_l border_users_b border_users_r" >
                ID
            </td>
            <td class="border_users_b border_users_r">
                <div align="" >&nbsp;{$companyDetails.company_id|escape}</div>
            </td>
        </tr>

        <tr height="20px">
            <td class="border_users_b border_users_l border_users_r">
                Name
            </td>
            <td class="border_users_b border_users_r">
                <div align="left" >	&nbsp;{$companyDetails.name|escape}</div>
            </td>
        </tr>

        <tr height="20px">
            <td class="border_users_b border_users_l border_users_r">
                Address
            </td>
            <td class="border_users_b border_users_r">
                <div align="left" >	&nbsp;{$companyDetails.address|escape}</div>
            </td>
        </tr>

        <tr height="20px">
            <td class="border_users_b border_users_l border_users_r">
                Contact
            </td>
            <td class="border_users_b border_users_r">
                <div align="left" >	&nbsp;{$companyDetails.contact|escape}</div>
            </td>
        </tr>

        <tr height="20px">
            <td class="border_users_b border_users_l border_users_r">
                Phone
            </td>
            <td class="border_users_b border_users_r">
                <div align="left" >	&nbsp;{$companyDetails.phone|phone_format|escape}</div>
            </td>
        </tr>
        <tr>
            <td class="border_users_b border_users_l border_users_r">
                Industry Types
            </td>
            <td class="border_users_l border_users_b border_users_r">
                <div align="left" style="float: left;">	
                    {$industrytypes|escape}
                </div>												
            </td>
        </tr>
        <tr>
            <td   height="20" class="users_u_bottom">
            </td> 
            <td class="users_u_bottom_r">
            </td>
        </tr>

    </table>	
    <div align="center">
        <div align="right" class="buttonpadd">
            <input type='submit' name='save' class="button" value='Save'>
        </div></div>

</div>
</form>	