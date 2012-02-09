<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>

		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>{$title}</title>
		
		<link href="style.css" rel="stylesheet" type="text/css">
		  <link rel="shortcut icon" href="images/vocicon.ico" type="image/x-icon">
		{*loadjs*}
		<!--  <script type="text/javascript" src='modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js'></script> -->
		<script type="text/javascript" src='modules/js/jquery-1.5.2.js'></script>
		
		{if $notify}
			
			
			<script type="text/javascript">

			var notifyParams = new Object();

			{if $notify.params}
					
			{foreach from=$notify.params item=i key=k}
				notifyParams.{$k} = "{$i}";
			{/foreach}
			
			 
			{/if}
			/*notifyParams.color = "Black";
			notifyParams.backgroundColor = "Yellow"; {"color": "Black", "backgroundColor": "Yellow" } ;*/
			
			var notifyText = "{$notify.text}";

			
			</script>
			<script type="text/javascript" src='modules/js/notify.js'></script>
			
		{/if}
		
		{foreach from=$jsSources item=jsSource}
			<script type="text/javascript" src="{$jsSource}"></script>
		{/foreach}
		
		{foreach from=$cssSources item=cssSource}
			<link href="{$cssSource}" rel="stylesheet" type="text/css"/>
		{/foreach}			
	</head>
	
	<body>		
	
		{include file="tpls:tpls/notify/popupNotify.tpl"}
								
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
													$request.bookmark == 'mix' ||
													$request.bookmark == 'solventplan'}
													
													{if $request.tab != "pfp"}
														{include file="tpls:tpls/sort.tpl"}
													{/if}
                                                                                                        
												{/if}
											{*/SORT*}							
											
											<table width='100%'>
												<tr>
													<td>
													{*FILTER*}
														{if $request.tab != "pfp" and ($request.bookmark == 'department' || 
															$request.bookmark == 'logbook'||
															$request.bookmark == 'product'||
															$request.bookmark == 'mix')}
															
															{include file="tpls:tpls/filter.tpl"}
															
														{/if}
													{*/FILTER*}
													</td>
													<td align='right'>
														<br>																																	
													{*SEARCH*}																																	
														{if $request.tab != 'pfp' and ($request.bookmark == 'mix' || $request.bookmark == 'product' || $request.bookmark == 'logbook' || $request.bookmark == 'accessory')}
                                                                                                                        
															<link href="modules/js/autocomplete/styles.css" rel="stylesheet" type="text/css"/>
																{literal}
																	<script>
																		var options, a;
																		jQuery(function(){
						  													options = { serviceUrl:'modules/ajax/autocomplete.php', 
						  														minChars:2, 
						  														delimiter: /(,|;)\s*/, 
						  														params: { {/literal}
                                                                                                                                                                            {if $request.category == 'facility' }
                                                                                                                                                                                facilityID
                                                                                                                                                                            {elseif $request.category == 'department'}
                                                                                                                                                                                departmentID
                                                                                                                                                                            {/if}
                                                                                                                                                                            :'{$request.id}{literal}',
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


											{if $request.action == 'browseCategory' && !($request.bookmark == 'inventory' && $request.tab != 'orders')}	
												{include file="tpls:tpls/controlChildCategoriesList.tpl"}
											{/if}

                                                                                

											{if $request.action == 'viewDetails' || $request.action == 'viewPFPDetails'}
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