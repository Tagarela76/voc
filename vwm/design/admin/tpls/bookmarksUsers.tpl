
<link href="style.css" rel="stylesheet" type="text/css">

<table cellspacing="0" cellpadding="0" width="100%" style="margin:10px 0 0 0">
  <tr>
  
   <td  align="right" 

	  {if $request.bookmark eq "company"} class="bookmark_fon_orange" {/if}
	  {if $request.bookmark eq "facility"} class="bookmark_fon_green" {/if}
	  {if $request.bookmark eq "department"} class="bookmark_fon_violet" {/if}
	  {if $request.bookmark eq "admin"} class="bookmark_fon" {/if}

	  
	  >
       <table cellspacing="0" cellpadding="0"height="100%" class="bookmarks">

   <tr>
   <td >
    <a href="admin.php?action=browseCategory&category=users&bookmark=company">
   {if $request.bookmark != "company"}
   <div class="deactiveBookmark"><div class="deactiveBookmark_right">
   {else}
 <div  class = "activeBookmark_orange">  <div class = "activeBookmark_orange_right">
   {/if}
  Company Level
   </div>
   </div></a>
   </td>
   
   <td >
    <a href="admin.php?action=browseCategory&category=users&bookmark=facility">
   {if $request.bookmark != "facility"}
   <div class="deactiveBookmark"><div class="deactiveBookmark_right">
   {else}
 <div  class = "activeBookmark_green">  <div class = "activeBookmark_green_right">
   {/if}
  Facility Level
   </div>
   </div></a>
   </td>
   
   <td >
    <a href="admin.php?action=browseCategory&category=users&bookmark=department">
   {if $request.bookmark != "department"}
  <div class="deactiveBookmark"><div class="deactiveBookmark_right">
   {else}
 <div class="activeBookmark_violet"><div class="activeBookmark_violet_right">
   {/if}
  Department Level
   </div>
   </div></a>
   </td>
   
   <td >
    <a href="admin.php?action=browseCategory&category=users&bookmark=admin">
   {if $request.bookmark != "admin"}
   <div class="deactiveBookmark"><div class="deactiveBookmark_right">
   {else}
 <div  class = "activeBookmark">  <div class = "activeBookmark_right">
   {/if}
  Superuser Level (admin)
   </div>
   </div></a>
   </td>
   
   
   
   <td>
    <td width="20px">  
  </td>
   
     </tr>
	  
	 <tr height="19px">
	 <td {if $request.bookmark eq "company"} class="active_bookmark_orange_fon" {/if}></td>
	 <td {if $request.bookmark eq "facility"} class="active_bookmark_green_fon" {/if}></td>
	 <td {if $request.bookmark eq "department"} class="active_bookmark_violet_fon" {/if}></td>
	 <td {if $request.bookmark eq "admin"} class="active_bookmark_fon" {/if}></td>
	
	 </tr>
</table>   
 </td>
   
    <td 
	{if $request.bookmark eq "company"} class="bookmark_fon_orange" {/if}
	 {if $request.bookmark eq "facility"} class="bookmark_fon_green" {/if}
	  {if $request.bookmark eq "department"} class="bookmark_fon_violet" {/if}
	  {if $request.bookmark eq "admin"} class="bookmark_fon" {/if}
	
	
	
	> </td >
  </tr>
</table>
{}