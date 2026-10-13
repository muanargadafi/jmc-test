<script setup>
defineProps({
  variant: {
    type: String,
    default: 'light',
  },
  showSubtitle: {
    type: Boolean,
    default: false,
  },
})
</script>

<template>
  <div class="flex items-center gap-3">
    <!-- JMC Wave Icon -->
    <div class="relative shrink-0 flex items-center justify-center">
      <img
        src="~/assets/images/logo/logo_jmc_black.png"
        alt="Warehouse Robotics"
        class="h-7.5 w-auto object-cover object-center"
      />
    </div>

    <!-- Brand Text -->
    <div class="flex flex-col select-none">
      <span
        class="font-bold tracking-tight leading-tight text-sm md:text-base"
        :class="variant === 'sidebar' ? 'text-white' : 'text-[#2b6cb0]'"
      >
        Aplikasi Pengelolaan Barang
      </span>
      <span v-if="showSubtitle" class="text-[11px] font-medium tracking-normal -mt-0.5">
        PT JMC Indonesia
      </span>
    </div>
  </div>
</template>
