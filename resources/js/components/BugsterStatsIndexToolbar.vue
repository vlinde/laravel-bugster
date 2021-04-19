<template>
  <div class="flex w-full justify-end items-center">
    <button class="btn btn-default btn-primary inline-flex items-center relative" @click="generate_links">Generate Stats!</button>
  </div>
</template>

<script>
export default {
    metaInfo() {
        return {
            title: 'BugsterStatsIndexToolbar',
        }
    },
    mounted() {
        //
    },
    data() {
      //
    },
    methods: {
        generate_links() {
            axios.post("/bugster_generate_stats")
                .then(response => {
                this.$toasted.success(response.data.message_success);
            });
        }
    }
}
</script>