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
	<td>
		<div id="slider1">
		<a class="buttons prev" href="#"><img src="images/slider-left-arrow.gif" width="16px" height="10px" title="Previous"/></a>
		<div class="viewport">
			<ul class="overview">
				
				{*BEGIN LIST*}				
				{section name=i loop=$bookmarks}    
					<li>  {i}
					     <a href="admin.php?action=browseCategory&category={$request.category}&bookmark={$request.bookmark}{if $bookmarks[i].supplier_id != 45}&subBookmark={$bookmarks[i].supplier_id}{/if}" style="text-decoration: none; color: #333333;">
							{if $request.subBookmark == $bookmarks[i].supplier_id}
								<div  class = "activeBookmark">  <div class = "activeBookmark_right">
									{else}                    
										
										<div class="deactiveBookmark"><div class="deactiveBookmark_right">
										
							{/if}
							{$bookmarks[i].supplier|lower}
							
							</div>
						</div></a>
					</li>
				{/section}	
				{*END LIST*}
				
				{*
				<li>  
					<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=apmethod" style="text-decoration: none; color: #333333;">
						{if $request.bookmark != "apmethod"}	
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
						{else}
								<div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
						{/if}
						{$smarty.const.AI_LABEL_APMETHOD_BOOKMARK}
									</div>
								</div>
					</a>
				</li>
				*}
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
