{if $edit == 'editItem'}

{*EditItem-has-his-own-interface*}

<div style="padding:7px;">

<form action="admin.php?action=vps&vpsAction=confirmEdit&itemID={$bookmarkType}" method="post">

        <table class="users" align="center" cellpadding="0" cellspacing="0">
            <tr class="users_top_yellowgreen" >
                <td class="users_u_top_red" height="27" colspan="2">
                    <span >
                    {*--header-constructor--*}
                        Are you sure you want to change discount ?                 
                    {*----------------------*}
                    </span>
                </td>
                <td class="users_u_top_r_red">
                </td>
            </tr>
            
            <tr  bgcolor="#e3e3e3">
                <td class="border_users_l border_users_b" width="20%">&nbsp;</td>                                       
                <td class="border_users_l border_users_b border_users_r" width="40%">From</td>
                <td class="border_users_r border_users_b">To</td>                               
            </tr>               
                    <tr height="20" class="hov_company">
                        <td class="border_users_l border_users_b border_users_r">
                            ID
                        </td>
                        <td class="border_users_r border_users_b">
                            {$from.id}&nbsp;
                        </td>
                        <td class="border_users_r border_users_b">
                            {$to.id}&nbsp;
                            <input type="hidden" name="customerID" value="{$to.id}">
                        </td>
                    </tr>
                    <tr height="20" class="hov_company">
                        <td class="border_users_l border_users_b border_users_r">
                            Name
                        </td>
                        <td class="border_users_r border_users_b">
                            {$from.name}&nbsp;
                        </td>
                        <td class="border_users_r border_users_b">
                            {$to.name}&nbsp;
                            <input type="hidden" name="name" value="{$to.name}">
                        </td>
                    </tr>
                    <tr height="20" class="hov_company">
                        <td class="border_users_l border_users_b border_users_r">
                            Current Billing Plan
                        </td>
                        <td class="border_users_r border_users_b">
                            {$from.billingPlan.bplimit}{if $from.billingPlan.bplimit eq 1} source{else} sources{/if} {$from.billingPlan.months_count}{if $from.billingPlan.months_count eq 1} month{else} months{/if} {$from.billingPlan.type}&nbsp;
                        </td>
                        <td class="border_users_r border_users_b">
                            {$to.billingPlan.bplimit}{if $to.billingPlan.bplimit eq 1} source{else} sources{/if} {$to.billingPlan.months_count}{if $to.billingPlan.months_count eq 1} month{else} months{/if} {$to.billingPlan.type}&nbsp;
                        </td>
                    </tr>
                    <tr height="20" class="hov_company">
                        <td class="border_users_l border_users_b border_users_r">
                            Total Charge
                        </td>
                        <td class="border_users_r border_users_b">                          
                            $ {$from.billingPlan.total_charge}                            
                        </td>
                        <td class="border_users_r border_users_b">
                            $ {$to.billingPlan.total_charge}
                        </td>
                    </tr>
                    <tr height="20" class="hov_company">
                        <td class="border_users_l border_users_b border_users_r">
                            Time with us (months)
                        </td>
                        <td class="border_users_r border_users_b">                          
                            {$from.time_with_us}                         
                        </td>
                        <td class="border_users_r border_users_b">
                            {$to.time_with_us}
                        </td>
                    </tr>
                    <tr height="20" class="hov_company">
                        <td class="border_users_l border_users_b border_users_r">
                            Discount
                        </td>
                        <td class="border_users_r border_users_b">
                            {if $from.discount > 0}-{/if}{$from.discount}%
                        </td>
                        <td class="border_users_r border_users_b">
                            {if $to.discount > 0}-{/if}{$to.discount}%
                            <input type="hidden" name="discount" value="{$to.discount}">
                        </td>
                    </tr>
                    
                    <tr>
                        <td  height="25" class="users_u_bottom">                            
                        </td>
                        <td height="25" colspan="2" class="users_u_bottom_r">                           
                        </td>
                    </tr>                                                           
        </table>
        <br>
        <table width="100%">
            <tr>
                <td width="80%">
                </td>
                <td>
                    <input type="submit" value="Yes">                   
                </td>
                <td>
                    <input type="button" value="No" onClick="location.href='admin.php?action=vps&vpsAction=browseCategory&itemID=discounts'">
                </td>
            </tr>
        </table>
        
</form>
     
{else}

