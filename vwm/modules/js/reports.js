
function onChangeFormat(select) {

	switch (select.options[select.selectedIndex].value) {
		case "csv":
			showExtraCSV();
			break;
		default:
			document.getElementById('csvFormatLabel').style.display="none";
			document.getElementById('csvFormatInputs').style.display="none";
			break;
	}
}

function showExtraCSV() {
	document.getElementById('csvFormatLabel').style.display="block";
	document.getElementById('csvFormatInputs').style.display="block";
}

function CheckDate(){
	var theForm = document.getElementById('createReportForm');
	var sDate = theForm.date_begin.value;
	var eDate = theForm.date_end.value;

	var sDate2 = new Date(sDate.substr(0,10));
	var eDate2 = new Date(eDate.substr(0,10));
	var currDate = new Date();


	if (currDate < eDate2) {
		document.getElementById('waitDate').innerHTML="Date end is a future";
		document.getElementById('waitDate').style.display="block";
	} else if (sDate2 > eDate2){
		document.getElementById('waitDate').innerHTML="Finish date is earlier then start date!!!";
		document.getElementById('waitDate').style.display="block";
	} else if (sDate2 < eDate2){
		document.getElementById('waitDate').style.display="none";
		theForm.target = "_blank";
		document.getElementById('createReportForm').submit();
	}

	if (sDate.length == 0 && eDate.length == 0) {
		document.getElementById('waitDate').innerHTML="Dates are empty";
		document.getElementById('waitDate').style.display="block";
	} else if (sDate.length == 0 && eDate.length > 0){
		document.getElementById('waitDate').innerHTML="Date begin is empty";
		document.getElementById('waitDate').style.display="block";
	} else if (sDate.length > 0 && eDate.length == 0){
		document.getElementById('waitDate').innerHTML="Date end is empty";
		document.getElementById('waitDate').style.display="block";
	} else if (sDate2.toString() == eDate2.toString()){
		document.getElementById('waitDate').innerHTML="Dates are equal";
		document.getElementById('waitDate').style.display="block";
	}
	document.getElementById('waitDate').style.color="red";
	return false;
}

function onChangeFreq(select){
	switch (select.options[select.selectedIndex].value) {
		case "annualy":
			fillMonthYear("annualy");
			break;
		case "monthly":
			fillMonthYear("monthly");
			break;
	}
}

function fillMonthYear(type) {
	document.getElementById('monthYearSelect').options.length = 0;


	var currDate = new Date();
	var year = currDate.getFullYear();
	var month = currDate.getMonth()+1;

	var y=new Array(year-2+"",year-1+"",year+"");

	for (i=2;i>=0;i--){
		if ( i == 2 ) {
			if (type == "monthly") {
				for (m=month;m>=1;m--) {
					var x=document.createElement('option');
					x.text=m+"/"+y[i].substring(y[i].length-2,y[i].length);
					x.value=m+"/01/"+y[i];
					try {
						document.getElementById('monthYearSelect').add(x,null); // standards compliant
  					} catch(ex) {
	  					document.getElementById('monthYearSelect').add(x); // IE only
  					}
  				}
			} else {
				var x=document.createElement('option');
				x.text=y[i];
				x.value="01/01/"+y[i];
				try {
					document.getElementById('monthYearSelect').add(x,null); // standards compliant
  				} catch(ex) {
	  				document.getElementById('monthYearSelect').add(x); // IE only
				}
			}
		} else {
			if (type == "monthly") {
				for (m=12;m>=1;m--) {
					var x=document.createElement('option');
					x.text=m+"/"+y[i].substring(y[i].length-2,y[i].length);
					x.value=m+"/01/"+y[i];
					try {
						document.getElementById('monthYearSelect').add(x,null); // standards compliant
  					} catch(ex) {
	  					document.getElementById('monthYearSelect').add(x); // IE only
  					}
  				}
			} else {
				var x=document.createElement('option');
				x.text=y[i];
				x.value="01/01/"+y[i];
				try {
					document.getElementById('monthYearSelect').add(x,null); // standards compliant
  				} catch(ex) {
	  				document.getElementById('monthYearSelect').add(x); // IE only
				}
			}
		}
	}
}