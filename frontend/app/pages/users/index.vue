<script setup>
import { createUserSchema, updateUserSchema } from '~/utils/schemas/user.schema'

definePageMeta({
  layout: 'default',
  title: 'User',
  middleware: 'auth',
})

const items = [{ label: 'HOME', to: '/' }, { label: 'MANAJEMEN USER' }]

const { getUsers, createUser, updateUser, deleteUser, toggleLockUser } = useUsers()

// --- roles untuk dropdown ---
const roleList = [
  { id: 1, code: 'admin', nama: 'Admin' },
  { id: 2, code: 'operator', nama: 'Operator' },
]

const roleOptions = [
  { label: 'Semua Role', value: null },
  ...roleList.map((r) => ({ label: r.nama, value: r.id })),
]

const formRoleOptions = roleList.map((r) => ({ label: r.nama, value: r.id }))

// --- getUsers ---
const { users, meta, loading, refresh, page, limit, search, roleId } = getUsers()

// fix: null supaya tidak tampil 0 di USelect filter
const filterRoleId = computed({
  get: () => roleId.value || null,
  set: (val) => {
    roleId.value = val || null
  },
})

// --- columns ---
const pageSize = limit.value

const columns = [
  {
    id: 'no',
    header: 'No',
    cell: ({ row }) => {
      const idx = users.value.findIndex((r) => r.id === row.original.id)
      return (page.value - 1) * pageSize + idx + 1
    },
  },
  { id: 'action', header: 'Action' },
  { accessorKey: 'username', header: 'Username' },
  { accessorKey: 'name', header: 'Nama' },
  { accessorKey: 'email', header: 'Email' },
  {
    id: 'role',
    header: 'Role',
    cell: ({ row }) => row.original.role?.name ?? '-',
  },
]

// --- alert manager ---
const manager = reactive({
  errorMessage: '',
  successMessage: '',
})

// --- modal & form ---
const isOpen = ref(false)
const isEdit = ref(false)
const editId = ref(null)
const formErrors = ref({})
const submitLoading = ref(false)

const form = ref({
  role_id: null,
  username: '',
  password: '',
  nama: '',
  email: '',
})

function openCreate() {
  isEdit.value = false
  editId.value = null
  formErrors.value = {}
  manager.errorMessage = ''
  form.value = { role_id: null, username: '', password: '', nama: '', email: '' }
  isOpen.value = true
}

function openEdit(row) {
  isEdit.value = true
  editId.value = row.id
  formErrors.value = {}
  manager.errorMessage = ''
  form.value = {
    role_id: row.role?.id ?? row.role_id ?? null,
    username: row.username,
    password: '',
    nama: row.name,
    email: row.email,
  }
  isOpen.value = true
}

// --- validasi ---
function validate() {
  const schema = isEdit.value ? updateUserSchema : createUserSchema
  const result = schema.safeParse({
    ...form.value,
    role_id: form.value.role_id ? Number(form.value.role_id) : undefined,
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

// --- submit ---
async function handleSubmit() {
  const data = validate()
  if (!data) return

  submitLoading.value = true
  try {
    const payload = {
      role_id: data.role_id,
      username: data.username,
      name: data.nama,
      email: data.email,
      ...(data.password ? { password: data.password } : {}),
    }

    if (isEdit.value) {
      await updateUser(editId.value, payload)
      manager.successMessage = 'User berhasil diperbarui.'
    } else {
      await createUser(payload)
      manager.successMessage = 'User berhasil ditambahkan.'
    }

    isOpen.value = false
    await refresh()
  } catch (e) {
    const serverErrors = e?.data?.errors
    if (serverErrors) {
      const mapped = {}
      for (const [key, messages] of Object.entries(serverErrors)) {
        const formKey = key === 'name' ? 'nama' : key
        mapped[formKey] = Array.isArray(messages) ? messages[0] : messages
      }
      formErrors.value = mapped
      manager.errorMessage = 'Periksa kembali isian form.'
    } else {
      manager.errorMessage = e?.data?.message || 'Terjadi kesalahan, coba lagi.'
    }
  } finally {
    submitLoading.value = false
  }
}

// --- delete ---
const deleteLoading = ref(null)

async function handleDelete(id) {
  deleteLoading.value = id
  try {
    await deleteUser(id)
    manager.successMessage = 'User berhasil dihapus.'
    await refresh()
  } catch (e) {
    manager.errorMessage = e?.data?.message || 'Gagal menghapus user.'
  } finally {
    deleteLoading.value = null
  }
}

// --- toggle lock ---
const lockLoading = ref(null)

async function handleToggleLock(row) {
  lockLoading.value = row.id
  try {
    await toggleLockUser(row.id)
    manager.successMessage = row.is_locked
      ? 'User berhasil dibuka aksesnya.'
      : 'User berhasil dikunci.'
    await refresh()
  } catch (e) {
    manager.errorMessage = e?.data?.message || 'Gagal mengubah status kunci user.'
  } finally {
    lockLoading.value = null
  }
}
</script>

<template>
  <div class="space-y-4">
    <SharedPageHeader title="Manajemen User" :breadcrumb="items" />

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

      <div class="flex items-center gap-2">
        <USelect v-model="filterRoleId" :items="roleOptions" class="w-40" />
        <UInput v-model="search" placeholder="Cari data..." icon="i-lucide-search" class="w-52" />
      </div>
    </div>

    <!-- Table -->
    <UCard :ui="{ body: 'p-0' }">
      <UTable :data="users" :columns="columns" :loading="loading">
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
              :icon="row.original.is_locked ? 'i-lucide-lock' : 'i-lucide-lock-open'"
              :color="row.original.is_locked ? 'warning' : 'neutral'"
              variant="ghost"
              size="xs"
              :loading="lockLoading === row.original.id"
              @click="handleToggleLock(row.original)"
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
    <UModal v-model:open="isOpen" :title="isEdit ? 'Edit User' : 'Form'">
      <template #body>
        <div class="space-y-4">
          <UFormField label="Role" required :error="formErrors.role_id">
            <USelect
              v-model="form.role_id"
              :items="formRoleOptions"
              placeholder="Pilih Role"
              class="w-full"
            />
          </UFormField>

          <UFormField label="Username" required :error="formErrors.username">
            <UInput v-model="form.username" placeholder="Username" class="w-full" />
          </UFormField>

          <UFormField
            :label="isEdit ? 'Password (kosongkan jika tidak diubah)' : 'Password'"
            :required="!isEdit"
            :error="formErrors.password"
          >
            <UInput v-model="form.password" type="password" placeholder="Password" class="w-full" />
          </UFormField>

          <UFormField label="Nama" required :error="formErrors.nama">
            <UInput v-model="form.nama" placeholder="Nama lengkap" class="w-full" />
          </UFormField>

          <UFormField label="Email" required :error="formErrors.email">
            <UInput v-model="form.email" type="email" placeholder="Email" class="w-full" />
          </UFormField>
        </div>
      </template>

      <template #footer>
        <div class="flex justify-end gap-2">
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
            @click="handleSubmit"
          />
        </div>
      </template>
    </UModal>
  </div>
</template>
