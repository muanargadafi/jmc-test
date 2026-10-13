<script setup lang="ts">
const open = defineModel('open', { default: false })

const props = defineProps({
  name: {
    type: String,
    required: false,
  },
  onConfirm: {
    type: Function,
    required: true,
  },
})

const loading = ref(false)

const onConfirm = async () => {
  loading.value = true

  try {
    await props.onConfirm()
  } finally {
    loading.value = false
    open.value = false
  }
}
</script>

<template>
  <UModal
    v-model:open="open"
    :title="`Delete ${name || 'item'}`"
    :description="`Are you sure, this action cannot be undone.`"
  >
    <slot />

    <template #body>
      <div class="flex justify-end gap-2">
        <UButton
          label="Cancel"
          color="neutral"
          variant="subtle"
          :disabled="loading"
          @click="open = false"
        />
        <UButton
          label="Delete"
          color="error"
          variant="solid"
          :loading="loading"
          @click="onConfirm"
        />
      </div>
    </template>
  </UModal>
</template>
