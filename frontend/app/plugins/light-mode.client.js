export default defineNuxtPlugin(() => {
  document.documentElement.classList.remove('dark')
  document.documentElement.classList.add('light')
  document.body.classList.remove('dark')
  document.body.classList.add('light')
})
