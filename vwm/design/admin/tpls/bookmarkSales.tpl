<link href="style.css" rel="stylesheet" type="text/css">

<table cellspacing="0" cellpadding="0" width="100%" style="margin:10px 0 0 0">
  <tr>
      
      <td  align="right" 

	  {if $request.bookmark eq "contacts"} class="bookmark_fon" {/if}
	>
       <table cellspacing="0" cellpadding="0"height="100%" class="bookmarks">

   <tr>
   <td >
    <a href="admin.php?action=browseCategory&category=salescontacts&bookmark=contacts">
   {if $request.subBookmark}
   <div class="deactiveBookmark"><div class="deactiveBookmark_right">
   {else}
 <div  class = "activeBookmark">  <div class = "activeBookmark_right">
   {/if}
  Contacts
   </div>
   </div></a>
   </td>
   
  
   
   <td >
    <a href="admin.php?action=browseCategory&category=salescontacts&bookmark=contacts&subBookmark=Government">
   		{if $request.subBookmark != "Government"}
  			 <div class="deactiveBookmark"><div class="deactiveBookmark_right">
  		{else}
			<div  class = "activeBookmark">  <div class = "activeBookmark_right">
  		{/if}
 		 Government Agencies
   			</div>
  		 </div></a>
   </td>
   
   <td >
    <a href="admin.php?action=browseCategory&category=salescontacts&bookmark=contacts&subBookmark=Affiliations">
   		{if $request.subBookmark != "Affiliations"}
  			 <div class="deactiveBookmark"><div class="deactiveBookmark_right">
  		{else}
			<div  class = "activeBookmark">  <div class = "activeBookmark_right">
  		{/if}
 		 Affiliations 
   			</div>
  		 </div></a>
   </td>


     	<td>
    	<td width="20px">  
  	</td>
   
     </tr>
	  
	 <tr height="19px">
	 <td {if !$request.subBookmark} class="active_bookmark_fon" {/if}></td>
         <td {if $request.subBookmark eq "Government"} class="active_bookmark_fon" {/if}></td>
         <td {if $request.subBookmark eq "Affiliations"} class="active_bookmark_fon" {/if}></td>         
	 <td {if $request.bookmark eq "facility"} class="active_bookmark_green_fon"{/if}></td>
	 <td {if $request.bookmark eq "department"} class="active_bookmark_violet_fon" {/if}></td>
	
	 </tr>
</table>   
 </td>
  
    
    

  </tr>
</table>