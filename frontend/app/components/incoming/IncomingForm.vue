<script setup>
const props = defineProps({
  initialData: {
    type: Object,
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['submit', 'back'])

const { getSubCategoryOptions } = useSubCategories()
const { getUsers } = useUsers()

// operator options — ambil user dengan role operator
const { users: operatorList } = getUsers({ limit: 100 })
const operatorOptions = computed(() =>
  operatorList.value.map((u) => ({ label: u.name, value: u.id })),
)

// sub category options
const { data: subCatData } = getSubCategoryOptions()
const subCategoryOptions = computed(() =>
  (subCatData.value?.data ?? []).map((s) => ({ label: s.name, value: s.id })),
)

// batas harga dari sub kategori yang dipilih
const selectedSubCategory = computed(() =>
  (subCatData.value?.data ?? []).find((s) => s.id === form.value.sub_category_id),
)

const formatRupiah = (val) =>
  val !== undefined && val !== null ? new Intl.NumberFormat('id-ID').format(val) : '-'

// --- form state ---
const form = ref({
  operator_id: null,
  sub_category_id: null,
  received_date: '',
  origin: '',
  letter_number: '',
  attachment: null,
  remove_attachment: false,
  items: [{ name: '', price: 0, quantity: 1, unit: 'Buah', expired_date: '' }],
})

// existing attachment (edit mode)
const existingAttachment = ref(null)

// populate form dari initialData (mode edit)
watch(
  () => props.initialData,
  (data) => {
    if (!data) return
    form.value = {
      operator_id: data.operator?.id ?? null,
      sub_category_id: data.sub_category?.id ?? null,
      received_date: data.received_date ?? '',
      origin: data.origin ?? '',
      letter_number: data.letter_number ?? '',
      attachment: null,
      remove_attachment: false,
      items: data.items?.length
        ? data.items.map((i) => ({
            name: i.name,
            price: i.price,
            quantity: i.quantity,
            unit: i.unit,
            expired_date: i.expired_date ?? '',
          }))
        : [{ name: '', price: 0, quantity: 1, unit: 'Buah', expired_date: '' }],
    }
    existingAttachment.value = data.attachment_name
      ? { name: data.attachment_name, url: data.attachment_url }
      : null
  },
  { immediate: true },
)

// --- items ---
function addItem() {
  form.value.items.push({ name: '', price: 0, quantity: 1, unit: 'Buah', expired_date: '' })
}

function removeItem(index) {
  if (form.value.items.length <= 1) return
  form.value.items.splice(index, 1)
}

const itemTotal = (item) => {
  const price = Number(item.price) || 0
  const qty = Number(item.quantity) || 0
  return price * qty
}

const grandTotal = computed(() => form.value.items.reduce((sum, item) => sum + itemTotal(item), 0))

// --- attachment ---
function onFileChange(e) {
  form.value.attachment = e.target.files[0] ?? null
}

function removeAttachment() {
  form.value.remove_attachment = true
  form.value.attachment = null
  existingAttachment.value = null
}

// --- submit ---
function handleSubmit() {
  const payload = {
    ...form.value,
    operator_id: form.value.operator_id ? Number(form.value.operator_id) : null,
    sub_category_id: form.value.sub_category_id ? Number(form.value.sub_category_id) : null,
    items: form.value.items.map((item) => ({
      ...item,
      price: Number(item.price),
      quantity: Number(item.quantity),
      expired_date: item.expired_date || null,
    })),
  }
  emit('submit', payload)
}
</script>

<template>
  <UCard>
    <div class="space-y-6">
      <!-- INFORMASI UMUM -->
      <div>
        <h2 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wide">Informasi Umum</h2>

        <div class="grid grid-cols-1 gap-4">
          <!-- Operator -->
          <UFormField label="Operator" required :error="errors.operator_id">
            <USelect
              v-model="form.operator_id"
              :items="operatorOptions"
              placeholder="Pilih Operator"
              class="w-full"
            />
          </UFormField>

          <!-- Sub Kategori + Batas Harga -->
          <div class="grid grid-cols-2 gap-4">
            <UFormField label="Sub Kategori" required :error="errors.sub_category_id">
              <USelect
                v-model="form.sub_category_id"
                :items="subCategoryOptions"
                placeholder="Pilih Sub Kategori"
                class="w-full"
              />
            </UFormField>

            <UFormField label="Batas Harga">
              <UInput
                :value="formatRupiah(selectedSubCategory?.price_limit)"
                disabled
                class="w-full"
              />
            </UFormField>
          </div>

          <!-- Tanggal Terima -->
          <UFormField label="Tanggal Terima" required :error="errors.received_date">
            <UInput v-model="form.received_date" type="date" class="w-full" />
          </UFormField>

          <!-- Asal Barang -->
          <UFormField label="Asal Barang" required :error="errors.origin">
            <UInput v-model="form.origin" placeholder="Asal barang" class="w-full" />
          </UFormField>

          <!-- Nomor Surat + Lampiran -->
          <div class="grid grid-cols-2 gap-4">
            <UFormField label="Nomor Surat" :error="errors.letter_number">
              <UInput v-model="form.letter_number" placeholder="Nomor surat" class="w-full" />
            </UFormField>

            <UFormField label="Lampiran" :error="errors.attachment">
              <div class="space-y-1">
                <!-- existing attachment (edit mode) -->
                <div
                  v-if="existingAttachment && !form.remove_attachment"
                  class="flex items-center gap-2 text-sm"
                >
                  <UIcon name="i-lucide-paperclip" class="w-4 h-4 text-gray-500" />
                  <a
                    :href="existingAttachment.url"
                    target="_blank"
                    class="text-blue-600 hover:underline truncate max-w-[200px]"
                  >
                    {{ existingAttachment.name }}
                  </a>
                  <UButton
                    icon="i-lucide-x"
                    color="error"
                    variant="ghost"
                    size="xs"
                    @click="removeAttachment"
                  />
                </div>
                <input
                  v-if="!existingAttachment || form.remove_attachment"
                  type="file"
                  accept=".pdf,.jpg,.jpeg,.png"
                  class="block w-full text-sm text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200"
                  @change="onFileChange"
                />
              </div>
            </UFormField>
          </div>
        </div>
      </div>

      <USeparator />

      <!-- INFORMASI BARANG -->
      <div>
        <h2 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wide">
          Informasi Barang
        </h2>

        <div v-if="errors.items" class="text-red-500 text-sm mb-2">{{ errors.items }}</div>

        <!-- Item rows -->
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500 text-xs border-b">
                <th class="pb-2 pr-2 font-medium">Nama Barang</th>
                <th class="pb-2 pr-2 font-medium">Harga (Rp.)</th>
                <th class="pb-2 pr-2 font-medium">Jumlah</th>
                <th class="pb-2 pr-2 font-medium">Satuan</th>
                <th class="pb-2 pr-2 font-medium">Total</th>
                <th class="pb-2 pr-2 font-medium">Tgl. Expired</th>
                <th class="pb-2 font-medium"></th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(item, index) in form.items"
                :key="index"
                class="border-b last:border-0 align-top"
              >
                <td class="py-2 pr-2">
                  <UFormField :error="errors[`items.${index}.name`]">
                    <UInput v-model="item.name" placeholder="Nama barang" class="w-36" />
                  </UFormField>
                </td>
                <td class="py-2 pr-2">
                  <UFormField :error="errors[`items.${index}.price`]">
                    <UInput v-model="item.price" type="number" min="0" class="w-28">
                      <template #trailing>
                        <span class="text-xs text-gray-400">Rp.</span>
                      </template>
                    </UInput>
                  </UFormField>
                </td>
                <td class="py-2 pr-2">
                  <UFormField :error="errors[`items.${index}.quantity`]">
                    <UInput
                      v-model="item.quantity"
                      type="number"
                      min="0.01"
                      step="0.01"
                      class="w-20"
                    />
                  </UFormField>
                </td>
                <td class="py-2 pr-2">
                  <UFormField :error="errors[`items.${index}.unit`]">
                    <UInput v-model="item.unit" placeholder="Buah" class="w-20" />
                  </UFormField>
                </td>
                <td class="py-2 pr-2">
                  <UInput :value="formatRupiah(itemTotal(item))" disabled class="w-28">
                    <template #trailing>
                      <span class="text-xs text-gray-400">Rp.</span>
                    </template>
                  </UInput>
                </td>
                <td class="py-2 pr-2">
                  <UFormField :error="errors[`items.${index}.expired_date`]">
                    <UInput v-model="item.expired_date" type="date" class="w-36" />
                  </UFormField>
                </td>
                <td class="py-2">
                  <UButton
                    icon="i-lucide-plus"
                    color="neutral"
                    variant="ghost"
                    size="xs"
                    @click="addItem"
                  />
                  <UButton
                    v-if="form.items.length > 1"
                    icon="i-lucide-trash-2"
                    color="error"
                    variant="ghost"
                    size="xs"
                    @click="removeItem(index)"
                  />
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="4" class="pt-3 text-right text-sm font-semibold text-gray-600 pr-2">
                  Grand Total
                </td>
                <td class="pt-3 pr-2">
                  <UInput :value="formatRupiah(grandTotal)" disabled class="w-28">
                    <template #trailing>
                      <span class="text-xs text-gray-400">Rp.</span>
                    </template>
                  </UInput>
                </td>
                <td colspan="2" />
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <template #footer>
      <div class="flex items-center gap-2">
        <UButton
          label="Kembali"
          color="neutral"
          variant="outline"
          icon="i-lucide-arrow-left"
          @click="emit('back')"
        />
        <UButton
          label="Simpan"
          color="primary"
          :loading="loading"
          class="bg-[#38789a] hover:bg-[#2c6582]"
          @click="handleSubmit"
        />
      </div>
    </template>
  </UCard>
</template>
