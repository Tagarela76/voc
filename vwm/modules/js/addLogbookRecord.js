$(document).ready(function(){
    //popUpCal.dateFormat = 'MDY/';
    //$("#calendar1").calendar();
    showElements($('select[name=typeOfRecord]').attr('value'));
    $('select[name=typeOfRecord]').change(function(el){
    	showElements($(this).attr('value'));
    })
    var selectDep= $('#department');
     
    if (selectDep.attr('value')!=null)
    {
    	showEquipments(selectDep.attr('value'));
    }
    selectDep.change(function(el){
    	showEquipments($(this).attr('value'));
    })
    
    $('#calendar1').datepicker({ dateFormat: 'mm/dd/yy' });
    
});
 
function showEquipments(departmentId)
{		
	
	$.ajax({
      		url: "modules/ajax/addLogbookRecord.php",      		
      		type: "POST",
      		async: false,
      		data: { "departmentId":departmentId},      			
      		dataType: "html",
      		success: function (eqList) {       			   		      												
				$('select[name=equipment] option').remove();
				if (eqList!='false')
				{
					equipmentList=eval("("+eqList+")");
					for (var i=0;i<equipmentList.length;i++)
					{
						$('select[name=equipment]').append(
							"<option value='"+equipmentList[i]['equipment_id']+"'>"+equipmentList[i]['equip_desc']+"</option>");					
					}
					$('select[name=equipment] option[value='+setEquipment+']').attr('selected',true);	
				}																		
      		}       		      		   			   	
		});				
}
 
function showElements(logbookAction)
{
	switch (logbookAction)
	{
		case "Inspection":
		{
			$('#tr_description, #tr_operator, #tr_department, #tr_equipment').css('display','table-row');
			$('#tr_installed, #tr_removed, #tr_filterType, #tr_filterSize, #tr_reason, #tr_action, #tr_upload').css('display','none');
			$('#description, #operator, #department, #equipment').attr('disabled',false);
			$('#installed, #removed, #filterType, #filterSize, #reason, #action, #upload').attr('disabled',true);
			break;
		}
		case "Sampling":
		{
			$('#tr_description, #tr_operator, #tr_action, #tr_department, #tr_equipment').css('display','table-row');
			$('#tr_installed, #tr_removed, #tr_filterType, #tr_filterSize, #tr_reason, #tr_upload').css('display','none');
			$('#description, #operator, #action, #department, #equipment').attr('disabled',false);
			$('#installed, #removed, #filterType, #filterSize, #reason, #upload').attr('disabled',true);
			break;
		}
		case "AccidentPlan":
		{	
			$('#tr_upload').css('display','table-row');
			$('#tr_description, #tr_operator, #tr_reason, #tr_action, #tr_installed, #tr_removed, #tr_filterType, #tr_filterSize, #tr_department, #tr_equipment').css('display','none');	
			$('#upload').attr('disabled',false);
			$('#description, #operator, #reason, #action, #installed, #removed, #filterType, #filterSize, #department, #equipment').attr('disabled',true);		
			break;
		}
		case "Malfunction":
		{
			$('#tr_operator, #tr_reason, #tr_department, #tr_equipment').css('display','table-row');
			$('#tr_description, #tr_installed, #tr_removed, #tr_filterType, #tr_filterSize, #tr_action, #tr_upload').css('display','none');
			$('#operator, #reason, #department, #equipment').attr('disabled',false);
			$('#description, #installed, #removed, #filterType, #filterSize, #action, #upload').attr('disabled',true);
			break;
		}
		case "Filter":
		{
			$('#tr_installed, #tr_removed, #tr_filterType, #tr_filterSize, #tr_department, #tr_equipment').css('display','table-row');
			$('#tr_description, #tr_operator, #tr_reason, #tr_action, #tr_upload').css('display','none');
			$('#installed, #removed, #filterType, #filterSize, #department, #equipment').attr('disabled',false);
			$('#description, #operator, #reason, #action, #upload').attr('disabled',true);
			break;
		}
	}
}
    