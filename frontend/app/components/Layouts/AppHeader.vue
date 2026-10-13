<script setup>
const emit = defineEmits(['toggle-sidebar'])

const { user, logout } = useAuth()
const isMenuOpen = ref(false)

function toggleUserMenu() {
  isMenuOpen.value = !isMenuOpen.value
}

function handleLogout() {
  isMenuOpen.value = false
  logout()
}
</script>

<template>
  <header
    class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-30 shadow-[0_1px_2px_rgba(0,0,0,0.03)]"
  >
    <!-- Left: Hamburger Menu Button -->
    <UButton
      icon="i-lucide-menu"
      color="neutral"
      variant="ghost"
      size="xl"
      class="text-[#38789a] hover:text-[#2c6582] p-1.5 rounded-md hover:bg-gray-50 transition-colors focus:outline-none"
      @click="emit('toggle-sidebar')"
    />

    <!-- Right: Notification Bell & User Profile -->
    <div class="flex items-center gap-5">
      <!-- Notification Bell -->
      <div class="relative cursor-pointer text-gray-500 hover:text-gray-700 transition-colors">
        <UIcon name="i-lucide-bell" class="w-5 h-5" />
        <!-- Red dot indicator -->
        <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white" />
      </div>

      <!-- User Profile Dropdown Trigger -->
      <div class="relative">
        <UButton
          type="button"
          color="neutral"
          variant="ghost"
          class="flex items-center gap-3 p-1 rounded-md transition-colors focus:outline-none"
          :ui="{
            // Menghilangkan atau menyamakan background saat state active/hover/focus
            base: 'hover:bg-transparent active:bg-transparent focus:bg-transparent',
          }"
          @click="toggleUserMenu"
        >
          <!-- Avatar Circle -->
          <UAvatar
            :alt="user?.user?.name"
            size="md"
            class="bg-[#38789a] text-white font-bold uppercase tracking-tight shadow-xs"
            :ui="{
              fallback: 'text-white',
            }"
          />

          <!-- User Name and Role -->
          <div class="hidden sm:flex flex-col text-left leading-tight">
            <span class="text-xs font-bold text-gray-800">{{ user?.user?.name }}</span>
            <span class="text-[11px] text-[#38789a] font-medium uppercase">
              {{ user?.user?.role?.name }}
            </span>
          </div>

          <!-- Chevron Down -->
          <UIcon name="i-lucide-chevron-down" class="w-3.5 h-3.5 text-gray-400" />
        </UButton>

        <!-- Dropdown Menu matching Screenshot 2 -->
        <div
          v-if="isMenuOpen"
          class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg border border-gray-100 py-1 z-50 animate-in fade-in zoom-in-95 duration-100"
        >
          <UButton
            size="md"
            color="error"
            variant="ghost"
            class="w-full px-3 py-2"
            @click="handleLogout"
          >
            <UIcon name="i-lucide-log-out" class="w-4 h-4 text-red-500" />
            <span>Logout</span>
          </UButton>
        </div>
      </div>
    </div>
  </header>
</template>
