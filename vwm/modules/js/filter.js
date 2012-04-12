var filterArray;

$(function()
{	
	
	if(filterStr!='')
		filterArray=eval ("("+filterStr+")");
	
	var filterFieldOptions="<option value='All' "+(filterFieldText=='All'?"All":"")+">Without filter</option>";
	for (key in filterArray)
	{		
		var selectedStr="";
		if (filterArray[key]['name_in_table']==filterFieldText)
		{			
			selectedStr=" selected ";
		}
		filterFieldOptions+="<option value='"+key+"'"+selectedStr+">"+filterArray[key]['field_name']+"</option>"
	}
	$('input:hidden[name=filterField]').attr('value',$('#filterField').attr('value'));		
	$('#filterField').html(filterFieldOptions);
		
	setConditionsAndValue();		
	if (filterConditionText!='')
		$('#filterCondition option[value ='+filterConditionText+']').attr('selected',true);
	if (filterValueText!='')
	{
		$('#filterValue').attr('value',filterValueText);
		$('#filterValueDate').attr('value',filterValueText);
	}	
	
	$('#filterField').change(function(e)
	{		
		setConditionsAndValue();
		validation();		
	});	
	
	$('#filterValue').keyup(function (e) 
	{	
		validation();
	});	
	
	$('#filterButton').click(function(e)
	{		
		submitFunction();
	});				
	
});

function submitFunction()
{			
	var fieldVal= $('#filterField').attr('value');
	if (fieldVal!='All')		
	{
		filterClass=filterArray[fieldVal]['filter_class'];			
		switch (filterClass)
		{			
			case 'numeric':
			case 'text':
			{ 
				
				var value=""+$('#filterValue').attr('value');
				value=value.replace(/^\s+/,"");
				value=value.replace(/\s+$/,"");										
				$('#filterValue').attr('value',value);			
				break;
			}
				
			case 'date':
			{
				var value=$('#filterValueDate').attr('value');
				value=value.replace(/^\s+/,"");
				value=value.replace(/\s+$/,"");			
				break;
			}
		}
	}
	
	return true;
}

function validation()
{
	var val= $('#filterField').attr('value');
	if (val!='All')
	{		
		filterClass=filterArray[val]['filter_class'];
		if (filterClass=='numeric')
		{	 
			var valStr = $('#filterValue').attr('value');			
			if (/^\s*[0-9]*[\.\,]?[0-9]*\s*$/.test(valStr))
			{						
				$('#filterButton').attr('disabled',false);
				$('#valueLabel').text('Value').css('color','gray');
			}
			else
			{
				$('#filterButton').attr('disabled',true);
				$('#valueLabel').text('Incorrect number').css('color','red');
			}
		}
		else
		{
			$('#filterButton').attr('disabled',false);
			$('#valueLabel').text('Value').css('color','gray');
		}
	}
	else
	{		
		$('#filterButton').attr('disabled',false);
		$('#valueLabel').text('Value').css('color','gray');
	}
}


function setConditionsAndValue()
{	
		
	var val= $('#filterField').attr('value');
	if (val=='All')
	{
		$('input:hidden[name=filterField]').attr('value','All');		
		$('#filterCondition').empty().attr('disabled',true);
		$('#filterValue').attr('disabled',true).css('display','none');
		$('#filterValueAll').css('display','block');
		$('#filterValueDate').attr('disabled',true).css('display','none');					
	}
	else
	{		
		$('#filterValueAll').css('display','none');
		$('input:hidden[name=filterField]').attr('value',filterArray[val]['name_in_table']);		
		filterClass=filterArray[val]['filter_class'];
		if (filterClass=='date')
		{
			$('#filterValueDate').css('display','inline-block');
			$('#filterValueDate').attr('disabled',false);
			$('#filterValue').css('display','none');
			$('#filterValue').attr('disabled',true);
			$('#filterValueDate').datepicker({ dateFormat: filterDateFormat,changeYear: true,changeMonth: true}); 
		}
		else
		{
			$('#filterValue').css('display','inline-block');
			$('#filterValue').attr('disabled',false);
			$('#filterValueDate').css('display','none');
			$('#filterValueDate').attr('disabled',true);			
		}
		
		$('#filterCondition').attr('disabled',false);
		$('#filterValue').attr('disabled',false);
		
		switch(filterClass)
		{
			case 'date':
				var optionsStr=
				"<option value='dateEquals'>=</option>" +
				"<option value='dateNotEquals'>\<\></option>" +
				"<option value='dateLessThan'>\<</option>" +
				"<option value='dateGreaterThan'>\></option>" +
				"<option value='dateLessThanOrEqual'>\<\=</option>" +
				"<option value='dateGreaterThanOrEqual'>\>\=</option>";
				
				$('#filterCondition').html(optionsStr);
				$('#filterCondition option[value=dateEquals]').attr('selected',true);						
				break;
			case 'numeric':
				var optionsStr=
				"<option value='equals'>=</option>" +
				"<option value='notEquals'>\<\></option>" +
				"<option value='lessThan'>\<</option>" +
				"<option value='greaterThan'>\></option>" +
				"<option value='lessThanOrEqual'>\<\=</option>" +
				"<option value='greaterThanOrEqual'>\>\=</option>";
				
				$('#filterCondition').html(optionsStr);
				$('#filterCondition option[value =equals]').attr('selected',true);						
				break;
			case 'text':
				var optionsStr=
				"<option value='contains' >contains</option>" +
				"<option value='notContains'>does not contains</option>" +
				"<option value='equalsStr'>\=</option>";
					
				$('#filterCondition').html(optionsStr);
				$('#filterCondition option[value =contains]').attr('selected',true);		
				break;
			default: $('#filterCondition').empty();		
		}		
	}	
}