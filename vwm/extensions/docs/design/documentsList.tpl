<div class="padd7" align="center">
    {if $color eq "green"}
    	{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
    {/if}
    {if $color eq "orange"}
    	{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
    {/if}
    {if $color eq "blue"}
    	{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
    {/if}   
    
    {literal}
	<script language="JavaScript">
		function showDir(id) {
			visibility = document.getElementById(id).style.display;
			if (visibility != "block") {
				document.getElementById(id).style.display = "block";
			} else {   
				document.getElementById(id).style.display = "none";
			}
		}
	</script>
	{/literal}
</div>
<div class="padd7" align="center" style='background-color:white;'>
	    {*shadow*}
    <div class="shadow_list_documents">
        <div class="shadow_list_r_documents">
            {**}
 <table class="daily_emissions_report" width="98%" align="center" cellspacing="0">
                <tr class="daily_emissions_report"><td>
<div class="category_documents" align="left">
	{if $InfoTree neq 0}
	{assign var=level value=0}
    	{foreach from=$InfoTree item=elem}
    		{if $level gt $elem.level}
    			{section name=level_iter loop=$level start=$elem.level}
    				</div>
    			{/section}
    		{elseif $level lt $elem.level}
    			<div id='{$divId}' style="display:none;padding-left:0px;" class="documents_padd">
    		{/if}
    		{assign var=level value=$elem.level}

    		{if $elem.type eq $doc_item}
    			{if $category eq 'wastestorage'}
    				<div class="category_link">
               		    <p>
                        	<input class="radio" type="radio" name="documentID" value="{$elem.info.id}" {if $data->document_id eq $elem.info.id}checked{/if}><div><a href ="{$elem.info.link}" title = "{$elem.info.description}">{$elem.info.name}</a></div>
                    	</p>
                	</div>
                {else}
    			<div class="category_link"><p><a href ="{$elem.info.link}" title = "{$elem.info.description}">{$elem.info.name}</a></p></div>
    			{/if}
    		{else}
    			{if $elem.info.count neq '0'}
    				<div id="folder" onclick="showDir('{$elem.info.id}')" class="category_folder">
    					<p>{$elem.info.name} ({$elem.info.count})</p>
    				</div>
    				{assign var=divId value=$elem.info.id}
    			{else}
    				<div id="folder" class="category_folder">
    					<p>{$elem.info.name} ({$elem.info.count})</p>
    				</div>
    			{/if}
    		{/if}
    	{/foreach}
   		{if $level gt 0}
    		{section name=level_iter loop=$level start=0}
    			</div>
    		{/section}
    	{/if}    
    	{if $category eq 'wastestorage'}
        	    <p>
                   	<input class="radio" type="radio" name="documentID" value="0" {if $data->document_id eq '0' || $data->document_id eq null}checked{/if}><i>No document</i>
               	</p>
        {/if}	
	{else}	
        {*BEGIN	EMPTY LIST*}        

                No documents in the list
      		
        {*END	EMPTY LIST*}
		
    {/if}  
	        </td>
                </tr>
            </table>            {*shadow*} 
        </div>
    </div>
    {**}
</div>
	{if $category neq 'wastestorage'}
    	</form> {*close FORM tag opened at controlCategoriesList.tpl*}
    {/if}