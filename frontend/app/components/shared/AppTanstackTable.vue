<script setup>
import {
  useVueTable,
  getCoreRowModel,
  getPaginationRowModel,
  getFilteredRowModel,
  getSortedRowModel,
  FlexRender,
} from '@tanstack/vue-table'

const props = defineProps({
  data: {
    type: Array,
    required: true,
  },
  columns: {
    type: Array,
    required: true,
  },
  globalFilter: {
    type: String,
    default: '',
  },
  pageSize: {
    type: Number,
    default: 10,
  },
})

const filter = computed(() => props.globalFilter)

const table = useVueTable({
  get data() {
    return props.data
  },
  get columns() {
    return props.columns
  },
  state: {
    get globalFilter() {
      return filter.value
    },
  },
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getSortedRowModel: getSortedRowModel(),
  initialState: {
    pagination: {
      pageSize: props.pageSize,
    },
  },
})

const pageIndex = computed(() => table.getState().pagination.pageIndex)
const pageCount = computed(() => table.getPageCount())

const pages = computed(() => {
  const total = Math.max(pageCount.value, 5) // match the 1, 2, 3, 4, 5 in screenshots
  const list = []
  for (let i = 0; i < Math.min(total, 5); i++) {
    list.push(i)
  }
  return list
})
</script>

<template>
  <div class="w-full overflow-hidden bg-white">
    <div class="overflow-x-auto w-full">
      <table class="w-full border-collapse text-left">
        <thead>
          <tr
            v-for="headerGroup in table.getHeaderGroups()"
            :key="headerGroup.id"
            class="border-b border-gray-200 bg-[#fbfcfd]"
          >
            <th
              v-for="header in headerGroup.headers"
              :key="header.id"
              class="py-3 px-4 text-[11px] font-semibold tracking-wider text-gray-500 uppercase whitespace-nowrap"
            >
              <FlexRender
                v-if="!header.isPlaceholder"
                :render="header.column.columnDef.header"
                :props="header.getContext()"
              />
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr
            v-for="row in table.getRowModel().rows"
            :key="row.id"
            class="hover:bg-gray-50/60 transition-colors"
          >
            <td
              v-for="cell in row.getVisibleCells()"
              :key="cell.id"
              class="py-3.5 px-4 text-xs text-gray-700 whitespace-nowrap align-middle"
            >
              <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
            </td>
          </tr>
          <tr v-if="table.getRowModel().rows.length === 0">
            <td :colspan="columns.length" class="py-8 text-center text-sm text-gray-400">
              Tidak ada data yang ditemukan.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Custom Pagination matching screenshot: 1 [2] 3 4 5 next > -->
    <div class="flex items-center justify-end gap-1.5 px-4 py-4 border-t border-gray-100 text-xs">
      <button
        v-for="p in pages"
        :key="p"
        type="button"
        class="w-6 h-6 flex items-center justify-center rounded text-xs transition-colors font-medium"
        :class="
          pageIndex === p
            ? 'bg-[#38789a] text-white shadow-xs'
            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
        "
        @click="table.setPageIndex(p)"
      >
        {{ p + 1 }}
      </button>

      <button
        type="button"
        class="px-2 py-1 text-gray-500 hover:text-gray-800 text-xs font-medium flex items-center gap-1 transition-colors ml-1"
        :disabled="!table.getCanNextPage()"
        @click="table.nextPage()"
      >
        <span>next</span>
        <UIcon name="i-lucide-chevron-right" class="w-3.5 h-3.5" />
      </button>
    </div>
  </div>
</template>
