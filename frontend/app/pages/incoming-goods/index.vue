<script setup>
import { h } from 'vue'

definePageMeta({
  layout: 'default',
  title: 'Barang Masuk',
  middleware: 'auth',
})

const items = [{ label: 'HOME', to: '/' }, { label: 'BARANG MASUK' }]

const { getIncomingGoods, deleteIncomingGood, verifyIncomingGood } = useIncomingGoods()
const { getCategoryOptions } = useCategories()
const { getSubCategoryOptions } = useSubCategories()

const {
  incomingGoods,
  meta,
  loading,
  refresh,
  page,
  limit,
  search,
  categoryId,
  subCategoryId,
  year,
} = getIncomingGoods()

// category options for filter
const { data: catData } = getCategoryOptions()
const categoryOptions = computed(() => [
  { label: 'All Categories', value: null },
  ...(catData.value?.data ?? []).map((c) => ({ label: c.name, value: c.id })),
])

const filterCategoryId = computed({
  get: () => categoryId.value || null,
  set: (val) => {
    categoryId.value = val || null
  },
})

// sub category options for filter
const { data: subCatData } = getSubCategoryOptions()
const subCategoryOptions = computed(() => [
  { label: 'All Sub Categories', value: null },
  ...(subCatData.value?.data ?? []).map((s) => ({ label: s.name, value: s.id })),
])

const filterSubCategoryId = computed({
  get: () => subCategoryId.value || null,
  set: (val) => {
    subCategoryId.value = val || null
  },
})

// year options for filter
const currentYear = new Date().getFullYear()
const yearOptions = computed(() => [
  { label: 'All Years', value: null },
  ...Array.from({ length: 6 }, (_, i) => {
    const y = currentYear - i
    return { label: String(y), value: y }
  }),
])

const filterYear = computed({
  get: () => year.value || null,
  set: (val) => {
    year.value = val || null
  },
})

const pageSize = limit.value

const formatRupiah = (val) => new Intl.NumberFormat('id-ID', { style: 'decimal' }).format(val ?? 0)

const formatQuantity = (val) => {
  const num = Number(val ?? 0)
  return num % 1 === 0 ? num.toString() : num.toString().replace(/\.?0+$/, '')
}

const formatDate = (val) => {
  if (!val) return '-'
  return new Date(val).toLocaleDateString('en-US', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

// renders one stacked line per item in a cell
const renderItemLines = (rowItems, formatter) => {
  const list = rowItems ?? []
  if (!list.length) return '-'
  return h(
    'div',
    { class: 'flex flex-col gap-1' },
    list.map((item, i) => h('span', { key: item.id ?? i }, formatter(item))),
  )
}

const columns = [
  {
    id: 'no',
    header: 'NO',
    cell: ({ row }) => {
      const idx = incomingGoods.value.findIndex((r) => r.id === row.original.id)
      return (page.value - 1) * pageSize + idx + 1
    },
  },
  { id: 'action', header: 'ACTION' },
  {
    id: 'received_date',
    header: 'TANGGAL',
    cell: ({ row }) => formatDate(row.original.received_date),
  },
  {
    id: 'origin',
    header: 'ASAL BARANG',
    cell: ({ row }) => row.original.origin,
  },

  {
    id: 'operator',
    header: 'PENERIMA',
    cell: ({ row }) => row.original.operator?.name ?? '-',
  },
  {
    id: 'sub_category',
    header: 'UNIT',
    cell: ({ row }) => row.original.sub_category?.name ?? '-',
  },
  {
    id: 'kode',
    header: 'KODE',
    cell: ({ row }) => renderItemLines(row.original.items, (item) => item.id),
  },
  {
    id: 'nama',
    header: 'NAMA',
    cell: ({ row }) => renderItemLines(row.original.items, (item) => item.name),
  },
  {
    id: 'harga',
    header: 'HARGA (RP.)',
    cell: ({ row }) => renderItemLines(row.original.items, (item) => formatRupiah(item.price)),
  },
  {
    id: 'jumlah',
    header: 'JUMLAH',
    cell: ({ row }) =>
      renderItemLines(
        row.original.items,
        (item) => `${formatQuantity(item.quantity)} ${item.unit}`,
      ),
  },
  {
    id: 'total_price',
    header: 'TOTAL (RP.)',
    cell: ({ row }) => formatRupiah(row.original.total_price),
  },
  {
    id: 'status',
    header: 'STATUS',
  },
]

// --- alert ---
const manager = reactive({ errorMessage: '', successMessage: '' })

// --- delete ---
const deleteLoading = ref(null)

async function handleDelete(id) {
  deleteLoading.value = id
  try {
    await deleteIncomingGood(id)
    manager.successMessage = 'Incoming good deleted successfully.'
    await refresh()
  } catch (e) {
    manager.errorMessage = e?.data?.message || 'Failed to delete incoming good.'
  } finally {
    deleteLoading.value = null
  }
}

// --- verify ---
const verifyLoading = ref(null)

async function handleVerify(id) {
  verifyLoading.value = id
  try {
    await verifyIncomingGood(id)
    manager.successMessage = 'Incoming good verified successfully.'
    await refresh()
  } catch (e) {
    manager.errorMessage = e?.data?.message || 'Failed to verify incoming good.'
  } finally {
    verifyLoading.value = null
  }
}
</script>

<template>
  <div class="space-y-4">
    <SharedPageHeader title="Barang Masuk" :breadcrumb="items" />

    <SharedAlert v-model:message="manager.errorMessage" type="error" />
    <SharedAlert v-model:message="manager.successMessage" type="success" />

    <!-- Toolbar -->
    <div class="flex items-center justify-between gap-3 flex-wrap">
      <UButton
        type="button"
        color="neutral"
        variant="solid"
        class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#38789a] hover:bg-[#2c6582] text-white text-xs font-semibold rounded transition-colors shadow-xs"
        :to="'/incoming-goods/create'"
      >
        <template #leading>
          <UIcon name="i-lucide-plus" class="w-3.5 h-3.5 stroke-[2.5]" />
        </template>
        <span>Tambah Data</span>
      </UButton>

      <div class="flex items-center gap-2 flex-wrap">
        <USelect v-model="filterCategoryId" :items="categoryOptions" class="w-44" />
        <USelect v-model="filterSubCategoryId" :items="subCategoryOptions" class="w-48" />
        <USelect v-model="filterYear" :items="yearOptions" class="w-32" placeholder="Tahun" />
        <UInput v-model="search" placeholder="Cari data..." icon="i-lucide-search" class="w-52" />
      </div>
    </div>

    <!-- Table -->
    <UCard :ui="{ body: 'p-0' }">
      <UTable :data="incomingGoods" :columns="columns" :loading="loading">
        <template #action-cell="{ row }">
          <div class="flex items-center gap-1">
            <UButton
              icon="i-lucide-pencil"
              color="neutral"
              variant="ghost"
              size="xs"
              :to="`/incoming-goods/${row.original.id}/edit`"
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

        <template #status-cell="{ row }">
          <div class="flex items-center gap-2">
            <template v-if="row.original.is_verified">
              <UIcon name="i-lucide-circle-check" class="text-green-500 w-4 h-4" />
            </template>
            <template v-else>
              <UButton
                icon="i-lucide-circle-minus"
                color="neutral"
                variant="ghost"
                size="xs"
                :loading="verifyLoading === row.original.id"
                @click="handleVerify(row.original.id)"
              />
            </template>
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
  </div>
</template>
