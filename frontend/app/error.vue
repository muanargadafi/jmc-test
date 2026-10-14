<script setup>
const props = defineProps({
  error: Object,
})

const backendData = computed(() => props.error?.data)
const statusCode = computed(() => props.error?.statusCode ?? 500)

const ERROR_CONFIG = {
  400: {
    icon: 'i-lucide-alert-triangle',
    title: 'Bad Request',
    fallbackMessage: 'The request could not be processed.',
  },
  401: {
    icon: 'i-lucide-lock',
    title: 'Unauthorized',
    fallbackMessage: 'You need to sign in to access this page.',
  },
  403: {
    icon: 'i-lucide-shield-x',
    title: 'Access Forbidden',
    fallbackMessage: 'You do not have permission to access this resource.',
  },
  404: {
    icon: 'i-lucide-file-x',
    title: 'Page Not Found',
    fallbackMessage: 'The page you are looking for does not exist.',
  },
  409: {
    icon: 'i-lucide-git-merge',
    title: 'Conflict',
    fallbackMessage: 'This action conflicts with the current data state.',
  },
  422: {
    icon: 'i-lucide-list-x',
    title: 'Validation Error',
    fallbackMessage: 'Some fields failed validation.',
  },
  429: {
    icon: 'i-lucide-timer',
    title: 'Too Many Requests',
    fallbackMessage: 'Please wait a moment before trying again.',
  },
  500: {
    icon: 'i-lucide-server-crash',
    title: 'Server Error',
    fallbackMessage: 'Something went wrong on our end.',
  },
}

const DEFAULT_CONFIG = {
  icon: 'i-lucide-circle-alert',
  title: 'Something Went Wrong',
  fallbackMessage: 'An unexpected error occurred.',
}

const config = computed(() => ERROR_CONFIG[statusCode.value] ?? DEFAULT_CONFIG)

const displayMessage = computed(
  () => backendData.value?.message ?? props.error?.statusMessage ?? config.value.fallbackMessage,
)

useHead({
  title: config.value.title,
  htmlAttrs: {
    lang: 'en',
  },
})
</script>

<template>
  <UApp>
    <UError
      :icon="config.icon"
      :error="{
        statusCode: statusCode,
        statusMessage: config.title,
        message: displayMessage,
      }"
      :clear="{
        label: 'Back to Home',
        color: 'neutral',
        variant: 'solid',
        class:
          'bg-[#38789a] hover:bg-[#2c6582] text-white text-xs font-semibold rounded transition-colors shadow-xs',
      }"
    />
  </UApp>
</template>
