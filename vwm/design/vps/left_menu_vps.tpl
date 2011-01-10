<td class="bg_left" valign="top" width="220px ">

 <table style="table-layout: fixed;padding-top:28px" width="100%" cellpadding="0" cellspacing="0" class="menu_padd_b">
		<tr>
			<td valign="top">
			
			
			    <div align="left" width="100%"  {if $category eq "dashboard"}class="left_m_active" {else}class="left_m" {/if}>
				<div align="left" width="100%">

					<ul class="link" >					
						<li><a href="vps.php?action=viewDetails&category=dashboard" class="id_company" >Dashboard</a></li>		
					</ul>
				</div>
				</div>
				
				<div align="left" width="100%"  {if $category eq "billing"}class="left_m_active" {else}class="left_m" {/if}>
				<div align="left" width="100%">
					<ul class="link"  >	
						<li><a href="vps.php?action=viewDetails&category=billing&subCategory=MyBillingPlan" class="id_company" >Billing</a></li>		
                     </ul>
	            </div>
				</div>
				
                <div align="left" width="100%"   {if $category eq "invoices"}class="left_m_active" {else}class="left_m" {/if}>
				<div align="left" width="100%">
					 
                     <ul class="link" >						
					<li><a href="vps.php?action=viewList&category=invoices&subCategory=All" class="id_company" >Invoices</a></li>		
					</ul>	
						</div>
				</div>
				
				<div align="left" width="100%"  {if $category eq "myInfo"}class="left_m_active" {else}class="left_m" {/if}>
				<div align="left" width="100%">

					<ul class="link" >	
					<li><a href="vps.php?action=viewDetails&category=myInfo" class="id_company" >My info</a></li>		
					</ul>
				</div>
				</div>
			
			
					
																</td></tr></table>
					  </td>