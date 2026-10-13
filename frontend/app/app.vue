<script setup>
const route = useRoute()

useHead({
  title: computed(() => route.meta.title || undefined),
})
</script>

<template>
  <UApp>
    <NuxtLoadingIndicator />

    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </UApp>
</template>
