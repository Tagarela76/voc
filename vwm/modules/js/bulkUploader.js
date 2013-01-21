$(document).ready(function() {
	getFacilityList();
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
			data: {
				"companyCode":companyID
			},      			
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
	