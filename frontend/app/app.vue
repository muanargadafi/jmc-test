<script setup>
const route = useRoute()

useHead({
  title: computed(() => route.meta.title || undefined),
})
</script>

<template>
  <div class="light" data-theme="light">
    <NuxtLoadingIndicator />

    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>
