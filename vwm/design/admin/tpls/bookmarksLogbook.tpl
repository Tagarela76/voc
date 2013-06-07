<table cellspacing="0" cellpadding="0" width="100%" style="margin:10px 0 0 0;">
	<tr>
		<td align="center" class="bookmark_fon">
			<table cellspacing="0" cellpadding="0" height="100%" class="bookmarks_big" align='right'>
				<tr>
					<td align="right">
							<a href="admin.php?action=browseCategory&category=logbook&bookmark=logbookSetupTemplate" style="text-decoration: none; color: #333333;">
								{if $bookmark != "logbookSetupTemplate"}
									<div class="deactiveBookmark_big">
										<div class="deactiveBookmark_right_big">
								{else}
                                    <div class = "activeBookmark_brown">
										<div class = "activeBookmark_brown_right">
								{/if}
										Logbook Setup Template
										</div>
									</div>
							</a>
					</td>
					<td align="right">
						<a href="admin.php?action=browseCategory&category=logbook&bookmark=logbookInspectionType" style="text-decoration: none; color: #333333;">
							{if $bookmark != "logbookInspectionType"}
								<div class="deactiveBookmark_big">
									<div class="deactiveBookmark_right_big">
							{else}
								<div class = "activeBookmark_brown">
									<div class = "activeBookmark_brown_right">
							{/if}
									Logbook Inspection Type
									</div>
								</div>
						</a>
					</td>
				</tr>
				<tr height="19">
					<td></td>
				</tr>
			</table>																															
		</td>
	</tr>
	<input type="hidden" name="hiddenSelectedBookmark" id="selectedBookmark" value="{$selectedBookmark}"/>
</table>