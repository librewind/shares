function printChart(el, chartData, startDate, stopDate) {
    Highcharts.chart(el, {

        title: {
            text: ''
        },

        xAxis: {
            type: 'datetime',
            min: startDate,
            max: stopDate,
            labels: {
                step: 1,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Arial,sans-serif'
                }
            },
            dateTimeLabelFormats: { // don't display the dummy year
                month: '%b \'%y',
                year: '%Y'
            },
            tickInterval: 30 * 24 * 3600 * 1000,
        },

        yAxis: {
            title: {
                text: 'Доходность, %'
            }
        },
        legend: {
            enabled: false,
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },

        tooltip: {
            headerFormat: '<b>{series.name}: </b>',
            pointFormat: '{point.y}%'
        },

        series: [{
            name: 'Доходность',
            data: chartData
        }]

    });
}