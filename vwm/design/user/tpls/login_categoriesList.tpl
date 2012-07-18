<table class="top_block" border="0" width="100%" cellpadding=0 cellspacing=0>
    <tr>

		{*All companies > Company name > Faciality name > Department name*}

        <td class="padd7" {if $request.category == "company" || $request.category == "facility"}width="40%"{else}width="60%"{/if} valign="top">
			{if $request.category eq "root"}

				{if !$permissions.root.view}
					<div>
						<h1 class="logininfo">Welcome to VOC-WEB-MANAGER!</h1>
					</div>
				{else}
					<a href="{$urlRoot}" class="id_company_link ">All companies</a>
				{/if}

            {elseif $request.category eq "company"}

				{if $permissions.root.view}
					<a href="{$urlRoot}" class="id_company_link ">All companies</a>
					>
				{/if}
				{if !$permissions.company.view}
					<a href="{$urlCompany}" class="id_company_link">{$companyName|escape}</a>
				{else}
					<span class="id_company_link ">{$companyName|escape}</span>
				{/if}

			{elseif $request.category eq "facility"}

				{if $permissions.root.view}
					<a href="{$urlRoot}" class="id_company_link ">All companies</a>
					>
				{/if}
				{if $permissions.company.view}
					<a href="{$urlCompany}" class="id_company_link">{$companyName|escape}</a>
					>
				{else}
					<span class="id_company_link ">{$companyName|escape} > </span>
				{/if}
				{if !$permissions.facility.view}
					<a href="{$urlFacility}" class="id_company_link">{$facilityName|escape}</a>
				{else}
					<span class="id_company_link ">{$facilityName|escape}</span>
				{/if}

			{elseif  $request.category eq "department"}

				{if $permissions.root.view}
					<a href="{$urlRoot}" class="id_company_link ">All companies</a>
					>
				{/if}
				{if $permissions.company.view}
					<a href="{$urlCompany}" class="id_company_link">{$companyName|escape}</a>
					>
				{else}
					<span class="id_company_link ">{$companyName|escape} > </span>
				{/if}
				{if $permissions.facility.view}
					<a href="{$urlFacility}" class="id_company_link">{$facilityName|escape}</a>
					>
				{else}
					<span class="id_company_link ">{$facilityName|escape} > </span>
				{/if}
				{if !$permissions.department.view}
					<a href="{$urlDepartment}" class="id_company_link">{$departmentName|escape}</a>
				{else}
					<span class="id_company_link ">{$departmentName|escape}</span>
				{/if}
            {/if}

			{*/All companies > Company name > Faciality name > Department name*}

            <br>
            <br>

			{*Contacts*}
            {if $request.category == "company" || $request.category == "facility"}
				{if $request.action != 'addItem' && $request.action != 'deleteItem'}
					<i>{$address|escape}</i>
					<br>
					<i>{$contact|escape}&nbsp;( {$phone|escape} )</i>
					<br>
				{/if}
            {/if}

			{if $request.category != "root" && $request.action=="browseCategory"}
				{include file="tpls:tpls/controlBrowseCategory.tpl"}
			{/if}
            {*/Contacts*}

        </td>

		{if  $request.category == "company" || $request.category == "facility"}
			<td align="left" class="padd7" valign="top" width="20%">
				<a href="?action=showTraining&category={$request.category}&id={$request.id}" class="id_company_link ">Training</a> |
				<!--<a href="{$urlRoot}" class="id_company_link " >Forms</a> -->
				<a href="#" class="id_company_link " id="toggler">Forms<small class="arrow_d"> ▼</small></a>
				<ul style="WIDTH: 150px" id="drop-down-list" class="no-display drop-down-block">
					<li><a href="?action=userRequest&category={$request.category}&id={$request.id}">Username & Password</a></li>
					{if ($request.category == 'company')}
						<li><a href="?action=companySetupRequest&category={$request.category}&id={$request.id}">Facility Setup</a></li>
					{/if}
					{if ($request.category == 'facility')}
						<li><a href="?action=companySetupRequest&category={$request.category}&id={$request.id}">Department Setup</a></li>
					{/if}
				</ul>
			</td>
		{elseif $request.category == "department"}
			<td align="left" class="padd7" valign="top" width="15%">
				<a href="?action=showTraining&category={$request.category}&id={$request.id}" class="id_company_link ">Training</a>
			</td>
		{/if}

        <td width="35%" class="padd7" valign="top" align="right">
            <table cellpadding=3 cellspacing=0>
                <tr>
					<td></td>
                    <td>
                        <span class="nameCompany">
                            <p>
                                {$accessname}
                            </p>
                        </span>
                    </td>
                    <td>
                        {if $request.category != 'root' && !($request.action == 'addItem' && $request.category == 'company')}
							<div align="center">
								{*<input type="button" class="button" value="Settings" onclick="location.href='?action=settings&itemID={$smarty.session.overCategoryType}&id={$smarty.session.CategoryID}'">*}
								{*<input type="button" class="button" value="Settings" onclick="location.href='?action=settings&category={if $request.action!='addItem'}{$request.category}{else}{$request.parent_category}{/if}&id={$request.id}'">*}
								<input type="button" class="button" value="Settings" onclick="location.href='?action=settings{if $request.category && $request.id && $request.bookmark}&category={$request.category}&id={$request.id}&bookmark={$request.bookmark}'"
								{elseif $request.category && $request.id && !$request.facilityID && !$request.departmentID}&category={$request.category}&id={$request.id}'"
								{elseif $request.category && $request.facilityID}&category=facility&id={$request.facilityID}&bookmark={$request.category}'"
								{elseif $request.category && $request.departmentID}&category=department&id={$request.departmentID}&bookmark={$request.category}'"
								{/if}>
                        </div>
						{/if}
						</td>
					</tr>
					<tr>
						<td><a href="?action=showIssueReport&category={if $request.action!='addItem'}{$request.category}&id={$request.id}{else}{$request.parent_category}&id={$request.parent_id}{/if}"><img src="images/question_y.png" title="{$smarty.const.DESCRIPTION_SUGGEST_FEATURE}"/></a></td>
						<td align="middle">
							<a href="?action=showIssueReport&category={if $request.action!='addItem'}{$request.category}&id={$request.id}{else}{$request.parent_category}&id={$request.parent_id}{/if}" style="color:#506480;font-size:12px;" title="{$smarty.const.DESCRIPTION_SUGGEST_FEATURE}"><b>{$smarty.const.LINK_SUGGEST_FEATURE}</b></a>
						</td>
						<td>
							<div class="" align="center">
								<input type="button" class="button" value=" &nbsp;Logout&nbsp; " onclick="location.href='?action=logout'">
							</div>
						</td>
					</tr>

					{if  $request.category == "company" || $request.category == "facility"}
						<tr>
							<td></td>
							<td align="middle"></td>
							<td>
								<div class="" align="center">
									<input type="button" class="greenButton" value=" Payment " onclick="location.href='vps.php'">
								</div>
							</td>
						</tr>
					{/if}
					{*SEARCH IS FREEZED*}
					{*<tr>
                    <td valign="top">
					<input type='text' name='' value='' style="float:right;">
                    </td>
                    <td>
					<div align="center">
					<input type="button" class="button" value=" Search ">
					</div>
                    </td>
					</tr>*}
				</table>
			</td>
		</tr>
		<tr>
			<td>
				{*GLOBAL NOTIFICATIONS*}
				{if $globalColor eq "green"}
					{include file="tpls:tpls/notify/greenNotify.tpl" text=$globalMessage}
				{/if}
				{if $globalColor eq "orange"}
					{include file="tpls:tpls/notify/orangeNotify.tpl" text=$globalMessage}
				{/if}
				{if $globalColor eq "blue"}
					{include file="tpls:tpls/notify/blueNotify.tpl" text=$globalMessage}
				{/if}
			</td>
		</tr>
	</table>


	<SCRIPT type="text/javascript">

		{literal}
								   // определение браузера
									function getNameBrouser() {
									 var ua = navigator.userAgent.toLowerCase();
									 // Определим Internet Explorer
									 if (ua.indexOf("msie") != -1 && ua.indexOf("opera") == -1 && ua.indexOf("webtv") == -1) {
									   return "msie"
									 }
									 // Opera
									 if (ua.indexOf("opera") != -1) {
									   return "opera"
									 }
									 // Gecko = Mozilla + Firefox + Netscape
									 if (ua.indexOf("gecko") != -1) {
									   return "gecko";
									 }
									 // Safari, используется в MAC OS
									 if (ua.indexOf("safari") != -1) {
									   return "safari";
									 }
									 // Konqueror, используется в UNIX-системах
									 if (ua.indexOf("konqueror") != -1) {
									   return "konqueror";
									 }
									 return "unknown";
									}

								// выпадающий список под ссылкой
									(function($) {
											$.fn.dropDownBlock = function(block, options) {
													var defaults = {
															speed: 'fast',
															top: $(this).height(),
															left: 0
													},
													opts 	= $.extend(defaults, options),
													toggler = $(this),
													block 	= $(block);
													toggler.css({'outline': 'none'})

													// определение типа браузера, если IE для верхнего отступа добавим 12px - Хак
													var browser =  getNameBrouser();
													var ie_top = 0;

													if(browser == 'msie')
													ie_top = 12;



													toggler.click(function(e) {
															e.preventDefault();
															$(block).css({
															'position' 	: 'absolute',
															'top' 		: ((toggler.offset().top + opts['top'])+ie_top) + 'px',
															'left' 		: (toggler.offset().left + opts['left']) + 'px'
													});
													if($(block).is(':visible')) $(block).fadeOut(opts['speed']);
													else $(block).fadeIn(opts['speed']);
													this.focus();
													});
													toggler.blur(function() {
															$(block).fadeOut(opts['speed']);
													});
											};
									})(jQuery);

									$('#toggler').dropDownBlock($('#drop-down-list'));





		{/literal}
	</SCRIPT>
