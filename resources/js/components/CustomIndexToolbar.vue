<template>
    <component v-if="hasComponent" :is="component" :resourceName="resourceName"/>
    <div v-else class="flex w-full justify-end items-center mx-3"></div>
</template>
<script>
    export default {
        props: ['resourceName'],
        computed: {
            component() {
              console.log(this.resourceName);
                return this.resourceName + '-index-toolbar';
            },
            hasComponent() {
                return this.component in this.$options.components;
            }
        }
    }
</script>