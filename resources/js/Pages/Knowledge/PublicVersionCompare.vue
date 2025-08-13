<template>
  <PublicLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Back Button -->
      <div class="mb-6">
        <Link 
          :href="`/knowledge/public/${knowledge.id}`"
          class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Kembali ke Pengetahuan
        </Link>
      </div>

      <!-- Header -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-8 text-white">
          <div class="text-center">
            <h1 class="text-3xl font-bold mb-4">Perbandingan Versi</h1>
            <p class="text-indigo-100 text-lg">{{ knowledge.title }}</p>
            <div class="flex items-center justify-center space-x-4 mt-4">
              <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white bg-opacity-20 text-white">
                Versi {{ version1.version_number }}
              </span>
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l4-4m0 0l4-4m-4 4H3m4 0v6a2 2 0 002 2h6a2 2 0 002-2v-6" />
              </svg>
              <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white bg-opacity-20 text-white">
                Versi {{ version2.version_number }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Comparison Content -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Version 1 -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <!-- Version 1 Header -->
          <div class="bg-blue-50 px-6 py-4 border-b border-blue-200">
            <div class="flex items-center justify-between">
              <h2 class="text-xl font-bold text-blue-900">Versi {{ version1.version_number }}</h2>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                {{ formatDate(version1.created_at) }}
              </span>
            </div>
            <p class="text-blue-700 mt-2">{{ version1.title }}</p>
          </div>

          <!-- Version 1 Content -->
          <div class="p-6 space-y-6">
            <!-- Summary -->
            <div v-if="version1.summary">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Ringkasan</h3>
              <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">{{ version1.summary }}</p>
            </div>

            <!-- Content -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Konten</h3>
              <div class="prose prose-sm max-w-none bg-gray-50 p-4 rounded-lg" v-html="version1.content"></div>
            </div>

            <!-- Change Info -->
            <div class="bg-blue-50 p-4 rounded-lg">
              <h4 class="text-sm font-semibold text-blue-900 mb-2">Informasi Perubahan</h4>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <span class="text-blue-700">Jenis:</span>
                  <span class="font-medium text-blue-900">{{ getChangeTypeLabel(version1.change_type) }}</span>
                </div>
                <div v-if="version1.change_reason" class="flex justify-between">
                  <span class="text-blue-700">Alasan:</span>
                  <span class="font-medium text-blue-900 text-right max-w-xs">{{ version1.change_reason }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-blue-700">Dibuat oleh:</span>
                  <span class="font-medium text-blue-900">{{ version1.creator?.name || '-' }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-blue-700">Diverifikasi oleh:</span>
                  <span class="font-medium text-blue-900">{{ version1.verifier?.name || '-' }}</span>
                </div>
              </div>
            </div>

            <!-- Tags -->
            <div v-if="version1.tags?.length">
              <h4 class="text-sm font-semibold text-gray-900 mb-2">Tags</h4>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="tag in version1.tags"
                  :key="tag.id"
                  class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full"
                >
                  {{ tag.name }}
                </span>
              </div>
            </div>

            <!-- Attachments -->
            <div v-if="version1.attachments?.length">
              <h4 class="text-sm font-semibold text-gray-900 mb-2">Lampiran ({{ version1.attachments.length }})</h4>
              <div class="space-y-2">
                <div
                  v-for="attachment in version1.attachments"
                  :key="attachment.id"
                  class="flex items-center justify-between p-2 bg-gray-50 rounded-lg"
                >
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <div>
                      <p class="text-xs font-medium text-gray-900">{{ attachment.original_filename }}</p>
                      <p class="text-xs text-gray-500">{{ formatFileSize(attachment.file_size) }}</p>
                    </div>
                  </div>
                  <a
                    :href="`/storage/${attachment.file_path}`"
                    target="_blank"
                    class="text-xs text-blue-600 hover:text-blue-800"
                  >
                    Download
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Version 2 -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <!-- Version 2 Header -->
          <div class="bg-green-50 px-6 py-4 border-b border-green-200">
            <div class="flex items-center justify-between">
              <h2 class="text-xl font-bold text-green-900">Versi {{ version2.version_number }}</h2>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                {{ formatDate(version2.created_at) }}
              </span>
            </div>
            <p class="text-green-700 mt-2">{{ version2.title }}</p>
          </div>

          <!-- Version 2 Content -->
          <div class="p-6 space-y-6">
            <!-- Summary -->
            <div v-if="version2.summary">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Ringkasan</h3>
              <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">{{ version2.summary }}</p>
            </div>

            <!-- Content -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Konten</h3>
              <div class="prose prose-sm max-w-none bg-gray-50 p-4 rounded-lg" v-html="version2.content"></div>
            </div>

            <!-- Change Info -->
            <div class="bg-green-50 p-4 rounded-lg">
              <h4 class="text-sm font-semibold text-green-900 mb-2">Informasi Perubahan</h4>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <span class="text-green-700">Jenis:</span>
                  <span class="font-medium text-green-900">{{ getChangeTypeLabel(version2.change_type) }}</span>
                </div>
                <div v-if="version2.change_reason" class="flex justify-between">
                  <span class="text-green-700">Alasan:</span>
                  <span class="font-medium text-green-900 text-right max-w-xs">{{ version2.change_reason }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-green-700">Dibuat oleh:</span>
                  <span class="font-medium text-green-900">{{ version2.creator?.name || '-' }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-green-700">Diverifikasi oleh:</span>
                  <span class="font-medium text-green-900">{{ version2.verifier?.name || '-' }}</span>
                </div>
              </div>
            </div>

            <!-- Tags -->
            <div v-if="version2.tags?.length">
              <h4 class="text-sm font-semibold text-gray-900 mb-2">Tags</h4>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="tag in version2.tags"
                  :key="tag.id"
                  class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full"
                >
                  {{ tag.name }}
                </span>
              </div>
            </div>

            <!-- Attachments -->
            <div v-if="version2.attachments?.length">
              <h4 class="text-sm font-semibold text-gray-900 mb-2">Lampiran ({{ version2.attachments.length }})</h4>
              <div class="space-y-2">
                <div
                  v-for="attachment in version2.attachments"
                  :key="attachment.id"
                  class="flex items-center justify-between p-2 bg-gray-50 rounded-lg"
                >
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <div>
                      <p class="text-xs font-medium text-gray-900">{{ attachment.original_filename }}</p>
                      <p class="text-xs text-gray-500">{{ formatFileSize(attachment.file_size) }}</p>
                    </div>
                  </div>
                  <a
                    :href="`/storage/${attachment.file_path}`"
                    target="_blank"
                    class="text-xs text-green-600 hover:text-green-800"
                  >
                    Download
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Version Navigation -->
      <div v-if="versionHistory && versionHistory.length > 2" class="mt-6 bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Bandingkan dengan Versi Lain</h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <Link
              v-for="v in versionHistory"
              :key="v.id"
              :href="`/knowledge/public/${knowledge.id}/version/${v.version_number}`"
              :class="[
                'flex flex-col items-center p-3 rounded-lg border-2 transition-colors',
                (v.id === version1.id || v.id === version2.id)
                  ? 'border-indigo-300 bg-indigo-50 text-indigo-800'
                  : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-700'
              ]"
            >
              <span class="text-sm font-medium">v{{ v.version_number }}</span>
              <span class="text-xs text-center mt-1">{{ formatDate(v.created_at) }}</span>
              <span v-if="v.id === version1.id || v.id === version2.id" class="text-xs mt-1 font-medium">
                (Dipilih)
              </span>
            </Link>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

defineProps({
  knowledge: {
    type: Object,
    required: true
  },
  version1: {
    type: Object,
    required: true
  },
  version2: {
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
  line-height: 1.6;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
  color: #111827;
  font-weight: 600;
  margin-top: 1.5rem;
  margin-bottom: 0.75rem;
}

.prose h1 {
  font-size: 1.5rem;
  line-height: 2rem;
}

.prose h2 {
  font-size: 1.25rem;
  line-height: 1.75rem;
}

.prose h3 {
  font-size: 1.125rem;
  line-height: 1.5rem;
}

.prose p {
  margin-bottom: 1rem;
}

.prose ul, .prose ol {
  margin-bottom: 1rem;
  padding-left: 1.25rem;
}

.prose li {
  margin-bottom: 0.25rem;
}

.prose blockquote {
  border-left: 4px solid #e5e7eb;
  padding-left: 1rem;
  margin: 1rem 0;
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
  padding: 0.75rem;
  border-radius: 0.5rem;
  overflow-x: auto;
  margin: 1rem 0;
}

.prose pre code {
  background-color: transparent;
  padding: 0;
  color: inherit;
}
</style>