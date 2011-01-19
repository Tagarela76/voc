<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>{$title}</title>
		
		<link href="style.css" rel="stylesheet" type="text/css">
		  <link rel="shortcut icon" href="images/vocicon.ico" type="image/x-icon">
		{*loadjs*}
		<script type="text/javascript" src='modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js'></script>
		{foreach from=$jsSources item=jsSource}
			<script type="text/javascript" src="{$jsSource}"></script>
		{/foreach}
		
		{foreach from=$cssSources item=cssSource}
			<link href="{$cssSource}" rel="stylesheet" type="text/css"/>
		{/foreach}			
	</head>
	
	<body>										
		<table width="100%" height="100%"  cellspacing="0" cellpadding="0" >			  						  
			<tr>											
				{include file="tpls:logo.tpl"}											
				<td  valign="top">
				{*table center*}										
					<table class="cell2"  cellspacing="0" cellpadding="0" height="100%" >
						<tr>
							<td  valign="top" align="center" >											
								<table width="100%" height="100%"   height="100%" cellspacing="0" cellpadding="0">
									<tr>																				
										{include file="tpls:tpls/categoriesListLeft.tpl"}							
										<td valign="top"  class="foot_block">
										
											{*Categories List*}											
											{include file="tpls:tpls/login_categoriesList.tpl"}
											
																																	
											{if $request.category == 'facility' && $request.action == 'browseCategory'}
												{include file="tpls:tpls/facBookmarksNew.tpl"}
											{elseif $request.category == 'department' && $request.action == 'browseCategory'}
												{include file="tpls:tpls/depBookmarks.tpl"}
											{/if}
											
											
											{*SORT*}
												{if $request.bookmark == 'department' || 
													$request.bookmark == 'logbook'||
													$request.bookmark == 'product'||
													$request.bookmark == 'accessory'||
													$request.bookmark == 'equipment'||
													$request.bookmark == 'inventory'||
													$request.bookmark == 'wastestorage'||
													$request.bookmark == 'mix'}
													{include file="tpls:tpls/sort.tpl"}
												{/if}
											{*/SORT*}							
											
											<table width='100%'>
												<tr>
													<td>
													{*FILTER*}
														{if $request.bookmark == 'department' || 
															$request.bookmark == 'logbook'||
															$request.bookmark == 'product'||
															$request.bookmark == 'mix'}
															{include file="tpls:tpls/filter.tpl"}
														{/if}
													{*/FILTER*}
													</td>
													<td align='right'>
														<br>																																	
													{*SEARCH*}																																	
														{if $request.bookmark == 'mix' || $request.bookmark == 'product' || $request.bookmark == 'logbook' || $request.bookmark == 'accessory'}
															<link href="modules/js/autocomplete/styles.css" rel="stylesheet" type="text/css"/>
																{literal}
																	<script>
																		var options, a;
																		jQuery(function(){
						  													options = { serviceUrl:'modules/ajax/autocomplete.php', 
						  														minChars:2, 
						  														delimiter: /(,|;)\s*/, 
						  														params: { {/literal}{if $request.bookmark == 'logbook'}facilityID{else}departmentID{/if}:'{$request.id}{literal}',
						  														 category:'{/literal}{$childCategory}{literal}'}, 
						  														deferRequestBy:300   								
						  													};
						  													a = $('#search').autocomplete(options);
																		});
																	</script>
																{/literal}
															{include file="tpls:tpls/search.tpl"}
														{/if}					
													{*/SEARCH*}
													</td>
												</tr>
											</table>						
																						
											
													
											{if $request.action == 'browseCategory'}			
												{include file="tpls:tpls/controlChildCategoriesList.tpl"}
											{/if}
											
											
											{if $request.action == 'viewDetails'}			
												{include file="tpls:tpls/controlViewDetailsCategory.tpl"}
											{/if}
								
								
											
											{include file="tpls:$tpl"}			
																			
										</td>										
									</tr>									
								</table>
							</td>
						</tr>
					</table>
				{*/table center*}								
				</td>
			</tr>
			{include file="tpls:footer.tpl"}																			
		</table>								
	</body>
	
</html>