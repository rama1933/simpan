<template>
  <AdminLayout :page-title="`Riwayat Perubahan - ${knowledge.title}`" :user="user">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
              <li class="inline-flex items-center">
                <Link :href="route('admin.knowledge.index')" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-brand-600">
                  <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                  </svg>
                  Pengetahuan
                </Link>
              </li>
              <li>
                <div class="flex items-center">
                  <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                  </svg>
                  <Link :href="route('admin.knowledge.show', knowledge.id)" class="ml-1 text-sm font-medium text-gray-700 hover:text-brand-600 md:ml-2">
                    {{ knowledge.title }}
                  </Link>
                </div>
              </li>
              <li aria-current="page">
                <div class="flex items-center">
                  <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                  </svg>
                  <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Riwayat Perubahan</span>
                </div>
              </li>
            </ol>
          </nav>
          <h1 class="text-3xl font-bold text-gray-900 tracking-tight mt-2">Riwayat Perubahan</h1>
          <p class="text-gray-600 mt-1">{{ knowledge.title }}</p>
        </div>
        <div class="flex space-x-3">
          <Link :href="route('admin.knowledge.show', knowledge.id)" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Lihat Pengetahuan
          </Link>
          <Link :href="route('admin.knowledge.edit', knowledge.id)" class="inline-flex items-center px-4 py-2 bg-brand-600 text-sm font-medium rounded-lg text-white hover:bg-brand-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Pengetahuan
          </Link>
        </div>
      </div>

      <!-- Knowledge Info Card -->
      <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <h3 class="text-sm font-medium text-gray-500">Status</h3>
            <div class="mt-1">
              <span :class="getStatusBadgeClass(knowledge.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                {{ getStatusName(knowledge.status) }}
              </span>
            </div>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Kategori</h3>
            <p class="mt-1 text-sm text-gray-900">{{ knowledge.category?.name || '-' }}</p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Penulis</h3>
            <p class="mt-1 text-sm text-gray-900">{{ knowledge.author?.name || '-' }}</p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Dibuat</h3>
            <p class="mt-1 text-sm text-gray-900">{{ formatDate(knowledge.created_at) }}</p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Diperbarui</h3>
            <p class="mt-1 text-sm text-gray-900">{{ formatDate(knowledge.updated_at) }}</p>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-500">Total Perubahan</h3>
            <p class="mt-1 text-sm text-gray-900">{{ changeLogs.total || 0 }} perubahan</p>
          </div>
        </div>
      </div>

      <!-- Timeline -->
      <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Timeline Perubahan</h3>
        
        <div v-if="changeLogs.data && changeLogs.data.length > 0" class="flow-root">
          <ul class="-mb-8">
            <li v-for="(log, index) in changeLogs.data" :key="log.id" class="relative pb-8">
              <div v-if="index !== changeLogs.data.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></div>
              <div class="relative flex space-x-3">
                <div>
                  <span :class="getTimelineIconClass(log.action)" class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white">
                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path v-if="log.action === 'created'" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                      <path v-else-if="log.action === 'updated' || log.action === 'field_updated'" fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                      <path v-else-if="log.action === 'deleted'" fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                      <path v-else fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                  </span>
                </div>
                <div class="min-w-0 flex-1">
                  <div class="flex justify-between items-start">
                    <div class="flex-1">
                      <p class="text-sm font-medium text-gray-900">
                        {{ log.description || log.formatted_description }}
                      </p>
                      <div v-if="log.field_name" class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                          {{ log.field_name }}
                        </span>
                      </div>
                      <div v-if="log.old_value || log.new_value" class="mt-3 space-y-2">
                        <div v-if="log.old_value" class="bg-red-50 border border-red-200 rounded-lg p-3">
                          <p class="text-xs font-medium text-red-800 mb-1">Nilai Lama:</p>
                          <p class="text-sm text-red-700 break-words">{{ truncateText(log.old_value, 200) }}</p>
                        </div>
                        <div v-if="log.new_value" class="bg-green-50 border border-green-200 rounded-lg p-3">
                          <p class="text-xs font-medium text-green-800 mb-1">Nilai Baru:</p>
                          <p class="text-sm text-green-700 break-words">{{ truncateText(log.new_value, 200) }}</p>
                        </div>
                      </div>
                    </div>
                    <div class="ml-4 flex-shrink-0 text-right">
                      <p class="text-sm text-gray-500">{{ formatDate(log.changed_at) }}</p>
                      <p class="text-xs text-gray-400 mt-1">{{ log.user?.name || 'System' }}</p>
                      <button @click="showLogDetail(log)" class="text-xs text-brand-600 hover:text-brand-800 mt-1">
                        Detail
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
        
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada perubahan</h3>
          <p class="mt-1 text-sm text-gray-500">Pengetahuan ini belum memiliki riwayat perubahan.</p>
        </div>
        
        <!-- Pagination -->
        <div v-if="changeLogs.data && changeLogs.data.length > 0 && changeLogs.last_page > 1" class="mt-6 border-t border-gray-200 pt-6">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Menampilkan {{ changeLogs.from }} sampai {{ changeLogs.to }} dari {{ changeLogs.total }} perubahan
            </div>
            <div class="flex space-x-2">
              <Link 
                v-for="link in changeLogs.links" 
                :key="link.label"
                :href="link.url"
                :class="[
                  'px-3 py-2 text-sm rounded-lg border',
                  link.active 
                    ? 'bg-brand-600 text-white border-brand-600' 
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
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
              <label class="block text-sm font-medium text-gray-700">Aksi</label>
              <p class="text-sm text-gray-900">{{ getActionName(selectedLog.action) }}</p>
            </div>
            <div v-if="selectedLog.field_name">
              <label class="block text-sm font-medium text-gray-700">Field</label>
              <p class="text-sm text-gray-900">{{ selectedLog.field_name }}</p>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <p class="text-sm text-gray-900">{{ selectedLog.description || selectedLog.formatted_description }}</p>
          </div>
          <div v-if="selectedLog.old_value || selectedLog.new_value" class="grid grid-cols-1 gap-4">
            <div v-if="selectedLog.old_value">
              <label class="block text-sm font-medium text-gray-700">Nilai Lama</label>
              <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                <pre class="text-sm text-red-800 whitespace-pre-wrap">{{ selectedLog.old_value }}</pre>
              </div>
            </div>
            <div v-if="selectedLog.new_value">
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
          <div v-if="selectedLog.ip_address">
            <label class="block text-sm font-medium text-gray-700">IP Address</label>
            <p class="text-sm text-gray-900">{{ selectedLog.ip_address }}</p>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  user: Object,
  knowledge: Object,
  changeLogs: Object
})

