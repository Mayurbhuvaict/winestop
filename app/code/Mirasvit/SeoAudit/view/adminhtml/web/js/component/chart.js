define([
    'jquery',
    'chartJs',
    'moment'
], function ($, Chart, moment) {
    $.widget('mst.seoAuditChart', {
        options: {
            id: '',
            type: '',
            data: null,
            chartOptions: null,
        },

        id: null,
        type: null,
        data: null,
        chartOptions: null,

        _create: function () {
            this.id = this.options.id;
            this.type = this.options.type;
            this.data = this.options.data;
            this.chartOptions = this.options.chartOptions;

            if (this.type === 'bar') {
                this.chartOptions.tooltips.callbacks = {
                    title: function (tooltipItems, data) {
                        return moment(tooltipItems[0].xLabel).format('MMM D');
                    }
                }

                var ctx = document.getElementById(this.id).getContext('2d');

                var gradient = ctx.createLinearGradient(0,235,0,0);
                gradient.addColorStop(0, 'rgb(244,67,54)');
                gradient.addColorStop(0.02, 'rgb(244,67,54)');
                gradient.addColorStop(0.4, 'rgb(252,220,37)');
                gradient.addColorStop(0.6, 'rgb(252,220,37)');
                gradient.addColorStop(0.98, 'rgb(71,205,74)');
                gradient.addColorStop(1, 'rgb(71,205,74)');

                var dataset = this.data.datasets[0];

                dataset.backgroundColor = gradient;

                this.data.datasets[0] = dataset;
            } else {
                this.chartOptions.tooltips.callbacks = {
                    label: function (tooltipItem, data) {
                        return data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index] + '%';
                    }
                }

                this.chartOptions.tooltips.mode = 'point';
            }

            if (this.id == 'healthScore') {
                var ctx = document.getElementById(this.id).getContext('2d');

                var gradient = ctx.createRadialGradient(30,200,30,55,60,190);
                gradient.addColorStop(0, 'rgb(244,67,54)');
                gradient.addColorStop(0.35, 'rgb(252,220,37)');
                gradient.addColorStop(0.6, 'rgb(252,220,37)');
                gradient.addColorStop(1, 'rgb(71,205,74)');

                var dataset = this.data.datasets[0];

                dataset.backgroundColor = [gradient, '#ccc'];

                this.data.datasets[0] = dataset;
            }

            var chartConfig = {
                type: this.type,
                data: this.data,
                options: this.chartOptions
            }

            var chart = new Chart(
                $("#" + this.id),
                chartConfig
            );
        }
    });

    return $.mst.seoAuditChart;
});
