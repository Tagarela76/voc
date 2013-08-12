<form action="vps.php?action=contactAdmin" method="post">
  {*shadow*}	
	  <table class="report_issue_green" cellspacing="0" cellpadding="0" align="center">
                          <tr>
                               <td valign="top" class="report_issue_top_green">
							   
                               </td>
							   </tr>
							   <tr>
                               <td valign="top" class="report_issue_center" align="center">
	 {**}
	<table cellspacing="0" cellpadding="0" valign="top" width="440px">		
			
		<tr>
			<td colspan="2" class="contactAdmin">
			     Describe yours performatives.<br></>Administrator will contact you in near future.
			</td>
		</tr>
		<tr>
			<td>
				Emission Sources
			</td>
			<td width="300px">
				<select name="bplimit">
					{section name=i loop=47}
					<option value="{$smarty.section.i.index+4}" {if $request.bplimit eq $smarty.section.i.index+4} selected {/if}>{$smarty.section.i.index+4}</option>
					{/section}
				</select>			    
			</td>
		</tr>
		<tr>
			<td>
				Months			    
			</td>
			<td>
				<select name="monthsCount">				
					{section name=i loop=36}
					<option value="{$smarty.section.i.index+1}" {if $request.monthsCount eq $smarty.section.i.index+1} selected {/if}>{$smarty.section.i.index+1}</option>
					{/section}
				</select>	    
			</td>
		</tr>
		<tr>
			<td>
				Type
			</td>
			<td>
				<select name="type">					
					<option value="self" {if $request.type eq "self"} selected {/if}>Self Compliance & Reporting</option>
					<option value="gyant" selected>Gyant Compliance & Reporting</option>					
				</select>						
			</td>
		</tr>
		<tr>
			<td>
				SDS Limit
			</td>
			<td>				
				<input type="text" name="MSDSDefaultLimit" value="{$request.MSDSDefaultLimit}">
				{if $problems.MSDSDefaultLimit}
				{*ERROR*}					
				<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
				<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
				{*/ERROR*}
				{/if}					
			</td>
		</tr>
		<tr>
			<td>
				Memory Storage Limit
			</td>
			<td>				
				<input type="text" name="memoryDefaultLimit" value="{$request.memoryDefaultLimit}"> Mb
				{if $problems.memoryDefaultLimit}
				{*ERROR*}					
				<div style="width:80px;margin:2px 0px 0px 5px;" align="left"><img src='design/user/img/alert1.gif' height=16  style="float:left;">
				<font style="float:left;vertical-align:bottom;color:red;margin:1px 0px 0px 5px;">Error!</font></div>
				{*/ERROR*}
				{/if}					
			</td>
		</tr>
		<tr>		
			<td valign="top">Description</td>			
			<td valign="top"  align="left">
			<textarea  class="reportIssue_text" name="description">{$request.description}</textarea>			
			</td>			
		</tr>
		
		<tr>
			<td style="padding:5px 5px 0px 5px" align="left" colspan="2">
			
			    <input type="submit" name="contactAdminAction" value="Discard"  class="button" Style="Float:Left;margin:0 10px">
			
			
			     <input type="submit" name="contactAdminAction" value="Send" class="button" >
		
			</td>
		</tr>
	</table>
	
		{*shadow*}	
					</td>
			   </tr>
				<tr>
                               
                     <td valign="top" class="report_issue_bottom_green">
                    </td>					
				</tr>
		</table>
		
		{**}	
</form>