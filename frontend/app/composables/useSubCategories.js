import { refDebounced } from '@vueuse/core'
import { useRouteQuery } from '@vueuse/router'

export const useSubCategories = () => {
  const api = useApi()
  const config = useRuntimeConfig()

  const SUB_CATEGORY_PREFIX = `${config.public.apiPrefix}/sub-categories`

  const getSubCategories = (params) => {
    const page = useRouteQuery('page', params?.page ?? 1, { transform: Number })
    const limit = ref(params?.limit ?? 10)
    const search = useRouteQuery('search', params?.search ?? '')

    const debouncedSearch = refDebounced(search, 400)

    watch([debouncedSearch, limit], () => {
      page.value = 1
    })

    const query = computed(() => {
      const activeSearch = debouncedSearch.value ? debouncedSearch.value.trim() : undefined
      return {
        limit: limit.value,
        ...(activeSearch ? { search: activeSearch } : {}),
      }
    })

    const { data, pending, error, refresh } = useApiFetch(SUB_CATEGORY_PREFIX, {
      query,
      watch: [query],
    })

    const subCategories = computed(() => data.value?.data ?? [])

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

    return { subCategories, meta, loading: pending, error, refresh, page, limit, search }
  }

  const getSubCategoryOptions = () => {
    return useApiFetch(`${SUB_CATEGORY_PREFIX}/options`)
  }

  const getSubCategoryById = (id) => {
    return useApiFetch(() => `${SUB_CATEGORY_PREFIX}/${toValue(id)}`)
  }

  const createSubCategory = (payload) => {
    return api(SUB_CATEGORY_PREFIX, { method: 'POST', body: payload })
  }

  const updateSubCategory = (id, payload) => {
    return api(`${SUB_CATEGORY_PREFIX}/${id}`, { method: 'PUT', body: payload })
  }

  const deleteSubCategory = (id) => {
    return api(`${SUB_CATEGORY_PREFIX}/${id}`, { method: 'DELETE' })
  }

  return {
    getSubCategories,
    getSubCategoryOptions,
    getSubCategoryById,
    createSubCategory,
    updateSubCategory,
    deleteSubCategory,
  }
}
