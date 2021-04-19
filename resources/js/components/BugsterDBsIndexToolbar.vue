<template>
  <div class="flex w-full justify-end items-center">
    <button class="btn btn-default btn-primary inline-flex items-center relative" @click="parse_logs">Parse logs!</button>
  </div>
</template>

<script>
export default {
    metaInfo() {
        return {
            title: 'BugsterDBsIndexToolbar',
        }
    },
    mounted() {
        //
    },
    data() {
      //
    },
    methods: {
        parse_logs() {
            axios.post("/bugster_parse_logs")
                .then(response => {
                this.$toasted.success(response.data.message_success);
            });
        }
    }
}
</script>