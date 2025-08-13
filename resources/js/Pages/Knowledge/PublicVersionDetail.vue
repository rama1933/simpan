<template>
  <PublicLayout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Back Button -->
      <div class="mb-6">
        <Link 
          :href="route('knowledge.public.show', knowledge.id)"
          class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Kembali ke Pengetahuan
        </Link>
      </div>

      <!-- Version Detail -->
      <div v-if="version" class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-8 text-white">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center space-x-3 mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-20 text-white">
                  Versi {{ version.version_number }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  Terverifikasi
                </span>
              </div>
              <h1 class="text-3xl font-bold mb-4">{{ version.title }}</h1>
              <p v-if="version.summary" class="text-purple-100 text-lg leading-relaxed">
                {{ version.summary }}
              </p>
            </div>
          </div>
        </div>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Left Column: Content & Attachments -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Content -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
              <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Konten Versi {{ version.version_number }}</h3>
              </div>
              <div class="p-6">
                <div class="prose prose-lg max-w-none" v-html="version.content"></div>
              </div>
            </div>

            <!-- Change Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
              <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Perubahan</h3>
              </div>
              <div class="p-6">
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Jenis Perubahan</label>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                          :class="{
                            'bg-green-100 text-green-800': version.change_type === 'created',
                            'bg-blue-100 text-blue-800': version.change_type === 'updated',
                            'bg-red-100 text-red-800': version.change_type === 'deleted'
                          }">
                      {{ getChangeTypeLabel(version.change_type) }}
                    </span>
                  </div>
                  <div v-if="version.change_reason">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Alasan Perubahan</label>
                    <p class="text-sm text-gray-900">{{ version.change_reason }}</p>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-if="version.effective_date">
                      <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Efektif</label>
                      <p class="text-sm text-gray-900">{{ formatDate(version.effective_date) }}</p>
                    </div>
                    <div v-if="version.expiry_date">
                      <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Kedaluwarsa</label>
                      <p class="text-sm text-gray-900">{{ formatDate(version.expiry_date) }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Attachments -->
            <div v-if="version?.attachments?.length" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
              <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-semibold text-gray-900">Lampiran</h3>
                  <span class="text-sm text-gray-500">{{ version.attachments.length }} file</span>
                </div>
              </div>
              <div class="p-6">
                <div class="space-y-3">
                  <div
                    v-for="attachment in version.attachments"
                    :key="attachment.id"
                    class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-colors"
                  >
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                      <svg class="w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">{{ attachment.original_filename }}</p>
                      <p class="text-xs text-gray-500 mt-1">{{ formatFileSize(attachment.file_size) }}</p>
                    </div>
                    <a
                      :href="`/storage/${attachment.file_path}`"
                      target="_blank"
                      class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                    >
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                      Download
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column: Version Info -->
          <div class="space-y-6">
            <!-- Version Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
              <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Informasi Versi</h3>
              </div>
              <div class="p-4 space-y-4">
                <!-- Creator Info -->
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Dibuat oleh</label>
                  <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                      <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                      </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-900">{{ version?.creator?.name || '-' }}</p>
                  </div>
                </div>
                
                <!-- Verifier Info -->
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Diverifikasi oleh</label>
                  <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                      <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900">{{ version?.verifier?.name || '-' }}</p>
                      <p class="text-xs text-gray-500">{{ formatDate(version?.verified_at) }}</p>
                    </div>
                  </div>
                </div>
                
                <!-- SKPD Info -->
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">SKPD</label>
                  <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                      <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-6a1 1 0 00-1-1H9a1 1 0 00-1 1v6a1 1 0 01-1 1H4a1 1 0 110-2V4z" clip-rule="evenodd"/>
                      </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-900">{{ version?.skpd?.nama_skpd || '-' }}</p>
                  </div>
                </div>
                
                <!-- Category Info -->
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
                  <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                      <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                      </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-900">{{ version?.category?.name || '-' }}</p>
                  </div>
                </div>
                
                <!-- Date Info -->
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Dibuat</label>
                  <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                      <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                      </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-900">{{ formatDate(version?.created_at) }}</p>
                  </div>
                </div>
                
                <!-- Tags Section -->
                <div class="border-t border-gray-200 pt-4">
                  <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tags</label>
                    <div class="flex flex-wrap gap-1">
                      <span
                        v-for="tag in version?.tags"
                        :key="tag.id"
                        class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full"
                      >
                        {{ tag.name }}
                      </span>
                      <span v-if="!version?.tags?.length" class="text-gray-400 text-xs">Tidak ada tags</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Version Navigation -->
            <div v-if="versionHistory && versionHistory.length > 1" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
              <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Versi Lainnya</h3>
              </div>
              <div class="p-4">
                <div class="space-y-2">
                  <Link
                    v-for="v in versionHistory"
                    :key="v.id"
                    :href="v.id === version.id ? '#' : route('knowledge.public.show.version', { knowledge: knowledge.id, version: v.version_number })"
                    :class="[
                      'flex items-center justify-between p-2 rounded-lg transition-colors',
                      v.id === version.id 
                        ? 'bg-purple-100 text-purple-800 cursor-default' 
                        : 'hover:bg-gray-50 text-gray-700 hover:text-gray-900'
                    ]"
                  >
                    <div class="flex items-center space-x-2">
                      <span class="text-sm font-medium">v{{ v.version_number }}</span>
                      <span v-if="v.id === version.id" class="text-xs">(Saat ini)</span>
                    </div>
                    <span class="text-xs text-gray-500">{{ formatDate(v.created_at) }}</span>
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else class="bg-white rounded-xl shadow-lg p-8 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Versi Tidak Ditemukan</h3>
        <p class="text-gray-600 mb-6">Versi pengetahuan yang Anda cari tidak ditemukan atau belum dipublikasikan.</p>
        <Link 
          :href="route('knowledge.public.show', knowledge?.id || 1)"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Kembali ke Pengetahuan
        </Link>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { route } from '@/core/helpers/route'

