$(function()
{	
	var disabledButton=false;
	var compSelectedVal=$.trim($('#selectCompany option:selected').attr('value'));
	var facSelectedVal=$.trim($('#selectFacility option:selected').attr('value'));
	var depSelectedVal=$.trim($('#selectDepartment option:selected').attr('value'));
		
	switch (accessLevel)
	{
		case 'company':
			if (compSelectedVal=="")
			{
				$('#compError').css('display','inline-block');
				disabledButton=true;
			}
			break;
		case 'facility': 
			if (compSelectedVal=="")
			{
				$('#compError').css('display','inline-block');
				disabledButton=true;
			}
			if (facSelectedVal=="")
			{
				$('#facError').css('display','inline-block');
				disabledButton=true;
			}			
			break;
		case 'department':
			if (compSelectedVal=="")
			{
				$('#compError').css('display','inline-block');
				disabledButton=true;
			}
			if (facSelectedVal=="")
			{
				$('#facError').css('display','inline-block');
				disabledButton=true;
			}	
			if (depSelectedVal=="")
			{
				$('#depError').css('display','inline-block');
				disabledButton=true;
			}				
			break;
	}
	if (disabledButton==true)
		$('#saveButton').attr('disabled',true);
	
	$('#selectCompany').change(function()
	{
		switch (accessLevel)
		{
			case 'facility': 
				getFacilityList();
				break;
			case 'department':
				getFacilityList();
				$('#selectFacility').trigger('change');					
				break;
		}
			
	});
	
	$('#selectFacility').change(function()
	{		
		if (accessLevel=='department')
		{
			getDepartmentsList();
		}		
	});
});

function getFacilityList()
{
	var companyID=$('#selectCompany').attr('value');
	$('#selectFacility').empty();	
	if(companyID.length>0){							
		$.ajax({
      	url: "modules/ajax/getFacilities.php",      		
      	type: "GET",
      	async: false,
      	data: { "companyCode":companyID},      			
      	dataType: "html",
      	success: function (response) 
      		{             						
      			if(response!='false')
      			{      					
		   			resp=eval("("+response+")");														
					for (key in resp)
					{
						$('#selectFacility').append(
						"<option value='"+resp[key]['facility_id']+"'>"+resp[key]['name']+"</option>");					
					}									
      			}      													
      		}        		   			   	
		});
	}	
	var facSelectedVal=$.trim($('#selectFacility option:selected').attr('value'));	
	if (facSelectedVal=="")
	{
		$('#facError').css('display','inline-block');
		$('#saveButton').attr('disabled',true);
	}	
	else
	{
		$('#facError').css('display','none');
		$('#saveButton').attr('disabled',false);
	}
}

function getDepartmentsList()
{	
	var facilityID=$('#selectFacility').attr('value');
	$('#selectDepartment').empty();		
	if(facilityID.length>0){						
		$.ajax({
      	url: "modules/ajax/getDepartments.php",      		
      	type: "GET",
      	async: false,
      	data: { "facilityCode":facilityID},      			
      	dataType: "html",
      	success: function (response) 
      		{         			          							
      			if(response!='false')
      			{      					
		      		resp=eval("("+response+")");														
					for (key in resp)
					{
						$('#selectDepartment').append(
						"<option value='"+resp[key]['department_id']+"'>"+resp[key]['name']+"</option>");					
					}									
      			}      													
      		}        		   			   	
		});
	}
	var depSelectedVal=$.trim($('#selectDepartment option:selected').attr('value'));
	if (depSelectedVal=="")
	{
		$('#depError').css('display','inline-block');
		$('#saveButton').attr('disabled',true);
	}
	else
	{
		$('#depError').css('display','none');
		$('#saveButton').attr('disabled',false);
	}
}			
			