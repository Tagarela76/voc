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
		$('#selectProdError').css('display','block');

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




$(function() {

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