<?php /* Smarty version 2.6.19, created on 2012-04-20 17:10:40
         compiled from /home/developer/Workspace/voc_src/vwm/design/supplier/tpls/productDiscount.tpl */ ?>
<div class="padd7" align="center">
	<table  class="users" width="100%" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF">
		<thead>	
		<tr  class="users_u_top_size users_top_blue">
			
			<td  class="users_u_top_blue">Product ID</td>
			
			<td  class="">
				
		            <div style='width:100%;  color:white;'>					
						Product Name <?php if ($this->_tpl_vars['sort'] == 1 || $this->_tpl_vars['sort'] == 2): ?><img src="<?php if ($this->_tpl_vars['sort'] == 1): ?>images/asc2.gif<?php endif; ?><?php if ($this->_tpl_vars['sort'] == 2): ?>images/desc2.gif<?php endif; ?>" alt=""/><?php endif; ?>
					</div>
					
			</td>
			<td  class="">
				
		            <div style='width:100%;  color:white;'>					
						Client <?php if ($this->_tpl_vars['sort'] == 1 || $this->_tpl_vars['sort'] == 2): ?><img src="<?php if ($this->_tpl_vars['sort'] == 1): ?>images/asc2.gif<?php endif; ?><?php if ($this->_tpl_vars['sort'] == 2): ?>images/desc2.gif<?php endif; ?>" alt=""/><?php endif; ?>
					</div>
					
			</td>			
			<td  class="users_u_top_r_blue">
				
		            <div style='width:100%;  color:white;'>						
		                Discount <?php if ($this->_tpl_vars['sort'] == 3 || $this->_tpl_vars['sort'] == 4): ?><img src="<?php if ($this->_tpl_vars['sort'] == 3): ?>images/asc2.gif<?php endif; ?><?php if ($this->_tpl_vars['sort'] == 4): ?>images/desc2.gif<?php endif; ?>" alt=""/><?php endif; ?>				
					</div>					
				
			</td>
			

		</tr>
		</thead>
		
		<tbody>
<?php if ($this->_tpl_vars['clients'] > 0): ?>						 

				
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['clients']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>	
	<tr class="hov_company">
			
			<td class="border_users_b border_users_l" >
				<a href="<?php echo $this->_tpl_vars['clients'][$this->_sections['i']['index']]['url']; ?>
<?php if ($this->_tpl_vars['page']): ?>&page=<?php echo $this->_tpl_vars['page']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['request']['subBookmark']): ?>&subBookmark=<?php echo $this->_tpl_vars['request']['subBookmark']; ?>
<?php endif; ?>"><div style="width:100%;"><?php echo $this->_tpl_vars['clients'][$this->_sections['i']['index']]['product_id']; ?>
</div ></a>
			</td>
			
			
            <td class="border_users_b border_users_l" >
				<a href="<?php echo $this->_tpl_vars['clients'][$this->_sections['i']['index']]['url']; ?>
<?php if ($this->_tpl_vars['page']): ?>&page=<?php echo $this->_tpl_vars['page']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['request']['subBookmark']): ?>&subBookmark=<?php echo $this->_tpl_vars['request']['subBookmark']; ?>
<?php endif; ?>"><div style="width:100%;"><?php echo $this->_tpl_vars['clients'][$this->_sections['i']['index']]['product_nr']; ?>
</div ></a>
			</td>
            <td class="border_users_b border_users_l" >
				<a href="<?php echo $this->_tpl_vars['clients'][$this->_sections['i']['index']]['url']; ?>
<?php if ($this->_tpl_vars['page']): ?>&page=<?php echo $this->_tpl_vars['page']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['request']['subBookmark']): ?>&subBookmark=<?php echo $this->_tpl_vars['request']['subBookmark']; ?>
<?php endif; ?>"><div style="width:100%;"><?php echo $this->_tpl_vars['clients'][$this->_sections['i']['index']]['name']; ?>
 > <?php echo $this->_tpl_vars['clients'][$this->_sections['i']['index']]['fname']; ?>
</div ></a>
			</td>			
			<td class="border_users_b border_users_l border_users_r" >
				<a href="<?php echo $this->_tpl_vars['clients'][$this->_sections['i']['index']]['url']; ?>
<?php if ($this->_tpl_vars['page']): ?>&page=<?php echo $this->_tpl_vars['page']; ?>
<?php endif; ?><?php if ($this->_tpl_vars['request']['subBookmark']): ?>&subBookmark=<?php echo $this->_tpl_vars['request']['subBookmark']; ?>
<?php endif; ?>"><div style="width:100%;"><?php echo $this->_tpl_vars['clients'][$this->_sections['i']['index']]['discount']; ?>
 %</div ></a>
			</td>	
		
	</tr>
<?php endfor; endif; ?>		 
							

<?php else: ?>

		<tr class="">
		    <td  class="border_users_l border_users_r" style='text-align:center; vertical-align:middle;' colspan="4" >There are no products used by the client</td>
		</tr>

<?php endif; ?>
		</tbody>
		
		<tfoot>
		<tr>
			  <td class="users_u_bottom"></td>
        	  <td colspan="3" height="30" class="users_u_bottom_r"></td>
		</tr>	
		</tfoot>		
	</table>

</div>