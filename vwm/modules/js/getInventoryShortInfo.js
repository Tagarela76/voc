function getInventoryShortInfo(select)
{
	var inventoryID=select.options[select.selectedIndex].value;
	//	Clear output elements
	$('#inventoryDescription').attr('value','');


	if (inventoryID.length>0)
	{
		$.ajax({
      	url: "modules/ajax/saveDepartment.php",
      	type: "GET",
      	async: false,
      	data: { "inventoryID":inventoryID},
      	dataType: "html",
      	success: function (response)
      		{
      			$('#inventoryDescription').attr('value',response);
      		}
		});
	}
}
