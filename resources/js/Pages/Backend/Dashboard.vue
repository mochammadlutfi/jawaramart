<template>
    <BaseLayout title="Dashboard">
        <div class="content">
            <h2 class="content-heading">Dashboard <small>Get Started</small></h2>
            <div class="row gutters-tiny">
                <!-- Row #1 -->
                <div class="col-md-6 col-xl-3" v-for="(d, i) in overview" :key="i">
                    <div class="block block-shadow-2 block-rounded">
                        <div class="block-content block-content-full text-right">
                            <div class="font-size-sm font-w600 text-uppercase text-muted">{{ d.title }}</div>
                            <div class="font-size-h2 font-w700">
                                <template v-if="d.type != 'money' ">
                                    {{ d.data }}
                                </template>
                                <template v-else>
                                    {{ currency(d.data) }}
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END Row #1 -->
            </div>
            <div class="block block-shadow block-bordered block-rounded">
                <div class="block-header">
                    <h3 class="block-title">
                        Earnings <small>7 Days Ago</small>
                    </h3>
                </div>
                <div class="block-content">
                    <chartjs-line :chart-data="chartjsLineBarsRadarData" :options="chartjsOptions"></chartjs-line>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import Chart from 'chart.js';
import moment from 'moment';


import ChartjsLine from '@/components/Chartjs/Line';

export default {
    components: {
        BaseLayout,
        ChartjsLine
    },
    props : {
        overview : Array,
        chartSales : Object,
    },
    data () {
        return {
            chartjsOptions: {
                maintainAspectRatio: false,
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            min : 0,
                            callback: (value, index, values) => {
                                return this.currency(value);
                            },
                        },
                    }],
                },
                
                tooltips: {
                    enabled: true,
                    callbacks: {
                        label: ((tooltipItems, data) => {
                            return this.currency(tooltipItems.yLabel)
                        })
                    }
                }
            },
            chartjsLineBarsRadarData: {
                labels: this.chartSales.label,
                datasets: [
                    {
                        label: 'Sale',
                        fill: !0,
                        backgroundColor: "rgba(66,165,245,.45)",
                        borderColor: "rgba(66,165,245,1)",
                        pointBackgroundColor: "rgba(66,165,245,1)",
                        pointBorderColor: "#fff",
                        pointHoverBackgroundColor: "#fff",
                        pointHoverBorderColor: "rgba(66,165,245,1)",
                        data: this.chartSales.sale
                    },
                ]
            },
        }
    },
    setup () {
        Chart.defaults.global.defaultFontColor              = '#555555'
        Chart.defaults.scale.gridLines.color                = "transparent"
        Chart.defaults.scale.gridLines.zeroLineColor        = "transparent"
        Chart.defaults.scale.display                        = false
        Chart.defaults.scale.ticks.beginAtZero              = true
        Chart.defaults.global.elements.point.radius         = 5
        Chart.defaults.global.elements.point.hoverRadius    = 7
        Chart.defaults.global.tooltips.cornerRadius         = 3
        Chart.defaults.global.legend.display                = false;
    },
}
</script>
