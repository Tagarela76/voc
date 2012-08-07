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

	<form name="editDocItem" enctype="multipart/form-data" action="admin.php?action={$request.action}&category=salesdocs&salesDocsCategory={$request.salesDocsCategory}" method="POST">
<div class="padd7">
		<table class="users addCompany" cellpadding="0" cellspacing="0" align="center">
			<tr class="users_u_top_size users_top">
				<td class="users_u_top ">
				&nbsp;
				</td>
				<td class="users_u_top_r">
					<span><b>Editing documents and folders</b></span>
				</td>
			</tr>

				<tr class="border_users_r border_users_l">
					<td height="20" colspan="2">
						Select file:
					</td>
					</tr>
					<tr class="border_users_b border_users_r border_users_l">
					<td colspan="2" style="padding:0px;">
						<div style="width:100%;">
							<div class="top_lightgray_documents float_documents_file">
							<input class="radio" type="radio" name="item_type" value={$doc_item} onClick="showEditItemType ('file')" {if $info.item_type eq $doc_item}checked{/if}><div>File</div></div>
							<div class="top_lightgray_documents float_documents_folder">
							<input class="radio" type="radio" name="item_type" value={$folder_item} onClick="showEditItemType('folder')" {if $info.item_type eq $folder_item}checked{/if}><div>Folder</div></div>
						</div>
						<div id="table" class="padd7 category_documents" align="center" style="display:table;">
                            <div id="div_input" align="left" {if $info.item_type eq $folder_item}style="display:none;"{/if}>
                                {if $InfoTree neq 0}
									{assign var=level value=0}
    								{foreach from=$InfoTree item=elem}
   								 		{if $level gt $elem.level}
    										{section name=level_iter loop=$level start=$elem.level}
    											</div>
    										{/section}
  								  		{elseif $level lt $elem.level}
   								 			<div id='f_{$divId}' style="display:none;padding-left:0px;" class="documents_padd">
  								  		{/if}
  								  		{assign var=level value=$elem.level}

    										{if $elem.type eq $doc_item}
                                            <div class="category_link">
                                                <p>
                                                    <input class="radio" type="radio" name="file" value="{$elem.info.id}" onClick="setInfo('{$elem.info.id}','{$elem.type}','{$elem.info.name}','{$elem.info.description}','{$elem.info.parent_id}')" {if $info.file eq $elem.info.id}checked{/if}><div><a href ="{$elem.info.link}" alt = "{$elem.info.description}">{$elem.info.name}</a></div>
                                                </p>
                                            </div>
    										{else}
    											{if $elem.info.count neq '0'}
    												<div id="folder" onclick="showDir('f_{$elem.info.id}')" class="category_folder category_folder_hover">
    													<p>{$elem.info.name}({$elem.info.count})</p>
    												</div>
    												{assign var=divId value=$elem.info.id}
    											{else}
    												<div id="folder" class="category_folder">
    													<p>{$elem.info.name}({$elem.info.count})</p>
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
			     			<div id="div_select" {if $info.item_type eq $doc_item}style="display:none;"{/if} class="category_documents" align="left">
								{if $InfoTree neq 0}
									{assign var=level value=0}
    								{foreach from=$InfoTree item=elem}
   								 		{if $level gt $elem.level}
    										{section name=level_iter loop=$level start=$elem.level}
    											</div>
    										{/section}
  								  		{elseif $level lt $elem.level}
   								 			<div id='ff_{$divId}' style="display:none;padding-left:0px;" class="documents_padd">
  								  		{/if}
  								  		{assign var=level value=$elem.level}

    										{if $elem.type neq $doc_item}
    											{if $elem.info.count neq '0'}
    												<div id="folder" onclick="showDir('ff_{$elem.info.id}')" class="category_folder category_folder_hover">
    													<p><input class="radio" type="radio" name="file" value="{$elem.info.id}" onClick="setInfo('{$elem.info.id}','{$elem.type}','{$elem.info.name}','{$elem.info.description}','{$elem.info.parent_id}')" {if $info.file eq $elem.info.id}checked{/if}><div>{$elem.info.name}({$elem.info.count})</div></p>
    												</div>
    												{assign var=divId value=$elem.info.id}
    											{else}
    												<div id="folder" class="category_folder">
    													<p><input class="radio" type="radio" name="file" value="{$elem.info.id}" onClick="setInfo('{$elem.info.id}','{$elem.type}','{$elem.info.name}','{$elem.info.description}','{$elem.info.parent_id}')" {if $info.file eq $elem.info.id}checked{/if}><div>{$elem.info.name}({$elem.info.count})</div></p>
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
			     				{*ERROR*}
								<div id="error_description" class="error_img" style="display:none;"><span class="error_text">Error!</span></div>
							    {*/ERROR*}
					</td>
				</tr>


				<tr class="border_users_b border_users_r">
					<td class="border_users_l" height="20" width="15%">
						Document name:
					</td>
					<td>
						<div id="docNameDiv" align="left">
							<input type='text' id='docName' name='name' value='{$info.name}'>
						</div>
							{*ERROR*}
								{if $request.action eq "addItem"}
									{if $error neq null}
										<div id="error_name" class="error_img" style="display:none;"><span class="error_text">Error! {$error}</span></div>
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
					<div id="docDescriptionDiv" align="left" >
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
 					<div id="div_select" onClick = "showDir('select')">
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
    											{*<!--<p><a href ="{$elem.info.link}" alt = "{$elem.info.description}">{$elem.info.name}</a></p>-->*}
    										{else}
    											{if $elem.info.count neq '0'}
    												<div id="folder" onclick="showDir('{$elem.info.id}')">
    													<p><input id="r_{$elem.info.id}" type="radio" name="folder" value="{$elem.info.id}" {if $info.folder eq $elem.info.id}checked{/if}>{$elem.info.name}({$elem.info.count})</p>
    												</div>
    												{assign var=divId value=$elem.info.id}
    											{else}
    												<div id="folder">
    													<p><input id="r_{$elem.info.id}" type="radio" name="folder" value="{$elem.info.id}" {if $info.folder eq $elem.info.id}checked{/if}>{$elem.info.name}({$elem.info.count})</p>
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
    										<p><input id="r_0" type="radio" name="folder" value="none" {if $info.folder eq 'none'}checked{/if}>MAIN FOLDER(without folder)</p>
   								 		</div>
                        </div>
                        	{*ERROR*}
									{if $error neq null}
										<div id="error_name" class="error_img" style="display:none;"><span class="error_text">Error! {$error}</span></div>
									{/if}
							{*/ERROR*}
				</td>
			</tr>

			<tr class="border_users_l border_users_r">
				<td colspan="2">&nbsp;
								{*ERROR*}
									{if $error neq null}
										<div id="error_name" class="error_img" ><span class="error_text">Error! {$error}</span></div>
									{/if}
								{*ERROR*}
				</td>
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
			<input type="submit" id='save' class="button" value="Save">
		</div>


		{*HIDDEN*}
		<input type='hidden' name='MAX_FILE_SIZE' value="52430000">
		<input type='hidden' name='action' value={$request.action}>
		<input type="hidden" name="id" value="{$request.id}">
		</form>
						</td>
			</tr>
		</table>
</div>


{include file="tpls:tpls/pleaseWait.tpl" text=$pleaseWaitReason}