<?php /* Smarty version 2.6.19, created on 2012-02-15 10:43:53
         compiled from /home/developer/Workspace/voc_src/vwm/extensions/inventory/design/inventoryOrders.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', '/home/developer/Workspace/voc_src/vwm/extensions/inventory/design/inventoryOrders.tpl', 85, false),)), $this); ?>
<?php if ($_REQUEST['tab'] == 'accessory'): ?>
	<?php $this->assign('accessory', true); ?>
<?php endif; ?>
<div class="padd7">
	<table class="users" height="200" cellspacing="0" cellpadding="0" align="center">
    <tr class="users_top_blue" height="27px">
		<?php if ($this->_tpl_vars['request']['tab'] != 'products'): ?>
        <td class="users_top_blue users_u_top_blue" width="60">
            <span style='display:inline-block; width:60px;'> <a onclick="CheckAll(this)" style='color:white'>All</a>/<a style='color:white' onclick="unCheckAll(this)" >None</a></span>
        </td>
		<?php endif; ?>
        <td class="<?php if ($this->_tpl_vars['request']['tab'] != 'products'): ?>users_top_blue<?php else: ?>users_top_blue users_u_top_blue<?php endif; ?>">
           <a style='color:white;' onclick='$("#sort").attr("value","<?php if ($this->_tpl_vars['sort'] == 1): ?>2<?php else: ?>1<?php endif; ?>"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	ID Number 		
					<?php if ($this->_tpl_vars['sort'] == 1 || $this->_tpl_vars['sort'] == 2): ?><img src="<?php if ($this->_tpl_vars['sort'] == 1): ?>images/asc2.gif<?php endif; ?><?php if ($this->_tpl_vars['sort'] == 2): ?>images/desc2.gif<?php endif; ?>" alt=""/><?php endif; ?>				
				</div>					
			</a>   
        </td>
        <td class="users_top_blue">
             <a style='color:white;' onclick='$("#sort").attr("value","<?php if ($this->_tpl_vars['sort'] == 3): ?>4<?php else: ?>3<?php endif; ?>"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Amount
					<?php if ($this->_tpl_vars['sort'] == 3 || $this->_tpl_vars['sort'] == 4): ?><img src="<?php if ($this->_tpl_vars['sort'] == 3): ?>images/asc2.gif<?php endif; ?><?php if ($this->_tpl_vars['sort'] == 4): ?>images/desc2.gif<?php endif; ?>" alt=""/><?php endif; ?>				
				</div>					
			</a> 
        </td>
        <td class="<?php if (! $this->_tpl_vars['accessory']): ?>users_top_blue<?php else: ?>users_u_top_r_blue<?php endif; ?>">
            <a style='color:white;' onclick='$("#sort").attr("value","<?php if ($this->_tpl_vars['sort'] == 5): ?>6<?php else: ?>5<?php endif; ?>"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Order Name	
					<?php if ($this->_tpl_vars['sort'] == 5 || $this->_tpl_vars['sort'] == 6): ?><img src="<?php if ($this->_tpl_vars['sort'] == 5): ?>images/asc2.gif<?php endif; ?><?php if ($this->_tpl_vars['sort'] == 6): ?>images/desc2.gif<?php endif; ?>" alt=""/><?php endif; ?>				
				</div>					
			</a> 
        </td>

        <td class="users_top_blue">
           
            <a style='color:white;' onclick='$("#sort").attr("value","<?php if ($this->_tpl_vars['sort'] == 7): ?>8<?php else: ?>7<?php endif; ?>"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Status	
					<?php if ($this->_tpl_vars['sort'] == 7 || $this->_tpl_vars['sort'] == 8): ?><img src="<?php if ($this->_tpl_vars['sort'] == 7): ?>images/asc2.gif<?php endif; ?><?php if ($this->_tpl_vars['sort'] == 8): ?>images/desc2.gif<?php endif; ?>" alt=""/><?php endif; ?>				
				</div>					
			</a> 					
			 
        </td>
        <td class="users_top_blue">
            
            <a style='color:white;' onclick='$("#sort").attr("value","<?php if ($this->_tpl_vars['sort'] == 9): ?>10<?php else: ?>9<?php endif; ?>"); $("#sortForm").submit();'>
            	<div style='width:100%;  color:white;'>						
                	Created Date	
					<?php if ($this->_tpl_vars['sort'] == 9 || $this->_tpl_vars['sort'] == 10): ?><img src="<?php if ($this->_tpl_vars['sort'] == 9): ?>images/asc2.gif<?php endif; ?><?php if ($this->_tpl_vars['sort'] == 10): ?>images/desc2.gif<?php endif; ?>" alt=""/><?php endif; ?>				
				</div>					
			</a> 				
			
        </td>
        <td class="users_top_blue">
            
            	<div style='width:100%;  color:white;'>						
                	Price 	
							
				</div>					
			
        </td>
        <td class="users_top_blue">
            
            	<div style='width:100%;  color:white;'>						
                	Discount 	
							
				</div>					
			
        </td>		
        <td class="users_u_top_r_blue">
            
            	<div style='width:100%;  color:white;'>						
                	Total 	
							
				</div>					
			
        </td>		

        
    </tr>

<?php if (count($this->_tpl_vars['orderList']) > 0): ?>  
      
    <?php $_from = $this->_tpl_vars['orderList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['order']):
?> 
    <tr class="hov_company" height="10px">
		<?php if ($this->_tpl_vars['request']['tab'] != 'products'): ?>
        <td class="border_users_b  border_users_l border_users_r">
            <input type="checkbox" value="<?php echo $this->_tpl_vars['order']['order_id']; ?>
" name="id[]">
        </td>
		<?php endif; ?>
        <td class="<?php if ($this->_tpl_vars['request']['tab'] != 'products'): ?>border_users_r border_users_b<?php else: ?>border_users_b  border_users_l border_users_r<?php endif; ?>">
            <a href="<?php echo $this->_tpl_vars['order']['url']; ?>
" class="id_company1">
                <div style="width:100%;">
                    <?php echo $this->_tpl_vars['order']['order_id']; ?>

                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="<?php echo $this->_tpl_vars['order']['url']; ?>
" class="id_company1">
                <div style="width:100%;">
                    <?php echo $this->_tpl_vars['order']['order_amount']; ?>

                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="<?php echo $this->_tpl_vars['order']['url']; ?>
" class="id_company1">
                <div style="width:100%;">
                    <?php echo $this->_tpl_vars['order']['order_name']; ?>

                </div>
            </a>
        </td>

        <td class="border_users_r border_users_b">
            <a href="<?php echo $this->_tpl_vars['order']['url']; ?>
" class="id_company1">
                <div style="width:100%;">
                   <?php if ($this->_tpl_vars['order']['order_status'] == 1): ?>In Progress <?php elseif ($this->_tpl_vars['order']['order_status'] == 2): ?>Confirm<?php elseif ($this->_tpl_vars['order']['order_status'] == 3): ?>Completed<?php elseif ($this->_tpl_vars['order']['order_status'] == 4): ?>Cnceled<?php endif; ?>
                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="<?php echo $this->_tpl_vars['order']['url']; ?>
" class="id_company1">
                <div style="width:100%;">
                    <?php echo $this->_tpl_vars['order']['order_created_date']; ?>

                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="<?php echo $this->_tpl_vars['order']['url']; ?>
" class="id_company1">
                <div style="width:100%;">
                    <?php echo $this->_tpl_vars['order']['order_price']; ?>

                </div>
            </a>
        </td>
        <td class="border_users_r border_users_b">
            <a href="<?php echo $this->_tpl_vars['order']['url']; ?>
" class="id_company1">
                <div style="width:100%;">
                    <?php echo $this->_tpl_vars['order']['discount']; ?>
 %
                </div>
            </a>
        </td>		
        <td class="border_users_b border_users_r">
            <a href="<?php echo $this->_tpl_vars['order']['url']; ?>
" class="id_company1">
                <div style="width:100%;">
                    $ <?php echo $this->_tpl_vars['order']['order_total']; ?>

                </div>
            </a>
        </td>

    </tr>
    <?php endforeach; endif; unset($_from); ?> 
    <tr>
        <td colspan="<?php if ($this->_tpl_vars['request']['tab'] != 'products'): ?>9<?php else: ?>8<?php endif; ?>" class="border_users_l border_users_r">
            &nbsp;
        </td>
    </tr>
    <?php else: ?>
        <tr>
        <td colspan="<?php if ($this->_tpl_vars['request']['tab'] != 'products'): ?>9<?php else: ?>8<?php endif; ?>" class="border_users_l border_users_r" align="center">
            No Orders in the facility
        </td>
    </tr>
    <?php endif; ?>
    <tr>
        <td class="users_u_bottom">
        </td>
        <td colspan="<?php if ($this->_tpl_vars['request']['tab'] != 'products'): ?>7<?php else: ?>6<?php endif; ?>" height="15" class="border_users">
        </td>
        <td class="users_u_bottom_r">
        </td>
    </tr>
</table></div>
</form>