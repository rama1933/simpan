<template>
  <AdminLayout page-title="Knowledge Management" :user="user">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Manajemen Pengetahuan</h1>
        <p class="text-gray-600 mt-2">Kelola dan atur semua pengetahuan dalam sistem</p>
      </div>
      <Link
        href="/knowledge/create"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Pengetahuan
      </Link>
    </div>

    <!-- Filter Data Section -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Filter Data</h3>
          <p class="text-sm text-gray-500 mt-1">Saring data berdasarkan kriteria yang diinginkan</p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="applyFiltersManually"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
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
                ? 'text-gray-600 bg-gray-50 border-gray-300 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500'
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

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
              placeholder="Cari judul atau konten..."
              class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 transition-colors duration-200"
            />
          </div>
        </div>

        <!-- Category -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Kategori</label>
          <VueSelect
            v-model="filters.category_id"
            :options="categoryOptions"
            placeholder="Pilih Kategori..."
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

        <!-- Status -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <VueSelect
            v-model="filters.status"
            :options="statusOptions"
            placeholder="Pilih Status..."
          />
        </div>

        <!-- Verifikasi -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Verifikasi</label>
          <VueSelect
            v-model="filters.verification_status"
            :options="verificationOptions"
            placeholder="Pilih Status Verifikasi..."
          />
        </div>

        <!-- Tags -->
        <div class="space-y-2 md:col-span-2 lg:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Tags</label>
          <input
            v-model="tagsQuery"
            @input="searchTags"
            type="text"
            placeholder="Cari tag..."
            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 transition-colors duration-200"
          />
          <div class="mt-2 flex flex-wrap gap-2">
            <button
              v-for="tag in tagOptions"
              :key="tag.id"
              type="button"
              @click="toggleTag(tag)"
              :class="selectedTagIds.includes(tag.id) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
              class="px-2.5 py-1.5 text-xs rounded-md border border-gray-200"
            >
              {{ tag.name }}
            </button>
          </div>
          <div v-if="selectedTagIds.length" class="mt-2 flex flex-wrap gap-2">
            <span v-for="id in selectedTagIds" :key="`sel-${id}`" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-indigo-100 text-indigo-800">
              {{ tagNameById(id) }}
              <button class="ml-1 text-indigo-700" @click="removeTag(id)">×</button>
            </span>
          </div>
        </div>
      </div>

      <!-- Active filters display -->
      <div v-if="hasActiveFilters" class="mt-4 pt-4 border-t border-gray-100">
        <div class="flex items-center space-x-2">
          <span class="text-sm text-gray-500">Filter aktif:</span>
          <div class="flex flex-wrap gap-2">
            <span
              v-if="filters.search"
              class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
            >
              Search: {{ filters.search }}
              <button
                @click="filters.search = ''"
                class="ml-1.5 text-blue-600 hover:text-blue-800"
              >
                ×
              </button>
            </span>
            <span
              v-if="filters.category_id"
              class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
            >
              Kategori: {{ getCategoryName(filters.category_id) }}
              <button
                @click="filters.category_id = ''"
                class="ml-1.5 text-green-600 hover:text-green-800"
              >
                ×
              </button>
            </span>
            <span
              v-if="filters.skpd_id"
              class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
            >
              SKPD: {{ getSKPDName(filters.skpd_id) }}
              <button
                @click="filters.skpd_id = ''"
                class="ml-1.5 text-purple-600 hover:text-purple-800"
              >
                ×
              </button>
            </span>
            <span
              v-if="filters.status"
              class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
            >
              Status: {{ getStatusLabel(filters.status) }}
              <button
                @click="filters.status = ''"
                class="ml-1.5 text-yellow-600 hover:text-yellow-800"
              >
                ×
              </button>
            </span>
            <span
              v-if="filters.verification_status"
              class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800"
            >
              Verifikasi: {{ getVerifyText(filters.verification_status) }}
              <button
                @click="filters.verification_status = ''"
                class="ml-1.5 text-amber-600 hover:text-amber-800"
              >
                ×
              </button>
            </span>
            <span
              v-for="id in selectedTagIds"
              :key="`chip-${id}`"
              class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
            >
              Tag: {{ tagNameById(id) }}
              <button @click="removeTag(id)" class="ml-1.5 text-indigo-700">×</button>
            </span>
          </div>
        </div>
      </div>

      <!-- Loading indicator -->
      <div v-if="isLoading" class="mt-4 text-center">
        <div class="inline-flex items-center px-4 py-2 text-sm text-blue-600 bg-blue-50 rounded-lg border border-blue-200">
          <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Memuat data...
        </div>
      </div>
    </div>

    <!-- Knowledge Table using VueTable component -->
    <VueTable
      :data="tableData"
      :columns="tableColumns"
      title="Daftar Pengetahuan"
      :empty-message="hasActiveFilters ? 'Data tidak ditemukan' : 'Tidak ada data'"
      :empty-description="hasActiveFilters ? 'Coba ubah filter atau hapus beberapa filter untuk melihat data yang tersedia.' : 'Belum ada pengetahuan yang ditambahkan.'"
    >
      <!-- Custom Title Column -->
      <template #title="{ item }">
        <div class="flex items-center">
          <div class="h-8 w-8 text-gray-400 mr-3">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <div>
            <div class="text-sm font-medium text-gray-900">{{ item?.title || 'Judul tidak tersedia' }}</div>
            <div class="text-sm text-gray-500">{{ formatDate(item?.created_at) || 'Tanggal tidak tersedia' }}</div>
          </div>
        </div>
      </template>

      <!-- Custom Status Column -->
      <template #status="{ item }">
        <span :class="getStatusBadgeClass(item?.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
          {{ getStatusText(item?.status) || 'Status tidak tersedia' }}
        </span>
      </template>

      <!-- Custom Verification Column -->
      <template #verification_status="{ item }">
        <span :class="getVerifyBadgeClass(item?.verification_status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
          {{ getVerifyText(item?.verification_status) || '-' }}
        </span>
      </template>

      <!-- Custom SKPD Column -->
      <template #skpd.kode_skpd="{ item }">
        <span class="text-sm text-gray-800">{{ item?.skpd?.nama_skpd || '-' }}</span>
      </template>

      <!-- Custom Actions Column -->
      <template #actions="{ item }">
        <div class="flex items-center gap-1">
          <template v-if="openRowId === item.id">
            <!-- Ikon aksi muncul di kiri -->
            <Link :href="`/knowledge/${item?.id}`" :title="'Lihat'" class="p-2 rounded-md text-indigo-700 hover:bg-indigo-50">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7.305 4.5 3.274 7.334 1.5 12c1.774 4.666 5.805 7.5 10.5 7.5s8.726-2.834 10.5-7.5C20.726 7.334 16.695 4.5 12 4.5zm0 12a4.5 4.5 0 110-9 4.5 4.5 0 010 9z"/></svg>
            </Link>
            <Link :href="`/knowledge/${item?.id}/edit`" :title="'Edit'" class="p-2 rounded-md text-emerald-700 hover:bg-emerald-50">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M4 17.25V21h3.75L17.81 10.94l-3.75-3.75L4 17.25zM20.71 7.04a1.003 1.003 0 000-1.42l-2.34-2.34a1.003 1.003 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/></svg>
            </Link>
            <button :title="'Hapus'" @click="confirmDelete(item)" class="p-2 rounded-md text-rose-700 hover:bg-rose-50">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M6 7h12l-1 12a2 2 0 01-2 2H9a2 2 0 01-2-2L6 7zm3-3h6a1 1 0 011 1v1H8V5a1 1 0 011-1z"/></svg>
            </button>
            <!-- Toggle menjadi ikon X di kanan -->
            <button @click="openRowId = null" class="p-2 rounded-full hover:bg-gray-100 focus:outline-none" :title="'Tutup'">
              <svg class="w-5 h-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </template>
          <template v-else>
            <button @click="toggleRowActions(item.id)" class="p-2 rounded-full hover:bg-gray-100 focus:outline-none" :title="'Aksi'">
              <svg class="w-5 h-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </button>
          </template>
        </div>
      </template>

      <!-- Empty Actions -->
      <template #empty-actions>
        <div v-if="hasActiveFilters" class="mt-4">
          <button
            @click="clearFilters"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Reset Filter
          </button>
        </div>
      </template>
    </VueTable>

    <!-- Pagination -->
    <div v-if="tableData.length > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
      <div class="flex-1 flex justify-between sm:hidden">
        <button
          v-if="pageMeta.prev_page_url"
          @click="goToPage(pageMeta.current_page - 1)"
          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
        >Previous</button>
        <button
          v-if="pageMeta.next_page_url"
          @click="goToPage(pageMeta.current_page + 1)"
          class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
        >Next</button>
      </div>
      <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            Showing
            <span class="font-medium">{{ pageMeta.from }}</span>
            to
            <span class="font-medium">{{ pageMeta.to }}</span>
            of
            <span class="font-medium">{{ pageMeta.total }}</span>
            results
          </p>
        </div>
        <div>
          <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <button
              v-if="pageMeta.prev_page_url"
              @click="goToPage(pageMeta.current_page - 1)"
              class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
            >
              <span class="sr-only">Previous</span>
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/>
              </svg>
            </button>
            <button
              v-if="pageMeta.next_page_url"
              @click="goToPage(pageMeta.current_page + 1)"
              class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
            >
              <span class="sr-only">Next</span>
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
              </svg>
            </button>
          </nav>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import VueSelect from '@/Components/VueSelect.vue'
