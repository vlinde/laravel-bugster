<template>
    <div>
        <heading class="mb-6">Status Codes</heading>

        <card class="flex flex-col p-4 w-full" style="min-height: 400px">
            <div class="flex mb-6 justify-between items-center">
                <h2 class="text-90 font-normal text-1xl">Stats</h2>
                <date-range-picker
                    ref="picker"
                    opens="left"
                    :locale-data="{ format: 'dd.mm.yyyy' }"
                    :maxDate="new Date()"
                    :ranges="pickerRanges()"
                    showDropdowns="true"
                    v-model="pickerDateRange"
                    @update="pickerDateUpdated"
                >
                </date-range-picker>
            </div>

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
import DateRangePicker from 'vue2-daterange-picker';
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css';

export default {
    metaInfo() {
        return {
            title: 'Status Codes',
        }
    },
    components: {
        VueApexCharts,
        DateRangePicker
    },
    data() {
        return {
            pickerDateRange: {
                startDate: new Date(),
                endDate: new Date()
            },
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
                    enabled: true,
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
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    floating: false,
                    offsetY: 0,
                    offsetX: -30
                }
            },
            chartSeries: [],
        }
    },
    methods: {
        getChartSeries() {

            let startDate = this.formatDate(this.pickerDateRange.startDate);
            let endDate = this.formatDate(this.pickerDateRange.endDate);

            this.$router.push({query: {startDate, endDate}}).catch(()=>{});

            axios.get(`/nova-vendor/vlinde/bugster/status-codes/chart/?startDate=${startDate}&endDate=${endDate}`)
                .then(({data}) => {
                    this.chartSeries = data.data;
                })
        },
        pickerMaxDate() {
            return new Date();
        },
        pickerDefaultDate() {
            let today = new Date();

            if (this.$route.query.startDate) {
                this.pickerDateRange.startDate = this.$route.query.startDate;
            } else {
                this.pickerDateRange.startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 7);
            }

            if (this.$route.query.endDate) {
                this.pickerDateRange.endDate = this.$route.query.endDate;
            } else {
                this.pickerDateRange.endDate = new Date();
            }
        },
        pickerRanges() {
            let today = new Date();
            today.setHours(0, 0, 0, 0);

            return {
                'Today': [today, today],
                'Last 7 days': [new Date(today.getFullYear(), today.getMonth(), today.getDate() - 7), today],
                'This month': [new Date(today.getFullYear(), today.getMonth(), 1), today],
                'This year': [new Date(today.getFullYear(), 0, 1), today],
            }
        },
        pickerDateUpdated() {
            this.getChartSeries();
        },
        formatDate(date) {
            let dateObject = new Date(date);

            let year = dateObject.getFullYear();
            let month = String(dateObject.getMonth() + 1).padStart(2, '0');
            let day = String(dateObject.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }
    },
    mounted() {
        this.pickerDefaultDate();
        this.getChartSeries();
    },
}
</script>
