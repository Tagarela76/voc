
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


<div class="padd7">

    <table  class="users" height="200"  cellspacing="0" cellpadding="0" align="center">
        {*TABLE HEADER*}
        <tr height="27" class="users_top_violet">

            <td width="10%" class="users_u_top_violet"><span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span></td>
            <td width="10%">Id</td>
            <td width="10%">Name</td>
            <td width="10%">Address</td>
            <td width="10%">Contact</td>
            <td width="60%" class="users_u_top_r_violet">Phone</td>
        </tr>

        {if $itemsCount > 0}						 

            {*BEGIN LIST*}						 

            {section name=i loop=$category}						

                <tr  height="10px" class="hov_company">

                    <td  class="border_users_l border_users_r border_users_b">
                        <input type="checkbox"  value="{$category[i].id}" name="item_{$smarty.section.i.index}">
                    </td>

                    <td  class="border_users_r border_users_b">
                        <a href="{$category[i].url}" >
                            <div style="width:100%;">
                                {$category[i].id}
                            </div >
                        </a>
                    </td>

                    <td class="border_users_r border_users_b">
                        <a href="{$category[i].url}">
                            <div style="width:300px;">
                                {$category[i].name}	
                            </div >
                        </a>
                    </td>

                    <td  class="border_users_r border_users_b">
                        <a href="{$category[i].url}" >
                            <div style="width:300px;">
                                {$category[i].address}	
                            </div >
                        </a>
                    </td>

                    <td  class="border_users_r border_users_b">
                        <a href="{$category[i].url}" >
                            <div style="width:300px;">
                                {$category[i].contact}	
                            </div >
                        </a>
                    </td>
                    <td  class="border_users_r border_users_b">
                        <a href="{$category[i].url}" >
                            <div style="width:100%;">
                                {$category[i].phone}	
                            </div >
                        </a>
                    </td>

                </tr>
            {/section}		


            <tr >
                <td   class="border_users_l border_users_r " colspan="6"  >&nbsp;</td>
            </tr>

            {*END LIST*}

        {else}
            {*BEGIN	EMPTY LIST*}
            <tr align='center'>
                <td class="border_users_l border_users_r" colspan="6"> <center>No Companies</center></td>
            </tr>
            {*END	EMPTY LIST*}
        {/if}

        {*TABLE PRETTY BOTTOM*}				
        <tr>
            <td  height="20" colspan="5" class="users_u_bottom">&nbsp;</td>
            <td   class="users_u_bottom_r"></td>
        </tr>
    </table>
</div>
</form>