import VueTable from '@/Components/VueTable.vue'
import axios from 'axios'
import { toast } from 'vue3-toastify'
import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'
import { route as r } from '@/core/helpers/route'

// Configure axios with CSRF token
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
axios.defaults.withCredentials = true

const page = usePage()

function resolveFlashVal(val: any) {
  return typeof val === 'function' ? val() : val
}

const props = defineProps({
  knowledge: {
    type: Object,
    default: () => ({ data: [], links: [] })
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  perPage: {
    type: Number,
    default: 15
  },
  user: {
    type: Object,
    default: null
  },
  skpds: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  }
});

// Knowledge data
const knowledge = ref(props.knowledge || { data: [], links: [] })

// Normalize data for table and pagination (supports paginator at root or nested under .data)
const tableData = computed<any[]>(() => {
  const k: any = knowledge.value
  if (!k) return []
  if (Array.isArray(k.data)) return k.data
  if (k.data && Array.isArray(k.data.data)) return k.data.data
  return []
})

const pageMeta = computed(() => {
  const k: any = knowledge.value
  const meta = Array.isArray(k?.data) ? k : k?.data
  return {
    from: meta?.from ?? 0,
    to: meta?.to ?? 0,
    total: meta?.total ?? 0,
    prev_page_url: meta?.prev_page_url ?? null,
    next_page_url: meta?.next_page_url ?? null,
    current_page: meta?.current_page ?? 1,
  }
})

