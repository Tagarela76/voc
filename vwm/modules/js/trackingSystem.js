
var selectedTrackID = null;

//var options, a;
$(function()
{
	loadSearchTemplate();
	loadLastTracks();	
});




function loadSearchTemplate()
{
	$("#preloader").css("display", "block");
	$.ajax({
      	url: "modules/ajax/trackingSystem.php",
      	type: "POST",
      	async: false,
      	data: { "action":"searchTpl"},
      	dataType: "html",
      	success: function (r) {
      		//	append HTML to special div
			$("#trackingContainer").html(r);
			$("#preloader").css("display", "none");
			//	hide preloader
      	}
	});

	$('#goSearchTrack').click(function(el)
	{
		var searchText=$('#searchTrack').attr('value');
		searchText=searchText.replace(/^\s+/,"");
		searchText=searchText.replace(/\s+$/,"");
		$('#searchTrack').attr('value',searchText);
		loadLastTracks(false,searchText);
	});

	$('#searchTrack').keyup(function(e){
     	 if(e.keyCode==13)
     		$('#goSearchTrack').trigger('click');
	});
}

function loadLastTracks(notification,searchText) {
	//	show preloader
	$("#preloader").css("display", "block");
	$.ajax({
      	url: "modules/ajax/trackingSystem.php",
      	type: "POST",
      	async: false,
      	data: { "action":"index","searchText":searchText},
      	dataType: "html",
      	success: function (r) {
      		//	append HTML to special div
			$("#trackTable").html(r);
			if (notification) {
				$("#notify").html(notification);
				$("#notify").fadeIn();
			}
			$("#preloader").css("display", "none");
			//	hide preloader

      	}
	});
}

function searchInputIni() {
	var searchInputID = "searchTrack",
		input = $("#" + searchInputID),
		label = input.parent(),
		labelSpan = label.children('span'),
		labelCss = {
        	'position': 'relative',
        	'color': '#D3D3D3'
        }

	if (!input.value) {
		labelSpan.text('Search action to rollback');
		label.css(labelCss);
	}

	input.focus(function(){
		labelSpan.css('display', 'none');
	});
	input.blur(function(){
		if (input.attr("value") == null) {
			labelSpan.css('display', 'block');
		}
	});
}



function rollback(trackID) {
	//	show preloader
	$("#preloader").css("display", "block");
	selectedTrackID = trackID;

	$.ajax({
      	url: "modules/ajax/trackingSystem.php",
      	type: "POST",
      	data: { "action":"rollback", "id":trackID },
      	dataType: "html",
      	success: function (r) {
      		//	append HTML to special div
			$("#trackingContainer").html(r);
			$("#notify").fadeIn();
			//	hide preloader
			$("#preloader").css("display", "none");
      	}
	});
}



function areYouSure() {
	//	show preloader
	$("#preloader").css("display", "block");

	$.ajax({
      	url: "modules/ajax/trackingSystem.php",
      	type: "POST",
      	data: { "action":"confirm", "id":selectedTrackID },
      	dataType: "html",
      	success: function (r) {
      		//	append HTML to special div
			$("#trackingContainer").html(r);
			$("#notify").fadeIn();
			//	hide preloader
			$("#preloader").css("display", "none");
      	}
	});
}



function iAmSure() {
	//	show preloader
	$("#preloader").css("display", "block");

	$.ajax({
      	url: "modules/ajax/trackingSystem.php",
      	type: "POST",
      	data: { "action":"rollbackConfirmed", "id":selectedTrackID },
      	dataType: "json",
      	success: function (r) {
      		loadSearchTemplate();
      		if (r.result) {
      			loadLastTracks(generateNotify('Rollback successfully completed. Thank you for using Tracking System','green'),'false');
      		} else {
      			loadLastTracks(generateNotify('Oops, call developers. PS: sorry(','red'),'false');
      		}
      	}
	});
}


function generateNotify(text, color) {
	var colorPrefix;
	var colorPrefixTail;

	//	generate prefix by color
	switch (color) {
		case 'red':
			colorPrefix = 'o';	//	orange
			colorPrefixTail = 'orange';
			break;
		case 'green':
			colorPrefix = 'gr';	//	green
			colorPrefixTail = 'green';
			break;
		default:
			colorPrefix = 'r';	//	blue
			colorPrefixTail = 'blue';
	}

	//	create table
	var table = document.createElement('TABLE');
	table.align = 'center';
	table.cellPadding = '0';
	table.cellSpacing = '0';
	table.className = 'pop_up';
	var tbody = document.createElement('TBODY');	//	TBODY is needed for IE

	//	create first row
	var row1 = document.createElement('TR');
	var data1 = document.createElement('TD');
	var divOut = document.createElement('DIV');
	divOut.className = 'bl_'+colorPrefix;
	var divMiddle = document.createElement('DIV');
	divMiddle.className = 'br_'+colorPrefix;
	var divIn = document.createElement('DIV');
	divIn.className = 'tl_'+colorPrefix;
	var divText = document.createElement('DIV');
	divText.className = 'tr_'+colorPrefix;

	//	create seond row
	var row2 = document.createElement('TR');
	var data2 = document.createElement('TD');
	data2.className = 'tail_'+colorPrefixTail;

	//	build model
	divText.appendChild(document.createTextNode(text));
	divIn.appendChild(divText);
	divMiddle.appendChild(divIn);
	divOut.appendChild(divMiddle);
	data1.appendChild(divOut);
	row1.appendChild(data1);
	row2.appendChild(data2);

	tbody.appendChild(row1);
	tbody.appendChild(row2);

	table.appendChild(tbody);

	return table;
}