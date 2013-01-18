<link href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="modules/js/jquery.tinycarousel.min.js"></script>
{literal}
	<script type="text/javascript">
		$(document).ready(function(){
			$('.viewport').width(document.body.clientWidth - $('.dotted_right').width().valueOf() - 230 + 'px');
			$('#slider1').tinycarousel({duration: 200, display:1, start: document.getElementById('selectedBookmark').value});
		});
	</script>
{/literal}
<table cellspacing="0" cellpadding="0" width="100%" style="margin:10px 0 0 0;">
	<tr>
		<td align="center" class="bookmark_fon">
			<table cellspacing="0" cellpadding="0" height="100%" class="bookmarks_big" style="margin-left:20px;">
				<tr>
					<td align="right">
						<div id="slider1">
							<a class="buttons prev" href="#"><img src="images/slider-left-arrow.gif" width="16px" height="10px" title="Previous"/></a>
							<div class="viewport">
								<ul class="overview">
									<li>  
										<a href="admin.php?action=browseCategory&category=bulkUploader&bookmark=bulkUploader" style="text-decoration: none; color: #333333;">
											{if $bookmark != "bulkUploader"}
												<div class="deactiveBookmark_big">
													<div class="deactiveBookmark_right_big">
											{else}
												<div class = "activeBookmark_big">
													<div class = "activeBookmark_right_big">
											{/if}
												BULK UPLOADER
												</div>
											</div>
										</a>
									</li>
									<li>  
										<a href="admin.php?action=browseCategory&category=bulkUploader&bookmark=processUploader" style="text-decoration: none; color: #333333;">
											{if $bookmark != "processUploader"}
												<div class="deactiveBookmark_big">
													<div class="deactiveBookmark_right_big">
											{else}
												<div class = "activeBookmark_big">
													<div class = "activeBookmark_right_big">
											{/if}
													PROCESS
												</div>
											</div>
										</a>
									</li>
								</ul>
							</div>
							<a class="buttons next" href="#"><img src="images/slider-right-arrow.gif" width="16px" height="10px" title="Next"/></a>
						</div>
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