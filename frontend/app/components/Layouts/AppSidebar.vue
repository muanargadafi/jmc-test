<script setup>
defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  isCollapsed: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['close'])

const route = useRoute()
const { user } = useAuth()

// --- abilities ---
const abilities = computed(() => user.value?.abilities ?? [])

function hasAbility(ability) {
  return abilities.value.includes(ability)
}

const canViewBarangMasuk = computed(() => hasAbility('barang-masuk.manage'))
const canViewMasterData = computed(() => hasAbility('master-data.manage'))
const canViewUsers = computed(() => hasAbility('users.manage'))

// Master data submenu open/close
const isMasterDataOpen = ref(true)

function toggleMasterData() {
  isMasterDataOpen.value = !isMasterDataOpen.value
}

const isBarangMasukActive = computed(() => {
  return route.path === '/incoming-goods' || route.path.startsWith('/incoming-goods/')
})

const isCategoryActive = computed(() => {
  return route.path === '/master-data/categories'
})

const isSubCategoryActive = computed(() => {
  return route.path === '/master-data/sub-categories'
})

const isMasterDataActive = computed(() => {
  return isCategoryActive.value || isSubCategoryActive.value
})

const isManajemenUserActive = computed(() => {
  return route.path === '/users'
})
</script>

<template>
  <aside
    class="fixed inset-y-0 left-0 z-40 w-64 bg-[#232d3d] text-slate-300 flex flex-col transition-transform duration-200 ease-in-out"
    :class="[
      isOpen ? 'translate-x-0' : '-translate-x-full',
      isCollapsed ? 'lg:-translate-x-full' : 'lg:translate-x-0',
    ]"
  >
    <!-- Brand / Logo Header -->
    <div class="h-16 flex items-center px-5 border-b border-[#2d3a4d]/60 shrink-0">
      <NuxtLink to="/incoming-goods" class="flex items-center gap-2.5">
        <LayoutsAppLogo variant="sidebar" />
      </NuxtLink>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 py-4 px-3 space-y-1 overflow-y-auto">
      <!-- Barang Masuk -->
      <NuxtLink
        v-if="canViewBarangMasuk"
        to="/incoming-goods"
        class="flex items-center gap-3 px-3.5 py-2.5 rounded text-xs font-medium transition-colors"
        :class="
          isBarangMasukActive
            ? 'bg-[#38789a] text-white shadow-xs'
            : 'text-slate-300 hover:bg-[#2c3749] hover:text-white'
        "
        @click="emit('close')"
      >
        <UIcon name="i-lucide-download" class="w-4 h-4 shrink-0" />
        <span class="tracking-wide">Barang Masuk</span>
      </NuxtLink>

      <!-- Master Data with Submenu -->
      <div v-if="canViewMasterData">
        <button
          type="button"
          class="w-full flex items-center justify-between px-3.5 py-2.5 rounded text-xs font-medium transition-colors"
          :class="
            isMasterDataActive && !isBarangMasukActive
              ? 'bg-[#38789a] text-white'
              : 'text-slate-300 hover:bg-[#2c3749] hover:text-white'
          "
          @click="toggleMasterData"
        >
          <div class="flex items-center gap-3">
            <UIcon name="i-lucide-bookmark" class="w-4 h-4 shrink-0" />
            <span class="tracking-wide">Master Data</span>
          </div>
          <UIcon
            name="i-lucide-chevron-down"
            class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400"
            :class="{ 'rotate-180': !isMasterDataOpen }"
          />
        </button>

        <!-- Submenu with connecting lines matching screenshot -->
        <div
          v-show="isMasterDataOpen"
          class="mt-1 ml-4 pl-3 border-l border-slate-700/60 space-y-1"
        >
          <!-- Category -->
          <NuxtLink
            to="/master-data/categories"
            class="flex items-center gap-2 px-3 py-2 rounded text-xs transition-colors"
            :class="
              isCategoryActive ? 'text-white font-semibold' : 'text-slate-400 hover:text-white'
            "
            @click="emit('close')"
          >
            <span class="w-2 h-px bg-slate-600 -ml-3" />
            <span>Kategori</span>
          </NuxtLink>

          <!-- Sub Category -->
          <NuxtLink
            to="/master-data/sub-categories"
            class="flex items-center gap-2 px-3 py-2 rounded text-xs transition-colors"
            :class="
              isSubCategoryActive ? 'text-white font-semibold' : 'text-slate-400 hover:text-white'
            "
            @click="emit('close')"
          >
            <span class="w-2 h-px bg-slate-600 -ml-3" />
            <span>Sub Kategori</span>
          </NuxtLink>
        </div>
      </div>

      <!-- Manajemen User -->
      <NuxtLink
        v-if="canViewUsers"
        to="/users"
        class="flex items-center gap-3 px-3.5 py-2.5 rounded text-xs font-medium transition-colors"
        :class="
          isManajemenUserActive
            ? 'bg-[#38789a] text-white shadow-xs'
            : 'text-slate-300 hover:bg-[#2c3749] hover:text-white'
        "
        @click="emit('close')"
      >
        <UIcon name="i-lucide-users" class="w-4 h-4 shrink-0" />
        <span class="tracking-wide">Manajemen User</span>
      </NuxtLink>
    </nav>
  </aside>
</template>
