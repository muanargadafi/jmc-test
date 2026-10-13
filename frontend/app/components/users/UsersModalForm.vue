<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  editData: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close', 'submit'])

const role = ref('Admin')
const username = ref('')
const password = ref('')
const name = ref('')
const email = ref('')

watch(
  () => props.editData,
  (newVal) => {
    if (newVal) {
      role.value = newVal.role
      username.value = newVal.username
      password.value = ''
      name.value = newVal.name
      email.value = newVal.email
    } else {
      role.value = 'Admin'
      username.value = ''
      password.value = ''
      name.value = ''
      email.value = ''
    }
  },
  { immediate: true },
)

function handleSubmit() {
  if (!username.value.trim() || !name.value.trim() || !email.value.trim()) return

  emit('submit', {
    role: role.value,
    username: username.value.trim(),
    password: password.value,
    name: name.value.trim(),
    email: email.value.trim(),
  })
  emit('close')
}
</script>

<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 animate-in fade-in duration-150"
    @click.self="emit('close')"
  >
    <div
      class="bg-white rounded-md shadow-2xl border border-gray-100 w-full max-w-md overflow-hidden animate-in zoom-in-95 duration-150"
    >
      <!-- Modal Header -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-bold text-gray-800">Form</h3>
        <button
          type="button"
          class="text-gray-400 hover:text-gray-600 transition-colors p-1"
          @click="emit('close')"
        >
          <UIcon name="i-lucide-x" class="w-4 h-4" />
        </button>
      </div>

      <!-- Modal Body -->
      <form class="p-6 space-y-3.5" @submit.prevent="handleSubmit">
        <!-- Role -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5"> Role </label>
          <div class="relative">
            <select
              v-model="role"
              class="w-full appearance-none px-3 py-1.5 pr-8 text-xs bg-white border border-gray-200 rounded focus:outline-none focus:border-[#38789a] focus:ring-1 focus:ring-[#38789a] transition-all text-gray-700"
              required
            >
              <option value="Admin">Admin</option>
              <option value="Operator">Operator</option>
            </select>
            <UIcon
              name="i-lucide-chevron-down"
              class="w-3.5 h-3.5 absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
            />
          </div>
        </div>

        <!-- Username -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5"> Username </label>
          <input
            v-model="username"
            type="text"
            class="w-full px-3 py-1.5 text-xs bg-white border border-gray-200 rounded focus:outline-none focus:border-[#38789a] focus:ring-1 focus:ring-[#38789a] transition-all"
            placeholder=""
            required
          />
        </div>

        <!-- Password -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5"> Password </label>
          <input
            v-model="password"
            type="password"
            class="w-full px-3 py-1.5 text-xs bg-white border border-gray-200 rounded focus:outline-none focus:border-[#38789a] focus:ring-1 focus:ring-[#38789a] transition-all"
            placeholder=""
            :required="!editData"
          />
        </div>

        <!-- Nama -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5"> Nama </label>
          <input
            v-model="name"
            type="text"
            class="w-full px-3 py-1.5 text-xs bg-white border border-gray-200 rounded focus:outline-none focus:border-[#38789a] focus:ring-1 focus:ring-[#38789a] transition-all"
            placeholder=""
            required
          />
        </div>

        <!-- Email -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1.5"> Email </label>
          <input
            v-model="email"
            type="email"
            class="w-full px-3 py-1.5 text-xs bg-white border border-gray-200 rounded focus:outline-none focus:border-[#38789a] focus:ring-1 focus:ring-[#38789a] transition-all"
            placeholder=""
            required
          />
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100">
          <button
            type="button"
            class="px-4 py-1.5 bg-[#4b5563] hover:bg-[#374151] text-white text-xs font-medium rounded transition-colors shadow-xs"
            @click="emit('close')"
          >
            Close
          </button>
          <button
            type="submit"
            class="px-4 py-1.5 bg-[#38789a] hover:bg-[#2c6582] text-white text-xs font-medium rounded transition-colors shadow-xs"
          >
            Submit
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
