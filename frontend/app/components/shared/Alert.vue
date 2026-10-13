<script setup>
const props = defineProps({
  type: {
    type: String,
    default: 'error',
  },
  duration: {
    type: Number,
    default: 5000,
  },
})

const message = defineModel('message')

const ALERT_CONFIG = {
  error: { icon: 'i-lucide-circle-alert', color: 'error' },
  primary: { icon: 'i-lucide-bell', color: 'primary' },
  secondary: { icon: 'i-lucide-bookmark', color: 'secondary' },
  success: { icon: 'i-lucide-circle-check', color: 'success' },
  info: { icon: 'i-lucide-info', color: 'info' },
  warning: { icon: 'i-lucide-triangle-alert', color: 'warning' },
  neutral: { icon: 'i-lucide-message-square', color: 'neutral' },
}

const config = computed(() => ALERT_CONFIG[props.type] || ALERT_CONFIG.error)

let timer = null

function clearTimer() {
  if (timer) {
    clearTimeout(timer)
    timer = null
  }
}

watch(
  message,
  (val) => {
    clearTimer()
    if (val && props.duration > 0) {
      timer = setTimeout(() => {
        message.value = null
      }, props.duration)
    }
  },
  { immediate: true },
)

onUnmounted(clearTimer)
</script>

<template>
  <UAlert
    v-if="message"
    :title="message"
    :icon="config.icon"
    :color="config.color"
    variant="soft"
    class="p-3.5 sm:p-4"
    :ui="{
      icon: 'size-4.5',
    }"
  />
</template>
