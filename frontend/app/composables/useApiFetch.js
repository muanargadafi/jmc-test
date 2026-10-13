export function useApiFetch(request, options = {}) {
  const api = useApi()

  const {
    server,
    lazy,
    immediate,
    watch: watchSources,
    deep,
    dedupe,
    getCachedData,
    default: defaultValue,
    transform,
    pick,
    ...fetchOptions
  } = options

  return useAsyncData(
    toValue(request),
    () =>
      api(toValue(request), {
        ...fetchOptions,
        query: toValue(fetchOptions.query),
      }),
    {
      server,
      lazy,
      immediate,
      watch: [() => toValue(request), ...(watchSources ?? [])],
      deep,
      dedupe,
      getCachedData,
      default: defaultValue,
      transform,
      pick,
    },
  )
}
