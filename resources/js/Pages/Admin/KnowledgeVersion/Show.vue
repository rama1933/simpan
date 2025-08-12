<template>
  <AdminLayout page-title="Detail Versi Pengetahuan" :user="user">
    <div class="min-h-screen bg-gray-50">
      <div class="w-full mx-auto py-6 px-6">
        <div class="px-0">
          <!-- Flash Success -->
          <div v-if="$page.props.flash?.success" class="mb-4">
            <div class="rounded-md bg-green-50 p-4 border border-green-200">
              <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
            </div>
          </div>

          <!-- Header -->
          <div class="mb-6 flex items-center justify-between">
            <Link :href="route('admin.knowledge-versions.index')" class="text-brand-700 hover:text-brand-800 mr-4">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </Link>
            <div class="flex-1">
              <h1 class="text-3xl font-bold text-gray-900">{{ version.title }}</h1>
              <p class="text-gray-600 mt-2">Versi {{ version.version_number }} - {{ version.knowledge?.title }}</p>
            </div>
            <div class="hidden md:flex items-center gap-3">
              <span :class="getStatusBadgeClass(version.status)">{{ getStatusLabel(version.status) }}</span>
              <div class="flex gap-2">
                <Link
                  v-if="canEdit"
                  :href="route('admin.knowledge-versions.edit', version.id)"
                  class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500"
                >
                  <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  Edit
                </Link>
                <button
                  v-if="canDelete"
                  @click="confirmDelete"
                  class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                  <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                  Hapus
                </button>
              </div>
            </div>
          </div>

          <!-- Status Actions -->
          <div v-if="showStatusActions" class="mb-6 bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Aksi Status</h3>
            <div class="flex flex-wrap gap-3">
              <button
                v-if="canPublish"
                @click="updateStatus('published')"
                :disabled="processing"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
              >
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Publikasikan
              </button>
              <button
                v-if="canArchive"
                @click="updateStatus('archived')"
                :disabled="processing"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 disabled:opacity-50"
              >
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l6 6 6-6"></path>
                </svg>
                Arsipkan
              </button>
              <button
                v-if="canVerify"
                @click="updateStatus('verified')"
                :disabled="processing"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
              >
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Verifikasi
              </button>
              <button
                v-if="canReject"
                @click="updateStatus('rejected')"
                :disabled="processing"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
              >
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Tolak
              </button>
            </div>
          </div>

          <!-- Content -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
              <!-- Description -->
              <div v-if="version.description" class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Deskripsi</h3>
                <p class="text-gray-700 leading-relaxed">{{ version.description }}</p>
              </div>

              <!-- Content -->
              <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Konten</h3>
                <div class="prose max-w-none text-gray-700 leading-relaxed whitespace-pre-wrap">{{ version.content }}</div>
              </div>

              <!-- Change Notes -->
              <div v-if="version.change_notes" class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Catatan Perubahan</h3>
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ version.change_notes }}</p>
              </div>

              <!-- Attachments -->
              <div v-if="version.attachments?.length" class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Lampiran</h3>
                <ul class="space-y-3">
                  <li v-for="attachment in version.attachments" :key="attachment.id" class="flex items-center justify-between bg-gray-50 border rounded-lg px-4 py-3">
                    <div class="flex items-center space-x-3">
                      <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                      </div>
                      <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ attachment.original_name }}</p>
                        <p class="text-sm text-gray-500">{{ formatFileSize(attachment.file_size) }}</p>
                      </div>
                    </div>
                    <a
                      :href="route('admin.knowledge-versions.download-attachment', attachment.id)"
                      class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-brand-700 bg-brand-100 hover:bg-brand-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500"
                    >
                      <svg class="-ml-0.5 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                      Download
                    </a>
                  </li>
                </ul>
              </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
              <!-- Version Info -->
              <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Versi</h3>
                <dl class="space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Nomor Versi</dt>
                    <dd class="text-sm text-gray-900">{{ version.version_number }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd><span :class="getStatusBadgeClass(version.status)">{{ getStatusLabel(version.status) }}</span></dd>
                  </div>
                  <div v-if="version.effective_date">
                    <dt class="text-sm font-medium text-gray-500">Tanggal Efektif</dt>
                    <dd class="text-sm text-gray-900">{{ formatDate(version.effective_date) }}</dd>
                  </div>
                  <div v-if="version.expiry_date">
                    <dt class="text-sm font-medium text-gray-500">Tanggal Kadaluarsa</dt>
                    <dd class="text-sm text-gray-900">{{ formatDate(version.expiry_date) }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                    <dd class="text-sm text-gray-900">{{ formatDateTime(version.created_at) }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Diperbarui</dt>
                    <dd class="text-sm text-gray-900">{{ formatDateTime(version.updated_at) }}</dd>
                  </div>
                </dl>
              </div>

              <!-- Knowledge Info -->
              <div v-if="version.knowledge" class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pengetahuan Terkait</h3>
                <div class="space-y-3">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">{{ version.knowledge.title }}</h4>
                    <p v-if="version.knowledge.description" class="text-sm text-gray-600 mt-1">{{ version.knowledge.description }}</p>
                  </div>
                  <Link
                    :href="route('admin.knowledge.show', version.knowledge.id)"
                    class="inline-flex items-center text-sm text-brand-600 hover:text-brand-500"
                  >
                    Lihat Pengetahuan
                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                  </Link>
                </div>
              </div>

              <!-- Tags -->
              <div v-if="version.tags?.length" class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tags</h3>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="tag in version.tags"
                    :key="tag.id"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-100 text-brand-800"
                  >
                    {{ tag.name }}
                  </span>
                </div>
              </div>

              <!-- Creator Info -->
              <div v-if="version.created_by_user" class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pembuat</h3>
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-brand-500 rounded-full flex items-center justify-center">
                      <span class="text-sm font-medium text-white">{{ getInitials(version.created_by_user.name) }}</span>
                    </div>
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ version.created_by_user.name }}</p>
                    <p class="text-sm text-gray-500">{{ version.created_by_user.email }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="showDeleteModal = false">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
        <div class="mt-3 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Hapus Versi Pengetahuan</h3>
          <div class="mt-2 px-7 py-3">
            <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus versi ini? Tindakan ini tidak dapat dibatalkan.</p>
          </div>
          <div class="items-center px-4 py-3">
            <button
              @click="deleteVersion"
              :disabled="processing"
              class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 disabled:opacity-50 mr-2"
            >
              <span v-if="processing" class="inline-block w-4 h-4 border-2 border-white/70 border-t-transparent rounded-full animate-spin mr-2"></span>
              {{ processing ? 'Menghapus...' : 'Ya, Hapus' }}
            </button>
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 mt-2"
            >
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { route } from '@/core/helpers/route'
import { toast } from 'vue3-toastify'

const props = defineProps({
  version: { type: Object, required: true },
  user: { type: Object, default: () => ({}) }
})

const processing = ref(false)
const showDeleteModal = ref(false)

const canEdit = computed(() => {
  return ['draft', 'pending_review'].includes(props.version.status)
})

const canDelete = computed(() => {
  return ['draft', 'rejected'].includes(props.version.status)
})

const showStatusActions = computed(() => {
  return canPublish.value || canArchive.value || canVerify.value || canReject.value
})

const canPublish = computed(() => {
  return ['verified'].includes(props.version.status)
})

const canArchive = computed(() => {
  return ['published'].includes(props.version.status)
})

const canVerify = computed(() => {
  return ['pending_review'].includes(props.version.status)
})

const canReject = computed(() => {
  return ['pending_review'].includes(props.version.status)
})

function getStatusLabel(status) {
  const labels = {
    draft: 'Draft',
    pending_review: 'Menunggu Review',
    verified: 'Terverifikasi',
    published: 'Dipublikasikan',
    archived: 'Diarsipkan',
    rejected: 'Ditolak'
  }
  return labels[status] || status
}

function getStatusBadgeClass(status) {
  const classes = {
    draft: 'inline-flex items-center rounded-full bg-gray-50 px-3 py-1 text-sm font-medium text-gray-700 border border-gray-100',
    pending_review: 'inline-flex items-center rounded-full bg-yellow-50 px-3 py-1 text-sm font-medium text-yellow-700 border border-yellow-100',
    verified: 'inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700 border border-blue-100',
    published: 'inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700 border border-green-100',
    archived: 'inline-flex items-center rounded-full bg-orange-50 px-3 py-1 text-sm font-medium text-orange-700 border border-orange-100',
    rejected: 'inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700 border border-red-100'
  }
  return classes[status] || classes.draft
}

function formatDate(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

function formatDateTime(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function formatFileSize(bytes) {
  if (!bytes) return '0 B'
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
}

function getInitials(name) {
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
}

function confirmDelete() {
  showDeleteModal.value = true
}

function deleteVersion() {
  processing.value = true
  
  router.delete(route('admin.knowledge-versions.destroy', props.version.id), {
    onSuccess: () => {
      toast.success('Versi pengetahuan berhasil dihapus!')
    },
    onError: () => {
      toast.error('Gagal menghapus versi pengetahuan!')
    },
    onFinish: () => {
      processing.value = false
      showDeleteModal.value = false
    }
  })
}

function updateStatus(status) {
  processing.value = true
  
  router.patch(route('admin.knowledge-versions.update-status', props.version.id), {
    status: status
  }, {
    onSuccess: () => {
      const statusLabels = {
        published: 'dipublikasikan',
        archived: 'diarsipkan',
        verified: 'diverifikasi',
        rejected: 'ditolak'
      }
      toast.success(`Versi pengetahuan berhasil ${statusLabels[status]}!`)
    },
    onError: () => {
      toast.error('Gagal mengupdate status versi pengetahuan!')
    },
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>