<script setup>
const isSidebarOpen = ref(false)

function toggleSidebar() {
  isSidebarOpen.value = !isSidebarOpen.value
}

function closeSidebar() {
  isSidebarOpen.value = false
}
</script>

<template>
  <div class="min-h-screen bg-[#f4f6fa] flex">
    <!-- Sidebar -->
    <LayoutsAppSidebar :is-open="isSidebarOpen" @close="closeSidebar" />

    <!-- Mobile Backdrop -->
    <div
      v-if="isSidebarOpen"
      class="fixed inset-0 z-30 bg-black/40 lg:hidden"
      @click="closeSidebar"
    />

    <!-- Main Content Container -->
    <div class="flex-1 flex flex-col min-w-0 lg:pl-64">
      <!-- Navbar / Header -->
      <LayoutsAppHeader @toggle-sidebar="toggleSidebar" />

      <!-- Page Content -->
      <main class="flex-1 p-4 lg:p-8">
        <slot />
      </main>
    </div>
  </div>
</template>
