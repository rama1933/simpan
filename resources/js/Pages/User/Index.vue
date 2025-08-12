<template>
  <AdminLayout page-title="User Management" :user="user">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Manajemen Pengguna</h1>
        <p class="text-gray-600 mt-1">Kelola dan atur semua pengguna dalam sistem</p>
      </div>
      <a
        :href="r('users.create')"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-brand-700 hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 shadow-sm"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Pengguna
      </a>
    </div>

    <!-- Filter Data Section -->
    <div class="bg-white p-6 rounded-xl shadow-md ring-1 ring-gray-100 mb-8">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Filter Data</h3>
          <p class="text-sm text-gray-500 mt-1">Saring data berdasarkan kriteria yang diinginkan</p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="applyFiltersManually"
            class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-brand-700 border border-transparent rounded-lg hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-all duration-200 shadow-sm"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
            </svg>
            Terapkan Filter
          </button>
          <button
            @click="clearFilters"
            :disabled="!hasActiveFilters"
            :class="[
              'inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition-all duration-200',
              hasActiveFilters
                ? 'text-gray-600 bg-gray-50 border-gray-300 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 shadow-sm'
                : 'text-gray-400 bg-gray-100 border-gray-200 cursor-not-allowed'
            ]"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Clear Filters
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-5">
        <!-- Search -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Cari</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Cari nama atau email..."
              class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 hover:border-gray-400 transition-colors duration-200"
            />
          </div>
        </div>

        <!-- Role -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Role</label>
          <VueSelect
            v-model="filters.role"
            :options="roleOptions"
            placeholder="Pilih Role..."
          />
        </div>

        <!-- SKPD -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">SKPD</label>
          <VueSelect
            v-model="filters.skpd_id"
            :options="skpdOptions"
            placeholder="Pilih SKPD..."
          />
        </div>
      </div>

      <!-- Active filters display -->
      <div v-if="hasActiveFilters" class="mt-4 pt-4 border-t border-gray-100">
        <div class="flex items-center space-x-2">
          <span class="text-sm text-gray-500">Filter aktif:</span>
          <div class="flex flex-wrap gap-2">
            <span v-if="filters.search" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-brand-100 text-brand-800">
              Search: {{ filters.search }}
              <button @click="filters.search = ''" class="ml-1.5 text-brand-700 hover:text-brand-900">×</button>
            </span>
            <span v-if="filters.role" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
              Role: {{ filters.role }}
              <button @click="filters.role = ''" class="ml-1.5 text-green-600 hover:text-green-800">×</button>
            </span>
            <span v-if="filters.skpd_id" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
              SKPD: {{ getSKPDName(filters.skpd_id) }}
              <button @click="filters.skpd_id = ''" class="ml-1.5 text-purple-600 hover:text-purple-800">×</button>
            </span>
          </div>
        </div>
      </div>

      <!-- Loading indicator -->
      <div v-if="isLoading" class="mt-4 text-center">
        <div class="inline-flex items-center px-4 py-2 text-sm text-brand-700 bg-brand-50 rounded-lg border border-brand-200">
          <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-brand-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Memuat data...
        </div>
      </div>
    </div>

    <!-- Users Table using VueTable component -->
    <VueTable
      :data="tableData"
      :columns="tableColumns"
      title="Daftar Pengguna"
      :empty-message="hasActiveFilters ? 'Data tidak ditemukan' : 'Tidak ada data'"
      :empty-description="hasActiveFilters ? 'Coba ubah filter atau hapus beberapa filter untuk melihat data yang tersedia.' : 'Belum ada pengguna yang ditambahkan.'"
    >
      <!-- Custom User Column -->
      <template #user="{ item }">
        <div class="flex items-center">
          <div class="h-8 w-8 mr-3 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold">
            {{ (item.name || '?').charAt(0).toUpperCase() }}
          </div>
          <div>
            <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
            <div class="text-sm text-gray-500">{{ item.email }}</div>
          </div>
        </div>
      </template>

      <!-- Roles Column -->
      <template #roles="{ item }">
        <div class="flex flex-wrap gap-1">
          <span v-for="role in (item.roles || [])" :key="role.id" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
            :class="role.name === 'Admin' ? 'bg-red-100 text-red-800' : 'bg-brand-100 text-brand-800'">
            {{ role.name }}
          </span>
        </div>
      </template>

      <!-- SKPD Column -->
      <template #skpd="{ item }">
        <span class="text-sm text-gray-700">{{ item.skpd?.nama_skpd || '-' }}</span>
      </template>

      <!-- Actions Column -->
      <template #actions="{ item }">
        <div class="flex items-center space-x-3">
          <a :href="r('users.edit', item.id)" class="text-brand-700 hover:text-brand-900">Edit</a>
          <button @click="confirmDelete(item)" class="text-red-600 hover:text-red-800">Hapus</button>
        </div>
      </template>
    </VueTable>

    <!-- Pagination -->
    <div v-if="tableData.length > 0" class="bg-white/80 backdrop-blur px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-b-xl mt-4">
      <div class="flex flex-1 justify-between sm:hidden">
        <button @click="goToPage(pageMeta.current_page - 1)" :disabled="!pageMeta.prev_page_url" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">Previous</button>
        <button @click="goToPage(pageMeta.current_page + 1)" :disabled="!pageMeta.next_page_url" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
      </div>
      <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            Menampilkan
            <span class="font-medium">{{ pageMeta.from }}</span>
            hingga
            <span class="font-medium">{{ pageMeta.to }}</span>
            dari
            <span class="font-medium">{{ pageMeta.total }}</span>
            hasil
          </p>
        </div>
        <div>
          <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
            <button @click="goToPage(pageMeta.current_page - 1)" :disabled="!pageMeta.prev_page_url" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 disabled:opacity-50 disabled:cursor-not-allowed">
              <span class="sr-only">Previous</span>
              <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L9.525 9.25H15a.75.75 0 010 1.5H9.525l3.245 2.96a.75.75 0 11-1.04 1.08l-4.5-4.1a.75.75 0 010-1.08l4.5-4.1a.75.75 0 011.06.02z" clip-rule="evenodd"/></svg>
            </button>
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 focus:z-20">Halaman {{ pageMeta.current_page }}</span>
            <button @click="goToPage(pageMeta.current_page + 1)" :disabled="!pageMeta.next_page_url" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 disabled:opacity-50 disabled:cursor-not-allowed">
              <span class="sr-only">Next</span>
              <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06l3.245-2.96H5a.75.75 0 010-1.5h5.475l-3.245-2.96a.75.75 0 111.04-1.08l4.5 4.1a.75.75 0 010 1.08l-4.5 4.1a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/></svg>
            </button>
          </nav>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import VueTable from '@/Components/VueTable.vue'
