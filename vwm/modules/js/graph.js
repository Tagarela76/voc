function flotGraph(placeholder, legend_cont, data, tick, ylabel, xlabel, y_cont, x_cont, show_legend) {
//   	var my_var = [];
//		my_var = [[1295301600000,25],[1295388000000,25.04],[1295474400000,25.08],[1295560800000,25.12],[1295647200000,25.16],[1295733600000,25.2],[1295820000000,25.24],[1295906400000,25.28],[1295992800000,25.32],[1296079200000,25.36],[1296165600000,25.4],[1296252000000,25.44],[1296338400000,25.48],[1296424800000,25.52],[1296511200000,25.56],[1296597600000,25.6],[1296684000000,25.64],[1296770400000,25.68],[1296856800000,25.72],[1296943200000,25.76],[1297029600000,25.8],[1297116000000,25.84],[1297202400000,25.88],[1297288800000,25.92],[1297375200000,25.96],[1297461600000,26],[1297548000000,26.04],[1297634400000,26.08],[1297720800000,26.12],[1297807200000,26.16]];

//	var tick = 5;
//	var ylabel = 'voc, lbs';
//	var xlabel = 'date';


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

      //  if ($("#enableTooltip:checked").length > 0) {
            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;

                    $("#tooltip").remove();
                    var x = item.datapoint[0].toFixed(2),
                        y = item.datapoint[1].toFixed(2);
                //    var date = new Date();
                //    date.setTime(x);
                //    var month = date.getMonth() + 1
				//	var day = date.getDate()
				//	var year = date.getFullYear()

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
