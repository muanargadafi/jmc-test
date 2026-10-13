export const useGoBack = () => {
  const router = useRouter()

  return (fallback = '/') => {
    if (import.meta.client && window.history.state?.back) {
      router.back()
    } else {
      navigateTo(fallback)
    }
  }
}
