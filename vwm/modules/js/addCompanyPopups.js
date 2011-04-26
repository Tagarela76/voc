	function showUnittype()
	{	
		$('input[id^=unitTypeID]').each(function(el)
		{
			if($(this).attr('checked')==true)
				$(this).attr('temp','true')
		});
		$("#UnitTypelist").dialog('open');
	}
	
	function showAPMethods()
	{
		$('input[id^=APMethodID]').each(function(el)
		{
			if($(this).attr('checked')==true)
				$(this).attr('temp','true')
		});
		$("#APMethodsList").dialog('open');
	}
	
	function cancelPopupAPMethod()
	{	
		var num=0;
		$('input[id^=APMethodID]').each(function(el)
		{
			var val=$(this).attr('temp');
			if (val=='true')
				$(this).attr('checked',true);
			else	
				$(this).attr('checked',false);						
			num++;			
		});		
		$("#APMethodsList").dialog('close');
	}
		
	function cancelPopupUnittype()
	{	
		var num=0;
		$('input[id^=unitTypeID]').each(function(el)
		{
			var val=$(this).attr('temp');			
			if (val=='true')
				$(this).attr('checked',true);	
			else
				$(this).attr('checked',false);						
			num++;			
		});		
		$("#UnitTypelist").dialog('close');
	}
	
	function addUnittypeData() {
		var unittypeData = document.getElementById("unittype_data");
		unittypeData.innerHTML = "";
		$('input[id^=unitTypeID]').each(function(el) {
			if ($(this).attr('checked')) {
			//	var checkInput = document.createElement("input");
			//	checkInput.type = "checkbox";
			//	checkInput.name = $(this).attr('name');
			//	checkInput.id = checkInput.name;
			//	checkInput.value = $(this).attr('value');
			//	checkInput.checked = $(this).attr('checked');
			//	unittypeData.appendChild(checkInput);
				var checkInput = '<input type="checkbox" name="' + $(this).attr('name') + '" id="' + $(this).attr('name') + '" value="' + $(this).attr('value') + '" checked />';
				$("#unittype_data").append(checkInput);
			}
		});	
	}
	
	function addAPMethodData() {
		var unittypeData = document.getElementById("apmethod_data");
		unittypeData.innerHTML = "";
		$('input[id^=APMethodID]').each(function(el) {
			if ($(this).attr('checked')) {
			//	var checkInput = document.createElement("input");
			//	checkInput.type = "checkbox";
			//	checkInput.name = $(this).attr('name');
			//	checkInput.id = checkInput.name;
			//	checkInput.value = $(this).attr('value');
			//	checkInput.checked = $(this).attr('checked');
			//	unittypeData.appendChild(checkInput);
				var checkInput = '<input type="checkbox" name="' + $(this).attr('name') + '" id="' + $(this).attr('name') + '" value="' + $(this).attr('value') + '" checked />';
				$("#apmethod_data").append(checkInput);
			}
		});	
	}
	
	$(function() {
		$("#UnitTypelist").dialog({
				width: 800,
				autoOpen: false,
				resizable: true,
				dragable: true,			
				modal: true,
				buttons: {				
					'Cancel': function() {					
						cancelPopupUnittype()
					},
					'Save': function() {
						addUnittypeData();
						$(this).dialog('close');
					}
				}	
			});
	});

	$(function() {
		$("#APMethodsList").dialog({
				width: 400,
				autoOpen: false,
				resizable: true,
				dragable: true,			
				modal: true,
				buttons: {				
					'Cancel': function() {					
						cancelPopupAPMethod()
					},
					'Save': function() {
						addAPMethodData();
						$(this).dialog('close');
					}
				}	
			});
	});