{*shadow_table*}    
                 <table cellspacing="0" cellpadding="0" align="center" width="100%">
                         <tr>
                               <td valign="top" class="report_uploader_t_l_green"></td>
                               <td valign="top" class="report_uploader_t_green"></td>
                               <td valign="top" class="report_uploader_t_r_green"></td>
                        </tr>
                          <tr>
                               <td valign="top" class="report_uploader_l_green"></td>
                               <td valign="top" class="report_uploader_c_green">
               {*shadow_table*}

{if $edit == 'showEdit'}
<form action="admin.php?action=vps&vpsAction=editItem&itemID={$bookmarkType}" method="post">
        <table border="0"  width="100%"  cellspacing="0" cellpadding="0" >      
            <tr><td colspan=2><b>{$customerDetails.name}</b></td></tr>
            <tr><td colspan=2>{$customerDetails.address}</td></tr>
            <tr><td colspan=2>{$customerDetails.contact}</td></tr>
            <tr><td colspan=2>&nbsp;</td></tr>
            <tr><td colspan=2>Current Billing Plan: {$customerDetails.billingPlan.bplimit} sources {$customerDetails.billingPlan.months_count} months {$customerDetails.billingPlan.type}</td></tr>
            <tr><td colspan=2>Date of trial period end: {$customerDetails.trial_end_date} ({$customerDetails.time_with_us} months)</td></tr>
            <tr><td colspan=2>Total Charge: $ {$customerDetails.billingPlan.total_charge}</td></tr>
            <tr><td colspan=2>&nbsp;</td></tr>
            <tr><td colspan=2 align="center">Give discount to customer <b>{$customerDetails.name}</b> ({$customerDetails.customer_id}) in size of <input type="text" size="7" maxlength="6" name="discount" value="{$customerDetails.discount}">%</td></tr>
            <tr><td colspan=2>&nbsp;</td></tr>                               
            <tr>
                <td><b>Note:</b> next invoice will be generated with discount &nbsp;</td>
                <td align="right">
                    <input type="submit" class="button" value="Give Discount">
                    <input type="hidden" name="customerID" value="{$customerDetails.company_id}">
                    <input type="hidden" name="edit" value="yes">                                                                                                       
                </td>
            </tr>
        </table>
</form>
                                             
{else}

    {*SHOW ALL REGISTERED COMPANIES IN VPS*}
    <table  width="100%"  cellspacing="0" cellpadding="5" >
    <tr><td colspan=6 valign="top"><span class="gray_header">Customers</span>
	<div class="br_10"></div></td></tr>
    {if $customers}
    <tr style="background:#50B27C;color:#ffffff">
    <td>ID</td>
    <td class="billingPlans_left">Name</td>
    <td class="billingPlans_left">Current Billing Plan</td>
    <td class="billingPlans_left">Total Charge</td>
    <td class="billingPlans_left">Time With Us</td>
    <td class="billingPlans_left">Discount</td>
    </tr>
    {section name=i loop=$customers}
    <tr  class="hov_company_discounts" onClick="location.href='admin.php?action=vps&vpsAction=showEdit&itemID=discounts&customerID={$customers[i].id}';">
        <td class="billingPlans_bot billingPlans_left">{$customers[i].id}</td>
        <td class="billingPlans_bot billingPlans_left">{$customers[i].name}</td>
        <td class="billingPlans_bot billingPlans_left">{$billingPlans[i].bplimit} sources {$billingPlans[i].months_count} months {$billingPlans[i].type}</td>
        <td class="billingPlans_bot billingPlans_left">$ {$billingPlans[i].total_charge}</td>
        <td class="billingPlans_bot billingPlans_left">{$customers[i].time_with_us}</td>
        <td class="billingPlans_bot billingPlans_left billingPlans_right">{if $customers[i].discount>0}-{$customers[i].discount}%{else}--{/if}</td> 
    </tr>
    {/section}
    {else}
    <tr>
        <td>No Customers Registered in VPS</td>
    </tr>
    {/if}
    </table>
{/if}           
    
           {*/shadow_table*}    
                             </td>
                              <td valign="top" class="report_uploader_r_green"></td>
                       </tr>
                        <tr>          
                             <td valign="top" class="report_uploader_b_l_green"></td>
                             <td valign="top" class="report_uploader_b_green"></td>
                             <td valign="top" class="report_uploader_b_r_green"></td>                                         
                       </tr>
              </table>
        
              {*/shadow_table*} 
{/if}
