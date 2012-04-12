$(function(){	
	var isSave=$('#isSave').attr('value');
	if (isSave!='true')
	{		
		var customerID=$('#selectCustomerID').attr('value');		
		getBillingRequest(customerID);
		
	}
	
	$('#selectCustomerID').change(function(el)
	{
		var customerID=$(this).attr('value');		
		getBillingRequest(customerID);
	});
});

function getBillingRequest(customerID)
{	
	if(customerID!=null){							
			$.ajax({
      		url: "modules/ajax/getBillingRequest.php",      		
      		type: "GET",
      		async: false,
      		data: { "customerID":customerID},      			
      		dataType: "html",
      		success: function (response) 
      			{        				   				   								
      				if(!(/^false/.test(response)))
      				{          				
      					resp=eval('('+response+')');  				
		      			$('#requestDiv').css('display','inline');	      			
		      			$('#descriptionDiv').text(resp['description']);	
		      			$('#dateDiv').text(resp['date']);
		      			$('#bplimit option[value ='+resp["bplimit"]+']').attr('selected',true);	
		      			$('#monthsCount option[value ='+resp["months_count"]+']').attr('selected',true);
		      			//$('#oneTimeCharge').attr('value',resp['one_time_charge']);
		      			$('#price').attr('value',resp['price']);
		      			$('#type option[value ='+resp["type"]+']').attr('selected',true);
		      			$('#MSDSDefaultLimit').attr('value',resp["MSDSLimit"]);		
		      			$('#memoryDefaultLimit').attr('value',resp["memoryLimit"]);										
      				}
      				else
      				{
      					$('#requestDiv').css('display','none');	      			
		      			$('#descriptionDiv').text("");	
		      			$('#dateDiv').text("");
		      			$('#bplimit option[value =1]').attr('selected',true);	
		      			$('#monthsCount option[value =1]').attr('selected',true);
		      			//$('#oneTimeCharge').attr('value',resp['one_time_charge']);
		      			$('#price').attr('value',"");
		      			$('#type option[value =gyant]').attr('selected',true);
		      			$('#MSDSDefaultLimit').attr('value',"");		
		      			$('#memoryDefaultLimit').attr('value',"");	
      				}      													
      			}        		   			   	
			});
		}
}