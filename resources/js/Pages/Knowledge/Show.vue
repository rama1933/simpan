<template>
  <AdminLayout page-title="Detail Pengetahuan" :user="user">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ knowledge.title }}</h1>
        <p class="text-sm text-gray-500">Dibuat: {{ formatDate(knowledge.created_at) }} • Oleh: {{ knowledge.author?.name || '-' }}</p>
      </div>
      <div class="flex items-center gap-2">
        <Link :href="`/knowledge/${knowledge.id}/edit`" class="px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Edit</Link>
        <Link href="/knowledge" class="px-3 py-2 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">Kembali</Link>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg border p-6 space-y-3">
          <h2 class="text-lg font-semibold text-gray-900">Deskripsi</h2>
          <p class="text-gray-700 whitespace-pre-wrap">{{ knowledge.description || '-' }}</p>
        </div>
        <div class="bg-white rounded-lg border p-6 space-y-3">
          <h2 class="text-lg font-semibold text-gray-900">Konten</h2>
          <div class="prose max-w-none" v-html="renderMarkdown(knowledge.content)"></div>
        </div>
      </div>

      <div class="space-y-6">
        <div class="bg-white rounded-lg border p-6 space-y-4">
          <h3 class="text-lg font-semibold text-gray-900">Info</h3>
          <div class="text-sm text-gray-600 space-y-2">
            <div><span class="font-medium">Kategori:</span> {{ knowledge.category?.name || '-' }}</div>
            <div><span class="font-medium">SKPD:</span> {{ knowledge.skpd?.nama_skpd || '-' }}</div>
            <div><span class="font-medium">Status:</span>
              <span :class="statusBadge(knowledge.status)" class="px-2 py-0.5 rounded text-xs">{{ statusText(knowledge.status) }}</span>
            </div>
            <div><span class="font-medium">Verifikasi:</span>
              <span :class="verifyBadge(knowledge.verification_status)" class="px-2 py-0.5 rounded text-xs">{{ verifyText(knowledge.verification_status) }}</span>
            </div>
          </div>
        </div>

        <div v-if="(knowledge.attachments || []).length" class="bg-white rounded-lg border p-6 space-y-3">
          <h3 class="text-lg font-semibold text-gray-900">Lampiran</h3>
          <ul class="text-sm text-gray-700 list-disc pl-5 space-y-1">
            <li v-for="att in knowledge.attachments" :key="att.id">
              <a :href="`/storage/${att.path}`" target="_blank" class="text-indigo-600 hover:text-indigo-800">
                {{ att.original_name }}
              </a>
              <span class="text-gray-500">• {{ (att.size_bytes/1024).toFixed(1) }} KB</span>
            </li>
          </ul>
        </div>

        <div v-if="knowledge.verification_status === 'pending' && isAdmin" class="bg-white rounded-lg border p-6 space-y-4">
          <h3 class="text-lg font-semibold text-gray-900">Verifikasi (Admin)</h3>
          <textarea v-model="verifyNote" rows="3" class="w-full border rounded-md px-3 py-2" placeholder="Catatan verifikasi (opsional)"></textarea>
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

        <div v-else-if="isAdmin && ['approved','rejected'].includes(knowledge.verification_status)" class="bg-white rounded-lg border p-6 space-y-3">
          <h3 class="text-lg font-semibold text-gray-900">Aksi Verifikasi</h3>
          <p class="text-sm text-gray-600">Status saat ini: <span :class="verifyBadge(knowledge.verification_status)" class="px-2 py-0.5 rounded text-xs">{{ verifyText(knowledge.verification_status) }}</span></p>
          <textarea v-model="verifyNote" rows="2" class="w-full border rounded-md px-3 py-2" placeholder="Catatan pembatalan (opsional)"></textarea>
          <button :disabled="loading" @click="unverify()" class="px-3 py-2 rounded-md bg-amber-600 text-white hover:bg-amber-700 disabled:opacity-50">
            <span v-if="loading && action==='unverify'" class="animate-spin inline-block h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
            Batalkan Verifikasi (Kembali ke Pending)
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
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

const verify = async (act: 'approve'|'reject') => {
  loading.value = true
  action.value = act
  try {
    const res = await axios.post(`/knowledge/${knowledge.id}/verify`, { action: act, note: verifyNote.value })
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

const unverify = async () => {
  loading.value = true
  action.value = 'unverify'
  try {
    const res = await axios.post(`/knowledge/${knowledge.id}/unverify`, { note: verifyNote.value })
    if (res?.data?.success) toast.success('Verifikasi dibatalkan')
    else toast.info(res?.data?.message || 'Pembatalan diproses')
    setTimeout(() => window.location.reload(), 300)
  } catch (e) {
    toast.error('Gagal membatalkan verifikasi')
  } finally {
    loading.value = false
    action.value = null
  }
}
</script>

