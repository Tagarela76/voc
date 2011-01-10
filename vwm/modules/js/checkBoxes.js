function CheckAll(Element) {
	//	check only at popup window
	if (Element.name == 'allChemicalClasses') {
		thisCheckBoxes = document.getElementById('chemClassList').getElementsByTagName('input');
	} else {
		thisCheckBoxes = document.getElementsByTagName('input');
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
		thisCheckBoxes = document.getElementsByTagName('input');
	}	
	
	for (i = 0; i < thisCheckBoxes.length; i++){
		if (thisCheckBoxes[i].type=='checkbox') {
			thisCheckBoxes[i].checked = false;
		}
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