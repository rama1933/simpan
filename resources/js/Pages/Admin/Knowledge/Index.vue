<template>
  <AdminLayout page-title="Knowledge Management" :user="user">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Manajemen Pengetahuan</h1>
          <p class="text-gray-600 mt-1">Kelola dan atur semua pengetahuan dalam sistem</p>
        </div>
        <Link
          :href="route('admin.knowledge.create')"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-brand-700 hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 shadow-sm"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Tambah Pengetahuan
        </Link>
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
              Hapus Filter
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
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
                class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 hover:border-gray-400 transition-colors duration-200"
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
              @input="debouncedSearchTags"
              type="text"
              placeholder="Cari tag..."
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 hover:border-gray-400 transition-colors duration-200"
            />
            <div v-if="tagOptions.length > 0" class="mt-2">
              <div class="text-xs text-gray-500 mb-2">Pilih tag:</div>
              <div class="flex flex-wrap gap-2 max-h-32 overflow-y-auto">
                <button
                  v-for="tag in tagOptions"
                  :key="tag.id"
                  type="button"
                  @click="toggleTag(tag)"
                  :class="selectedTagIds.includes(tag.id) ? 'bg-brand-700 text-white border-brand-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border-gray-200'"
                  class="px-2.5 py-1.5 text-xs rounded-md border transition-colors duration-200"
                >
                  {{ tag.name }}
                  <span v-if="selectedTagIds.includes(tag.id)" class="ml-1">✓</span>
                </button>
              </div>
            </div>
            <div v-if="selectedTagIds.length" class="mt-3">
              <div class="text-xs text-gray-500 mb-2">Tag terpilih ({{ selectedTagIds.length }}):</div>
              <div class="flex flex-wrap gap-2">
                <span v-for="id in selectedTagIds" :key="`sel-${id}`" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-indigo-100 text-indigo-800">
                  {{ tagNameById(id) }}
                  <button class="ml-1.5 text-indigo-600 hover:text-indigo-800 font-medium" @click="removeTag(id)">×</button>
                </span>
              </div>
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
                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-brand-100 text-brand-800"
              >
                Search: {{ filters.search }}
                <button
                  @click="filters.search = ''"
                  class="ml-1.5 text-brand-700 hover:text-brand-900"
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
                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"
              >
                Verifikasi: {{ getVerificationLabel(filters.verification_status) }}
                <button
                  @click="filters.verification_status = ''"
                  class="ml-1.5 text-red-600 hover:text-red-800"
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
          <div class="inline-flex items-center px-4 py-2 text-sm text-brand-700 bg-brand-50 rounded-lg border border-brand-200">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-brand-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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

        <!-- Custom Category Column -->
        <template #category.name="{ item }">
          <span class="text-sm text-gray-800">{{ item?.category?.name || '-' }}</span>
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
              <Link :href="route('admin.knowledge.show', item?.id)" :title="'Lihat'" class="p-2 rounded-md text-brand-700 hover:bg-brand-50">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7.305 4.5 3.274 7.334 1.5 12c1.774 4.666 5.805 7.5 10.5 7.5s8.726-2.834 10.5-7.5C20.726 7.334 16.695 4.5 12 4.5zm0 12a4.5 4.5 0 110-9 4.5 4.5 0 010 9z"/></svg>
              </Link>
              <Link :href="route('admin.knowledge.edit', item?.id)" :title="'Edit'" class="p-2 rounded-md text-emerald-700 hover:bg-emerald-50">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M4 17.25V21h3.75L17.81 10.94l-3.75-3.75L4 17.25zM20.71 7.04a1.003 1.003 0 000-1.42l-2.34-2.34a1.003 1.003 0 00-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/></svg>
              </Link>
              <button
                v-if="item.verification_status === 'pending'"
                @click="verifyKnowledge(item.id)"
                :title="'Verifikasi'"
                class="p-2 rounded-md text-green-700 hover:bg-green-50"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </button>
              <button
                v-if="item.verification_status === 'verified'"
                @click="unverifyKnowledge(item.id)"
                :title="'Batal Verifikasi'"
                class="p-2 rounded-md text-yellow-700 hover:bg-yellow-50"
              >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </button>
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
      </VueTable>

      <!-- Pagination -->
      <div v-if="tableData.length > 0" class="bg-white/80 backdrop-blur px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 rounded-b-xl mt-4">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            v-if="pageMeta.prev_page_url"
            @click="goToPage(pageMeta.current_page - 1)"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >Sebelumnya</button>
          <button
            v-if="pageMeta.next_page_url"
            @click="goToPage(pageMeta.current_page + 1)"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >Selanjutnya</button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
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
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                v-if="pageMeta.prev_page_url"
                @click="goToPage(pageMeta.current_page - 1)"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Sebelumnya</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
              <button
                v-for="page in paginationPages"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  page === pageMeta.current_page
                    ? 'z-10 bg-brand-50 border-brand-500 text-brand-600'
                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                ]"
              >
                {{ page }}
              </button>
              <button
                v-if="pageMeta.next_page_url"
                @click="goToPage(pageMeta.current_page + 1)"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Selanjutnya</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import VueTable from '@/Components/VueTable.vue'
