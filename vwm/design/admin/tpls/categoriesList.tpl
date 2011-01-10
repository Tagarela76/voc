<table width="100%" cellspacing="0" cellpadding="7"><tr><td >
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
                        <table class="cell bordertd " height="189" width="100%" cellspacing="0" cellpadding="0">
                          <tr class="polostop" height="27">
<td  class="header_table"   width="10%"> Select</td>
<td  class="bgtd">ID Number</td>
{if $categoryType eq "facility"}
<td  class="bgtd">Facility name</td>
{elseif $categoryType eq "department"}
<td  class="bgtd">Department name</td>
{else}
<td  class="bgtd">Company name</td>
{/if}
{if $categoryType ne "department"}
<td  class="bgtd">Location/Contact</td>
{/if}


				
						</tr>
						 
{if $itemsCount > 0}						 
						 
{*BEGIN LIST*}						 

{section name=i loop=$category}						
						
						 <tr class="td2fix hov_company" height="10px">

<td class="bgtdleft">

 <input type="checkbox"  value="{$category[i].id}" name="item_{$smarty.section.i.index}" onclick="return CheckCB(this);"></td>

 <td class="border_td">
              <a href="{$category[i].url}" class="id_company1">
			           <div style="width:100%;">
					         {$category[i].id}
					    </div >
			  </a>
</td>
<td class="border_td">
             <a href="{$category[i].url}" class="id_company1">
			             <div style="width:100%;">
						         {$category[i].name}
						 </div >
			 </a>
</td>
{if $categoryName eq "facility"}
{else}
<td class="border_td" width="40%">
             <a href="{$category[i].url}" class="id_company1">
			             <div style="width:100%;">
					        {$category[i].address},&nbsp;
							{$category[i].contact}&nbsp({$category[i].phone})
							
							
						 </div >
			 </a>
</td>
{/if}
				
						</tr>
{/section}		
		
		
						<tr class="cell">
						<td  class="bgtdleft_b" ></td><td colspan="3" class="border_td"></td>
						</tr>

{*END LIST*}

{else}

{*BEGIN	EMPTY LIST*}
	{if $categoryType eq "company"}
		<tr class="cell">
			<td  class="bgtdleft" ></td><td colspan="3" class="border_td">No companies in the list</td>
		</tr>
	{elseif $categoryType eq "facility"}
		<tr class="cell">
			<td  class="bgtdleft" ></td><td colspan="3" class="border_td">No facilities in chosen company</td>
		</tr>
	{elseif $categoryType eq "department"}
		<tr class="cell">
			<td  class="bgtdleft" ></td><td colspan="3" class="border_td">No departments in chosen facility</td>
		</tr>
	{/if}
		


{*END	EMPTY LIST*}

{/if}
						<tr>
						<td colspan="14" bgcolor="#e3e9f8" height="30" class="bgtdtbbot"></td></tr>
						</table>
</td></tr></table>
</form>