<script setup>
const isSidebarOpen = ref(false)
const isDesktopCollapsed = ref(false)

function toggleSidebar() {
  if (window.matchMedia('(min-width: 1024px)').matches) {
    isDesktopCollapsed.value = !isDesktopCollapsed.value
  } else {
    isSidebarOpen.value = !isSidebarOpen.value
  }
}

function closeSidebar() {
  isSidebarOpen.value = false
}
</script>

<template>
  <div class="min-h-screen bg-[#f4f6fa] flex">
    <!-- Sidebar -->
    <LayoutsAppSidebar
      :is-open="isSidebarOpen"
      :is-collapsed="isDesktopCollapsed"
      @close="closeSidebar"
    />

    <!-- Mobile Backdrop -->
    <div
      v-if="isSidebarOpen"
      class="fixed inset-0 z-35 bg-black/40 lg:hidden"
      @click="closeSidebar"
    />

    <!-- Main Content Container -->
    <div
      class="flex-1 flex flex-col min-w-0 transition-[padding] duration-200"
      :class="isDesktopCollapsed ? 'lg:pl-0' : 'lg:pl-64'"
    >
      <!-- Navbar / Header -->
      <LayoutsAppHeader @toggle-sidebar="toggleSidebar" />

      <!-- Page Content -->
      <main class="flex-1 p-4 lg:p-8">
        <slot />
      </main>
    </div>
  </div>
</template>
