function addstepToUrl(){
	var url = $('#urlMixAdd').val();
	var step = $('#availableSteps option:selected').val();
	var count = url.indexOf('&stepID');
	url = url.substr(0, count);
	url += '&stepID='+step;
	$('#urlMixAdd').val(url);
	console.log(url.indexOf('&stepID'));
}


