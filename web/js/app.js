function buildChart(data)
{
    var
        view = data[0][0],
        services = data[0][1],
        service = view == 'uptime' ? view : services,
        series = 'uptime' == view ? [] : [[], []],
        d, record, i, j,
        yAxis;

    for (i = 1; i < data.length; i++) {
        d = data[i];
        switch (view) {
            case 'uptime':
                record = [
                    Date.UTC(d[0], d[1], d[2], d[3]),
                    d[4]
                ];
                series.push(record);
                break; // case 'uptime'

            case 'details':
                for (j = 0; j < 2; j++) {
                    if (('S' == d[5]) || !j) {
                        // Success, common point
                        record = [
                            Date.UTC(d[0], d[1], d[2], d[3], d[4]),
                            d[6 + j]
                        ];
                    } else {
                        record = {
                            x: Date.UTC(d[0], d[1], d[2], d[3], d[4]),
                            y: d[6 + j],
                            marker: {
                                fillColor: 'red',
                                symbol: 'diamond',
                                radius: 4
                            }
                        };
                    }
                    series[j].push(record);
                }

                break; // case 'details'
        }
    }
    yAxis =
        view == 'uptime'
            ? {
                title: {
                    text: '%'
                },
                min: 0,
                max: 100
            } : {
                title: {
                    text: 'Time, sec.'
                },
                min: 0
            };

    $('#container-' + service).highcharts({
        chart: {
            zoomType: 'x'
        },

        title: {
            text: view.toUpperCase()
        },

        subtitle: {
            text:
                document.ontouchstart === undefined
                    ? 'Click and drag in the plot area to zoom in'
                    : 'Pinch the chart to zoom in'
        },

        legend: {
            enabled: view != 'uptime'
        },

        xAxis: {
            type: 'datetime'
        },
        yAxis: yAxis,

        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 1
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },

        tooltip: {
            shared:     true,
            crosshairs: true
        },

        series:
            'uptime' == view
                ? [{
                    type:  'area',
                    name:  'Uptime, %',
                    // color: '#000',
                    data:  series
                }]
                : [{
                    type:  'area',
                    name:  'Total time',
                    color: '#000',
                    data:  series[1]
                },
                {
                    type: 'area',
                    name: 'Connect time',
                    data: series[0]
                }]
    });
}
