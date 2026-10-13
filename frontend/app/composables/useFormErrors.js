const getBackendError = (err) => {
  const data = err?.data

  if (
    typeof data === 'object' &&
    data !== null &&
    'message' in data &&
    typeof data.message === 'string'
  ) {
    return data
  }

  return undefined
}

export const useFormErrors = () => {
  const generalError = ref(null)
  const fieldErrors = reactive({})

  const reset = () => {
    generalError.value = null
    Object.keys(fieldErrors).forEach((key) => Reflect.deleteProperty(fieldErrors, key))
  }

  const handleError = (err) => {
    reset()

    const backend = getBackendError(err)
    const status = getErrorStatus(err)

    if (status === 422 && backend?.errors?.length) {
      generalError.value = backend.message
      backend.errors.forEach((item) => {
        fieldErrors[item.field] = item.message
      })
      return
    }

    generalError.value = backend?.message ?? 'Something went wrong. Please try again.'
  }

  return {
    generalError,
    fieldErrors,
    reset,
    handleError,
  }
}
