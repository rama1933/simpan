<template>
  <AdminLayout page-title="Detail Pengetahuan" :user="user">
    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">{{ knowledge?.title }}</h1>
          <p class="text-gray-600 mt-1">{{ knowledge?.description }}</p>
        </div>
        <div class="flex items-center space-x-3">
          <Link :href="route('knowledge.index')" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
          </Link>
          <Link :href="route('knowledge.edit', knowledge?.id)" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
          </Link>
        </div>
      </div>

      <!-- Knowledge Info -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Kategori</label>
            <p class="text-gray-900">{{ knowledge?.category?.name }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Penulis</label>
            <p class="text-gray-900">{{ knowledge?.author?.name }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">SKPD</label>
            <p class="text-gray-900">{{ knowledge?.skpd?.nama_skpd }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Dibuat</label>
            <p class="text-gray-900">{{ formatDate(knowledge?.created_at) }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
            <span :class="getStatusClass(knowledge?.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
              {{ getStatusText(knowledge?.status) }}
            </span>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Status Verifikasi</label>
            <div class="flex items-center space-x-2">
              <span :class="getVerificationClass(knowledge?.verification_status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                {{ getVerificationText(knowledge?.verification_status) }}
              </span>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Tags</label>
            <div class="flex flex-wrap gap-1">
              <span
                v-for="tag in knowledge?.tags"
                :key="tag"
                class="inline-flex px-2 py-1 text-xs font-medium bg-brand-100 text-brand-800 rounded-full"
              >
                {{ tag }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Konten</h3>
        <div class="prose max-w-none" v-html="renderedContent"></div>
      </div>

      <!-- Attachments -->
      <div v-if="knowledge?.attachments?.length" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Lampiran</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="attachment in knowledge.attachments"
            :key="attachment.id"
            class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50"
          >
            <svg class="w-8 h-8 text-gray-400 mr-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-900">{{ attachment.original_name }}</p>
              <p class="text-xs text-gray-500">{{ formatFileSize(attachment.size_bytes) }}</p>
            </div>
            <a
              :href="`/storage/${attachment.path}`"
              target="_blank"
              class="text-brand-600 hover:text-brand-800 text-sm font-medium"
            >
              Download
            </a>
          </div>
        </div>
      </div>

      <!-- Verification Section for Admin -->
      <div v-if="knowledge?.verification_status === 'pending' && isAdmin" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Verifikasi (Admin)</h3>
        <textarea v-model="verifyNote" rows="3" class="w-full border rounded-md px-3 py-2 mb-4" placeholder="Catatan verifikasi (opsional)"></textarea>
        <div class="flex gap-2">
          <button :disabled="loading" @click="verify('approve')" class="px-3 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50">
            <span v-if="loading && action==='approve'" class="animate-spin inline-block h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
            Setujui
          </button>
          <button :disabled="loading" @click="verify('reject')" class="px-3 py-2 rounded-md bg-rose-600 text-white hover:bg-rose-700 disabled:opacity-50">
            <span v-if="loading && action==='reject'" class="animate-spin inline-block h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
            Tolak
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { marked } from 'marked'
import { toast } from 'vue3-toastify'
import axios from 'axios'

const props = defineProps<{ knowledge: any, user?: any, canVerify?: boolean }>()
const knowledge = props.knowledge
const user = props.user || (usePage().props as any)?.auth?.user || null
const isAdmin = computed(() => !!props.canVerify)

const verifyNote = ref('')
const loading = ref(false)
const action = ref<'approve'|'reject'|null>(null)

const statusText = (s?: string) => ({ draft: 'Draft', published: 'Published', archived: 'Archived' }[s || ''] || '-')
const statusBadge = (s?: string) => ({ draft: 'bg-yellow-100 text-yellow-800', published: 'bg-green-100 text-green-800', archived: 'bg-gray-100 text-gray-800' }[s || ''] || 'bg-blue-100 text-blue-800')
const verifyText = (v?: string) => ({ pending: 'Menunggu', approved: 'Disetujui', rejected: 'Ditolak' }[v || ''] || '-')
const verifyBadge = (v?: string) => ({ pending: 'bg-amber-100 text-amber-800', approved: 'bg-emerald-100 text-emerald-800', rejected: 'bg-rose-100 text-rose-800' }[v || ''] || 'bg-gray-100 text-gray-800')

const renderMarkdown = (md?: string) => md ? marked.parse(md) as string : ''
const formatDate = (d?: string) => d ? new Date(d).toLocaleString('id-ID') : '-'

const verify = async (type: 'approve'|'reject') => {
  loading.value = true
  action.value = type
  try {
    const res = await axios.post(`/admin/knowledge/${knowledge.id}/verify`, { 
      type: type, 
      note: verifyNote.value 
    })
    if (res?.data?.success) toast.success('Verifikasi tersimpan')
    else toast.info(res?.data?.message || 'Verifikasi diproses')
    setTimeout(() => window.location.reload(), 300)
  } catch (e) {
    toast.error('Gagal menyimpan verifikasi')
  } finally {
    loading.value = false
    action.value = null
  }
}

const formatFileSize = (bytes: number) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>

