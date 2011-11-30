function CheckAll(Element) {
	//	check only at popup window
	if (Element.name == 'allChemicalClasses') {
		thisCheckBoxes = document.getElementById('chemClassList').getElementsByTagName('input');
	} else {
		if (Element.name == 'allTypesClasses'){
			if (document.getElementById('typesClassList') != null){
			thisCheckBoxes = document.getElementById('typesClassList').getElementsByTagName('input');
			}else if(document.getElementById('companiesList')!= null){
			thisCheckBoxes = document.getElementById('companiesList').getElementsByTagName('input');	
			}else if(document.getElementById('supplierListPP')!= null){
				thisCheckBoxes = document.getElementById('supplierListPP').getElementsByTagName('input');
			}
		} else {
			thisCheckBoxes = document.getElementsByTagName('input');
	}	
	}	
	for (i = 0; i < thisCheckBoxes.length; i++) {
		if (thisCheckBoxes[i].type == 'checkbox') {
			thisCheckBoxes[i].checked = true;
		}
	}
	
}

function unCheckAll(Element) {
	//	check only at popup window
	if (Element.name == 'allChemicalClasses') {
		thisCheckBoxes = document.getElementById('chemClassList').getElementsByTagName('input');
	} else {
		if (Element.name == 'allTypesClasses'){
			if (document.getElementById('typesClassList') != null){
			thisCheckBoxes = document.getElementById('typesClassList').getElementsByTagName('input');
			}else if(document.getElementById('companiesList')!= null){
			thisCheckBoxes = document.getElementById('companiesList').getElementsByTagName('input');	
			}else if(document.getElementById('supplierListPP')!= null){
				thisCheckBoxes = document.getElementById('supplierListPP').getElementsByTagName('input');
			}
		} else {
			thisCheckBoxes = document.getElementsByTagName('input');
	}	
	}
	for (i = 0; i < thisCheckBoxes.length; i++){
		if (thisCheckBoxes[i].type=='checkbox') {
			thisCheckBoxes[i].checked = false;
		}
	}
}
function CheckCB(Element){
	if(document.getElementById) {
		if(document.getElementById(Element.id.replace('cb','tr'))){Element.checked = !Element.checked;}
	}
}
function CheckClassOfUnitTypes(Element) {
	thisCheckBoxes = document.getElementById(Element.id).getElementsByTagName('input');
	
	for (i = 0; i < thisCheckBoxes.length; i++) {
		if (thisCheckBoxes[i].type == 'checkbox') {
			thisCheckBoxes[i].checked = true;
		}
	}
}

function unCheckClassOfUnitTypes(Element) {
	thisCheckBoxes = document.getElementById(Element.id).getElementsByTagName('input');
	
	for (i = 0; i < thisCheckBoxes.length; i++) {
		if (thisCheckBoxes[i].type == 'checkbox') {
			thisCheckBoxes[i].checked = false;
		}
	}
}