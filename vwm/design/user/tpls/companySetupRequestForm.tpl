{if $color eq "green"}
    {include file="tpls:tpls/notify/greenNotify.tpl" text=$message}
{/if}
{if $color eq "orange"}
    {include file="tpls:tpls/notify/orangeNotify.tpl" text=$message}
{/if}
{if $color eq "blue"}
    {include file="tpls:tpls/notify/blueNotify.tpl" text=$message}
{/if}

<form enctype="multipart/form-data" method="POST" action="?action=userRequest&category={$request.category}&id={$request.id}">
    {*shadow*}
    <table class="report_issue" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td valign="top" class="report_issue_top">
            </td>
        </tr>
        <tr>
            <td valign="top" class="report_issue_center" align="center">
                <h2>{$setupLevel} Setup Request Form</h2>
		{if $companyDetails|@count > 0}
                <div>
			<h3 onclick = "$('#companyDetails').slideToggle();">Company Details</h3>
			<div id="companyDetails" {if $facilityDetails|@count > 0}style="display:none;"{/if}>
				<table cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td class="border_users_l border_users_b us_gray">
							Company name:
						</td>
						<td class="border_users_l border_users_b border_users_r">
							<div class="floatleft">
								<input type='' name='name' value='{$companyDetails.name}' maxlength="96">
							</div>{if $validStatus.summary eq 'false'}
							{if $validStatus.name eq 'failed'}
								{*ERORR*}
								<div class="error_img">
									<span class="error_text">Error!</span>
								</div>
								{*/ERORR*}
							{elseif $validStatus.name eq 'alredyExist'}
								<div class="error_img">
									<span class="error_text">Entered name is alredy in use!</span>
								</div>
							{/if}
						{/if}
					</td>
				</tr>

				<tr>
					<td class="border_users_l border_users_b us_gray">
						Address:
					</td>
					<td class="border_users_l border_users_b border_users_r">
						<div class="floatleft">
							<input type='text' name='address' value='{$companyDetails.address}' maxlength="384">
						</div>{if $validStatus.summary eq 'false'}
						{if $validStatus.address eq 'failed'}
							{*ERORR*}
							<div class="error_img">
								<span class="error_text">Error!</span>
							</div>
							{*/ERORR*}
						{/if}
						{/if}
						</td>
					</tr>


					<tr>
						<td class="border_users_l border_users_b us_gray">
							Phone:
						</td>
						<td class="border_users_l border_users_b border_users_r">
							<div class="floatleft">
								<input type='text' name='phone' value='{$companyDetails.phone}' maxlength="32">
							</div>{if $validStatus.summary eq 'false'}
							{if $validStatus.phone eq 'failed'}
								{*ERORR*}
								<div class="error_img">
									<span class="error_text">Error!</span>
								</div>
								{*/ERORR*}
							{/if}
							{/if}
							</td>
						</tr>

						<tr>
							<td class="border_users_l border_users_b us_gray">
								Fax:
							</td>
							<td class="border_users_l border_users_b border_users_r">
								<div class="floatleft">
									<input type='text' name='fax' value='{$companyDetails.fax}' maxlength="32">
								</div>{if $validStatus.summary eq 'false'}
								{if $validStatus.fax eq 'failed'}
									{*ERORR*}
									<div class="error_img">
										<span class="error_text">Error!</span>
									</div>
									{*/ERORR*}
								{/if}
								{/if}
								</td>
							</tr>

							<tr>
								<td class="border_users_l border_users_b us_gray">
									Email:
								</td>
								<td class="border_users_l border_users_b border_users_r">
									<div class="floatleft">
										<input type='text' name='email' value='{$companyDetails.email}' maxlength="128">
									</div>{if $validStatus.summary eq 'false'}
									{if $validStatus.email eq 'failed'}
										{*ERORR*}
										<div class="error_img">
											<span class="error_text">Error!</span>
										</div>
										{*/ERORR*}
									{/if}
									{/if}
									</td>
								</tr>
								<!--</tr>-->

								<tr>
									<td class="border_users_l border_users_b us_gray">
										Contact:
									</td>
									<td class="border_users_l border_users_b border_users_r">
										<div class="floatleft">
											<input type='text' name='contact' value='{$companyDetails.contact}' maxlength="384">
										</div>{if $validStatus.summary eq 'false'}
										{if $validStatus.contact eq 'failed'}
											{*ERORR*}
											<div class="error_img">
												<span class="error_text">Error!</span>
											</div>
											{*/ERORR*}
										{/if}
										{/if}
										</td>
									</tr>


								</table>
							</div>
		</div>
		{/if}



		{if $facilityDetails|@count > 0}
		<div>
			<h3>Facility Details</h3>
			<div>

			</div>
		</div>
		{/if}


				<div>
					<input type="button" class="button" value="Add Additional facilities"/>
					<input type="button" class="button" value="Cancel"/>
					<input type="submit" class="button" value="Submit"/>
				</div>
                {*<input type="hidden" name="action" value="reportIssue">*}
                {*shadow*}
            </td>
        </tr>
        <tr>
            <td valign="top" class="report_issue_bottom">
            </td>
        </tr>
    </table>
    {**}
</form>