<script setup>
import {
  createSubCategorySchema,
  updateSubCategorySchema,
} from '~/utils/schemas/subCategory.schema'

definePageMeta({
  layout: 'default',
  title: 'Sub Kategori',
  middleware: 'auth',
})

const items = [{ label: 'HOME', to: '/' }, { label: 'SUB KATEGORI BARANG' }]

const { getSubCategories, createSubCategory, updateSubCategory, deleteSubCategory } =
  useSubCategories()
const { getCategoryOptions } = useCategories()

const { subCategories, meta, loading, refresh, page, limit, search } = getSubCategories()

// category options for dropdown in form
const { data: categoryOptionsData } = getCategoryOptions()
const categoryOptions = computed(() =>
  (categoryOptionsData.value?.data ?? []).map((c) => ({ label: c.name, value: c.id })),
)

const pageSize = limit.value

const formatRupiah = (val) => new Intl.NumberFormat('id-ID', { style: 'decimal' }).format(val)

const columns = [
  {
    id: 'no',
    header: 'NO',
    cell: ({ row }) => {
      const idx = subCategories.value.findIndex((r) => r.id === row.original.id)
      return (page.value - 1) * pageSize + idx + 1
    },
  },
  { id: 'action', header: 'ACTION' },
  {
    id: 'category',
    header: 'KATEGORI BARANG',
    cell: ({ row }) => row.original.category?.name ?? '-',
  },
  { accessorKey: 'name', header: 'SUB KATEGORI BARANG' },
  {
    id: 'price_limit',
    header: 'BATAS HARGA (RP)',
    cell: ({ row }) => formatRupiah(row.original.price_limit),
  },
]

// --- alert ---
const manager = reactive({ errorMessage: '', successMessage: '' })

// --- modal ---
const isOpen = ref(false)
const isEdit = ref(false)
const editId = ref(null)
const formErrors = ref({})
const submitLoading = ref(false)

const form = ref({ category_id: null, name: '', price_limit: '' })

function openCreate() {
  isEdit.value = false
  editId.value = null
  formErrors.value = {}
  manager.errorMessage = ''
  form.value = { category_id: null, name: '', price_limit: '' }
  isOpen.value = true
}

function openEdit(row) {
  isEdit.value = true
  editId.value = row.id
  formErrors.value = {}
  manager.errorMessage = ''
  form.value = {
    category_id: row.category?.id ?? null,
    name: row.name,
    price_limit: row.price_limit,
  }
  isOpen.value = true
}

function validate() {
  const schema = isEdit.value ? updateSubCategorySchema : createSubCategorySchema
  const result = schema.safeParse({
    category_id: form.value.category_id ? Number(form.value.category_id) : undefined,
    name: form.value.name,
    price_limit: form.value.price_limit !== '' ? Number(form.value.price_limit) : undefined,
  })

  if (!result.success) {
    const errors = {}
    for (const issue of result.error.issues) {
      const key = issue.path[0]
      if (key && !errors[key]) errors[key] = issue.message
    }
    formErrors.value = errors
    return false
  }

  formErrors.value = {}
  return result.data
}

async function handleSubmit() {
  const data = validate()
  if (!data) return

  submitLoading.value = true
  try {
    if (isEdit.value) {
      await updateSubCategory(editId.value, data)
      manager.successMessage = 'Sub category updated successfully.'
    } else {
      await createSubCategory(data)
      manager.successMessage = 'Sub category added successfully.'
    }
    isOpen.value = false
    await refresh()
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

const deleteLoading = ref(null)

async function handleDelete(id) {
  deleteLoading.value = id
  try {
    await deleteSubCategory(id)
    manager.successMessage = 'Sub category deleted successfully.'
    await refresh()
  } catch (e) {
    manager.errorMessage = e?.data?.message || 'Failed to delete sub category.'
  } finally {
    deleteLoading.value = null
  }
}
</script>

<template>
  <div class="space-y-4">
    <SharedPageHeader title="Sub Kategori Barang" :breadcrumb="items" />

    <SharedAlert v-model:message="manager.errorMessage" type="error" />
    <SharedAlert v-model:message="manager.successMessage" type="success" />

    <!-- Toolbar -->
    <div class="flex items-center justify-between gap-3 flex-wrap">
      <UButton
        type="button"
        color="neutral"
        variant="solid"
        class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#38789a] hover:bg-[#2c6582] text-white text-xs font-semibold rounded transition-colors shadow-xs"
        @click="openCreate"
      >
        <template #leading>
          <UIcon name="i-lucide-plus" class="w-3.5 h-3.5 stroke-[2.5]" />
        </template>
        <span>Tambah Data</span>
      </UButton>

      <UInput v-model="search" placeholder="Cari data..." icon="i-lucide-search" class="w-52" />
    </div>

    <!-- Table -->
    <UCard :ui="{ body: 'p-0' }">
      <UTable :data="subCategories" :columns="columns" :loading="loading">
        <template #action-cell="{ row }">
          <div class="flex items-center gap-1">
            <UButton
              icon="i-lucide-pencil"
              color="neutral"
              variant="ghost"
              size="xs"
              @click="openEdit(row.original)"
            />
            <UButton
              icon="i-lucide-trash-2"
              color="error"
              variant="ghost"
              size="xs"
              :loading="deleteLoading === row.original.id"
              @click="handleDelete(row.original.id)"
            />
          </div>
        </template>
      </UTable>
    </UCard>

    <!-- Pagination -->
    <div class="flex justify-end">
      <UPagination
        v-model:page="page"
        :total="meta.totalItems"
        :items-per-page="meta.limit"
        :ui="{
          item: 'data-[selected=true]:bg-[#2c6582]! data-[selected=true]:hover:bg-[#2c6582]/90!',
        }"
      />
    </div>

    <!-- Modal -->
    <UModal v-model:open="isOpen" :title="isEdit ? 'Edit Sub Kategori' : 'Tmabah Sub Kategori'">
      <template #body>
        <div class="space-y-4">
          <UFormField label="Kategori" required :error="formErrors.category_id">
            <USelect
              v-model="form.category_id"
              :items="categoryOptions"
              placeholder="Pilih Kategori"
              class="w-full"
            />
          </UFormField>

          <UFormField label="Nama Sub Kategori" required :error="formErrors.name">
            <UInput v-model="form.name" placeholder="Nama sub kategori" class="w-full" />
          </UFormField>

          <UFormField label="Batas Harga" required :error="formErrors.price_limit">
            <UInput
              v-model="form.price_limit"
              type="number"
              min="0"
              placeholder="0"
              class="w-1/2 text-right"
              :ui="{
                base: 'text-right [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none',
              }"
            />
          </UFormField>
        </div>
      </template>

      <template #footer>
        <div class="w-full flex items-center justify-end gap-2">
          <UButton
            label="Close"
            color="neutral"
            variant="outline"
            :disabled="submitLoading"
            @click="isOpen = false"
          />

          <UButton
            label="Submit"
            color="neutral"
            variant="solid"
            class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#38789a] hover:bg-[#2c6582] text-white text-xs font-semibold transition-colors shadow-xs"
            :loading="submitLoading"
            :disabled="submitLoading"
            @click="handleSubmit"
          />
        </div>
      </template>
    </UModal>
  </div>
</template>
