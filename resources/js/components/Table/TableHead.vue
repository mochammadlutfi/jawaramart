<template>
  <th @click.prevent="sortBy(name)" class="th">
    <slot>{{ name }}</slot>
    <slot name="icon">
      <span v-if="getCurrentSort == name" v-html="getSortIcon" />
    </slot>
  </th>
</template>
<style>
    .table .th {
        cursor: pointer;
    }
</style>
<script>
export default {
    name: "TableHead",
    inject: ["getCurrentSort", "sortBy", "getSortIcon"],
    props: {
        name: {
            type: String,
            required: true,
            default: ""
        }
    },
};
</script>