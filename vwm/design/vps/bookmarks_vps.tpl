<link href="style.css" rel="stylesheet" type="text/css">


<table cellspacing="0" cellpadding="0" width="100%" style="margin:3px 0 0 0">
	<tr>
		<td  align="right" {if $currentBookmark eq "MyBillingPlan"} class="bookmark_fon_green"	{elseif $currentBookmark eq "AvailableBillingPlans"}class="bookmark_fon_yellowgreen"{else}class="bookmark_fon_yellowgreen"{/if}>		   
			<table cellspacing="0" cellpadding="0"height="100%" class="bookmarks">	
      			<tr>
      			{section name=i loop=$bookmark}
	     			<td nowrap="nowrap" align="left">
	     			{if !($pleaseWait && $bookmark[i].name eq "AvailableBillingPlans")}	     					     			
						<a href="{$bookmark[i].url}" >
					{/if} 
						{if $bookmark[i].name eq "$currentBookmark"}
							{if $currentBookmark eq "MyBillingPlan"}
						<div  class = "activeBookmark_green">
						<div class = "activeBookmark_green_right">
							{elseif $currentBookmark eq "AvailableBillingPlans"}
						<div  class = "activeBookmark_yellowgreen">
						<div class = "activeBookmark_yellowgreen_right">
							{else}
						<div  class = "activeBookmark_yellowgreen">
						<div class = "activeBookmark_yellowgreen_right">
							{/if}
						{else} 
						<div  class = "deactiveBookmark">
						<div class = "deactiveBookmark_right">
						{/if}
						{$bookmark[i].label}
						</div>
						</div>
					{if !($pleaseWait && $bookmark[i].name eq "AvailableBillingPlans")}	     					     			
						</a>
					{/if} 												
	   				</td>
	   			{/section}
	   
					<td width="50px"> </td>   
     			</tr>
       
	 			<tr height="19">
	   				{section name=i loop=$bookmark}
						{if $bookmark[i].name eq "$currentBookmark"}
							{if $currentBookmark eq "MyBillingPlan"}	 
					<td class="active_bookmark_green_fon"> 
							{elseif $currentBookmark eq "AvailableBillingPlans"}
					<td class="active_bookmark_yellowgreen_fon"> 
							{else}	
					<td class="active_bookmark_yellowgreen_fon"> 
	 						{/if}
	  				</td>
	  					{else}
	  				<td  class="deactive_bookmark_fon" >
	  				</td>
						{/if}
		 			{/section}
	   				<td>
	   				</td>
	   	 		</tr>
			</table> 
		</td>
    	<td class="bookmark_fon_green">
		</td >
	</tr>
</table>