const selectedLog = ref(null)

const showLogDetail = (log) => {
  selectedLog.value = log
}

const closeLogDetail = () => {
  selectedLog.value = null
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

const getStatusName = (status) => {
  const statuses = {
    draft: 'Draft',
    published: 'Dipublikasikan',
    archived: 'Diarsipkan'
  }
  return statuses[status] || status
}

const getStatusBadgeClass = (status) => {
  const classes = {
    draft: 'bg-yellow-100 text-yellow-800',
    published: 'bg-green-100 text-green-800',
    archived: 'bg-gray-100 text-gray-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getActionName = (action) => {
  const actions = {
    created: 'Dibuat',
    updated: 'Diperbarui',
    deleted: 'Dihapus',
    published: 'Dipublikasikan',
    archived: 'Diarsipkan',
    field_updated: 'Field Diperbarui',
    restored: 'Dipulihkan'
  }
  return actions[action] || action
}

const getTimelineIconClass = (action) => {
  const classes = {
    created: 'bg-green-500',
    updated: 'bg-blue-500',
    deleted: 'bg-red-500',
    published: 'bg-purple-500',
    archived: 'bg-gray-500',
    field_updated: 'bg-yellow-500',
    restored: 'bg-indigo-500'
  }
  return classes[action] || 'bg-gray-500'
}

const truncateText = (text, maxLength) => {
  if (!text) return ''
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength) + '...'
}
</script>