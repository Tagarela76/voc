var category_id;
var selYear;  
var category;


function getNoxLog(year,category_id, category)
{	
	$('tr[name=limits]').each(function(el)
	{	
		 $(this).remove();
	});
	
	$.ajax({
      		url: "modules/ajax/getEmissionLog.php",      		
      		type: "POST",
      		data: { "year":year,"category_id":category_id,"action":"getNoxLog", "category":category},      			
      		dataType: "html",
      		success: function (response) {  
      			if(response!='false')
      			{         							 					
		      		resp=eval("("+response+")");
		      		for(key in resp)
		      		{  	
		      			if (key!='total')
		      			{	 
		      				var facLimit= resp[key]['facLimit']?"Yes":"No";  				
		      				row="<tr name='limits' "+((resp[key]['facLimit'])? "class='us_red'":"")+">"+
		      					"	<td>"+
		      							resp[key]['month']+
		      					"	</td>"+	
		      					"	<td>"+
		      							resp[key]['nox']+
		      					"	</td>"+
		      					"	<td>"+
		      							facLimit+
		      					"	</td>"+     						
		      					"</tr>";			
							$('#noxLogTable').append(row);
		      			}
		      		}
		      		row="<tr name='limits' align='center'"+"class='users_u_top_size users_top_lightgray'"+">"+		      				
		      			"	<td colspan='4'>"+
		      					"TOTAL "+selYear+": "+resp['total']['nox']+
		      			"	</td>"+	      					 						
		      			"</tr>";
		      		$('#noxLogTable').append(row);															
      			}													
      		}      			   	
		});
}


$(function() {
	category=$('#nox_popup_category').attr('value');
	category_id=$('#nox_popup_category_id').attr('value');
	selYear=$('#noxSelectYear').attr('value');
	
	$('#noxSelectYear').change(function(){
		selYear=$(this).attr('value');
		getNoxLog(selYear,category_id, category);
	});	
			
	$("#noxLog").dialog({
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
		getNoxLog(selYear,category_id, category);
});