<link href="style.css" rel="stylesheet" type="text/css">

<table cellspacing="0" cellpadding="0" width="100%" style="margin:10px 0 0 0">
  <tr>
      
      <td  align="right" 

	  {if $request.bookmark eq "contacts"} class="bookmark_fon" {/if}
	>
       <table cellspacing="0" cellpadding="0"height="100%" class="bookmarks">

   <tr>
       
    {*BEGIN LIST*}				
    {section name=i loop=$bookmarks}    
        <td >
            <a href="admin.php?action=browseCategory&category=salescontacts&bookmark=contacts{if $bookmarks[i]->id != 1}&subBookmark={$bookmarks[i]->name}{/if}">
   		{if $request.subBookmark|escape == $bookmarks[i]->name}
  			<div  class = "activeBookmark">  <div class = "activeBookmark_right">
  		{else}                    
                     {if ($bookmarks[i]->id == 1)&(!$request.subBookmark)}
			<div  class = "activeBookmark">  <div class = "activeBookmark_right">
                     {else}
                        <div class="deactiveBookmark"><div class="deactiveBookmark_right">
                     {/if}
  		{/if}
 		 {*$bookmarks[i]->name|capitalize:true*}
                 {$bookmarks[i]->name}
   			</div>
  		 </div></a>
        </td>
    {/section}						
    {*END LIST*}
    
     <td>
    	<td width="20px">  
  	</td>
   
     </tr>
     
     <tr height="19px">
        {section name=i loop=$bookmarks}  
        {if !$request.subBookmark}
            {if $bookmarks[i]->id == 1}
                <td class="active_bookmark_fon"></td>
            {/if}
        {/if}
        <td {if $request.subBookmark|escape eq $bookmarks[i]->name} class="active_bookmark_fon" {/if}></td>	
        {/section}
     </tr>
{$bookmarks[i]->name}
</table>   
 </td>
  </tr>
</table>