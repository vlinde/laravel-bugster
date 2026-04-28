<template>
  <div>
    <Head title="Status Codes" />
    <Heading class="mb-6">Status Codes</Heading>

    <Card class="flex flex-col p-4 w-full" style="min-height: 400px">
      <div class="flex mb-6 justify-between items-center">
        <h2 class="text-90 font-normal text-xl">Stats</h2>
        <DateRangePicker
          ref="picker"
          opens="left"
          :locale-data="{ format: 'dd.mm.yyyy' }"
          :max-date="new Date()"
          :date-range="pickerDateRange"
          :ranges="pickerRanges()"
          :show-dropdowns="true"
          @update:modelValue="pickerDateUpdated"
          @update="pickerDateUpdated"
        >
        </DateRangePicker>
      </div>

      <div class="flex items-center justify-center w-full">
        <VueApexCharts
          type="line"
          height="400"
          :options="chartOptions"
          :series="chartSeries"
          class="w-full"
        ></VueApexCharts>
      </div>
    </Card>
  </div>
</template>

<script>
import VueApexCharts from "vue3-apexcharts";
import DateRangePicker from "vue3-daterange-picker";

export default {
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
          type: "line",
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
            }
          }
        },
        dataLabels: {
          enabled: true
        },
        stroke: {
          curve: "smooth"
        },
        markers: {
          size: 1
        },
        xaxis: {
          type: "category"
        },
        yaxis: {
          title: {
            text: "Count"
          }
        },
        legend: {
          position: "top",
          horizontalAlign: "left",
          floating: false,
          offsetY: 0,
          offsetX: -30
        }
      },
      chartSeries: []
    };
  },
  methods: {
    getChartSeries() {
      let startDate = this.formatDate(this.pickerDateRange.startDate);
      let endDate = this.formatDate(this.pickerDateRange.endDate);

      let url = new URL(window.location.href);
      url.searchParams.set("startDate", startDate);
      url.searchParams.set("endDate", endDate);
      window.history.replaceState({}, "", url);

      Nova.request()
        .get(
          `/nova-vendor/laravel-bugster/status-codes/chart/?startDate=${startDate}&endDate=${endDate}`
        )
        .then(({ data }) => {
          this.chartSeries = data.data;
        });
    },
    pickerDefaultDate() {
      let today = new Date();
      let query = new URLSearchParams(window.location.search);

      if (query.get("startDate")) {
        this.pickerDateRange.startDate = new Date(query.get("startDate"));
      } else {
        this.pickerDateRange.startDate = new Date(
          today.getFullYear(),
          today.getMonth(),
          today.getDate() - 7
        );
      }

      if (query.get("endDate")) {
        this.pickerDateRange.endDate = new Date(query.get("endDate"));
      } else {
        this.pickerDateRange.endDate = new Date();
      }
    },
    pickerRanges() {
      let today = new Date();
      today.setHours(0, 0, 0, 0);

      return {
        Today: [today, today],
        "Last 7 days": [
          new Date(today.getFullYear(), today.getMonth(), today.getDate() - 7),
          today
        ],
        "This month": [
          new Date(today.getFullYear(), today.getMonth(), 1),
          today
        ],
        "This year": [new Date(today.getFullYear(), 0, 1), today]
      };
    },
    pickerDateUpdated(dateRange) {
      if (dateRange && dateRange.startDate && dateRange.endDate) {
        this.pickerDateRange = dateRange;
      }

      this.getChartSeries();
    },
    formatDate(date) {
      let dateObject = new Date(date);

      let year = dateObject.getFullYear();
      let month = String(dateObject.getMonth() + 1).padStart(2, "0");
      let day = String(dateObject.getDate()).padStart(2, "0");

      return `${year}-${month}-${day}`;
    }
  },
  mounted() {
    this.pickerDefaultDate();
    this.getChartSeries();
  }
};
</script>
