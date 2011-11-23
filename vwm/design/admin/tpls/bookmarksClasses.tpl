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
				
				<li>  
					<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=coat" style="text-decoration: none; color: #333333;">
						{if $request.bookmark != "coat"}
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
						{else}
								<div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
						{/if}
						{$smarty.const.AI_LABEL_COAT_BOOKMARK}
									</div>
								</div>
					</a>
				</li>
				
				{*<li>  
					<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=product" style="text-decoration: none; color: #333333;">
						{if $request.bookmark != "product"}
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
						{else}
								<div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
						{/if}
						{$smarty.const.AI_LABEL_PRODUCT_BOOKMARK}
									</div>
								</div>
					</a>
				</li> 
				*}
				<li>  
					<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=pfpLibrary" style="text-decoration: none; color: #333333;">
						{if $request.bookmark != "pfpLibrary"}
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
						{else}
								<div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
						{/if}
						{$smarty.const.AI_LABEL_PFP_LIBRARY_BOOKMARK}
									</div>
								</div>
					</a>
				</li>
				
				<li>  
					<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=industryType" style="text-decoration: none; color: #333333;">
						{if $request.bookmark != "industryType"}
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
						{else}
								<div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
						{/if}
						{$smarty.const.AI_LABEL_INDUSTRY_TYPE_BOOKMARK}
									</div>
								</div>
					</a>
				</li>
				
				<li>  
					<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=industrySubType" style="text-decoration: none; color: #333333;">
						{if $request.bookmark != "industrySubType"}
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
						{else}
								<div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
						{/if}
						{$smarty.const.AI_LABEL_INDUSTRY_SUB_TYPE_BOOKMARK}
									</div>
								</div>
					</a>
				</li>
				
				<li>  
					<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=components" style="text-decoration: none; color: #333333;">
						{if $request.bookmark != "components"}
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
						{else}
								<div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
						{/if}
						{$smarty.const.AI_LABEL_COMPOUNDS_BOOKMARK}
									</div>
								</div>
					</a>
				</li>
				
				<li>  
					<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=agency" style="text-decoration: none; color: #333333;">
						{if $request.bookmark != "agency"}
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
						{else}
								<div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
						{/if}
						{$smarty.const.AI_LABEL_AGENCIES_BOOKMARK}
									</div>
								</div>
					</a>
				</li>
				
				<li>  
					<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=country" style="text-decoration: none; color: #333333;">
						{if $request.bookmark != "country"}
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
						{else}
								<div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
						{/if}
						{$smarty.const.AI_LABEL_COUNTRY_BOOKMARK}
									</div>
								</div>
					</a>
				</li>
				
				<li>  
					<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=rule" style="text-decoration: none; color: #333333;">
						{if $request.bookmark != "rule"}
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
						{else}
								<div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
						{/if}
						{$smarty.const.AI_LABEL_RULE_BOOKMARK}
									</div>
								</div>
					</a>
				</li>
				
				<li>  
					<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=substrate" style="text-decoration: none; color: #333333;">
						{if $request.bookmark != "substrate"}
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
						{else}
								<div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
						{/if}
						{$smarty.const.AI_LABEL_SUBSTRATE_BOOKMARK}
									</div>
								</div>
					</a>
				</li>
				
				<li>  
					<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=supplier" style="text-decoration: none; color: #333333;">
						{if $request.bookmark != "supplier"}
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
						{else}
								<div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
						{/if}
						{$smarty.const.AI_LABEL_SUPPLIER_BOOKMARK}
									</div>
								</div>
					</a>
				</li>
				
				{if $showFootprint neq 'false'}
				<li>  
					<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=emissionFactor" style="text-decoration: none; color: #333333;">
						{if $request.bookmark != "emissionFactor"}
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
						{else}
								<div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
						{/if}
						{$smarty.const.AI_LABEL_EFACTOR_BOOKMARK}
									</div>
								</div>
					</a>
				</li>	
				{/if}
				{*
				<li>  
					<a href="admin.php?action=browseCategory&category={$request.category}&bookmark=tabs" style="text-decoration: none; color: #333333;">
						{if $request.bookmark != "tabs"}
						<div class="deactiveBookmark_big">
							<div class="deactiveBookmark_right_big">
						{else}
								<div class = "activeBookmark_big">
									<div class = "activeBookmark_right_big">
						{/if}
						{$smarty.const.AI_LABEL_TABS_BOOKMARK}
									</div>
								</div>
					</a>
				</li>*}
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