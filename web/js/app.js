function buildChart(data)
{
    var
        view = data[0][0],
        services = data[0][1],
        service = view == 'uptime' ? view : services,
        d, record, i, j, limit, maxTime = 0, maxHits = 0,
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

            case 'grouped-details':
            case 'details':
                limit = ('details' == view ? 2 : 6);
                for (j = 0; j < limit; j++) {
                    if ('undefined' == typeof(series[j])) {
                        series[j] = [];
                    }
                    if ('details' == view) {
                        if ('F' == d[6] && j == 1) {
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
                    } else {
                        if (0 == j) {
                            // Max total time
                            maxTime = Math.max(maxTime, d[4 + j]);
                        }
                        if (4 == j) {
                            // Total hits
                            maxHits = Math.max(maxHits, d[4 + j]);
                        }
                        record = [
                            // Date.UTC(d[0], d[1], d[2], d[3], d[4], d[5]),
                            Date.UTC(d[0], d[1], d[2], d[3], 0, 0),
                            d[4 + j]
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
            _series = [
                {
                    // type: 'area',
                    name: 'Connect time, sec.',
                    data:  series[0]
                },
                {
                    type:  'area',
                    name:  'Total time, sec.',
                    // color: '#000',
                    data:  series[1]
                }
            ];

            view += ' (last 24 hours)';

            break; // case 'details'

        case 'grouped-details':
            // console.log('maxTime', maxTime);///
            // console.log('maxHits', maxHits);///
            _yAxis = [
                { // left y axis
                    title: {
                        text: 'Time, sec.'
                    },
                    min: 0,
                    max: maxTime,
                    tickInterval: 0.1,
                    labels: {
                        align: 'left',
                        x: 3,
                        y: 16/*,
                        format: '{value:.,0f}'*/
                    },
                    showFirstLabel: false
                },
                { // right y axis
                    min: 0,
                    // max: maxHits,
                    max: 100,
                    tickInterval: 10,
                    gridLineWidth: 0,
                    opposite: true,
                    title: {
                        text: 'Hits'
                    },
                    labels: {
                        align: 'right',
                        x: -3,
                        y: 16/*,
                        format: '{value:.,0f}'*/
                    },
                    showFirstLabel: false
                }
            ];

            series[6] = [];
            var t = new Date('2015-07-04 22:00:00');
            t = t.getTime();
            for (j in series[4]) {
                record = series[4][j];
                var runsPerHour = record[0] < t ? 60 : 360;
                record[1] = (runsPerHour - (runsPerHour - record[1]) - series[5][j][1]) * 100 / runsPerHour;
                series[6].push(record);
            }

            _series = [
                {
                    name:    'Uptime',
                    data:    series[6],
                    yAxis:   1,
                },
                /*
                {
                    // type:  'area',
                    name:    'Total hits',
                    data:    series[4],
                    visible: false,
                    yAxis:   1
                },
                {
                    // type: 'area',
                    name:    'Failed hits',
                    data:    series[5],
                    visible: false,
                    yAxis:   1
                },
                */
                {
                    // type: 'area',
                    name: 'Average connect time, sec.',
                    data:  series[3],
                    yAxis: 0
                },
                {
                    // type: 'area',
                    name: 'Average total time, sec.',
                    data:  series[1],
                    yAxis: 0
                },
                {
                    // type:  'area',
                    name:  'Max connect time, sec.',
                    data:  series[2],
                    yAxis: 0
                },
                {
                    type:  'area',
                    name:  'Max total time, sec.',
                    data:  series[0],
                    yAxis: 0
                }
            ];
            service = 'grouped-' + service;

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
                (
                    'uptime' == view
                        ? 'Hits grouped by hours, 100% means all hits were done and successfull.<br />'
                        : ''
                ) +
                (
                    document.ontouchstart === undefined
                        ? 'Click and drag in the plot area to zoom in.'
                        : 'Pinch the chart to zoom in.'
                )
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
