var currentProductID;

function addProduct(productID) {
	if (productID != null)
	{	
		isFind= /no products/.test($("select#selectProduct option:selected").text());								
		if (!isFind)
		{
			//	show preloader
			$("#preloader").css("display", "block");
			
			// remove product from dropdown
			$("select#selectProduct option:selected").remove();
		
			$.ajax({
	      		url: "modules/ajax/saveInventory.php",      		
	      		type: "POST",
	      		data: { "action":"addProduct", "productID":productID, "tab":$("#inventoryType").val()},      			
	      		dataType: "html",
	      		success: function (productRow) {      		      												
					$('#productTableBody').append(productRow);
	
					//	hide preloader
					$("#preloader").css("display", "none");											
	      		}      			   	
			});
		}
		else
		{
			$('#selectProdError').css('display','block');
		}
	}
	else 
		document.getElementById( 'selectProdError').style.display="block";
		//	А еще можно так: $('#selectProdError').css('display','block'); - в духе jQuery.
	
}


function editStorageLocation(productID) {
	currentProductID = productID;
	//	remove jquery ui class	
	$("button.ui-button").removeClass().addClass('button');
	
	//	update checkboxes
	$(":input[name='department_id[]']:checked").each(function(){
		$(this).removeAttr('checked');						
	});			
	$("#hiddenDepartmentsList_"+currentProductID+" > input").each(function() {		
		$(":input[name='department_id[]'][value='"+$(this).val()+"']").attr('checked',true);
	});
			
	$('#departmentForm').dialog('open');	
}

/*//86400000 - 1 day in milliseconds
function set_cookie(name, value, expires, path, domain, secure) {  
	//define expires time  
	var today = new Date();  
	var expires_date = new Date(today.getTime() + (expires * 86400000));  
	  
	//set cookie  
	document.cookie =  
	    name + '=' + escape(value) +  
	    (expires ? ';expires=' + expires_date.toUTCString() : '') +  
	    (path    ? ';path=' + path : '' ) +  
	    (domain  ? ';domain=' + domain : '' ) +  
	    (secure  ? ';secure' : '' );  
}  
	
function get_cookie ( cookie_name )
{
	var results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );
		 
	if ( results )
		return ( unescape ( results[2] ) );
	else
		return null;
}*/


$(function() {
	
	/*var tab=$('#selectProduct').attr('tab');
	if(get_cookie('incSearch')=='true'&& tab=='material')
	{
		$('#AddRemoveSearch').attr('value','Remove Product Search');
		$('#selectProduct').AddIncSearch({
			maxListSize   : 20,
			maxMultiMatch : 70
		});
	}
	
	$('#AddRemoveSearch').click(function()
		{
			if($(this).attr('value')=='Enable Product Search')
			{				
				$(this).attr('value','Disable Product Search');				
				$('#selectProduct').AddIncSearch({
			        maxListSize   : 20,
			        maxMultiMatch : 70
		   		});
		   		set_cookie ('incSearch', 'true',30);
			}
			else
			{				
				$(this).attr('value','Enable Product Search');
				$('#selectProduct').RemoveIncSearch();
				set_cookie ('incSearch', 'false',30);
			}
		});*/
		

	$("#departmentForm").dialog({
			autoOpen: false,
			height: 500,
			width: 350,
			modal: true,
			buttons: {
				'Select': function() {
					$("#hiddenDepartmentsList_"+currentProductID+"").empty();
					var htmlVisualDepartmentList = "";									
					$(":input[name='department_id[]']:checked").each(function(){												
						$("#hiddenDepartmentsList_"+currentProductID).append("<input type='hidden' name='useLocation["+currentProductID+"][]' value='"+$(this).val()+"'>");						
						htmlVisualDepartmentList += "<a  href='javascript:void(0);' onclick='editStorageLocation("+currentProductID+");'>"+$("#name_"+$(this).val()).html()+"</a>&nbsp;";
												
					});	
														
					if (htmlVisualDepartmentList == "") {
						htmlVisualDepartmentList += "<a href='javascript:void(0);' onclick='editStorageLocation("+currentProductID+");'>Edit</a>&nbsp;";
					}									
					$("#visualDepartmentsList_"+currentProductID+"").html(htmlVisualDepartmentList);	
					$(this).dialog('close');																				
				},				
				Cancel: function() {					
					$(this).dialog('close');
				}
			}	
		});

		$(".draggableAvailableInv, .draggableDepInv").draggable({ revert: 'invalid', 																						 
											 cursorAt: {top: -1, left: -1},
											 helper:  'clone'										
											});
		
		$("#depInvTbody").droppable({
			accept: '.draggableAvailableInv',
			activeClass: 'ui-state-hover',
			hoverClass: 'ui-state-active',
			drop: function(event, ui) {					
				$('#emptyDepInv').remove();										
				$('#depInvTbody').append('<tr class="draggableDepInv users_u_top_size hov_DepInventory" id='+ui.draggable.attr("id")+'>'+ui.draggable.html()+'</tr>');
				$('#hiddenFields').append('<input type="hidden" name="id[]" value="'+ui.draggable.attr("id")+'">');
				ui.helper.remove();			
				ui.draggable.remove();
				
				if ($('#availableInvTbody > tr').length == 0) {
					$('#availableInvTbody').append('<tr id="emptyAvailableInv" class="users_u_top_size"><td>No inventories at facility</td></tr>');
				}				
				reIni();		
			}
		});
		
		$("#availableInvTbody").droppable({
			accept: '.draggableDepInv',
			activeClass: 'ui-state-hover',
			hoverClass: 'ui-state-active',
			drop: function(event, ui) {					
				$('#emptyAvailableInv').remove();						
				$('#availableInvTbody').append('<tr class="draggableAvailableInv users_u_top_size hov_DepInventory" id='+ui.draggable.attr("id")+'>'+ui.draggable.html()+'</tr>');				
				$('input[value='+ui.draggable.attr("id")+']').remove();
				ui.helper.remove();			
				ui.draggable.remove();
				if ($('#depInvTbody > tr').length == 0) {
					$('#depInvTbody').append('<tr id="emptyDepInv" class="users_u_top_size"><td>No inventories at department</td></tr>');
				}	
				reIni();							
			}
		});
				
		function reIni() {
			$(".draggableAvailableInv, .draggableDepInv").draggable({ revert: 'invalid', 																						
											 cursorAt: {top: -1, left: -1},
											 helper:  'clone'										
											});
		}

	});