<div style="padding:7px;">
		<table class="users" align="center" cellpadding="0" cellspacing="0">
			<tr class="users_top_yellowgreen users_u_top_size">
				<td class="users_u_top_yellowgreen" width="27%">
					<span >View Contact details</span>
				</td>
				<td class="users_u_top_r_yellowgreen" width="300">
					&nbsp;
				</td>								
			</tr>

			<tr>
				<td class="border_users_l border_users_b" height="20">
					Company:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->company|escape}</div>								
				</td>
			</tr>		
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Contact:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->contact|escape}</div>								
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Title:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->title|escape}</div>								
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Phone:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->phone|phone_format|escape}</div>								
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Cell/mobile phone:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->cellphone|phone_format|escape}</div>								
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Fax:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->fax|phone_format|escape}</div>								
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Email:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->email|escape}</div>								
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Website:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->website|escape}</div>								
				</td>
			</tr>	
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Mailing address:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->mail|escape}</div>								
				</td>
			</tr>
												
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Industry:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->industry|escape}</div>								
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Comments:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->getCommentsHTML()|escape}</div>								
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					State:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->state_name|escape}</div>								
				</td>
			</tr>

			<tr>
				<td class="border_users_l border_users_b" height="20">
					City:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->city|escape}</div>								
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Zip Code:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->zip_code|escape}</div>								
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Country:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->country_name|escape}</div>								
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					State:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->state_name|escape}</div>								
				</td>
			</tr>
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Account number:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->acc_number|escape}</div>								
				</td>
			</tr>	
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Paint Supplier:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->paint_supplier|escape}</div>								
				</td>
			</tr>	
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Paint System:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->paint_system|escape}</div>								
				</td>
			</tr>	
			
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Jobber:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->jobber|escape}</div>								
				</td>
			</tr>

			<tr>
				<td class="border_users_l border_users_b" height="20">
					Shop type:
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$contact->getShopTypeName()|escape}</div>
				</td>
			</tr>
			<tr>
				<td class="border_users_l border_users_b" height="20">
					Features :
				</td>
				<td class="border_users_l border_users_b border_users_r">
					<div align="left" >&nbsp;{$conatactPreferedFeatures|escape}</div>
				</td>
			</tr>	
						<tr>
             				 <td height="20" class="users_u_bottom">
             	 				&nbsp;
                			 </td>
                			 <td height="20" class="users_u_bottom_r">
                 				&nbsp;
                 			</td>
           				</tr>
			</table>
</div>

{include file="tpls:tpls/_meetingsWithContact.tpl" meetings=$contact->getMeetings()}