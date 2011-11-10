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
	<form name="addDocItem" enctype="multipart/form-data" action="admin.php?action={$request.action}&category=salesdocs" method="POST">
		<table class="users addCompany" cellpadding="0" cellspacing="0" align="center">
			<tr class="users_u_top_size users_top">
				<td class="users_u_top" width="30%">
				<span><b>Adding for a new document or folder</b></span>
				</td>	
				<td class="users_u_top_r">
					&nbsp;
				</td>								
			</tr>

				<tr class="border_users_r border_users_l">			
					<td height="20" colspan="2">
						Select file:
					</td>
					</tr>
					<tr class="border_users_b border_users_r border_users_l">
					<td colspan="2" style="padding:0px;">
							<div class="top_lightgray_documents">
							<div style="width:100%;display:block;">
							<div class="float_documents_file">
							<input class="radio" type="radio" name="item_type" value={$doc_item} onClick="showAddItemType('file')" {if $info.item_type eq $doc_item}checked{/if}>
							<div>File</div>
							</div>
							<div class="float_documents_folder">
							<input class="radio" type="radio" name="item_type" value={$folder_item} onClick="showAddItemType('folder')" {if $info.item_type eq $folder_item}checked{/if}>
							<div>Folder</div>
							</div>
							</div>
						</div>
                            <div style="height:1px;"></div>
							<div id="div_input" {if $info.item_type eq $folder_item}style="display:none;"{/if}>
							<div>
                                <input id="input" name="inputFile" type="file" onClick="fileSelected('upload')">
							</div>	
							{*ERROR*}	
								{if $error eq 'path'}					
									<div id="error_path" class="error_img" {*style="display:none;"*}><span id='err' class="error_text">Error! Empty file path!</span></div>					    						
								{/if}
							{*/ERROR*}
							</div>			    						
					</td>					
				</tr>
	
					
				<tr class="border_users_b border_users_r">		
					<td class="border_users_l" height="20" width="15%">
						Document name:
					</td>
					<td>
						<div align="left">
							<input type='text' id='docName' name='name' value='{$info.name}'>
						</div>					
							{*ERROR*}
								{if $request.action eq "addItem"}
									{if $error eq 'name'}					
										<div id="error_name" class="error_img" {*style="display:none;"*}><span class="error_text">Error! Empty name!</span></div>					    						
									{/if}
								{/if}
								<div id="error_name_alredyExist" class="error_img" style="display:none;"><span class="error_text">Entered name is alredy in use!</span></div>
							{*/ERROR*}																
													
					</td>					
				</tr>
			
			<tr class="border_users_b border_users_r">			
				<td height="20" class="border_users_l">
					Document description:
				</td>
				<td>
					<div align="left">
						<input type='text' id='docDescription' name='description' value='{$info.description}' {if $info.item_type eq $folder_item}style="display:none;"{/if}>
					</div>							
			     				{*ERROR*}					
								<div id="error_description" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}						    						
				</td>					
			</tr>

			<tr class="border_users_b border_users_r">			
				<td height="20" class="border_users_l">
					Containing folder:
				</td>
				<td>
 					<div id="div_select_fold" onClick = "showDir('select')">
                        Select...
                    </div>
                        <div id="select" style="display:none">
                        	{if $InfoTree neq 0}
									{assign var=level value=0}
    									{foreach from=$InfoTree item=elem}
   								 		{if $level gt $elem.level}
    										{section name=level_iter loop=$level start=$elem.level}
    											</div>
    										{/section}
  								  		{elseif $level lt $elem.level}
   								 			<div id='{$divId}' style="display:none; padding-left:15">
  								  		{/if}
  								  		{assign var=level value=$elem.level}

    										{if $elem.type eq $doc_item}
    											<!--<p><a href ="{$elem.info.link}" alt = "{$elem.info.description}">{$elem.info.name}</a></p>-->
    										{else}
    											{if $elem.info.count neq '0'}
    												<div id="folder" onclick="showDir('{$elem.info.id}')">
    													<p><input type="radio" name="folder" value="{$elem.info.id}" {if $info.folder eq $elem.info.id}checked{/if}>{$elem.info.name}({$elem.info.count})</p>
    												</div>
    												{assign var=divId value=$elem.info.id}
    											{else}
    												<div id="folder">
    													<p><input type="radio" name="folder" value="{$elem.info.id}" {if $info.folder eq $elem.info.id}checked{/if}>{$elem.info.name}({$elem.info.count})</p>
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
  								  	    <div id="folder">
    										<p><input type="radio" name="folder" value="none" {if $info.folder eq 'none'}checked{/if}>MAIN FOLDER(without folder)</p>
   								 		</div>
                        </div>    						
				</td>					
			</tr>
												
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
			<input type="submit" id='upload' class="button" value="Upload" {if $info.item_type eq $doc_item}disabled{/if}>				
		</div>
		
		
		{*HIDDEN*}
		<input type='hidden' name='MAX_FILE_SIZE' value="52430000">
		<input type='hidden' name='action' value={$request.action}>		
		</form>
						</td>
			</tr>
		</table>
</div>


{include file="tpls:tpls/pleaseWait.tpl" text=$pleaseWaitReason}	