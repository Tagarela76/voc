
<link href="style.css" rel="stylesheet" type="text/css">

<table cellspacing="0" cellpadding="0" width="100%" style="margin:10px 0 0 0">
  <tr>
  
   <td  align="right" 

	  {if $bookmarkType eq "billing"} class="bookmark_fon_orange" {/if}
	  {if $bookmarkType eq "discounts"} class="bookmark_fon_green" {/if}
	  {if $bookmarkType eq "customers"} class="bookmark_fon_violet" {/if}
	  {if $bookmarkType eq "other"} class="bookmark_fon" {/if}

	  
	  >
       <table cellspacing="0" cellpadding="0"height="100%" class="bookmarks">

   <tr>
   <td >
    <a href="admin.php?action=vps&vpsAction=browseCategory&itemID=billing">
   {if $bookmarkType != "billing"}
   <div class="deactiveBookmark"><div class="deactiveBookmark_right">
   {else}
 <div  class = "activeBookmark_orange">  <div class = "activeBookmark_orange_right">
   {/if}
  Billing Plans
   </div>
   </div></a>
   </td>
   
   <td >
    <a href="admin.php?action=vps&vpsAction=browseCategory&itemID=discounts">
   {if $bookmarkType != "discounts"}
   <div class="deactiveBookmark"><div class="deactiveBookmark_right">
   {else}
 <div  class = "activeBookmark_green">  <div class = "activeBookmark_green_right">
   {/if}
  Discounts
   </div>
   </div></a>
   </td>
   
   <td >
    <a href="admin.php?action=vps&vpsAction=browseCategory&itemID=customers">
   {if $bookmarkType != "customers"}
  <div class="deactiveBookmark"><div class="deactiveBookmark_right">
   {else}
 <div class="activeBookmark_violet"><div class="activeBookmark_violet_right">
   {/if}
  Customers
   </div>
   </div></a>
   </td>
   
   <td >
    <a href="admin.php?action=vps&vpsAction=browseCategory&itemID=other">
   {if $bookmarkType != "other"}
   <div class="deactiveBookmark"><div class="deactiveBookmark_right">
   {else}
 <div  class = "activeBookmark">  <div class = "activeBookmark_right">
   {/if}
  Other Settings
   </div>
   </div></a>
   </td>
   
   
   
   <td>
    <td width="30px">  
  </td>
   
     </tr>
	  
	 <tr height="19">
	 <td {if $bookmarkType eq "billing"} class="active_bookmark_orange_fon" {/if}></td>
	 <td {if $bookmarkType eq "discounts"} class="active_bookmark_green_fon" {/if}></td>
	 <td {if $bookmarkType eq "debtors"} class="active_bookmark_violet_fon" {/if}></td>
	 <td {if $bookmarkType eq "other"} class="active_bookmark_fon" {/if}></td>
	
	 </tr>
</table>   
 </td>
   
    <td 
	{if $bookmarkType eq "billing"} class="bookmark_fon_orange" {/if}
	 {if $bookmarkType eq "discounts"} class="bookmark_fon_green" {/if}
	  {if $bookmarkType eq "debtors"} class="bookmark_fon_violet" {/if}
	  {if $bookmarkType eq "other"} class="bookmark_fon" {/if}
	
	
	 width="5%"
	
	> </td >
  </tr>
</table>
{}