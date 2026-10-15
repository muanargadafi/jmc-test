import { refDebounced } from '@vueuse/core'
import { useRouteQuery } from '@vueuse/router'

export const useUsers = () => {
  const api = useApi()
  const config = useRuntimeConfig()

  const USER_PREFIX = `${config.public.apiPrefix}/users`

  const getUsers = (params) => {
    const page = useRouteQuery('page', params?.page ?? 1, { transform: Number })
    const limit = ref(params?.limit ?? 10)
    const search = useRouteQuery('search', params?.search ?? '')
    const roleId = useRouteQuery('role_id', params?.roleId ?? null, { transform: Number })

    const debouncedSearch = refDebounced(search, 400)

    watch([debouncedSearch, limit, roleId], () => {
      page.value = 1
    })

    const query = computed(() => {
      const activeSearch = debouncedSearch.value ? debouncedSearch.value.trim() : undefined

      return {
        page: page.value,
        limit: limit.value,
        ...(activeSearch ? { search: activeSearch } : {}),
        ...(roleId.value ? { role_id: roleId.value } : {}),
      }
    })

    const { data, pending, error, refresh } = useApiFetch(USER_PREFIX, {
      query,
      watch: [query],
    })

    const users = computed(() => data.value?.data ?? [])

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
        if (page.value > serverMeta.totalPages) {
          page.value = serverMeta.totalPages
        }
      },
    )

    return {
      users,
      meta,
      loading: pending,
      error,
      refresh,
      page,
      limit,
      search,
      roleId,
    }
  }

  const getUserById = (id) => {
    return useApiFetch(() => `${USER_PREFIX}/${toValue(id)}`)
  }

  const createUser = (payload) => {
    return api(USER_PREFIX, {
      method: 'POST',
      body: payload,
    })
  }

  const updateUser = (id, payload) => {
    return api(`${USER_PREFIX}/${id}`, {
      method: 'PUT',
      body: payload,
    })
  }

  const deleteUser = (id) => {
    return api(`${USER_PREFIX}/${id}`, {
      method: 'DELETE',
    })
  }

  const toggleLockUser = (id) => {
    return api(`${USER_PREFIX}/${id}/toggle-lock`, {
      method: 'PATCH',
    })
  }

  return {
    getUsers,
    getUserById,
    createUser,
    updateUser,
    deleteUser,
    toggleLockUser,
  }
}
