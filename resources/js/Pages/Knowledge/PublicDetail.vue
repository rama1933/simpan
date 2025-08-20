<template>
  <PublicLayout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Back Button -->
      <div class="mb-6">
        <Link
          :href="'/knowledge/public'"
          class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Kembali ke Daftar Pengetahuan
        </Link>
      </div>

      <!-- Knowledge Detail -->
      <div v-if="knowledge" class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8 text-white">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <h1 class="text-3xl font-bold mb-4">{{ knowledge.title }}</h1>
              <p v-if="knowledge.description" class="text-blue-100 text-lg leading-relaxed">
                {{ knowledge.description }}
              </p>
            </div>
            <div class="ml-6 flex-shrink-0 space-y-3">
              <div class="flex justify-end">
                <button
                  @click="toggleAIAssistant"
                  class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
                >
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                  </svg>
                  AI Assistant
                </button>
              </div>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Terverifikasi
              </span>
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
                <h3 class="text-lg font-semibold text-gray-900">Konten Pengetahuan</h3>
              </div>
              <div class="p-6">
                <div class="prose prose-lg max-w-none" v-html="renderedContent"></div>
              </div>
            </div>

            <!-- Attachments -->
            <div v-if="knowledge?.attachments?.length" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
              <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                  <h3 class="text-lg font-semibold text-gray-900">Lampiran</h3>
                  <span class="text-sm text-gray-500">{{ knowledge.attachments.length }} file</span>
                </div>
              </div>
              <div class="p-6">
                <div class="space-y-3">
                  <div
                    v-for="attachment in knowledge.attachments"
                    :key="attachment.id"
                    class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-colors"
                  >
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                      <svg class="w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">{{ attachment.original_name }}</p>
                      <p class="text-xs text-gray-500 mt-1">{{ formatFileSize(attachment.size_bytes) }}</p>
                    </div>
                    <a
                      :href="`/storage/${attachment.path}`"
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

          <!-- Right Column: Knowledge Info -->
          <div class="space-y-6">
            <!-- Knowledge Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
              <!-- Basic Info Section -->
              <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Informasi Pengetahuan</h3>
              </div>

              <div class="p-4 space-y-4">
                <!-- Author Info -->
                <!-- <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Penulis</label>
                  <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                      <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900">{{ knowledge?.author?.name || '-' }}</p>
                      <p class="text-xs text-gray-500">{{ knowledge?.author?.email || '-' }}</p>
                    </div>
                  </div>
                </div> -->

                <!-- SKPD Info -->
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">SKPD</label>
                  <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                      <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-6a1 1 0 00-1-1H9a1 1 0 00-1 1v6a1 1 0 01-1 1H4a1 1 0 110-2V4z" clip-rule="evenodd"/>
                      </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-900">{{ knowledge?.skpd?.nama_skpd || '-' }}</p>
                  </div>
                </div>

                <!-- Category Info -->
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
                  <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                      <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                      </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-900">{{ knowledge?.category?.name || '-' }}</p>
                  </div>
                </div>

                <!-- Date Info -->
                <div>
                  <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Dipublikasi</label>
                  <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                      <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                      </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-900">{{ formatDate(knowledge?.published_at || knowledge?.created_at) }}</p>
                  </div>
                </div>

                <!-- Tags Section -->
                <div class="border-t border-gray-200 pt-4">
                  <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tags</label>
                    <div class="flex flex-wrap gap-1">
                      <span
                        v-for="tag in knowledge?.tags"
                        :key="tag"
                        class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full"
                      >
                        {{ tag }}
                      </span>
                      <span v-if="!knowledge?.tags?.length" class="text-gray-400 text-xs">Tidak ada tags</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Version History Section -->
            <div v-if="versionHistory && versionHistory.length > 1" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
              <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                  <h3 class="text-base font-semibold text-gray-900">Riwayat Versi</h3>
                  <span class="text-sm text-gray-500">{{ versionHistory.length }} versi</span>
                </div>
              </div>
              <div class="p-4">
                <div class="space-y-4">
                  <div
                    v-for="version in versionHistory"
                    :key="version.id"
                    class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 hover:shadow-sm transition-all duration-200"
                  >
                    <!-- Version Header -->
                    <div class="mb-3">
                      <div class="flex items-center space-x-2 flex-wrap mb-2">
                        <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 rounded-full">
                          v{{ version.version_number }}
                        </span>
                        <span v-if="version.id === currentVersion?.id" class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                          </svg>
                          Versi Aktif
                        </span>
                      </div>
                      <div v-if="version.id !== currentVersion?.id" class="flex gap-2">
                        <Link
                          :href="`/knowledge/public/${knowledge.id}/version/${version.version_number}`"
                          class="flex-1 inline-flex items-center justify-center px-2 py-1.5 text-xs font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-md transition-colors"
                        >
                          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                          </svg>
                          Lihat
                        </Link>
                        <button
                          @click="currentVersion?.version_number && version.version_number ? compareVersions(currentVersion.version_number, version.version_number) : null"
                          :disabled="!currentVersion?.version_number || !version.version_number"
                          class="flex-1 inline-flex items-center justify-center px-2 py-1.5 text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 hover:border-gray-400 rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                          </svg>
                          Bandingkan
                        </button>
                      </div>
                    </div>

                    <!-- Version Content -->
                    <div class="mb-3">
                      <h4 class="text-sm font-medium text-gray-900 mb-1">{{ version.title }}</h4>
                      <p v-if="version.summary" class="text-xs text-gray-600 line-clamp-2">{{ version.summary }}</p>
                    </div>

                    <!-- Version Meta -->
                    <div class="grid grid-cols-1 sm:grid-cols-1 gap-2 text-xs text-gray-500">
                      <!-- <div class="flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ version.creator?.name || 'Tidak diketahui' }}</span>
                      </div> -->
                      <!-- <div class="flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ version.verifier?.name || 'Belum diverifikasi' }}</span>
                      </div> -->
                      <div class="flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ formatDate(version.created_at) }}</span>
                      </div>
                    </div>
                  </div>
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
        <h3 class="text-lg font-medium text-gray-900 mb-2">Pengetahuan Tidak Ditemukan</h3>
        <p class="text-gray-600 mb-6">Pengetahuan yang Anda cari tidak ditemukan atau belum dipublikasikan.</p>
        <Link
          :href="'/knowledge/public'"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Kembali ke Daftar Pengetahuan
        </Link>
      </div>
    </div>

    <!-- AI Assistant Modal -->
    <div v-if="showAIModal" class="fixed inset-0 z-50 overflow-y-auto" @click="closeAIModal">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full" @click.stop>
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                AI Assistant
              </h3>
              <button @click="closeAIModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Chat Messages -->
            <div class="h-96 overflow-y-auto border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50">
              <div v-if="chatMessages.length === 0" class="text-center text-gray-500 mt-20">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p>Mulai percakapan dengan AI Assistant tentang pengetahuan ini</p>
              </div>

              <div v-for="(message, index) in chatMessages" :key="index" class="mb-4">
                <div :class="message.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                  <div :class="[
                    'max-w-xs lg:max-w-md px-4 py-2 rounded-lg',
                    message.role === 'user'
                      ? 'bg-blue-600 text-white'
                      : 'bg-white border border-gray-200 text-gray-800'
                  ]">
                    <p class="text-sm whitespace-pre-wrap">{{ message.content }}</p>
                  </div>
                </div>
              </div>

              <div v-if="isLoading" class="flex justify-start mb-4">
                <div class="bg-white border border-gray-200 text-gray-800 max-w-xs lg:max-w-md px-4 py-2 rounded-lg">
                  <div class="flex items-center space-x-2">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-purple-600"></div>
                    <span class="text-sm">AI sedang berpikir...</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Input Area -->
            <div class="flex space-x-2">
              <input
                v-model="userMessage"
                @keypress.enter="sendMessage"
                type="text"
                :placeholder="`Tanyakan tentang: ${props.knowledge?.title || 'dokumen ini'} (contoh: jelaskan konsep utama, apa tujuan dari...)`"
                class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                :disabled="isLoading"
              />
              <button
                @click="sendMessage"
                :disabled="isLoading || !userMessage.trim()"
                class="px-6 py-2 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { computed, ref, nextTick } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
  knowledge: {
    type: Object,
    default: null
  },
  versionHistory: {
    type: Array,
    default: () => []
  },
  currentVersion: {
    type: Object,
    default: null
  }
})

