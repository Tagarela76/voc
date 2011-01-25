$(document).ready(docReady);

function Notificator(text, params)// params: color,backgroundColor, width, height)
{	
	if(params)
	{
		color = params.color || "Black";
		backgroundColor = params.backgroundColor || "Red";
		width = params.width || "600px";
		this.height = params.height || "37px";
	}
	else
	{
		alert("else");
	}
	


var notify = $("#notify");
$(notify).css("text-align","center");
$(notify).css("background-color",backgroundColor);
$(notify).css("color",color);
$(notify).css("display","none");
$(notify).css("width",width);
$(notify).css("position","fixed");
$(notify).css("height","0%");
//$("#notify").css("bottom",$(window).height() / 2 +"px");
$(notify).css("top",0+"px");
$(notify).css("line-break","strict");
$(notify).css("padding","10px");
$(notify).css("opacity","0.8");

$(notify).css("vertical-align","middle");
$(notify).css("font-size","16px");

$(notify).css("left",($(window).width() / 2) - ($("#notify").width() / 2)); 

$("#text").text(text);

this.show = function()/*Show Notify*/
	{
		$("#notify").css("display","block");
		
		$("#notify").animate({ height: "+=" + this.height }, 1000);
		$("#notifyClose").click(this.close);
	}
this.close = function ()/*Close Notify*/
	{
		$("#notify").css("display","none");
		//$("#notify").animate({ height: "-=25%" }, 1000, null, function () {$("#notify").css("display","none");});
		
	}
}


function docReady()
{
	var appendText = "<div id='notify' valign='center'><a href='#' id='notifyClose' style='position:absolute;left:0px;top:0px;background-color:inherit;border:none;'><img src='images/close.png' style='border: 0;' /></a><span id='text'></span></div>";
	$("body").append(appendText);
	//alert('docReady: notifyText: ' + notifyText + " params: " + notifyParams);
	var notificator = new Notificator(notifyText,notifyParams);// { color: "White", backgroundColor: "Black"});
	notificator.show();
}


