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
						$(this).dialog('close');
					}
				}	
			});
	});