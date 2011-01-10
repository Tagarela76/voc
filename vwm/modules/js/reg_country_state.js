function getStateList(select)
{
	var countryID=select.options[select.selectedIndex].value;
	document.getElementById('selectState').options.length = 0;
	
	if (countryID == 215)
	{
		document.getElementById('textState').style.display="none";
		document.getElementById('selectState').style.display="block";
		
	}
	else
	{
		document.getElementById('textState').style.display="block";
		document.getElementById('selectState').style.display="none";
		
	}
		
	if (countryID.length>0)
	{		
		$.ajax({
      	url: "modules/ajax/getStateList.php",      		
      	type: "GET",
      	async: false,
      	data: {"countryID":countryID},      			
      	dataType: "html",
      	success: function (response) 
      		{   
      			var obj = document.getElementById('selectState');
				eval(response);						
      		}        		   			   	
		});
	}
}

