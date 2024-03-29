$(document).ready(docReady);

function Notificator(text, params)// params: color,backgroundColor, width, height)
{
	if(params)
	{
		color = params.color || "Black";
		backgroundColor = params.backgroundColor || "Red";
		width = params.width || "400px";
		this.height = params.height || "34px";
		fontSize = params.fontSize || "18px";
	}
	else
	{
		alert("else");
	}



var notify = $("#notify");
$(notify).css("text-align","center");
$(notify).css("background-color",backgroundColor);
$("#text").css("color",color);
$(notify).css("display","none");
$(notify).css("width",width);
$(notify).css("position","fixed");
$(notify).css("height","0%");
//$("#notify").css("bottom",$(window).height() / 2 +"px");
$(notify).css("top",0+"px");
$(notify).css("line-break","strict");
$(notify).css("padding","10px");
//$(notify).css("opacity","0.8");

$(notify).css("vertical-align","middle");
$("#text").css("font-size",fontSize);

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


	}
}


function docReady()
{
	var notificator = new Notificator(notifyText,notifyParams);// { color: "White", backgroundColor: "Black"});
	notificator.show();
}


