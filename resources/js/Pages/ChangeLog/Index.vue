<template>
  <AdminLayout page-title="Manajemen Perubahan" :user="user">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Manajemen Perubahan</h1>
          <p class="text-gray-600 mt-1">Pantau semua perubahan yang terjadi dalam sistem</p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="exportLogs"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export
          </button>
          <button
            @click="showCleanDialog = true"
            class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Bersihkan Log Lama
          </button>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-100 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Total Perubahan</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.total_changes?.toLocaleString() || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-100 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Hari Ini</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.today_changes?.toLocaleString() || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-100 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Minggu Ini</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.week_changes?.toLocaleString() || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-100 p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Bulan Ini</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.month_changes?.toLocaleString() || 0 }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Entitas</label>
            <select v-model="filters.entity_type" @change="applyFilters" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
              <option value="">Semua Entitas</option>
              <option v-for="(label, value) in entityTypes" :key="value" :value="value">{{ label }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Aksi</label>
            <select v-model="filters.action" @change="applyFilters" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
              <option value="">Semua Aksi</option>
              <option v-for="(label, value) in actions" :key="value" :value="value">{{ label }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dari</label>
            <input v-model="filters.date_from" @change="applyFilters" type="date" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sampai</label>
            <input v-model="filters.date_to" @change="applyFilters" type="date" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
          </div>
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
          <div class="relative">
            <input 
              v-model="filters.search" 
              @input="debouncedSearch"
              type="text" 
              placeholder="Cari dalam deskripsi, field, atau nilai..."
              class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500"
            >
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
          </div>
        </div>
        <div class="mt-4 flex justify-end space-x-3">
          <button @click="resetFilters" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            Reset Filter
          </button>
        </div>
      </div>

      <!-- Change Logs Table -->
      <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Log Perubahan</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entitas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="log in changeLogs.data" :key="log.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatDate(log.changed_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ getEntityName(log.entity_type) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getActionBadgeClass(log.action)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                    {{ getActionName(log.action) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ log.field_name || '-' }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                  {{ log.description || log.formatted_description }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ log.user?.name || 'System' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button @click="showLogDetail(log)" class="text-brand-600 hover:text-brand-900">
                    Detail
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div v-if="changeLogs.data && changeLogs.data.length > 0" class="px-6 py-4 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Menampilkan {{ changeLogs.from }} sampai {{ changeLogs.to }} dari {{ changeLogs.total }} hasil
            </div>
            <div class="flex space-x-2">
              <Link 
                v-for="link in changeLogs.links" 
                :key="link.label"
                :href="link.url || '#'"
                method="get"
                :class="[
                  'px-3 py-2 text-sm rounded-lg border',
                  link.active 
                    ? 'bg-brand-600 text-white border-brand-600' 
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                  !link.url ? 'opacity-50 cursor-not-allowed' : ''
                ]"
                :disabled="!link.url"
                v-html="link.label"
              ></Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Log Detail Modal -->
    <div v-if="selectedLog" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeLogDetail">
      <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" @click.stop>
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Detail Perubahan</h3>
          <button @click="closeLogDetail" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Waktu</label>
              <p class="text-sm text-gray-900">{{ formatDate(selectedLog.changed_at) }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">User</label>
              <p class="text-sm text-gray-900">{{ selectedLog.user?.name || 'System' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Entitas</label>
              <p class="text-sm text-gray-900">{{ getEntityName(selectedLog.entity_type) }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Aksi</label>
              <p class="text-sm text-gray-900">{{ getActionName(selectedLog.action) }}</p>
            </div>
          </div>
          <div v-if="selectedLog.field_name">
            <label class="block text-sm font-medium text-gray-700">Field</label>
            <p class="text-sm text-gray-900">{{ selectedLog.field_name }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <p class="text-sm text-gray-900">{{ selectedLog.description || selectedLog.formatted_description }}</p>
          </div>
          <div v-if="selectedLog.old_value" class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Nilai Lama</label>
              <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                <pre class="text-sm text-red-800 whitespace-pre-wrap">{{ selectedLog.old_value }}</pre>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Nilai Baru</label>
              <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                <pre class="text-sm text-green-800 whitespace-pre-wrap">{{ selectedLog.new_value }}</pre>
              </div>
            </div>
          </div>
          <div v-if="selectedLog.metadata">
            <label class="block text-sm font-medium text-gray-700">Metadata</label>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
              <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ JSON.stringify(selectedLog.metadata, null, 2) }}</pre>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Clean Dialog -->
    <div v-if="showCleanDialog" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="showCleanDialog = false">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Bersihkan Log Lama</h3>
          <button @click="showCleanDialog = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Simpan log untuk (hari)</label>
            <input v-model="cleanDays" type="number" min="30" max="3650" class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
            <p class="text-xs text-gray-500 mt-1">Log yang lebih lama dari ini akan dihapus</p>
          </div>
          <div class="flex justify-end space-x-3">
            <button @click="showCleanDialog = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
              Batal
            </button>
            <button @click="cleanLogs" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
              Hapus
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import debounce from 'lodash/debounce'
import { route } from '@/core/helpers/route.js'

const props = defineProps({
  user: {
    type: Object,
    default: () => ({})
  },
  changeLogs: {
    type: Object,
    default: () => ({ data: [], links: {}, meta: {} })
  },
  statistics: {
    type: Object,
    default: () => ({})
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  entityTypes: {
    type: Object,
    default: () => ({})
  },
  actions: {
    type: Object,
    default: () => ({})
  }
})

const selectedLog = ref(null)
const showCleanDialog = ref(false)
const cleanDays = ref(365)

const filters = reactive({
  entity_type: props.filters?.entity_type || '',
  action: props.filters?.action || '',
  user_id: props.filters?.user_id || '',
  date_from: props.filters?.date_from || '',
  date_to: props.filters?.date_to || '',
  search: props.filters?.search || ''
})

const applyFilters = () => {
  router.get(route('admin.change-logs.index'), filters, {
    preserveState: true,
    preserveScroll: true
  })
}

const debouncedSearch = debounce(() => {
  applyFilters()
}, 500)

const resetFilters = () => {
  Object.keys(filters).forEach(key => {
    filters[key] = ''
  })
  applyFilters()
}

const showLogDetail = (log) => {
  selectedLog.value = log
}

const closeLogDetail = () => {
  selectedLog.value = null
}

const exportLogs = () => {
  // TODO: Implement export functionality
  alert('Fitur export akan segera tersedia')
}

const cleanLogs = async () => {
  if (confirm(`Apakah Anda yakin ingin menghapus log yang lebih lama dari ${cleanDays.value} hari?`)) {
    try {
      await router.delete(route('admin.change-logs.clean'), {
        data: {
          days_to_keep: cleanDays.value
        }
      })
      showCleanDialog.value = false
    } catch (error) {
      console.error('Error cleaning logs:', error)
    }
  }
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getEntityName = (entityType) => {
  return props.entityTypes[entityType] || entityType
}

const getActionName = (action) => {
  return props.actions[action] || action
}

const getActionBadgeClass = (action) => {
  const classes = {
    created: 'bg-green-100 text-green-800',
    updated: 'bg-blue-100 text-blue-800',
    deleted: 'bg-red-100 text-red-800',
    published: 'bg-purple-100 text-purple-800',
    archived: 'bg-gray-100 text-gray-800',
    field_updated: 'bg-yellow-100 text-yellow-800',
    restored: 'bg-indigo-100 text-indigo-800'
  }
  return classes[action] || 'bg-gray-100 text-gray-800'
}
</script>