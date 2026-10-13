<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  breadcrumb: {
    type: Array,
    required: false,
    default: () => [],
  },
})
</script>

<template>
  <!-- Breadcrumb -->
  <UBreadcrumb
    v-if="breadcrumb?.length"
    :items="breadcrumb"
    separator="/"
    color="brand"
    :ui="{
      list: 'gap-1 text-[11px] font-semibold tracking-wider',
      link: 'text-[11px] tracking-wider',
      separator: 'text-gray-300',
    }"
  >
    <template #separator>
      <span class="mx-1 text-gray-300">/</span>
    </template>
  </UBreadcrumb>

  <!-- Title -->
  <h1 class="text-xl font-bold text-gray-800 tracking-tight">{{ title }}</h1>
</template>
