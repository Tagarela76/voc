<td class="dotted_right bg_left " valign="top" width="180px" >
	<table cellspacing="0" cellpadding="0" width="180px">
		<tr>
			<td width="100%">
				<div align="left" width="100%"  class="{if $categoryID == 'tab_class'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
					  <ul class="link">
						<li>
							<a href="admin.php?action=browseCategory&categoryID=class&itemID=apmethod" class="id_company">Tables</a>
						</li>
					</ul>
				</div>
			</div>
						
					<div align="left" width="100%"  class="{if $categoryID == 'tab_users'}left_m_active{else}left_m{/if}" >
					   <div align="left" width="100%">
					<ul class="link">
						<li>
							<a href="admin.php?action=browseCategory&categoryID=users&itemID=company" class="id_company">Users</a>
						</li>
					</ul>
				</div>
						</div>
						
				<div align="left" width="100%"  class="{if $categoryID == 'tab_issues'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
					  <ul class="link">
						<li>
							<a href="admin.php?action=browseCategory&categoryID=issues" class="id_company">Issues</a>
						</li>
					</ul>
				</div>				
			</div>
				<div align="left" width="100%"  class="{if $categoryID == 'tab_bulkUploader'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
					  <ul class="link">
						<li>
							<a href="admin.php?action=browseCategory&categoryID=bulkUploader" class="id_company">Bulk Uploader</a>
						</li>
					</ul>
					</div>
				</div>
				
				<div align="left" width="100%"  class="{if $categoryID == 'tab_vps'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
						<li>
							<a href="admin.php?action=vps&vpsAction=browseCategory&itemID=billing" class="id_company">VOC Payment System</a>
						</li>
						</ul>
					</div>
				</div>
				
				<div align="left" width="100%"  class="{if $categoryID == 'tab_track'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
						<li>
							<a href="admin.php?action=browseCategory&categoryID=track" class="id_company">Tracking System</a>
						</li>
						</ul>
					</div>
				</div>
				
				<div align="left" width="100%"  class="{if $categoryID == 'tab_modulars'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
						<li>
							<a href="admin.php?action=browseCategory&categoryID=modulars" class="id_company">Modulars</a>
						</li>
						</ul>
					</div>
				</div>
				
				{if $showReports neq 'false'}
					<div align="left" width="100%"  class="{if $categoryID == 'tab_reports'}left_m_active{else}left_m{/if}">
						<div align="left" width="100%">
							<ul class="link">
							<li>
								<a href="admin.php?action=browseCategory&categoryID=reports" class="id_company">Reports</a>
							</li>
							</ul>
						</div>
					</div>
				{/if}
				
			</td>
		</tr>
	</table>
</td>