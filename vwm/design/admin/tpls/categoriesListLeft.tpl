<td class="dotted_right bg_left " valign="top" width="180px" >
	<table cellspacing="0" cellpadding="0" width="180px">
		<tr>
			<td width="100%">
				<div align="left" width="100%"  class="{if $parent == 'tables'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
					  <ul class="link">
						<li>
							<a href="admin.php?action=browseCategory&category=tables&bookmark=apmethod" class="id_company">Tables</a>
						</li>
					</ul>
				</div>
			</div>
						
					<div align="left" width="100%"  class="{if $parent == 'users'}left_m_active{else}left_m{/if}" >
					   <div align="left" width="100%">
					<ul class="link">
						<li>
							<a href="admin.php?action=browseCategory&category=users&bookmark=company" class="id_company">Users</a>
						</li>
					</ul>
				</div>
						</div>
						
				<div align="left" width="100%"  class="{if $parent == 'issue'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
					  <ul class="link">
						<li>
							<a href="admin.php?action=browseCategory&category=issue" class="id_company">Issues</a>
						</li>
					</ul>
				</div>				
			</div>
				<div align="left" width="100%"  class="{if $parent == 'bulkUploader'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
					  <ul class="link">
						<li>
							<a href="admin.php?action=browseCategory&category=bulkUploader" class="id_company">Bulk Uploader</a>
						</li>
					</ul>
					</div>
				</div>
				
				<div align="left" width="100%"  class="{if $parent == 'vps'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
						<li>
							<a href="admin.php?action=vps&vpsAction=browseCategory&itemID=billing" class="id_company">VOC Payment System</a>
						</li>
						</ul>
					</div>
				</div>
				
				<div align="left" width="100%"  class="{if $parent == 'track'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
						<li>
							<a href="admin.php?action=browseCategory&category=track" class="id_company">Tracking System</a>
						</li>
						</ul>
					</div>
				</div>
				
				<div align="left" width="100%"  class="{if $parent == 'modulars'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
						<li>
							<a href="admin.php?action=browseCategory&category=modulars" class="id_company">Modulars</a>
						</li>
						</ul>
					</div>
				</div>
				
				{if $showReports neq 'false'}
					<div align="left" width="100%"  class="{if $parent == 'reports'}left_m_active{else}left_m{/if}">
						<div align="left" width="100%">
							<ul class="link">
							<li>
								<a href="admin.php?action=browseCategory&category=reports" class="id_company">Reports</a>
							</li>
							</ul>
						</div>
					</div>
				{/if}
				
				<div align="left" width="100%"  class="{if $parent == 'salescontacts'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
						<li>
							<a href="admin.php?action=browseCategory&category=salescontacts&bookmark=contacts" class="id_company">Sales</a>
						</li>
						</ul>
					</div>
				</div>

				<div align="left" width="100%"  class="{if $parent == 'requests'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
						<li>
							<a href="admin.php?action=browseCategory&category=requests" class="id_company">Requests</a>
						</li>
						</ul>
					</div>
				</div>
				
			</td>
		</tr>
	</table>
</td>