// Table columns configuration
const tableColumns = ref([
  {
    key: 'title',
    label: 'JUDUL',
    type: 'custom'
  },
  {
    key: 'category.name',
    label: 'KATEGORI',
    type: 'badge'
  },
  {
    key: 'status',
    label: 'STATUS',
    type: 'badge'
  },
  {
    key: 'verification_status',
    label: 'VERIFIKASI',
    type: 'custom'
  },
  {
    key: 'author.name',
    label: 'PENULIS',
    type: 'text'
  },
  {
    key: 'skpd.kode_skpd',
    label: 'SKPD',
    type: 'custom'
  },
  {
    key: 'actions',
    label: 'AKSI',
    type: 'custom'
  }
])

// Filter state
const filters = ref({
    search: props.filters?.search || '',
    category_id: props.filters?.category_id || '',
    skpd_id: props.filters?.skpd_id || '',
    status: props.filters?.status || '',
    verification_status: props.filters?.verification_status || ''
})

// Tags state
const tagsQuery = ref('')
const tagOptions = ref<any[]>([])
const selectedTagIds = ref<number[]>(Array.isArray((props as any)?.filters?.tags) ? (props as any)?.filters?.tags : [])

const searchTags = async () => {
  try {
    const res = await axios.get('/api/knowledge/tags', { params: { q: tagsQuery.value } })
    tagOptions.value = res?.data?.data || []
  } catch (_) { /* ignore */ }
}

const toggleTag = (tag: any) => {
  const id = Number(tag.id)
  if (selectedTagIds.value.includes(id)) {
    selectedTagIds.value = selectedTagIds.value.filter(t => t !== id)
  } else {
    selectedTagIds.value = [...selectedTagIds.value, id]
  }
  // sinkronkan ke filters untuk apply
  ;(filters.value as any).tags = [...selectedTagIds.value]
}

const removeTag = (id: number) => {
  selectedTagIds.value = selectedTagIds.value.filter(t => t !== id)
  ;(filters.value as any).tags = [...selectedTagIds.value]
}

const tagNameById = (id: number) => tagOptions.value.find(t => t.id === id)?.name || `#${id}`

// Computed properties for filter options
const categoryOptions = computed(() => {
    return props.categories?.map((category: any) => ({
        value: category.id,
        label: category.name
    })) || []
})

const statusOptions = computed(() => [
  { value: '', label: 'Semua Status' },
  { value: 'draft', label: 'Draft' },
  { value: 'published', label: 'Published' },
  { value: 'archived', label: 'Archived' }
])

