function flotGraph(placeholder, legend_cont, data, tick, ylabel, xlabel, y_cont, x_cont, show_legend) {


    var plot = $.plot(placeholder,
           data, {
               series: {
                   lines: { show: true },
                   points: { show: true }
               },
               grid: { hoverable: true, clickable: false },
			   xaxes: [ { mode: 'time',
			   			timeformat: "%0m/%0d/%y"
						} ],
               yaxis: { min: 0 },
			   xaxis: { tickSize: [tick, "day"] },
			   legend: { container: legend_cont, show: show_legend }
             });

	// add labels
	placeholder.append('<div style="position:absolute;left:' + (0) + 'px;top:' + -20 + 'px;color:#666;font-size:smaller">' + ylabel + '</div>');
	placeholder.append('<div style="position:absolute;left:' + (600) + 'px;top:' + 300 + 'px;color:#666;font-size:smaller">' + xlabel + '</div>');

//managing titles
    function showTooltip(x, y, contents) {
        $('<div id="tooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y + 5,
            left: x + 5,
            border: '1px solid #fdd',
            padding: '2px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
    }

//check for title change
    var previousPoint = null;
    placeholder.bind("plothover", function (event, pos, item) {
    	var date = new Date();
    	date.setTime(pos.x.toFixed(2));
        var month = date.getMonth() + 1
		var day = date.getDate()
		var year = date.getFullYear()
        x_cont.text(month + "/" + day + "/" +year);
        y_cont.text(pos.y.toFixed(2));


            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;

                    $("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(2),
                        y = item.datapoint[1].toFixed(2);
      

                    showTooltip(item.pageX, item.pageY,
                                item.series.label + " of " + month + "/" + day + "/" +year+ "= " + y);
                }
            }
            else {
                $("#tooltip").remove();
                previousPoint = null;
            }
       // }
    });


};
