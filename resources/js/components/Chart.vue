<template>
    <div>
        <heading class="mb-6">...</heading>

        <card class="flex flex-col p-4 w-full" style="min-height: 400px">
            <div class="flex items-center justify-center w-full">
                <vue-apex-charts type="line" height="400" :options="chartOptions"
                                 :series="chartSeries"
                                 class="w-full"></vue-apex-charts>
            </div>
        </card>
    </div>
</template>

<script>
import axios from 'axios';
import VueApexCharts from 'vue-apexcharts';

export default {
    components: {
        VueApexCharts
    },
    data() {
        return {
            chartOptions: {
                chart: {
                    type: 'line',
                    toolbar: {
                        show: true,
                        offsetX: 0,
                        offsetY: 0,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: false,
                            zoomin: false,
                            zoomout: false,
                            pan: false,
                            reset: false,
                            customIcons: []
                        },
                    }
                },
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    curve: 'smooth'
                },
                markers: {
                    size: 1
                },
                xaxis: {
                    type: 'category',
                },
                yaxis: {
                    title: {
                        text: 'Count'
                    },
                },
                legend: {
                    show: false
                }
            },
            chartSeries: [],
        }
    },
    methods: {
        getChartSeries() {
            axios.get(`/nova-vendor/stats-by-countries/${this.countryId}/companies`)
                .then(({data}) => {
                    this.chartSeries = data.counts;
                })
        },
    },
    mounted() {
        this.getChartSeries();
    },
}
</script>
