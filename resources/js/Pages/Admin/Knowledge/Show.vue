<template>
  <AdminLayout page-title="Detail Pengetahuan" :user="user">
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">{{ knowledge?.title }}</h1>
          <p class="text-gray-600 mt-1">{{ knowledge?.description }}</p>
        </div>
        <div class="flex items-center space-x-3">
          <Link :href="route('admin.knowledge.index')" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
          </Link>
          <Link :href="route('admin.change-logs.knowledge', knowledge?.id)" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            Riwayat Perubahan
          </Link>
          <Link v-if="canEdit" :href="route('admin.knowledge.edit', knowledge?.id)" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
          </Link>
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

        <!-- Right Column: Verification & Knowledge Info -->
        <div class="space-y-6">


          <!-- Knowledge Info -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Basic Info Section -->
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
              <h3 class="text-base font-semibold text-gray-900">Informasi Pengetahuan</h3>
            </div>
            
            <div class="p-4 space-y-4">
              <!-- Author Info -->
              <div>
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
              </div>
              
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
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Dibuat</label>
                <div class="flex items-center space-x-2">
                  <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                  <p class="text-sm font-medium text-gray-900">{{ formatDate(knowledge?.created_at) }}</p>
                </div>
              </div>
              
              <!-- Status Section -->
              <div class="border-t border-gray-200 pt-4">
                <div class="space-y-3">
                  <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status Publikasi</label>
                    <span :class="getStatusClass(knowledge?.status)" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full">
                      {{ getStatusText(knowledge?.status) }}
                    </span>
                  </div>
                  
                  <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status Verifikasi</label>
                    <span :class="getVerificationClass(knowledge?.verification_status)" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full">
                      {{ getVerificationText(knowledge?.verification_status) }}
                    </span>
                  </div>
                  
                  <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tags</label>
                    <div class="flex flex-wrap gap-1">
                      <span
                        v-for="tag in knowledge?.tags"
                        :key="tag"
                        class="inline-flex items-center px-2 py-1 text-xs font-medium bg-brand-100 text-brand-800 rounded-full"
                      >
                        {{ tag }}
                      </span>
                      <span v-if="!knowledge?.tags?.length" class="text-gray-400 text-xs">Tidak ada tags</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Verification Section for Admin -->
          <div v-if="knowledge?.verification_status === 'pending' && canVerify" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-yellow-50 px-4 py-3 border-b border-yellow-200">
              <div class="flex items-center space-x-2">
                <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center">
                  <svg class="w-3 h-3 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-sm font-semibold text-yellow-800">Verifikasi</h3>
                  <p class="text-xs text-yellow-700">Menunggu verifikasi</p>
                </div>
              </div>
            </div>
            
            <div class="p-4 space-y-4">
              <!-- Verification Note -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Catatan Verifikasi</label>
                <textarea 
                  v-model="verifyNote" 
                  rows="3" 
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                  placeholder="Tambahkan catatan (opsional)..."
                ></textarea>
              </div>
              
              <!-- Action Buttons -->
              <div class="flex flex-col gap-2">
                <button 
                  :disabled="loading" 
                  @click="verify('approve')" 
                  class="w-full inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  <svg v-if="loading && action==='approve'" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  {{ loading && action==='approve' ? 'Memproses...' : 'Setujui' }}
                </button>
                
                <button 
                  :disabled="loading" 
                  @click="verify('reject')" 
                  class="w-full inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-rose-600 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  <svg v-if="loading && action==='reject'" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                  </svg>
                  {{ loading && action==='reject' ? 'Memproses...' : 'Tolak' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Unverify Section for Admin (for verified knowledge) -->
          <div v-if="(knowledge?.verification_status === 'approved' || knowledge?.verification_status === 'rejected') && canVerify" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-blue-50 px-4 py-3 border-b border-blue-200">
              <div class="flex items-center space-x-2">
                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                  <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-sm font-semibold text-blue-800">Batalkan Verifikasi</h3>
                  <p class="text-xs text-blue-700">Status: {{ getVerificationText(knowledge?.verification_status) }}</p>
                </div>
              </div>
            </div>
            
            <div class="p-4 space-y-4">
              <!-- Current verification info -->
              <div v-if="knowledge?.verification_note" class="bg-gray-50 rounded-lg p-3">
                <label class="block text-xs font-medium text-gray-700 mb-1">Catatan Verifikasi Sebelumnya</label>
                <p class="text-sm text-gray-600">{{ knowledge.verification_note }}</p>
              </div>
              
              <!-- New note for unverify -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Catatan Pembatalan</label>
                <textarea 
                  v-model="verifyNote" 
                  rows="3" 
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                  placeholder="Alasan pembatalan verifikasi (opsional)..."
                ></textarea>
              </div>
              
              <!-- Unverify Button -->
              <button 
                :disabled="loading" 
                @click="unverify()" 
                class="w-full inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <svg v-if="loading && action==='unverify'" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" clip-rule="evenodd"/>
                </svg>
                {{ loading && action==='unverify' ? 'Memproses...' : 'Batalkan Verifikasi' }}
              </button>
            </div>
          </div>
        </div>
      </div>


    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { route } from '@/core/helpers/route'
import axios from 'axios'
import { toast } from 'vue3-toastify'
import { marked } from 'marked'

const props = defineProps({
  knowledge: Object,
  user: Object,
  canVerify: Boolean,
  canEdit: Boolean
})

const verifyNote = ref('')
const loading = ref(false)
const action = ref(null)

const renderedContent = computed(() => {
  if (!props.knowledge?.content) return ''
  
  // Simple markdown to HTML conversion
  let html = props.knowledge.content
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/^# (.*$)/gim, '<h1>$1</h1>')
    .replace(/^## (.*$)/gim, '<h2>$1</h2>')
    .replace(/^### (.*$)/gim, '<h3>$1</h3>')
    .replace(/\n/g, '<br>')
  
  return html
})

const verify = async (type) => {
  loading.value = true
  action.value = type
  try {
    const response = await axios.post(route('admin.knowledge.verify', props.knowledge.id), {
      action: type,
      note: verifyNote.value
    })
    
    // Show success toast
    const message = type === 'approve' ? 'Pengetahuan berhasil disetujui!' : 'Pengetahuan berhasil ditolak!'
    showToast(message, 'success')
    
    router.reload()
  } catch (error) {
    console.error('Error verifying knowledge:', error)
    showToast('Gagal memverifikasi pengetahuan', 'error')
  } finally {
    loading.value = false
    action.value = null
  }
}

const unverify = async () => {
  loading.value = true
  action.value = 'unverify'
  try {
    await axios.post(route('admin.knowledge.unverify', props.knowledge.id), {
      note: verifyNote.value
    })
    
    showToast('Verifikasi berhasil dibatalkan!', 'success')
    router.reload()
  } catch (error) {
    console.error('Error unverifying knowledge:', error)
    showToast('Gagal membatalkan verifikasi', 'error')
  } finally {
    loading.value = false
    action.value = null
  }
}

const showToast = (message, type = 'success') => {
  if (type === 'success') {
    toast.success(message)
  } else {
    toast.error(message)
  }
}

const getStatusClass = (status) => {
  const classes = {
    published: 'bg-green-100 text-green-800',
    draft: 'bg-yellow-100 text-yellow-800',
    archived: 'bg-gray-100 text-gray-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
  const texts = {
    published: 'Dipublikasi',
    draft: 'Draft',
    archived: 'Diarsipkan'
  }
  return texts[status] || status
}

const getVerificationClass = (status) => {
  const classes = {
    verified: 'bg-green-100 text-green-800',
    pending: 'bg-yellow-100 text-yellow-800',
    rejected: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getVerificationText = (status) => {
  const texts = {
    verified: 'Terverifikasi',
    pending: 'Menunggu',
    rejected: 'Ditolak'
  }
  return texts[status] || status
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>

<style scoped>
.prose {
  @apply text-gray-900 leading-relaxed;
}

.prose h1 {
  @apply text-2xl font-bold mb-4 mt-6;
}

.prose h2 {
  @apply text-xl font-bold mb-3 mt-5;
}

.prose h3 {
  @apply text-lg font-bold mb-2 mt-4;
}

.prose strong {
  @apply font-semibold;
}

.prose em {
  @apply italic;
}
</style>