import VueSelect from '@/Components/VueSelect.vue'
import { route } from '@/core/helpers/route'
import axios from 'axios'

const props = defineProps({
  knowledge: Object,
  categories: Array,
  skpds: Array,
  filters: Object,
  user: Object
})

// Knowledge data
const knowledge = ref(props.knowledge || { data: [], links: [] })

// Normalize data for table and pagination
const tableData = computed(() => {
  const k = knowledge.value
  if (!k) return []
  if (Array.isArray(k.data)) return k.data
  if (k.data && Array.isArray(k.data.data)) return k.data.data
  return []
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
    type: 'custom'
  },
  {
    key: 'status',
    label: 'STATUS',
    type: 'custom'
  },
  {
    key: 'verification_status',
    label: 'VERIFIKASI',
    type: 'custom'
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
const selectedTagIds = ref(Array.isArray(props.filters?.tags) ? props.filters.tags : [])
const tagOptions = ref([])
const selectedTags = ref([]) // Cache untuk menyimpan data tag yang dipilih

// State
const isLoading = ref(false)
const openRowId = ref(null)

// Computed properties
const hasActiveFilters = computed(() => {
  return filters.value.search || filters.value.category_id || filters.value.skpd_id || 
         filters.value.status || filters.value.verification_status || selectedTagIds.value.length > 0
})

const pageMeta = computed(() => {
  const k = knowledge.value
  const meta = Array.isArray(k?.data) ? k : k?.data
  return {
    from: meta?.from ?? 0,
    to: meta?.to ?? 0,
    total: meta?.total ?? 0,
    prev_page_url: meta?.prev_page_url ?? null,
    next_page_url: meta?.next_page_url ?? null,
    current_page: meta?.current_page ?? 1,
    last_page: meta?.last_page ?? 1
  }
})

const paginationPages = computed(() => {
  const pages = []
  const current = pageMeta.value.current_page
  const last = pageMeta.value.last_page
  
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }
  
  return pages
})

const categoryOptions = computed(() => {
  return props.categories?.map(category => ({
    value: category.id,
    label: category.name
  })) || []
})

const skpdOptions = computed(() => {
  return props.skpds?.map(skpd => ({
    value: skpd.id,
    label: skpd.nama_skpd
  })) || []
})

const statusOptions = computed(() => [
  { value: 'draft', label: 'Draft' },
  { value: 'published', label: 'Dipublikasi' },
  { value: 'archived', label: 'Diarsipkan' }
])

const verificationOptions = computed(() => [
  { value: 'pending', label: 'Menunggu' },
  { value: 'verified', label: 'Terverifikasi' },
  { value: 'rejected', label: 'Ditolak' }
])

// Helper methods
const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID')
}

