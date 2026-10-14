<script setup>
import { updateIncomingGoodSchema } from '~/utils/schemas/incomingGood.schema'

definePageMeta({
  layout: 'default',
  title: 'Edit Barang Masuk',
  middleware: 'auth',
})

const breadcrumb = [
  { label: 'HOME', to: '/' },
  { label: 'BARANG MASUK', to: '/incoming-goods' },
  { label: 'EDIT DATA' },
]

const route = useRoute()
const router = useRouter()
const id = route.params.id

const { getIncomingGoodById, updateIncomingGood } = useIncomingGoods()

const { data, pending: fetchLoading } = getIncomingGoodById(id)
const currentData = computed(() => data.value?.data ?? null)

const manager = reactive({ errorMessage: '', successMessage: '' })
const formErrors = ref({})
const submitLoading = ref(false)

async function handleSubmit(payload) {
  const result = updateIncomingGoodSchema.safeParse({
    ...payload,
    operator_id: payload.operator_id ? Number(payload.operator_id) : undefined,
    sub_category_id: payload.sub_category_id ? Number(payload.sub_category_id) : undefined,
    items: payload.items.map((i) => ({
      ...i,
      price: Number(i.price),
      quantity: Number(i.quantity),
    })),
  })

  if (!result.success) {
    const errors = {}
    for (const issue of result.error.issues) {
      const key = issue.path.join('.')
      if (key && !errors[key]) errors[key] = issue.message
    }
    formErrors.value = errors
    manager.errorMessage = 'Please check the form inputs.'
    return
  }

  formErrors.value = {}
  submitLoading.value = true

  try {
    await updateIncomingGood(id, result.data)
    await router.push('/incoming-goods')
  } catch (e) {
    const serverErrors = e?.data?.errors
    if (serverErrors) {
      const mapped = {}
      for (const [key, messages] of Object.entries(serverErrors)) {
        mapped[key] = Array.isArray(messages) ? messages[0] : messages
      }
      formErrors.value = mapped
      manager.errorMessage = 'Please check the form inputs.'
    } else {
      manager.errorMessage = e?.data?.message || 'An error occurred, please try again.'
    }
  } finally {
    submitLoading.value = false
  }
}
</script>

<template>
  <div class="space-y-4">
    <SharedPageHeader title="Edit Barang Masuk" :breadcrumb="breadcrumb" />

    <SharedAlert v-model:message="manager.errorMessage" type="error" />
    <SharedAlert v-model:message="manager.successMessage" type="success" />

    <div v-if="fetchLoading" class="flex justify-center py-10">
      <UIcon name="i-lucide-loader-circle" class="animate-spin w-6 h-6 text-gray-400" />
    </div>

    <IncomingForm
      v-else
      :initial-data="currentData"
      :loading="submitLoading"
      :errors="formErrors"
      @submit="handleSubmit"
      @back="$router.push('/incoming-goods')"
    />
  </div>
</template>
