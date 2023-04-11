<template>
    <router-link :to="{
            name: 'bugster-status-codes-chart',
            params: {
                code: code,
                display_name: display_name
            }
        }"
        class="text-white ml-8 no-underline dim">
        {{ display_name }}
    </router-link>
</template>

<script>
export default {
    props: [
        'code',
        'display_name'
    ]
}
</script>
