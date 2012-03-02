<td class="dotted_right bg_left " valign="top" width="180px" >
	{if $request.category != 'root' && $request.action != 'addItem' }
	<table cellspacing="0" cellpadding="0" width="180px">
		<tr>
			<td width="100%">
				<div align="left" width="100%"  class="{if $parent == 'sales'}left_m_active {else} left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
							<li>
								<a href="supplier.php?action=browseCategory&category=sales&bookmark=clients" class="id_company">Sales</a>
							</li>
						</ul>
					</div>
				</div>
{*
				<div align="left" width="100%"  class="{if $parent == 'profile'}left_m_active {else} left_m{/if}" >
					<div align="left" width="100%">
						<ul class="link">
							<li>
								<a href="supplier.php?action=browseCategory&category=profile" class="id_company">Profile</a>
							</li>
						</ul>
					</div>
				</div>
*}		
				<div align="left" width="100%"  class="{if $parent == 'users'}left_m_active {else} left_m{/if}" >
					<div align="left" width="100%">
						<ul class="link">
							<li>
								<a href="supplier.php?action=browseCategory&category=usersSupplier" class="id_company">Users</a>
							</li>
						</ul>
					</div>
				</div>
			</td>
		</tr>
	</table>
{/if}
</td>