<template>
  <AdminLayout page-title="Edit Versi Pengetahuan" :user="user">
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
              <h1 class="text-3xl font-bold text-gray-900">Edit Versi Pengetahuan</h1>
              <p class="text-gray-600 mt-2">Edit versi {{ version.version_number }} - {{ version.title }}</p>
            </div>
            <div class="hidden md:flex items-center gap-3">
              <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-sm font-medium text-brand-700 border border-brand-100">Edit</span>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="onSubmit" class="space-y-6">
            <div class="grid grid-cols-1 gap-6">
              <!-- Konten Utama -->
              <div class="space-y-6">
                <div class="bg-white shadow rounded-lg p-6">
                  <div class="space-y-6">
                    <!-- Knowledge Selection -->
                    <div>
                      <label for="knowledge_id" class="block text-sm font-medium text-gray-700 mb-2">Pengetahuan <span class="text-red-500">*</span></label>
                      <VueSelect
                        v-model="form.knowledge_id"
                        :options="knowledgeOptions"
                        placeholder="Pilih Pengetahuan..."
                        :class="errors.knowledge_id ? 'border-red-500' : 'border-gray-300'"
                      />
                      <div v-if="errors.knowledge_id" class="mt-1 text-sm text-red-600">{{ errors.knowledge_id }}</div>
                    </div>

                    <!-- Version Number -->
                    <div>
                      <label for="version_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor Versi <span class="text-red-500">*</span></label>
                      <input
                        v-model="form.version_number"
                        id="version_number"
                        type="text"
                        class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors"
                        :class="errors.version_number ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'"
                        placeholder="Contoh: 1.0, 1.1, 2.0"
                      />
                      <div v-if="errors.version_number" class="mt-1 text-sm text-red-600">{{ errors.version_number }}</div>
                    </div>

                    <!-- Title -->
                    <div>
                      <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul <span class="text-red-500">*</span></label>
                      <input
                        v-model="form.title"
                        id="title"
                        type="text"
                        class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors"
                        :class="errors.title ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'"
                        placeholder="Masukkan judul versi pengetahuan"
                      />
                      <div v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</div>
                    </div>

                    <!-- Description -->
                    <div>
                      <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Singkat</label>
                      <textarea
                        v-model="form.description"
                        id="description"
                        rows="3"
                        class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors"
                        :class="errors.description ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'"
                        placeholder="Masukkan deskripsi singkat versi pengetahuan"
                      ></textarea>
                      <div v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</div>
                    </div>

                    <!-- Content -->
                    <div>
                      <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Konten <span class="text-red-500">*</span></label>
                      <textarea
                        v-model="form.content"
                        id="content"
                        rows="14"
                        class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors resize-none"
                        :class="errors.content ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'"
                        placeholder="Masukkan konten lengkap versi pengetahuan"
                      ></textarea>
                      <div v-if="errors.content" class="mt-1 text-sm text-red-600">{{ errors.content }}</div>
                    </div>

                    <!-- Existing Attachments -->
                    <div v-if="version.attachments?.length">
                      <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran Saat Ini</label>
                      <ul class="space-y-2">
                        <li v-for="attachment in version.attachments" :key="attachment.id" class="flex items-center justify-between bg-gray-50 border rounded px-3 py-2 text-sm">
                          <div class="truncate">
                            <span class="font-medium">{{ attachment.original_name }}</span>
                            <span class="ml-2 text-gray-500">({{ formatFileSize(attachment.file_size) }})</span>
                          </div>
                          <button type="button" @click="removeExistingAttachment(attachment.id)" class="text-red-600 hover:text-red-700">Hapus</button>
                        </li>
                      </ul>
                    </div>

                    <!-- New Attachments -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Tambah Lampiran Baru</label>
                      <div
                        class="border-2 border-dashed rounded-lg p-4 text-center cursor-pointer bg-white hover:bg-gray-50"
                        :class="dragOver ? 'border-brand-400 bg-brand-50/30' : 'border-gray-300'"
                        @dragover.prevent="onDragOver"
                        @dragleave.prevent="onDragLeave"
                        @drop.prevent="onDrop"
                        @click="() => fileInput?.click()"
                      >
                        <input ref="fileInput" type="file" multiple class="hidden" @change="onFilesSelected" />
                        <div class="flex flex-col items-center gap-1 text-sm text-gray-600">
                          <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12l-2 2m0 0l-2-2m2 2V4"/>
                          </svg>
                          <span><span class="font-medium text-brand-700">Klik</span> atau seret file ke sini</span>
                          <span class="text-xs text-gray-400">Dokumen (PDF, Office) atau Gambar (JPG/PNG). Total maks 5MB.</span>
                        </div>
                      </div>
                      <ul v-if="newAttachments.length" class="mt-3 space-y-2">
                        <li v-for="(f, i) in newAttachments" :key="i" class="flex items-center justify-between bg-white border rounded px-3 py-2 text-sm">
                          <div class="truncate">
                            <span class="font-medium">{{ f.name }}</span>
                            <span class="ml-2 text-gray-500">({{ (f.size/1024/1024).toFixed(2) }} MB)</span>
                          </div>
                          <button type="button" @click="removeNewAttachment(i)" class="text-red-600 hover:text-red-700">Hapus</button>
                        </li>
                      </ul>
                    </div>

                    <!-- Tags -->
                    <div>
                      <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                      <VueSelect
                        v-model="form.tags"
                        :options="tagOptions"
                        placeholder="Pilih Tags..."
                        multiple
                        :class="errors.tags ? 'border-red-500' : 'border-gray-300'"
                      />
                      <div v-if="errors.tags" class="mt-1 text-sm text-red-600">{{ errors.tags }}</div>
                    </div>

                    <!-- Effective Date -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Efektif</label>
                        <input
                          v-model="form.effective_date"
                          id="effective_date"
                          type="date"
                          class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors"
                          :class="errors.effective_date ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'"
                        />
                        <div v-if="errors.effective_date" class="mt-1 text-sm text-red-600">{{ errors.effective_date }}</div>
                      </div>

                      <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kadaluarsa</label>
                        <input
                          v-model="form.expiry_date"
                          id="expiry_date"
                          type="date"
                          class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors"
                          :class="errors.expiry_date ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'"
                        />
                        <div v-if="errors.expiry_date" class="mt-1 text-sm text-red-600">{{ errors.expiry_date }}</div>
                      </div>
                    </div>

                    <!-- Change Notes -->
                    <div>
                      <label for="change_notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Perubahan</label>
                      <textarea
                        v-model="form.change_notes"
                        id="change_notes"
                        rows="3"
                        class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors"
                        :class="errors.change_notes ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'"
                        placeholder="Jelaskan perubahan yang dilakukan pada versi ini"
                      ></textarea>
                      <div v-if="errors.change_notes" class="mt-1 text-sm text-red-600">{{ errors.change_notes }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 pt-6">
              <Link
                :href="route('admin.knowledge-versions.index')"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500"
              >
                Batal
              </Link>
              <button
                type="submit"
                :disabled="processing"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 disabled:opacity-50"
              >
                <span v-if="processing" class="inline-block w-4 h-4 border-2 border-white/70 border-t-transparent rounded-full animate-spin mr-2"></span>
                {{ processing ? 'Menyimpan...' : 'Update Versi' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import VueSelect from '@/Components/VueSelect.vue'
import { route } from '@/core/helpers/route'
import { toast } from 'vue3-toastify'

const props = defineProps({
  version: { type: Object, required: true },
  knowledgeList: { type: Array, default: () => [] },
  tagList: { type: Array, default: () => [] },
  user: { type: Object, default: () => ({}) }
})

const form = ref({
  knowledge_id: '',
  version_number: '',
  title: '',
  description: '',
  content: '',
  tags: [],
  effective_date: '',
  expiry_date: '',
  change_notes: ''
})

const errors = ref({})
const processing = ref(false)
const newAttachments = ref([])
const dragOver = ref(false)
const fileInput = ref(null)
const removedAttachments = ref([])

const knowledgeOptions = computed(() => 
  props.knowledgeList.map(knowledge => ({
    value: knowledge.id,
    label: knowledge.title
  }))
)

const tagOptions = computed(() => 
  props.tagList.map(tag => ({
    value: tag.id,
    label: tag.name
  }))
)

onMounted(() => {
  // Populate form with existing data
  form.value = {
    knowledge_id: props.version.knowledge_id,
    version_number: props.version.version_number,
    title: props.version.title,
    description: props.version.description || '',
    content: props.version.content,
    tags: props.version.tags?.map(tag => tag.id) || [],
    effective_date: props.version.effective_date || '',
    expiry_date: props.version.expiry_date || '',
    change_notes: props.version.change_notes || ''
  }
})

function formatFileSize(bytes) {
  return (bytes / 1024 / 1024).toFixed(2) + ' MB'
}

function removeExistingAttachment(attachmentId) {
  removedAttachments.value.push(attachmentId)
  // Remove from display
  const index = props.version.attachments.findIndex(att => att.id === attachmentId)
  if (index > -1) {
    props.version.attachments.splice(index, 1)
  }
}

function onDragOver() {
  dragOver.value = true
}

function onDragLeave() {
  dragOver.value = false
}

function onDrop(event) {
  dragOver.value = false
  const files = Array.from(event.dataTransfer.files)
  addFiles(files)
}

function onFilesSelected(event) {
  const files = Array.from(event.target.files)
  addFiles(files)
}

function addFiles(files) {
  const maxSize = 5 * 1024 * 1024 // 5MB
  const allowedTypes = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'image/jpeg',
    'image/png'
  ]

  files.forEach(file => {
    if (!allowedTypes.includes(file.type)) {
      toast.error(`File ${file.name} tidak didukung`)
      return
    }

    if (file.size > maxSize) {
      toast.error(`File ${file.name} terlalu besar (maks 5MB)`)
      return
    }

    newAttachments.value.push(file)
  })
}

function removeNewAttachment(index) {
  newAttachments.value.splice(index, 1)
}

function onSubmit() {
  processing.value = true
  errors.value = {}

  const formData = new FormData()
  
  // Add form fields
  Object.keys(form.value).forEach(key => {
    if (key === 'tags') {
      form.value[key].forEach(tag => {
        formData.append('tags[]', tag)
      })
    } else {
      formData.append(key, form.value[key] || '')
    }
  })

  // Add new attachments
  newAttachments.value.forEach((file, index) => {
    formData.append(`attachments[${index}]`, file)
  })

  // Add removed attachments
  removedAttachments.value.forEach(id => {
    formData.append('removed_attachments[]', id)
  })

  // Add method override for PUT
  formData.append('_method', 'PUT')

  router.post(route('admin.knowledge-versions.update', props.version.id), formData, {
    onSuccess: () => {
      toast.success('Versi pengetahuan berhasil diupdate!')
    },
    onError: (error) => {
      errors.value = error
      toast.error('Gagal mengupdate versi pengetahuan!')
    },
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>