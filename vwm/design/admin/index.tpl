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

											{if $request.action == 'browseCategory'}
												{if $request.category == 'tables'}
													{include file="tpls:tpls/bookmarksClasses.tpl"}
												{elseif $request.category == 'users'}
													{include file="tpls:tpls/bookmarksUsers.tpl"}
												{elseif $request.category == 'pfps'}
													{include file="tpls:tpls/bookmarksPfpLibrary.tpl"}
												{elseif $request.category == 'product'}
													{include file="tpls:tpls/bookmarksProduct.tpl"}	
												{elseif $request.category == 'salescontacts'}
													{include file="tpls:tpls/bookmarkSales.tpl"}
												{elseif $request.category == 'requests'}
													{include file="tpls:tpls/bookmarksRequests.tpl"}	
												{/if}
											{/if}

                                                                                        {if $request.category == 'salescontacts'}
                                                                                                {include file="tpls:tpls/subBookmarks.tpl"}
                                                                                        {/if}
																						
											{if $request.category == 'product'}
													{include file="tpls:tpls/productTypesDropDown.tpl"}
											{/if}
											{*SORT*}
												{if $request.bookmark == 'coat'||
													$request.bookmark == 'components'||
													$request.bookmark == 'country'||
													$request.bookmark == 'rule'||
													$request.bookmark == 'supplier'||
													$request.bookmark == 'agency'||
													$request.category == 'product'||
													$request.category == 'accessory'||
													$request.category == 'salescontacts' ||
													$request.category == 'logging' ||
													$request.category == 'users'}
													{include file="tpls:tpls/sort.tpl"}
												{/if}
											{*/SORT*}

											{if $request.action == 'browseCategory'}
											<table width='100%'>
												<tr>
													<td>
													{*FILTER*}
														{if $request.bookmark == 'coat'||
															$request.bookmark == 'components'||
															$request.bookmark == 'country'||
															$request.bookmark == 'rule'||
															$request.bookmark == 'supplier'||
															$request.bookmark == 'agency'||
															$request.category == 'users' ||
															
                                                            $request.category == 'salescontacts'}
															{include file="tpls:tpls/filter.tpl"}
														{/if}
													{*/FILTER*}
													
													
													</td>
													<td align='right'>
														<br>
														{*SEARCH*}
															{if $request.bookmark == "industryType" || $request.bookmark == "industrySubType"}
															<link href="modules/js/autocomplete/styles.css" rel="stylesheet" type="text/css"/>
															{literal}
																<script>
																	var options, a;
																	jQuery(function(){
																		options = { serviceUrl:'modules/ajax/autocomplete.php',
																					minChars:2,
																					delimiter: /(,|;)\s*/,
																					params: {category: '{/literal}{$request.bookmark}All{literal}'},
																					deferRequestBy:300
																		};
																		a = $('#search').autocomplete(options);
																	});
																</script>
															{/literal}
															{include file="tpls:tpls/search.tpl"}
															
															{elseif $request.category == "product"}
															<link href="modules/js/autocomplete/styles.css" rel="stylesheet" type="text/css"/>
															{literal}
																<script>
																	var options, a;
																	jQuery(function(){
																		options = { serviceUrl:'modules/ajax/autocomplete.php',
																					minChars:2,
																					delimiter: /(,|;)\s*/,
																					params: {category: '{/literal}{$request.category}All{literal}'},
																					deferRequestBy:300
																		};
																		a = $('#search').autocomplete(options);
																	});
																</script>
															{/literal}
															{include file="tpls:tpls/search.tpl"}
															
															{elseif $request.category == "accessory"}
															<link href="modules/js/autocomplete/styles.css" rel="stylesheet" type="text/css"/>
															{literal}
																<script>
																	var options, a;
																	jQuery(function(){
																		options = { serviceUrl:'modules/ajax/autocomplete.php',
																					minChars:2,
																					delimiter: /(,|;)\s*/,
																					params: {category: '{/literal}{$request.category}All{literal}'},
																					deferRequestBy:300
																		};
																		a = $('#search').autocomplete(options);
																	});
																</script>
															{/literal}
															{include file="tpls:tpls/search.tpl"}
															
															{elseif $request.category == "salescontacts"}
															<link href="modules/js/autocomplete/styles.css" rel="stylesheet" type="text/css"/>
															{literal}
																<script>
																	var options, a;
																	jQuery(function(){
																		options = { serviceUrl:'modules/ajax/autocomplete.php',
																					minChars:2,
																					delimiter: /(,|;)\s*/,
																					params: {subBookmark:'{/literal}{$request.subBookmark}{literal}',
                                                                                    category:'{/literal}{$request.category}{literal}'},
																					deferRequestBy:300
																		};
																		a = $('#search').autocomplete(options);
																	});
																</script>
															{/literal}
															{include file="tpls:tpls/search.tpl"}
															
															{elseif $request.category == "logging"}
															<link href="modules/js/autocomplete/styles.css" rel="stylesheet" type="text/css"/>
															{literal}
																<script>
																	var options, a;
																	jQuery(function(){
																		options = { serviceUrl:'modules/ajax/autocomplete.php',
																					minChars:2,
																					delimiter: /(,|;)\s*/,
																					params: {category: '{/literal}{$request.category}All{literal}'},
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
											{/if}

											{if !$doNotShowControls}
												{if $request.action == 'browseCategory'}
													{include file="tpls:tpls/controlCategoriesList.tpl"}
												{elseif $request.action == 'viewDetails'}
													{include file="tpls:tpls/controlViewDetailsCategory.tpl"}
												{/if}
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