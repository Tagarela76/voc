	function showJobber()
	{	
		$('input[id^=jobber]').each(function(el)
		{
			if($(this).attr('checked')==true)
				$(this).attr('temp','true')
		});
		$("#Jobberlist").dialog('open');
	}
	
	function showSupplier()
	{	
		$('input[id^=supplier]').each(function(el)
		{
			if($(this).attr('checked')==true)
				$(this).attr('temp','true')
		});
		$("#Supplierlist").dialog('open');
	}	

	function cancelPopupSupplier()
	{	
		var num=0;
		$('input[id^=supplier]').each(function(el)
		{
			var val=$(this).attr('temp');			
			if (val=='true')
				$(this).attr('checked',true);	
			else
				$(this).attr('checked',false);						
			num++;			
		});		
		$("#Supplierlist").dialog('close');
	}
		function cancelPopupJobber()
	{	
		var num=0;
		$('input[id^=jobber]').each(function(el)
		{
			var val=$(this).attr('temp');			
			if (val=='true')
				$(this).attr('checked',true);	
			else
				$(this).attr('checked',false);						
			num++;			
		});		
		$("#Jobberlist").dialog('close');
	}
	
	function addSupplierData() {
		var supplierData = document.getElementById("supplier_data");
		supplierData.innerHTML = "";
		$('input[id^=supplier]').each(function(el) {
			if ($(this).attr('checked')) {
				var checkInput = '<input type="checkbox" name="' + $(this).attr('name') + '" id="' + $(this).attr('name') + '" value="' + $(this).attr('value') + '" checked />';
				$("#supplier_data").append(checkInput);
			}
		});	
	}
	
	function addJobberData() {
		var jobberData = document.getElementById("jobber_data");
		jobberData.innerHTML = "";
		$('input[id^=jobber]').each(function(el) {
			if ($(this).attr('checked')) {
				var checkInput = '<input type="checkbox" name="' + $(this).attr('name') + '" id="' + $(this).attr('name') + '" value="' + $(this).attr('value') + '" checked />';
				$("#jobber_data").append(checkInput);
			}
		});	
	}	

	$(function() {
		$("#Supplierlist").dialog({
				width: 800,
				autoOpen: false,
				resizable: true,
				dragable: true,			
				modal: true,
				buttons: {				
					'Cancel': function() {					
						cancelPopupSupplier()
					},
					'Save': function() {
						addSupplierData();
						$(this).dialog('close');
					}
				}	
			});
	});
	
	$(function() {
		$("#Jobberlist").dialog({
				width: 800,
				autoOpen: false,
				resizable: true,
				dragable: true,			
				modal: true,
				buttons: {				
					'Cancel': function() {					
						cancelPopupJobber()
					},
					'Save': function() {
						addJobberData();
						$(this).dialog('close');
					}
				}	
			});
	});	
