<td class="dotted_right bg_left " valign="top" width="180px" >
	<table cellspacing="0" cellpadding="0" width="180px">
		<tr>
			<td width="100%">
				<div align="left" width="100%"  class="{if $parent == 'dashboard'}left_m_active {else} left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
							<li>
								<a href="sales.php?action=browseCategory&category=dashboard" class="id_company">Dashboard</a>
							</li>
						</ul>
					</div>
				</div>

				<div align="left" width="100%"  class="{if $request.salesDocsCategory == 1}left_m_active {else} left_m{/if}" >
					<div align="left" width="100%">
						<ul class="link">
							<li>
								<a href="sales.php?action=browseCategory&category=contracts&salesDocsCategory=1" class="id_company">Marketing</a>
							</li>
						</ul>
					</div>
				</div>

				<div align="left" width="100%"  class="{if $parent == 'tutorials'}left_m_active {else} left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
							<li>
								<a href="sales.php?action=browseCategory&category=tutorials" class="id_company">Video Tutorials</a>
							</li>
						</ul>
					</div>
				</div>

				<div align="left" width="100%"  class="{if $parent == 'forms'}left_m_active {else} left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
							<li>
								<a href="sales.php?action=browseCategory&category=forms&bookmark=userRequest" class="id_company">New Customer Setup Form</a>
							</li>
						</ul>
					</div>
				</div>

				<div align="left" width="100%"  class="{if $request.salesDocsCategory == 2}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
							<li>
								<a href="sales.php?action=browseCategory&category=contracts&salesDocsCategory=2" class="id_company">Training</a>
							</li>
						</ul>
					</div>
				</div>

				<div align="left" width="100%"  class="{if $parent == 'liveDemo'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
							<li>
								<a href="index.php?action=browseCategory&category=facility&id=118&bookmark=department" class="id_company">Live Demo</a>
							</li>
						</ul>
					</div>
				</div>

				<div align="left" width="100%"  class="{if $parent == 'salescontacts'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
						<li>
							<a href="sales.php?action=browseCategory&category=salescontacts&bookmark=contacts" class="id_company">My Contacts</a>
						</li>
						</ul>
					</div>
				</div>
<!-- CALENDAR
				<div align="left" width="100%"  class="{if $parent == 'calendar'}left_m_active{else}left_m{/if}">
					<div align="left" width="100%">
						<ul class="link">
						<li>
							<a href="sales.php?action=browseCategory&category=calendar" class="id_company">Calendar</a>
						</li>
						</ul>
					</div>
				</div>
-->
			</td>
		</tr>
	</table>
</td>