// AI Assistant State
const showAIModal = ref(false)
const chatMessages = ref([])
const userMessage = ref('')
const isLoading = ref(false)

// AI Assistant Functions
function toggleAIAssistant() {
  showAIModal.value = !showAIModal.value

  // Tambahkan pesan pembuka jika belum ada pesan
  if (chatMessages.value.length === 0) {
    chatMessages.value.push({
      role: 'assistant',
      content: `Halo! Saya adalah AI Assistant yang akan membantu Anda memahami dokumen "${props.knowledge?.title || 'pengetahuan ini'}".\n\nSaya hanya dapat menjawab pertanyaan yang berkaitan dengan dokumen ini. Silakan ajukan pertanyaan tentang konten, konsep, atau hal-hal yang terkait dengan dokumen pengetahuan ini.`
    })
  }
}

function closeAIModal() {
  showAIModal.value = false
}

async function sendMessage() {
  if (!userMessage.value.trim() || isLoading.value) return

  const message = userMessage.value.trim()
  userMessage.value = ''

  // Add user message
  chatMessages.value.push({
    role: 'user',
    content: message
  })

  isLoading.value = true

  try {
    // Call Gemini AI API
    const response = await fetch('/api/ai/gemini/chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: JSON.stringify({
        message: message,
        context: {
          knowledge_title: props.knowledge?.title,
          knowledge_content: props.currentVersion?.content,
          knowledge_description: props.knowledge?.description
        }
      })
    })

    if (!response.ok) {
      throw new Error('Failed to get AI response')
    }

    const data = await response.json()

    // Add AI response
    chatMessages.value.push({
      role: 'assistant',
      content: data.response
    })
  } catch (error) {
    console.error('AI Error:', error)
    chatMessages.value.push({
      role: 'assistant',
      content: 'Maaf, terjadi kesalahan saat menghubungi AI Assistant. Silakan coba lagi.'
    })
  } finally {
    isLoading.value = false
    await nextTick()
    // Scroll to bottom of chat
    const chatContainer = document.querySelector('.h-96.overflow-y-auto')
    if (chatContainer) {
      chatContainer.scrollTop = chatContainer.scrollHeight
    }
  }
}

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

const compareVersions = (version1, version2) => {
  if (!version1 || !version2) {
    console.error('Version numbers are required for comparison')
    return
  }

  console.log('Comparing versions:', version1, 'vs', version2)
  console.log('Knowledge ID:', props.knowledge.id)

  const url = `/knowledge/public/${props.knowledge.id}/compare/${version1}/${version2}`
  console.log('Navigating to:', url)

  router.visit(url)
}

// Computed property for rendered content
const renderedContent = computed(() => {
  if (props.currentVersion?.content) {
    return props.currentVersion.content
  }
  return props.knowledge?.content || ''
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

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