const verificationOptions = computed(() => [
  { value: '', label: 'Semua Verifikasi' },
  { value: 'pending', label: 'Menunggu' },
  { value: 'approved', label: 'Disetujui' },
  { value: 'rejected', label: 'Ditolak' }
])

const skpdOptions = computed(() => {
    return props.skpds?.map((skpd: any) => ({
        value: skpd.id,
        label: skpd.nama_skpd
    })) || []
})

// Check if any filters are active
const hasActiveFilters = computed(() => {
  return Object.values(filters.value).some(value => value !== '');
});

// Loading state
const isLoading = ref(false)

// Apply filters via AJAX
const applyFilters = async (filterData: any, page?: number) => {
    isLoading.value = true

    try {
        const url = page ? `/api/knowledge/filter?page=${page}` : '/api/knowledge/filter'
        const response = await axios.post(url, filterData)

        if (response.data.knowledge && response.data.knowledge.success) {
            knowledge.value = response.data.knowledge.data
        } else {
            knowledge.value = { data: [], links: [] }
        }
    } catch (error) {
        toast.error('Terjadi kesalahan saat memfilter data. Silakan coba lagi.')
    } finally {
        isLoading.value = false
    }
}

// Go to specific page using current filters
const goToPage = (page: number) => {
  if (!page || page < 1) return
  applyFilters(filters.value, page)
}

// Load initial data when page loads
const loadInitialData = async () => {
    await applyFilters({
        search: '',
        category_id: '',
        skpd_id: '',
        status: '',
        verification_status: '',
        tags: selectedTagIds.value
    })
}

onMounted(() => {
  const flash: any = (page.props as any)?.flash
  const success = resolveFlashVal(flash?.success)
  const error = resolveFlashVal(flash?.error)
  const message = resolveFlashVal(flash?.message)
  if (success) toast.success(success)
  if (error) toast.error(error)
  if (message) toast.info(message)
  window.scrollTo({ top: 0, behavior: 'auto' })
  loadInitialData()
})

// Apply filters manually
const applyFiltersManually = () => {
    ;(filters.value as any).tags = selectedTagIds.value
    applyFilters(filters.value, 1)
}

// Clear all filters
const clearFilters = () => {
    filters.value = {
        search: '',
        category_id: '',
        skpd_id: '',
        status: '',
        verification_status: '',
        tags: []
    }
    selectedTagIds.value = []
    // Apply empty filters to reset data
    applyFilters(filters.value, 1)
}

const confirmDelete = async (item: any) => {
  if (!item?.id) return
  const result = await Swal.fire({
    title: 'Hapus pengetahuan?',
    text: item?.title ? `Judul: ${item.title}` : 'Tindakan ini tidak dapat dibatalkan.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280'
  })
  if (!result.isConfirmed) return
  try {
    await axios.delete(r('knowledge.delete', item.id))
    toast.success('Pengetahuan berhasil dihapus')
    // refresh data
    applyFilters(filters.value, 1)
  } catch (_) {
    toast.error('Gagal menghapus pengetahuan')
  }
}

const getStatusBadgeClass = (status) => {
  const classes = {
    draft: 'bg-yellow-100 text-yellow-800',
    published: 'bg-green-100 text-green-800',
    archived: 'bg-gray-100 text-gray-800'
  };
  return classes[status] || 'bg-blue-100 text-blue-800';
};

const getStatusText = (status) => {
  const texts = {
    draft: 'Draft',
    published: 'Published',
    archived: 'Archived'
  };
  return texts[status] || 'Tidak Diketahui';
};

const getVerifyBadgeClass = (v) => {
  const classes = {
    pending: 'bg-amber-100 text-amber-800',
    approved: 'bg-emerald-100 text-emerald-800',
    rejected: 'bg-rose-100 text-rose-800'
  }
  return classes[v] || 'bg-gray-100 text-gray-800'
}

const getVerifyText = (v) => ({ pending: 'Menunggu', approved: 'Disetujui', rejected: 'Ditolak' }[v] || '-')

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const getCategoryName = (value) => {
  const category = categoryOptions.value.find(opt => opt.value === value);
  return category ? category.label : value;
};

const getSKPDName = (value) => {
  const skpd = skpdOptions.value.find(opt => opt.value === value);
  return skpd ? skpd.label : value;
};

const getStatusLabel = (value) => {
  const status = statusOptions.value.find(opt => opt.value === value);
  return status ? status.label : value;
};

// Row actions toggle
const openRowId = ref<number|null>(null)
const toggleRowActions = (id: number) => {
  openRowId.value = openRowId.value === id ? null : id
}
</script>
