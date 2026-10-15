<script setup>
import { createCategorySchema, updateCategorySchema } from '~/utils/schemas/category.schema'

definePageMeta({
  layout: 'default',
  title: 'Kategori',
  middleware: 'auth',
})

const items = [{ label: 'HOME', to: '/' }, { label: 'KATEGORI BARANG' }]

const { getCategories, createCategory, updateCategory, deleteCategory } = useCategories()
const { categories, meta, loading, refresh, page, limit, search } = getCategories()

const pageSize = limit.value

const columns = [
  {
    id: 'no',
    header: 'NO',
    cell: ({ row }) => {
      const idx = categories.value.findIndex((r) => r.id === row.original.id)
      return (page.value - 1) * pageSize + idx + 1
    },
  },
  { id: 'action', header: 'ACTION' },
  { accessorKey: 'code', header: 'KODE' },
  { accessorKey: 'name', header: 'NAMA KATEGORI' },
]

// --- alert ---
const manager = reactive({ errorMessage: '', successMessage: '' })

// --- modal ---
const isOpen = ref(false)
const isEdit = ref(false)
const editId = ref(null)
const formErrors = ref({})
const submitLoading = ref(false)

const form = ref({ code: '', name: '' })

function openCreate() {
  isEdit.value = false
  editId.value = null
  formErrors.value = {}
  manager.errorMessage = ''
  form.value = { code: '', name: '' }
  isOpen.value = true
}

function openEdit(row) {
  isEdit.value = true
  editId.value = row.id
  formErrors.value = {}
  manager.errorMessage = ''
  form.value = { code: row.code, name: row.name }
  isOpen.value = true
}

function validate() {
  const schema = isEdit.value ? updateCategorySchema : createCategorySchema
  const result = schema.safeParse(form.value)

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
      await updateCategory(editId.value, data)
      manager.successMessage = 'Category updated successfully.'
    } else {
      await createCategory(data)
      manager.successMessage = 'Category added successfully.'
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
    await deleteCategory(id)
    manager.successMessage = 'Category deleted successfully.'
    await refresh()
  } catch (e) {
    manager.errorMessage = e?.data?.message || 'Failed to delete category.'
  } finally {
    deleteLoading.value = null
  }
}
</script>

<template>
  <div class="space-y-4">
    <SharedPageHeader title="Kategori Barang" :breadcrumb="items" />

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
      <UTable :data="categories" :columns="columns" :loading="loading">
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
    <UModal v-model:open="isOpen" :title="isEdit ? 'Edit Kategori' : 'Tambah Kategori'">
      <template #body>
        <div class="space-y-4">
          <UFormField label="Kode Kategori" required :error="formErrors.code">
            <UInput v-model="form.code" placeholder="Kode kategori" class="w-1/3" />
          </UFormField>

          <UFormField label="Nama Kategori" required :error="formErrors.name">
            <UInput v-model="form.name" placeholder="Nama kategori" class="w-full" />
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