defineProps({
  knowledge: {
    type: Object,
    required: true
  },
  version: {
    type: Object,
    required: true
  },
  versionHistory: {
    type: Array,
    default: () => []
  }
})

function formatDate(dateString) {
  if (!dateString) return 'Tidak diketahui'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

function formatFileSize(bytes) {
  if (!bytes) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

function getChangeTypeLabel(changeType) {
  const labels = {
    'created': 'Dibuat',
    'updated': 'Diperbarui',
    'deleted': 'Dihapus'
  }
  return labels[changeType] || changeType
}
</script>

<style scoped>
.prose {
  color: #374151;
  line-height: 1.75;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
  color: #111827;
  font-weight: 600;
  margin-top: 2rem;
  margin-bottom: 1rem;
}

.prose h1 {
  font-size: 2.25rem;
  line-height: 2.5rem;
}

.prose h2 {
  font-size: 1.875rem;
  line-height: 2.25rem;
}

.prose h3 {
  font-size: 1.5rem;
  line-height: 2rem;
}

.prose p {
  margin-bottom: 1.25rem;
}

.prose ul, .prose ol {
  margin-bottom: 1.25rem;
  padding-left: 1.625rem;
}

.prose li {
  margin-bottom: 0.5rem;
}

.prose blockquote {
  border-left: 4px solid #e5e7eb;
  padding-left: 1rem;
  margin: 1.5rem 0;
  font-style: italic;
  color: #6b7280;
}

.prose code {
  background-color: #f3f4f6;
  padding: 0.125rem 0.25rem;
  border-radius: 0.25rem;
  font-size: 0.875rem;
}

.prose pre {
  background-color: #1f2937;
  color: #f9fafb;
  padding: 1rem;
  border-radius: 0.5rem;
  overflow-x: auto;
  margin: 1.5rem 0;
}

.prose pre code {
  background-color: transparent;
  padding: 0;
  color: inherit;
}
</style>