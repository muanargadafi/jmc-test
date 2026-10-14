// Map path prefix -> ability yang wajib dimiliki untuk mengakses path tersebut.
// Diperiksa di setiap navigasi, termasuk saat URL diketik/diakses langsung.
const PROTECTED_ROUTES = [
  { prefix: '/incoming-goods', ability: 'barang-masuk.manage' },
  { prefix: '/master-data', ability: 'master-data.manage' },
  { prefix: '/users', ability: 'users.manage' },
]

export default defineNuxtRouteMiddleware((to) => {
  const { user } = useAuth()

  // belum login: biarkan middleware `auth` yang handle redirect ke halaman login
  if (!user.value) return

  const matched = PROTECTED_ROUTES.find(
    (route) => to.path === route.prefix || to.path.startsWith(`${route.prefix}/`),
  )

  // path ini tidak dibatasi ability apa pun
  if (!matched) return

  const abilities = user.value?.abilities ?? []

  if (!abilities.includes(matched.ability)) {
    return navigateTo('/403')
  }
})
