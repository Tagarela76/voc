<div id="notifyContainer">
	{if $color eq "green"}
		{include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
	{/if}
	{if $color eq "orange"}
		{include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
	{/if}
	{if $color eq "blue"}
		{include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
	{/if}
</div>

<div class="padd7">
	<form name="deleteDocItem" enctype="multipart/form-data" 
		{if $step eq 'choose'}
			action="admin.php?action={$request.action}&category=salesdocs"
		{elseif $empty neq 'true'} 
			action="admin.php?action=confirmDelete&category=salesdocs"
		{else}
			action="admin.php?action=browseCategory&category=salesdocs"
		{/if} method="POST">
		<table class="users addCompany" cellpadding="0" cellspacing="0" align="center">
			<tr class="users_top_red users_u_top_size">
				<td class="users_u_top_red">
				&nbsp;
				</td>	
				<td class="users_u_top_r_red">
					<span><b>
					{if $step eq 'choose'}
						Detele documents and folders
					{else}
						Confirm delete documents and folders
					{/if}
					</b></span>
				</td>								
			</tr>

			<tr class="border_users_b border_users_r">			
				<td height="20" class="border_users_l">
					{if $step eq 'choose'}
						Select documents:
					{else}
						Selected documents:
					{/if}
				</td>
				<td>
					{if $empty neq 'true'}
					
                        <div id="select" {*style="display:none"*}>
                        	{if $InfoTree neq 0}
									{assign var=level value=0}
    									{foreach from=$InfoTree item=elem}
   								 		{if $level gt $elem.level}
    										{section name=level_iter loop=$level start=$elem.level}
    											</div>
    										{/section}
  								  		{elseif $level lt $elem.level}
   								 			<div id='{$divId}' style="{if $step eq 'choose'}display:none;{/if} padding-left:15">
  								  		{/if}
  								  		{assign var=level value=$elem.level}

    										{if $elem.type eq $doc_item}
    											{if $step eq 'choose'}
    												<p><input type="checkbox" name="doc_{$elem.info.id}" value="{$elem.info.id}"><a href ="{$elem.info.link}" alt = "{$elem.info.description}">{$elem.info.name}</a></p>
    											{elseif $id_delete[$elem.info.id] eq "true"}
    												<p><input type="hidden" name="doc_{$elem.info.id}" value="{$elem.info.id}"><a href ="{$elem.info.link}" alt = "{$elem.info.description}">{$elem.info.name}</a></p>
    											{/if}
    										{else}
    											{if $elem.info.count neq '0'}
    												<div id="folder" onclick="showDir('{$elem.info.id}')">
    													{if $step eq 'choose'}
    														<p><input type="checkbox" name="doc_{$elem.info.id}" value="{$elem.info.id}">{$elem.info.name}({$elem.info.count})</p>
    													{elseif $id_delete[$elem.info.id] eq "true"}
    														<p><input type="hidden" name="doc_{$elem.info.id}" value="{$elem.info.id}">{$elem.info.name}({$elem.info.count})</p>
    													{/if}
    												</div>
    												{assign var=divId value=$elem.info.id}
    											{else}
    												<div id="folder">
    													{if $step eq 'choose'}
    														<p><input type="checkbox" name="doc_{$elem.info.id}" value="{$elem.info.id}">{$elem.info.name}({$elem.info.count})</p>
   								 						{elseif $id_delete[$elem.info.id] eq "true"}
   								 							<p><input type="hidden" name="doc_{$elem.info.id}" value="{$elem.info.id}">{$elem.info.name}({$elem.info.count})</p>
   								 						{/if}
   								 					</div>
  								  			{/if}
   								 		{/if}
  								  	{/foreach}
   								 		{if $level gt 0}
    										{section name=level_iter loop=$level start=0}
    											</div>
    										{/section}
    									{/if}
							{else}	
  								      {*BEGIN	EMPTY LIST*}        

 								               No documents in the list
      		
  								      {*END	EMPTY LIST*}
  							{/if}
                        </div>  
                    {else}
                    	No documents selected!
                    {/if}  						
				</td>					
			</tr>

		{if $step eq 'choose'}
			<tr class="border_users_l border_users_r">
				<td colspan="2">						
					<div>
						<p><input type="radio" name="delete_type" value='all'>Delete with sub-files<br />
						<input type="radio" name="delete_type" value='only' checked>Delete only selected files</p>
					</div>
				</td>
			</tr>
		{/if}
												
			<tr class="border_users_l border_users_r">
				<td colspan="2">&nbsp;</td>
			</tr>
			
			<tr>
				<td height="20" class="users_u_bottom">&nbsp;</td>
				<td height="20" class="users_u_bottom_r">&nbsp;</td>
			</tr>
		</table>
				
		
		<table cellpadding="5" cellspacing="0" align="center" width="95%">
			<tr>
				<td>
		{*BUTTONS*}
		<div align="right">
			<input type='button' name='cancel' class="button" value='Cancel' 
				onClick="location.href='admin.php?action=browseCategory&category=salesdocs'">
			{if $step eq 'choose'}	
				<input type="submit" id='delete' class="button" value="Delete">
			{elseif $empty eq "true"}	
				<input type="submit" id='ok' class="button" value="Ok"
					onClick="location.href='admin.php?action=browseCategory&category=salesdocs'">
			{else}
				<input type="submit" id='delete' class="button" value="Confirm delete">
			{/if}					
		</div>
		
		
		{*HIDDEN*}
		<input type='hidden' name='MAX_FILE_SIZE' value="52430000">
		{if $step eq 'choose'}
			<input type="hidden" name="step" value='confirm'>
		{else}
			<input type="hidden" name="itemID" value='docs'>
			<input type="hidden" name="confirm" value='Yes'>
			<input type="hidden" name="delete_type" value="alone">
		{/if}
		<input type='hidden' name='action' value={$request.action}>		
		<input type='hidden' name='facilityID' value={$request.facilityID}>
		</form>
						</td>
			</tr>
		</table>
</div>


{include file="tpls:tpls/pleaseWait.tpl" text=$pleaseWaitReason}	