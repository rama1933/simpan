<template>
  <AdminLayout page-title="Knowledge Version Management" :user="user">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Manajemen Versi Pengetahuan</h1>
          <p class="text-gray-600 mt-1">Kelola dan atur semua versi pengetahuan dalam sistem</p>
        </div>
        <Link
          :href="route('admin.knowledge-versions.create')"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-brand-700 hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 shadow-sm"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Tambah Versi
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

          <!-- Status -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <VueSelect
              v-model="filters.status"
              :options="statusOptions"
              placeholder="Pilih Status..."
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

          <!-- Knowledge -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700">Pengetahuan</label>
            <VueSelect
              v-model="filters.knowledge_id"
              :options="knowledgeOptions"
              placeholder="Pilih Pengetahuan..."
            />
          </div>
        </div>
      </div>

      <!-- Table Section -->
      <div class="bg-white rounded-xl shadow-md ring-1 ring-gray-100">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-semibold text-gray-900">Daftar Versi Pengetahuan</h3>
              <p class="text-sm text-gray-500 mt-1">Total {{ versions?.total || 0 }} versi ditemukan</p>
            </div>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Versi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengetahuan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKPD</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="version in versions?.data" :key="version.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">v{{ version.version_number }}</div>
                  <div class="text-sm text-gray-500">{{ version.title }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ version.knowledge?.title }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(version.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                    {{ getStatusText(version.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ version.skpd?.nama_skpd }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(version.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-2">
                    <Link
                      :href="route('admin.knowledge-versions.show', version.id)"
                      class="text-brand-600 hover:text-brand-900"
                    >
                      Lihat
                    </Link>
                    <Link
                      :href="route('admin.knowledge-versions.edit', version.id)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      Edit
                    </Link>
                    <button
                      v-if="version.status === 'draft'"
                      @click="publishVersion(version.id)"
                      class="text-green-600 hover:text-green-900"
                    >
                      Publish
                    </button>
                    <button
                      v-if="version.status === 'published'"
                      @click="archiveVersion(version.id)"
                      class="text-yellow-600 hover:text-yellow-900"
                    >
                      Archive
                    </button>
                    <button
                      @click="deleteVersion(version.id)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="versions?.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <Link
                v-if="versions.prev_page_url"
                :href="versions.prev_page_url"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Previous
              </Link>
              <Link
                v-if="versions.next_page_url"
                :href="versions.next_page_url"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Next
              </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing {{ versions.from }} to {{ versions.to }} of {{ versions.total }} results
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <Link
                    v-if="versions.prev_page_url"
                    :href="versions.prev_page_url"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                  >
                    Previous
                  </Link>
                  <Link
                    v-if="versions.next_page_url"
                    :href="versions.next_page_url"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                  >
                    Next
                  </Link>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import VueSelect from '@/Components/VueSelect.vue'
import { route } from '@/core/helpers/route'
import { toast } from 'vue3-toastify'
import Swal from 'sweetalert2'

const props = defineProps({
  versions: { type: Object, default: () => ({}) },
  skpdList: { type: Array, default: () => [] },
  knowledgeList: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
  user: { type: Object, default: () => ({}) }
})

const filters = ref({
  search: props.filters.search || '',
  status: props.filters.status || '',
  skpd_id: props.filters.skpd_id || '',
  knowledge_id: props.filters.knowledge_id || ''
})

const statusOptions = [
  { value: '', label: 'Semua Status' },
  { value: 'draft', label: 'Draft' },
  { value: 'published', label: 'Published' },
  { value: 'archived', label: 'Archived' },
  { value: 'pending_verification', label: 'Pending Verification' },
  { value: 'verified', label: 'Verified' },
  { value: 'rejected', label: 'Rejected' }
]

const skpdOptions = computed(() => [
  { value: '', label: 'Semua SKPD' },
  ...props.skpdList.map(skpd => ({
    value: skpd.id,
    label: skpd.nama_skpd
  }))
])

const knowledgeOptions = computed(() => [
  { value: '', label: 'Semua Pengetahuan' },
  ...props.knowledgeList.map(knowledge => ({
    value: knowledge.id,
    label: knowledge.title
  }))
])

const hasActiveFilters = computed(() => {
  return filters.value.search || filters.value.status || filters.value.skpd_id || filters.value.knowledge_id
})

function applyFiltersManually() {
  router.get(route('admin.knowledge-versions.index'), filters.value, {
    preserveState: true,
    preserveScroll: true
  })
}

function clearFilters() {
  filters.value = {
    search: '',
    status: '',
    skpd_id: '',
    knowledge_id: ''
  }
  applyFiltersManually()
}

function getStatusClass(status) {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    published: 'bg-green-100 text-green-800',
    archived: 'bg-yellow-100 text-yellow-800',
    pending_verification: 'bg-blue-100 text-blue-800',
    verified: 'bg-purple-100 text-purple-800',
    rejected: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

function getStatusText(status) {
  const texts = {
    draft: 'Draft',
    published: 'Published',
    archived: 'Archived',
    pending_verification: 'Pending Verification',
    verified: 'Verified',
    rejected: 'Rejected'
  }
  return texts[status] || status
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

function publishVersion(id) {
  Swal.fire({
    title: 'Publish Versi?',
    text: 'Versi ini akan dipublikasikan dan dapat diakses publik.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#10B981',
    cancelButtonColor: '#6B7280',
    confirmButtonText: 'Ya, Publish!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('admin.knowledge-versions.publish', id), {}, {
        onSuccess: () => {
          toast.success('Versi berhasil dipublikasikan!')
        },
        onError: () => {
          toast.error('Gagal mempublikasikan versi!')
        }
      })
    }
  })
}

function archiveVersion(id) {
  Swal.fire({
    title: 'Archive Versi?',
    text: 'Versi ini akan diarsipkan dan tidak dapat diakses publik.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#F59E0B',
    cancelButtonColor: '#6B7280',
    confirmButtonText: 'Ya, Archive!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('admin.knowledge-versions.archive', id), {}, {
        onSuccess: () => {
          toast.success('Versi berhasil diarsipkan!')
        },
        onError: () => {
          toast.error('Gagal mengarsipkan versi!')
        }
      })
    }
  })
}

function deleteVersion(id) {
  Swal.fire({
    title: 'Hapus Versi?',
    text: 'Data versi akan dihapus permanen dan tidak dapat dikembalikan.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#EF4444',
    cancelButtonColor: '#6B7280',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('admin.knowledge-versions.destroy', id), {
        onSuccess: () => {
          toast.success('Versi berhasil dihapus!')
        },
        onError: () => {
          toast.error('Gagal menghapus versi!')
        }
      })
    }
  })
}
</script>