const getStatusText = (status) => {
  const statusMap = {
    'draft': 'Draft',
    'published': 'Dipublikasi',
    'archived': 'Diarsipkan'
  }
  return statusMap[status] || status
}

const getStatusBadgeClass = (status) => {
  const classMap = {
    'draft': 'bg-gray-100 text-gray-800',
    'published': 'bg-green-100 text-green-800',
    'archived': 'bg-red-100 text-red-800'
  }
  return classMap[status] || 'bg-gray-100 text-gray-800'
}

const getVerifyText = (status) => {
  const statusMap = {
    'pending': 'Menunggu',
    'verified': 'Terverifikasi',
    'rejected': 'Ditolak'
  }
  return statusMap[status] || '-'
}

const getVerifyBadgeClass = (status) => {
  const classMap = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'verified': 'bg-green-100 text-green-800',
    'rejected': 'bg-red-100 text-red-800'
  }
  return classMap[status] || 'bg-gray-100 text-gray-800'
}

const getCategoryText = (categoryId) => {
  const category = props.categories?.find(c => c.id == categoryId)
  return category?.name || '-'
}

const getSkpdText = (skpdId) => {
  const skpd = props.skpds?.find(s => s.id == skpdId)
  return skpd?.nama_skpd || '-'
}

const getCategoryName = (categoryId) => {
  const category = props.categories?.find(c => c.id == categoryId)
  return category?.name || '-'
}

const getSKPDName = (skpdId) => {
  const skpd = props.skpds?.find(s => s.id == skpdId)
  return skpd?.nama_skpd || '-'
}

const getStatusLabel = (status) => {
  const statusMap = {
    'draft': 'Draft',
    'published': 'Dipublikasi',
    'archived': 'Diarsipkan'
  }
  return statusMap[status] || status
}

const getVerificationLabel = (status) => {
  const statusMap = {
    'pending': 'Menunggu',
    'verified': 'Terverifikasi',
    'rejected': 'Ditolak'
  }
  return statusMap[status] || status
}

const tagNameById = (id) => {
  // Cari di tagOptions terlebih dahulu
  const tag = tagOptions.value.find(t => t.id === id)
  if (tag) return tag.name
  
  // Jika tidak ditemukan, cari di selectedTags cache
  const selectedTag = selectedTags.value.find(t => t.id === id)
  return selectedTag?.name || `Tag ${id}`
}

const removeTag = (id) => {
  selectedTagIds.value = selectedTagIds.value.filter(t => t !== id)
  // Hapus juga dari cache selectedTags
  selectedTags.value = selectedTags.value.filter(t => t.id !== id)
}

const toggleTag = (tag) => {
  if (selectedTagIds.value.includes(tag.id)) {
    removeTag(tag.id)
  } else {
    selectedTagIds.value.push(tag.id)
    // Simpan data tag ke cache jika belum ada
    if (!selectedTags.value.find(t => t.id === tag.id)) {
      selectedTags.value.push(tag)
    }
  }
}

const searchTags = async () => {
  if (tagsQuery.value.length < 2) {
    tagOptions.value = []
    return
  }
  
  try {
    const response = await axios.get('/api/knowledge/tags', {
      params: { q: tagsQuery.value }
    })
    tagOptions.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Error searching tags:', error)
    tagOptions.value = []
  }
}

// Debounced search function
let searchTimeout = null
const debouncedSearchTags = () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  searchTimeout = setTimeout(searchTags, 300)
}

const toggleRowActions = (itemId) => {
  openRowId.value = openRowId.value === itemId ? null : itemId
}

