<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>VOC WEB MANAGER stats</title>
    <link href="modules/js/flot/layout.css" rel="stylesheet" type="text/css"></link>
    <!--[if IE]><script language="javascript" type="text/javascript" src="modules/js/flot/excanvas.pack.js"></script><![endif]-->
    <script language="javascript" type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-1.4.2.min.js"></script>
    <script language="javascript" type="text/javascript" src="modules/js/flot/jquery.flot.js"></script>
	<link type="text/css" href="modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css" rel="stylesheet" />		
	<script type="text/javascript" src="modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js"></script> 
 </head>
    <body onload="ini();">   
	<div style="width:650px">
		<div style="text-align:center;"> <h1>VOCWEBMANAGER's users</h1></div>
		<div style="height:24px;">
	 		<div id="preloader" style="display:none;text-align:center"><img src="images/preloaderOrange.gif"></div>
		</div>				
	</div>
	
	<div style="width:500px;padding:0 75px 0 75px;">
		<div style="float:left;">
			<div>From</div>
			<div id="datepickerStart"></div>
		</div>
	 	<div style="float:right;">
			<div>To</div>
			<div id="datepickerFinish"></div>
		</div>
		<div style="clear:both"></div>		
	</div>

    <div id="placeholder" style="width:650px;height:300px;"></div>
 	 
	    
{literal}
<script id="source" language="javascript" type="text/javascript">
	var d = new Date();	
	var finishDate = d.getTime();
	var startDate = finishDate - 60000*60*24*7;		
		
	function ini() {
		//	get data fo first time (page load)
		$('#preloader').css('display','block');
		$.ajax ({url: "admin.php?action=stats",
			type: "POST",
			dataType: "json", 
			data: {"startDate": startDate,											 
			 		"finishDate": finishDate
					},											
			success: function (r) {
				parseAnswer(r);													 	
			}
		}) 								 
		
		
		// Datepicker
		$('#datepickerStart').datepicker({
			inline: true,
			defaultDate: -7,
			dateFormat: "@",
			onSelect: function(dateText, inst) {
									//$('#datepickerFinish').datepicker('option', { dateFormat: 'yy-mm-dd'});
									startDate = dateText;
									$('#preloader').css('display','block');
									$.ajax ({url: "admin.php?action=stats",
											 type: "POST",
											 dataType: "json", 
											 data: {"startDate": dateText,											 
											 		"finishDate": finishDate
													},											
											 success: function (r) {
											 			parseAnswer(r);													 	
											 		}
								      		}) 								 
									}
		});
		$('#datepickerFinish').datepicker({
			inline: true,
			dateFormat: "@",
			//defaultDate: 0,
			onSelect: function(dateText, inst) {
									//$('#datepickerStart').datepicker('option', { dateFormat: 'yy-mm-dd'});
									finishDate = dateText;
									$('#preloader').css('display','block');
									$.ajax ({url: "admin.php?action=stats",
											 type: "POST", 
											 dataType: "json",
											 data: {"startDate": startDate,											 
											 		"finishDate": dateText
													},											
											 success: function (r) {
											 				parseAnswer(r);											 				
											 		}
								      		}) 								 
									}							
			});
	}
	
	
	function parseAnswer(r) {		
		var plotData = new Array();	
		
		if (r.failure === true) {
			$('#preloader').css('display','none');
			alert ('Incorrect input!');
		} else {			
			for (var property in r.data) {
				point = [property, r.data[property]]
				plotData[plotData.length] = point;			
			}
			$('#preloader').css('display','none');																
			showPlot(plotData);	
		}				
	}
	
	
	function showPlot(d1) {
		$.plot($("#placeholder"), [d1], { xaxis: { mode: "time" } });	
	}
</script>
{/literal}

 </body>
</html>