<link href="style.css" rel="stylesheet" type="text/css">

<table cellspacing="0" cellpadding="0" width="100%" style="margin:10px 0 0 0">
  <tr>
  
   <td  align="right" 

	  {if $request.bookmark eq "contacts"} class="bookmark_fon_orange" {/if}
	>
       <table cellspacing="0" cellpadding="0"height="100%" class="bookmarks">

   <tr>
   <td >
    <a href="admin.php?action=browseCategory&category=salescontacts&bookmark=contacts">
   {if $request.bookmark != "contacts"}
   <div class="deactiveBookmark"><div class="deactiveBookmark_right">
   {else}
 <div  class = "activeBookmark_orange">  <div class = "activeBookmark_orange_right">
   {/if}
  Contacts
   </div>
   </div></a>
   </td>
   
   
   <td>
    <td width="20px">  
  </td>
   
     </tr>
	  
	 <tr height="19px">
	 <td {if $request.bookmark eq "contacts"} class="active_bookmark_orange_fon" {/if}></td>
	 <td {if $request.bookmark eq "facility"} class="active_bookmark_green_fon" {/if}></td>
	 <td {if $request.bookmark eq "department"} class="active_bookmark_violet_fon" {/if}></td>
	 <td {if $request.bookmark eq "admin"} class="active_bookmark_fon" {/if}></td>
	
	 </tr>
</table>   
 </td>
   
    <td 
	{if $request.bookmark eq "contacts"} class="bookmark_fon_orange" {/if}
	
	
	
	> </td >
  </tr>
</table>