const applyFilters = async () => {
  isLoading.value = true
  
  try {
    const filterParams = {
      ...filters.value,
      tags: selectedTagIds.value.length > 0 ? selectedTagIds.value : undefined
    }
    
    // Remove empty values
    Object.keys(filterParams).forEach(key => {
      if (filterParams[key] === '' || filterParams[key] === null || filterParams[key] === undefined) {
        delete filterParams[key]
      }
    })
    
    const response = await axios.post('/admin/knowledge/filter', filterParams)
    
    if (response.data.success && response.data.knowledge && response.data.knowledge.data) {
      // Update knowledge data dengan struktur yang benar
      const knowledgeData = response.data.knowledge.data
      knowledge.value = {
        data: knowledgeData.data || knowledgeData,
        links: knowledgeData.links || [],
        current_page: knowledgeData.current_page || 1,
        last_page: knowledgeData.last_page || 1,
        per_page: knowledgeData.per_page || 15,
        total: knowledgeData.total || 0,
        from: knowledgeData.from || 0,
        to: knowledgeData.to || 0,
        prev_page_url: knowledgeData.prev_page_url || null,
        next_page_url: knowledgeData.next_page_url || null
      }
    } else {
      knowledge.value = { data: [], links: [] }
    }
  } catch (error) {
    console.error('Error applying filters:', error)
    knowledge.value = { data: [], links: [] }
  } finally {
    isLoading.value = false
  }
}

const applyFiltersManually = () => {
  applyFilters()
}

const clearFilters = async () => {
  filters.value.search = ''
  filters.value.category_id = ''
  filters.value.skpd_id = ''
  filters.value.status = ''
  filters.value.verification_status = ''
  selectedTagIds.value = []
  await applyFilters()
}

const verifyKnowledge = async (id) => {
  try {
    await axios.post(`/admin/knowledge/${id}/verify`)
    // Refresh data
    router.reload()
  } catch (error) {
    console.error('Error verifying knowledge:', error)
  }
}

const unverifyKnowledge = async (id) => {
  try {
    await axios.post(`/admin/knowledge/${id}/unverify`)
    // Refresh data
    router.reload()
  } catch (error) {
    console.error('Error unverifying knowledge:', error)
  }
}

const confirmDelete = (item) => {
  if (confirm('Apakah Anda yakin ingin menghapus knowledge ini?')) {
    deleteKnowledge(item.id)
  }
}

const deleteKnowledge = async (id) => {
  try {
    await axios.delete(`/admin/knowledge/${id}`)
    // Refresh data
    router.reload()
  } catch (error) {
    console.error('Error deleting knowledge:', error)
  }
}

const goToPage = async (page) => {
  isLoading.value = true
  
  try {
    const filterParams = {
      ...filters.value,
      tags: selectedTagIds.value.length > 0 ? selectedTagIds.value : undefined,
      page: page
    }
    
    // Remove empty values
    Object.keys(filterParams).forEach(key => {
      if (filterParams[key] === '' || filterParams[key] === null || filterParams[key] === undefined) {
        delete filterParams[key]
      }
    })
    
    const response = await axios.post('/admin/knowledge/filter', filterParams)
    
    if (response.data.success && response.data.knowledge && response.data.knowledge.data) {
      const knowledgeData = response.data.knowledge.data
      knowledge.value = {
        data: knowledgeData.data || knowledgeData,
        links: knowledgeData.links || [],
        current_page: knowledgeData.current_page || 1,
        last_page: knowledgeData.last_page || 1,
        per_page: knowledgeData.per_page || 15,
        total: knowledgeData.total || 0,
        from: knowledgeData.from || 0,
        to: knowledgeData.to || 0,
        prev_page_url: knowledgeData.prev_page_url || null,
        next_page_url: knowledgeData.next_page_url || null
      }
    } else {
      knowledge.value = { data: [], links: [] }
    }
  } catch (error) {
    console.error('Error changing page:', error)
    knowledge.value = { data: [], links: [] }
  } finally {
    isLoading.value = false
  }
}

// Load initial tag options if there are selected tags
onMounted(async () => {
  if (selectedTagIds.value.length > 0) {
    // Load tag names for selected tags
    try {
      const response = await axios.get('/api/knowledge/tags', {
        params: { ids: selectedTagIds.value.join(',') }
      })
      const loadedTags = response.data.data || response.data || []
      selectedTags.value = loadedTags
      tagOptions.value = loadedTags
    } catch (error) {
      console.error('Error loading selected tags:', error)
    }
  }
})
</script>