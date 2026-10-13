import { ref, computed } from 'vue'

const categories = ref([
  { id: 1, code: 'PK', name: 'Perlengkapan Kantor' },
  { id: 2, code: 'FO', name: 'Makanan' },
])

const subCategories = ref([
  {
    id: 1,
    category_id: 1,
    category_name: 'Perlengkapan Kantor',
    name: 'Alat Tulis',
    price_limit: 500000,
  },
  {
    id: 2,
    category_id: 1,
    category_name: 'Perlengkapan Kantor',
    name: 'Perabotan',
    price_limit: 7500000,
  },
])

const users = ref([
  {
    id: 1,
    username: 'admin',
    name: 'Administrator',
    email: 'admin@gmail.com',
    role: 'Admin',
    is_locked: false,
  },
  {
    id: 2,
    username: 'joko',
    name: 'Joko Suyanto',
    email: 'jokos@gmail.com',
    role: 'Operator',
    is_locked: false,
  },
])

const incomingGoods = ref([
  {
    id: 1,
    date: '04/10/23 10:00:00',
    origin: 'PT. Sidomakmur',
    receiver: 'Liana',
    unit: 'Gudang Utama',
    operator_name: 'Budi',
    sub_category_name: 'Alat Tulis',
    letter_number: 'SRT/2023/10/001',
    is_verified: false,
    items: [
      {
        id: 101,
        code: 'ATK0001',
        name: 'Kertas HVS A4 60Gr',
        price: 61000,
        quantity: 4,
        unit: 'Rim',
        total: 244000,
        expired_date: '2026-10-31',
        status: 'pending',
      },
      {
        id: 102,
        code: 'ATK0002',
        name: 'Bolpoint Pilot Std',
        price: 20000,
        quantity: 5,
        unit: 'Dus',
        total: 100000,
        expired_date: '2027-01-15',
        status: 'pending',
      },
    ],
  },
  {
    id: 2,
    date: '01/10/23 11:00:00',
    origin: 'PT. Sejahtera',
    receiver: 'Liana',
    unit: 'Gudang Utama',
    operator_name: 'Budi',
    sub_category_name: 'Alat Tulis',
    letter_number: 'SRT/2023/10/002',
    is_verified: true,
    items: [
      {
        id: 201,
        code: 'ATK0001',
        name: 'Kertas HVS A4 60Gr',
        price: 60000,
        quantity: 1,
        unit: 'Rim',
        total: 60000,
        expired_date: '2026-10-31',
        status: 'verified',
      },
      {
        id: 202,
        code: 'ATK0002',
        name: 'Bolpoint Pilot Std',
        price: 20000,
        quantity: 1,
        unit: 'Dus',
        total: 20000,
        expired_date: '2027-01-15',
        status: 'verified',
      },
    ],
  },
])

export function useData() {
  // Categories
  function addCategory(payload) {
    const nextId = categories.value.length ? Math.max(...categories.value.map((c) => c.id)) + 1 : 1
    categories.value.push({ id: nextId, ...payload })
  }

  function updateCategory(id, payload) {
    const current = categories.value.find((c) => c.id === id)
    if (current) {
      current.code = payload.code
      current.name = payload.name
    }
  }

  function deleteCategory(id) {
    categories.value = categories.value.filter((c) => c.id !== id)
  }

  // Sub Categories
  function addSubCategory(payload) {
    const nextId = subCategories.value.length ? Math.max(...subCategories.value.map((s) => s.id)) + 1 : 1
    subCategories.value.push({ id: nextId, ...payload })
  }

  function updateSubCategory(id, payload) {
    const current = subCategories.value.find((s) => s.id === id)
    if (current) {
      current.category_name = payload.category_name
      current.name = payload.name
      current.price_limit = payload.price_limit
    }
  }

  function deleteSubCategory(id) {
    subCategories.value = subCategories.value.filter((s) => s.id !== id)
  }

  // Users
  function addUser(payload) {
    const nextId = users.value.length ? Math.max(...users.value.map((u) => u.id)) + 1 : 1
    users.value.push({ id: nextId, ...payload, is_locked: false })
  }

  function updateUser(id, payload) {
    const current = users.value.find((u) => u.id === id)
    if (current) {
      Object.assign(current, payload)
    }
  }

  function toggleLockUser(id) {
    const user = users.value.find((u) => u.id === id)
    if (user) {
      user.is_locked = !user.is_locked
    }
  }

  function deleteUser(id) {
    users.value = users.value.filter((u) => u.id !== id)
  }

  // Incoming Goods
  function addIncomingGood(item) {
    const nextId = incomingGoods.value.length ? Math.max(...incomingGoods.value.map((g) => g.id)) + 1 : 1
    incomingGoods.value.unshift({ id: nextId, ...item })
  }

  function deleteIncomingGood(id) {
    incomingGoods.value = incomingGoods.value.filter((g) => g.id !== id)
  }

  function toggleVerifyIncomingGood(id) {
    const good = incomingGoods.value.find((g) => g.id === id)
    if (good) {
      good.is_verified = !good.is_verified
      good.items.forEach((i) => {
        i.status = good.is_verified ? 'verified' : 'pending'
      })
    }
  }

  return {
    categories: computed(() => categories.value),
    subCategories: computed(() => subCategories.value),
    users: computed(() => users.value),
    incomingGoods: computed(() => incomingGoods.value),
    addCategory,
    updateCategory,
    deleteCategory,
    addSubCategory,
    updateSubCategory,
    deleteSubCategory,
    addUser,
    updateUser,
    toggleLockUser,
    deleteUser,
    addIncomingGood,
    deleteIncomingGood,
    toggleVerifyIncomingGood,
  }
}
