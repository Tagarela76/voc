$(function()
	{
		getComponentInfo();
		$('#selectComponent').change(function(el)
		{
			getComponentInfo();
		});
	})	
				
	function getComponentInfo() {
		var componentID=$('#selectComponent').attr('value');				
		if(componentID.length>0){			
			$.ajax({
      		url: "modules/ajax/getComponentDetails.php",      		
      		type: "GET",
      		async: false,
      		data: { "componentID":componentID},      			
      		dataType: "html",
      		success: function (response) 
      			{   
      				if(response!='false')
      				{      		      						
		      			resp=eval("("+response+")");  					
						$('#componentDescription').attr('value',resp['description']);
						$('#componentCas').attr('value',resp['cas']);												
      				}      													
      			}        		   			   	
			});
		}
	}
