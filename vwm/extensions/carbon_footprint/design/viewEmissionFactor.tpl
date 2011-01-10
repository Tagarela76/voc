<div style="padding:7px;">
    <table class="users" align="center" cellpadding="0" cellspacing="0">
    	<thead>
    		<tr class="users_top_yellowgreen users_u_top_size">
    			<td class="users_u_top_yellowgreen" width="37%" height="30"><span>View Emission Factor</span></td>
            	<td class="users_u_top_r_yellowgreen" width="300"></td>
        	</tr>        	
    	</thead> 
    	<tbody>
    	
    		{if $emissionFactor->name}
    		
    		<tr>
    			<td class="border_users_l border_users_b" height="20">Emission Factor</td>
    			<td class="border_users_l border_users_b border_users_r"><div align="left">&nbsp; {$emissionFactor->name}</div></td>
    		</tr>
    		<tr>
    			<td class="border_users_l border_users_b" height="20">Unit type</td>
    			<td class="border_users_l border_users_b border_users_r"><div align="left">&nbsp; {$unittype->getNameByID($emissionFactor->unittype_id)}</div></td>
    		</tr>
    		<tr>
    			<td class="border_users_l border_users_b" height="20">Value</td>
    			<td class="border_users_l border_users_b border_users_r"><div align="left">&nbsp; {$emissionFactor->emission_factor}</div></td>
    		</tr>
    		
    		{else}
    		
    		<tr>
    			<td colspan="2" class="border_users_l border_users_b border_users_r" align="center">404 | Emission factor not found</td>    			
    		</tr>
    		
    		{/if}
    		
    	</tbody> 
    	<tfoot>
    		<tr>
            	<td height="20" class="users_u_bottom"></td>
            	<td height="20" class="users_u_bottom_r"></td>
        	</tr>
        </tfoot>      
    </table>
    <div align="right">
    </div>   
</div>