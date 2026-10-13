<script setup>
import { loginBodySchema } from '~/utils/schemas/auth.schema'

definePageMeta({
  layout: 'auth',
  title: 'Login',
  middleware: 'guest',
})

const { login } = useAuth()
const route = useRoute()
const { generalError: errorMessage, fieldErrors, handleError, reset } = useFormErrors()

const isLoading = ref(false)

const form = reactive({
  email: '',
  password: '',
})

const handleSubmit = async () => {
  reset()
  isLoading.value = true

  try {
    await login(form)
    await navigateTo(route.query.redirect || '/')
  } catch (err) {
    handleError(err)
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <!-- Main Container: Full screen (h-screen & w-screen), tanpa margin/padding luar -->
  <div class="h-screen w-screen grid grid-cols-1 lg:grid-cols-12 bg-white overflow-hidden">
    <!-- Left Column: Form (col-lg-5 di Desktop / 5 dari 12 kolom) -->
    <!-- Di mobile (default) otomatis mengambil 100% lebar (col-span-12) -->
    <div
      class="lg:col-span-5 flex flex-col justify-center items-center px-8 sm:px-12 lg:px-16 xl:px-20 py-12 bg-white"
    >
      <!-- Mengganti col-md-8 dengan w-full md:w-8/12 (setara 8 dari 12 kolom di Tailwind) -->
      <div class="w-full max-w-105 2xl:max-w-105">
        <!-- Logo Header -->
        <div class="mb-10">
          <LayoutsAppLogo show-subtitle />
        </div>

        <!-- Login Heading -->
        <div class="mb-6">
          <h1 class="text-base font-bold text-[#38789a] tracking-wide mb-1.5 uppercase">LOGIN</h1>
          <p class="text-xs text-gray-500 leading-relaxed">
            Selamat Datang, silahkan masukkan username dan password anda!
          </p>
        </div>

        <UForm
          :schema="loginBodySchema"
          :state="form"
          class="space-y-4"
          @submit.prevent="handleSubmit"
        >
          <SharedAlert v-model:message="errorMessage" type="error" />

          <UFormField name="username" :error="fieldErrors?.username">
            <UInput
              v-model="form.username"
              type="text"
              placeholder="Username"
              class="w-full"
              variant="none"
              :ui="{
                base: 'w-full px-4 py-2.5 text-xs bg-[#fbfcfd] border border-gray-200 rounded focus:outline-none focus:border-[#38789a] focus:ring-1 focus:ring-[#38789a] placeholder:text-gray-400 transition-all text-gray-700 shadow-none',
              }"
            />
          </UFormField>

          <UFormField name="password" :error="fieldErrors?.password">
            <UInput
              v-model="form.password"
              type="password"
              placeholder="Password"
              class="w-full"
              variant="none"
              :ui="{
                base: 'w-full px-4 py-2.5 text-xs bg-[#fbfcfd] border border-gray-200 rounded focus:outline-none focus:border-[#38789a] focus:ring-1 focus:ring-[#38789a] placeholder:text-gray-400 transition-all text-gray-700 shadow-none',
              }"
            />
          </UFormField>

          <div class="pt-2">
            <UButton
              type="submit"
              label="MASUK"
              class="w-full py-2.5 px-4 bg-[#38789a] hover:bg-[#2c6582] text-white text-xs font-bold rounded tracking-wider uppercase transition-colors shadow-xs justify-center cursor-pointer"
            />
          </div>
        </UForm>
      </div>
    </div>

    <!-- Right Column: Gambar (col-lg-7 di Desktop / 7 dari 12 kolom) -->
    <!-- `hidden lg:block` memastikan gambar hilang total di tampilan Mobile/Tablet dan baru muncul di Desktop (breakpoint `lg:`) -->
    <div class="hidden lg:block lg:col-span-7 relative bg-slate-100 overflow-hidden h-full">
      <img
        src="~/assets/images/warehouse.webp"
        alt="Warehouse Robotics"
        class="w-full h-full object-cover object-center"
      />
      <div
        class="absolute inset-0 bg-linear-to-t from-black/20 via-transparent to-transparent pointer-events-none"
      />
    </div>
  </div>
</template>
