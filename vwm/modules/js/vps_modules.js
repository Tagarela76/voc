function setPrice(price,id, idarray) {
	document.getElementById('price_'+id).value = price;
	var len = idarray.length;
	var sum = 0;
	for (var i = 0; i < len; i++) {
		sum += parseFloat(document.getElementById('price_'+idarray[i]).value);
	}
	document.getElementById('price').innerHTML = sum+'';
}

function deselectModule(id,idarray) {
	var len = document.getElementsByName('selectedModulePlan_'+id).length;
	for (var i = 0; i < len; i++) {
		document.getElementsByName('selectedModulePlan_'+id)[i].checked = false;
	}
	setPrice(0,id,idarray);
}

function billingRadioButtonClick(text)
{
	if(document.getElementById('btnSubmit').disabled) // Check if submit button disabled - enable it (cause we choised a billing plan)
	{
		document.getElementById('btnSubmit').disabled = false;
	}
	
	flag = false; //self == true, gyant == false
	if(text == 'self')
	{
		flag = true;	
	}
	else if(text == 'gyant')
	{
		flag = false;
	}
	else
	{
		return;
	}

	for(i = 0; i< rbuttonsSelf.length; i++)
	{
		btnGyant = document.getElementById(rbuttonsGyant[i][0]);
		btnSelf = document.getElementById(rbuttonsSelf[i][0]);
		
		if(flag)//select from Gyant to Self
		{
			btnSelf.disabled = false;
			if(btnGyant.checked)
			{
				//Recount total price
				setPrice(rbuttonsSelf[i][1],rbuttonsSelf[i][2],rbuttonsSelf[i][3]);
				
				//Check parallel gyant radio
				btnSelf.checked = true;
				//Uncheck parallel self radio
				btnGyant.checked = false;
			}
			//Uncheck all radio buttons in another billing type
			btnGyant.disabled = true;	
		}
		else //select from Self to Gyant
		{
			btnGyant.disabled = false;
			if(btnSelf.checked)
			{
				//Recount total price
				setPrice(rbuttonsGyant[i][1],rbuttonsGyant[i][2],rbuttonsGyant[i][3]);
				//Check parallel gyant radio
				btnGyant.checked = true;
				//Uncheck parallel self radio
				btnSelf.checked = false;
			}
			//Uncheck all radio buttons in another billing type
			btnSelf.disabled = true;
		}
	}
}
