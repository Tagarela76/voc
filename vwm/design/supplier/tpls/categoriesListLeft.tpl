<td class="dotted_right bg_left " valign="top" width="180px" >
	{if $request.category != 'root' && $request.action != 'addItem' && $request.action != 'deleteItem' }
	<table cellspacing="0" cellpadding="0" width="180px">
		<tr>
			<td width="100%">
				
				{foreach from=$upCategory item=data}
				<div align="left" width="100%"  class="{if $leftCategoryID == $data.id}left_m_active {else} left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
							<li>
								<a href="{$data.url}" class="id_company">{$data.name}</a>
							</li>
							
						</ul>
					</div>
				</div>
				{/foreach}

			</td>
		</tr>
	</table>
{/if}
</td>