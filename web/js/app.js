function buildChart(data)
{
    var
        view = data[0][0],
        services = data[0][1],
        service = view == 'uptime' ? view : services,
        d, record, i, j,
        series = [], _yAxis, _series = [], highchart;

    for (i = 1; i < data.length; i++) {
        d = data[i];
        if (!d) {
            continue;
        }
        switch (view) {
            case 'uptime':
                for (j = 0; j < services.length; j++) {
                    if ('undefined' == typeof(series[j])) {
                        series[j] = [];
                    }
                    record = [
                        Date.UTC(d[0], d[1], d[2], d[3]),
                        d[4 + j]
                    ];
                    series[j].push(record);
                }

                break; // case 'uptime'

            case 'details':
                for (j = 0; j < 6; j++) {
                    if ('undefined' == typeof(series[j])) {
                        series[j] = [];
                    }
                    if ('undefined' == typeof(d[7 + j])) {
                        continue;
                    }
                    if ('F' == d[6] && !j) {
                        // Fail
                        record = {
                            x: Date.UTC(d[0], d[1], d[2], d[3], d[4], d[5]),
                            y: d[7 + j],
                            marker: {
                                fillColor: 'red',
                                symbol: 'diamond',
                                radius: 4
                            }
                        };
                    } else {
                        // Success, common point
                        record = [
                            Date.UTC(d[0], d[1], d[2], d[3], d[4], d[5]),
                            d[7 + j]
                        ];
                    }
                    series[j].push(record);
                }

                break; // case 'details'
        }
    }

    switch (view) {
        case 'uptime':
            _yAxis = {
                title: {
                    text: '%'
                },
                min: 0,
                max: 100
            };
            _series = [];
            for (j = 0; j < services.length; j++) {
                _series[j] = {
                    // type:  'area',
                    name:  services[j],
                    // color: '#000',
                    data:  series[j]
                };
            }

            break; // case 'uptime'

        case 'details':
            _yAxis = {
                title: {
                    text: 'Time, sec.'
                },
                min: 0
            };
            _series = [{
                type:  'area',
                name:  'Total time',
                color: '#000',
                data:  series[1]
            },
            {
                type: 'area',
                name: 'Connect time',
                data: series[0]
            },
            {
                type:  'area',
                name:  'Max total time',
                data:  series[2]
            },
            {
                type: 'area',
                name: 'Average total time',
                data: series[3]
            },
            {
                type:  'area',
                name:  'Max connect time',
                data:  series[4]
            },
            {
                type: 'area',
                name: 'Average connect time',
                data: series[5]
            }
            ];

            break; // case 'details'
    }
    highchart = {
        chart: {
            zoomType: 'x',
            animation: false,
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
            layout:        'vertical',
            align:         'right',
            verticalAlign: 'middle'
        },

        xAxis: {
            type: 'datetime'
        },

        yAxis: _yAxis,

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
                /*
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                */
                threshold: null
            }
        },

        tooltip: {
            shared:     true,
            crosshairs: true
        },

        series: _series

    };

    $('#container-' + service).highcharts(highchart);
}
