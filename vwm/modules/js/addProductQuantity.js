function showQuantity(select)
{
	if (select.options[select.selectedIndex].value == '0')
	{
		document.getElementById('quantity').disabled = true;
		document.getElementById('quantity').value = '';
	}
	else
	{
		document.getElementById('quantity').disabled = false;
	}
	
	getInventoryShortInfo(select);
}