var category; //facility or department
var category_id;
var selYear;  


function getEmissionLog(year,category,category_id)
{	
	$('tr[name=limits]').each(function(el)
	{	
		 $(this).remove();
	});
	
	$.ajax({
      		url: "modules/ajax/getEmissionLog.php",      		
      		type: "POST",
      		data: { "year":year,"category":category,"category_id":category_id},      			
      		dataType: "html",
      		success: function (response) {  
      			if(response!='false')
      			{         							 					
		      		resp=eval("("+response+")");
		      		for(key in resp)
		      		{  	
		      			if (key!='total')
		      			{	
		      				var depLimit= resp[key]['depLimit']?"Yes":"No";  
		      				var facLimit= resp[key]['facLimit']?"Yes":"No";  				
		      				row="<tr name='limits' "+((resp[key]['depLimit']||resp[key]['facLimit'])? "class='us_red'":"")+">"+
		      					"	<td>"+
		      							resp[key]['month']+
		      					"	</td>"+	
		      					"	<td>"+
		      							resp[key]['voc']+
		      					"	</td>"+
		      					"	<td>"+
		      							facLimit+
		      					"	</td>"+
		      					"	<td>"+
		      							depLimit+
		      					"	</td>"+      						
		      					"</tr>";			
							$('#emmisionLogTable').append(row);
		      			}
		      		}
		      		row="<tr name='limits' align='center'"+"class='users_u_top_size users_top_lightgray'"+">"+		      				
		      			"	<td colspan='4'>"+
		      					"TOTAL "+selYear+": "+resp['total']['voc']+
		      			"	</td>"+	      					 						
		      			"</tr>";
		      		$('#emmisionLogTable').append(row);															
      			}													
      		}      			   	
		});
}


$(function() {
	
	
	category=$('#popup_category').attr('value');
	category_id=$('#popup_category_id').attr('value');
	selYear=$('#selectYear').attr('value');
	
	$('#selectYear').change(function(){
		selYear=$(this).attr('value');
		getEmissionLog(selYear,category,category_id);
	;});	
			
	$("#emissionLog").dialog({
			width: 400,
			autoOpen: false,
			resizable: true,
			dragable: true,			
			modal: true,
			buttons: {				
				'Cancel': function() {					
					$(this).dialog('close');
				}
			}	
		});
		getEmissionLog(selYear,category,category_id);
});