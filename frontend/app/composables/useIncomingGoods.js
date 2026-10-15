import { refDebounced } from '@vueuse/core'
import { useRouteQuery } from '@vueuse/router'

export const useIncomingGoods = () => {
  const api = useApi()
  const config = useRuntimeConfig()

  const PREFIX = `${config.public.apiPrefix}/incoming-goods`

  const getIncomingGoods = (params) => {
    const page = useRouteQuery('page', params?.page ?? 1, { transform: Number })
    const limit = ref(params?.limit ?? 10)
    const search = useRouteQuery('search', params?.search ?? '')
    const categoryId = useRouteQuery('category_id', params?.categoryId ?? null, {
      transform: Number,
    })
    const subCategoryId = useRouteQuery('sub_category_id', params?.subCategoryId ?? null, {
      transform: Number,
    })
    const year = useRouteQuery('year', params?.year ?? null, {
      transform: Number,
    })

    const debouncedSearch = refDebounced(search, 400)

    watch([debouncedSearch, limit, categoryId, subCategoryId, year], () => {
      page.value = 1
    })

    const query = computed(() => {
      const activeSearch = debouncedSearch.value ? debouncedSearch.value.trim() : undefined

      return {
        page: page.value,
        limit: limit.value,
        ...(activeSearch ? { search: activeSearch } : {}),
        ...(categoryId.value ? { category_id: categoryId.value } : {}),
        ...(subCategoryId.value ? { sub_category_id: subCategoryId.value } : {}),
        ...(year.value ? { year: year.value } : {}),
      }
    })

    const { data, pending, error, refresh } = useApiFetch(PREFIX, {
      query,
      watch: [query],
    })

    const incomingGoods = computed(() => data.value?.data ?? [])

    const meta = computed(() => {
      const dataMeta = data.value?.meta || {
        page: page.value,
        limit: limit.value,
        totalItems: 0,
        totalPages: 1,
      }
      const firstItem = dataMeta.totalItems === 0 ? 0 : (dataMeta.page - 1) * dataMeta.limit + 1
      const lastItem = Math.min(dataMeta.page * dataMeta.limit, dataMeta.totalItems)
      return { ...dataMeta, firstItem, lastItem }
    })

    watch(
      () => data.value?.meta,
      (serverMeta) => {
        if (!serverMeta || serverMeta.totalItems === 0) return
        if (page.value > serverMeta.totalPages) page.value = serverMeta.totalPages
      },
    )

    return {
      incomingGoods,
      meta,
      loading: pending,
      error,
      refresh,
      page,
      limit,
      search,
      categoryId,
      subCategoryId,
      year,
    }
  }

  const getIncomingGoodById = (id) => {
    return useApiFetch(() => `${PREFIX}/${toValue(id)}`)
  }

  const createIncomingGood = (payload) => {
    const formData = new FormData()

    for (const [key, value] of Object.entries(payload)) {
      if (key === 'items') continue
      if (value === null || value === undefined) continue
      formData.append(key, value)
    }

    if (payload.items) {
      payload.items.forEach((item, index) => {
        for (const [key, value] of Object.entries(item)) {
          if (value === null || value === undefined) continue
          formData.append(`items[${index}][${key}]`, value)
        }
      })
    }

    return api(PREFIX, { method: 'POST', body: formData })
  }

  const updateIncomingGood = (id, payload) => {
    const formData = new FormData()
    formData.append('_method', 'PUT')

    // Laravel's `boolean` rule only accepts true/false/0/1/'0'/'1' (strict).
    // Compute this once and exclude BOTH possible key names from the loop
    // below, so the field is never sent twice with conflicting values.
    const removeAttachment = Boolean(payload.removeAttachment ?? payload.remove_attachment)
    formData.append('remove_attachment', removeAttachment ? '1' : '0')

    for (const [key, value] of Object.entries(payload)) {
      if (key === 'items') continue
      if (key === 'removeAttachment' || key === 'remove_attachment') continue
      if (key === 'attachment' && !(value instanceof File)) continue
      if (value === null || value === undefined) continue
      formData.append(key, value)
    }

    if (payload.items) {
      payload.items.forEach((item, index) => {
        for (const [key, value] of Object.entries(item)) {
          if (value === null || value === undefined) continue
          formData.append(`items[${index}][${key}]`, value)
        }
      })
    }

    return api(`${PREFIX}/${id}`, { method: 'POST', body: formData })
  }

  const verifyIncomingGood = (id) => {
    return api(`${PREFIX}/${id}/verify`, { method: 'PATCH' })
  }

  const deleteIncomingGood = (id) => {
    return api(`${PREFIX}/${id}`, { method: 'DELETE' })
  }

  return {
    getIncomingGoods,
    getIncomingGoodById,
    createIncomingGood,
    updateIncomingGood,
    verifyIncomingGood,
    deleteIncomingGood,
  }
}