import VueSelect from '@/Components/VueSelect.vue'
import { route as r } from '@/core/helpers/route'

interface Props {
  users?: any
  filters?: any
  roles?: any[]
  skpds?: any[]
  user?: any
}

const props = withDefaults(defineProps<Props>(), {
  users: () => ({ data: [], links: [] }),
  filters: () => ({}),
  roles: () => [],
  skpds: () => [],
  user: null
})

// State
const users = ref<any>(props.users || { data: [], links: [] })
const filters = ref({
  search: props.filters?.search || '',
  role: props.filters?.role || '',
  skpd_id: props.filters?.skpd_id || ''
})

// Table data normalization
const tableData = computed<any[]>(() => {
  const u: any = users.value
  if (!u) return []
  if (Array.isArray(u.data)) return u.data
  if (u.data && Array.isArray(u.data.data)) return u.data.data
  return []
})

const pageMeta = computed(() => {
  const u: any = users.value
  const meta = Array.isArray(u?.data) ? u : u?.data
  return {
    from: meta?.from ?? 0,
    to: meta?.to ?? 0,
    total: meta?.total ?? 0,
    prev_page_url: meta?.prev_page_url ?? null,
    next_page_url: meta?.next_page_url ?? null,
    current_page: meta?.current_page ?? 1,
  }
})

// Columns
const tableColumns = ref([
  { key: 'user', label: 'USER', type: 'custom' },
  { key: 'roles', label: 'ROLE', type: 'custom' },
  { key: 'skpd', label: 'SKPD', type: 'custom' },
  { key: 'actions', label: 'AKSI', type: 'custom' }
])

// Options
const roleOptions = computed(() => {
  const opts = (props.roles || []).map((r: any) => ({ value: r.name, label: r.name }))
  return [{ value: '', label: 'Semua Role' }, ...opts]
})

const skpdOptions = computed(() => {
  return [{ value: '', label: 'Semua SKPD' }, ...((props.skpds || []).map((s: any) => ({ value: s.id, label: s.nama_skpd })))]
})

const hasActiveFilters = computed(() => Object.values(filters.value).some(v => v !== ''))
const isLoading = ref(false)

// API call mirroring Knowledge Index
const applyFilters = async (filterData: any, page?: number) => {
  isLoading.value = true
  try {
    const url = page ? `/api/users/filter?page=${page}` : '/api/users/filter'
    const response = await axios.post(url, filterData)
    if (response.data.users && response.data.users.success) {
      users.value = response.data.users.data
    } else {
      users.value = { data: [], links: [] }
    }
  } catch (_) {
    // silent
  } finally {
    isLoading.value = false
  }
}

const goToPage = (page: number) => {
  if (!page || page < 1) return
  applyFilters(filters.value, page)
}

const loadInitialData = async () => {
  await applyFilters({ search: '', role: '', skpd_id: '' })
}

const applyFiltersManually = () => applyFilters(filters.value, 1)

const clearFilters = () => {
  filters.value = { search: '', role: '', skpd_id: '' }
  applyFilters(filters.value, 1)
}

const getSKPDName = (value: any) => {
  const s = (skpdOptions.value || []).find((x: any) => x.value === value)
  return s ? s.label : value
}

const confirmDelete = async (item: any) => {
  if (!item?.id) return
  const ok = confirm('Hapus pengguna ini?')
  if (!ok) return
  try {
    const url = r('users.destroy', item.id) || `/users/${item.id}`
    await axios.post(url, { _method: 'DELETE' })
    applyFilters(filters.value, 1)
  } catch (_) {
    alert('Gagal menghapus pengguna')
  }
}

onMounted(() => {
  loadInitialData()
})
</script>
