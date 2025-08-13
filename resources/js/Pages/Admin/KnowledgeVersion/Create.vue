<template>
  <AdminLayout page-title="Tambah Versi Pengetahuan" :user="user">
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
              <h1 class="text-3xl font-bold text-gray-900">Tambah Versi Pengetahuan Baru</h1>
              <p class="text-gray-600 mt-2">Buat versi baru untuk pengetahuan yang sudah ada</p>
            </div>
            <div class="hidden md:flex items-center gap-3">
              <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-sm font-medium text-brand-700 border border-brand-100">Form</span>
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
                      <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                          <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M4 4a2 2 0 012-2h3l2 2h3a2 2 0 012 2v2H4V4zM4 9h12v5a2 2 0 01-2 2H6a2 2 0 01-2-2V9z"/></svg>
                        </div>
                        <input
                          v-model="form.title"
                          id="title"
                          type="text"
                          class="w-full pl-10 pr-24 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors"
                          :class="errors.title ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'"
                          placeholder="Masukkan judul versi pengetahuan"
                        />
                        <button type="button" @click="() => onTitleReady(form.title)" :disabled="aiLoading" class="absolute inset-y-0 right-2 my-1 px-3 inline-flex items-center gap-2 rounded-md text-sm font-medium text-white bg-gradient-to-r from-brand-700 to-brand-500 shadow hover:from-brand-600 hover:to-brand-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 disabled:opacity-50">
                          <span v-if="aiLoading" class="inline-block w-4 h-4 border-2 border-white/70 border-t-transparent rounded-full animate-spin"></span>
                          <span>{{ aiLoading ? 'Memproses…' : 'Bantu AI' }}</span>
                        </button>
                      </div>
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

                    <!-- Attachments -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran</label>
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
                      <ul v-if="attachments.length" class="mt-3 space-y-2">
                        <li v-for="(f, i) in attachments" :key="i" class="flex items-center justify-between bg-white border rounded px-3 py-2 text-sm">
                          <div class="truncate">
                            <span class="font-medium">{{ f.name }}</span>
                            <span class="ml-2 text-gray-500">({{ (f.size/1024/1024).toFixed(2) }} MB)</span>
                          </div>
                          <button type="button" @click="removeAttachment(i)" class="text-red-600 hover:text-red-700">Hapus</button>
                        </li>
                      </ul>
                    </div>

                    <!-- Tags -->
                    <div>
                      <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                      <div class="space-y-2">
                        <div class="flex gap-2">
                          <input v-model="tagsInput" id="tags" type="text" class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors hover:border-gray-400" placeholder="Ketik tag lalu Enter, atau klik saran di bawah" @focus="loadInitialTags" @input="onTagInput" @keydown.enter.prevent="addTag" />
                        </div>
                        <div v-if="tagSuggestions.length > 0" class="bg-white border border-gray-200 rounded-md shadow-sm divide-y z-10 max-h-56 overflow-auto">
                          <button
                            v-for="s in tagSuggestions"
                            :key="s.id || s.name"
                            type="button"
                            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50"
                            @click="addTagFromSuggestion(s.name)"
                          >
                            {{ s.name }}
                          </button>
                        </div>
                        <div v-if="form.tags.length > 0" class="flex flex-wrap gap-2">
                          <span v-for="(tag, index) in form.tags" :key="index" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-brand-100 text-brand-800">
                            {{ tag }}
                            <button type="button" @click="removeTag(index)" class="ml-2 text-brand-700 hover:text-brand-900">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                          </span>
                        </div>
                      </div>
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
                {{ processing ? 'Menyimpan...' : 'Simpan Versi' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import VueSelect from '@/Components/VueSelect.vue'
import { route } from '@/core/helpers/route'
import { toast } from 'vue3-toastify'
import axios from 'axios'

const props = defineProps({
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
const attachments = ref([])
const dragOver = ref(false)
const fileInput = ref(null)
const aiLoading = ref(false)

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

// Tags functionality
const tagsInput = ref('')
const tagSuggestions = ref([])
let tagFetchTimer = null

const loadInitialTags = async () => {
  if (tagsInput.value.trim().length > 0) return
  try {
    const res = await axios.get('/api/knowledge/tags')
    tagSuggestions.value = res.data?.data || []
  } catch {
    tagSuggestions.value = []
  }
}

const addTag = () => {
  const raw = tagsInput.value.trim()
  if (!raw) return
  raw
    .split(',')
    .map((t) => t.trim())
    .filter(Boolean)
    .forEach((tag) => {
      if (!form.value.tags.includes(tag)) form.value.tags.push(tag)
    })
  tagsInput.value = ''
}

const removeTag = (idx) => {
  form.value.tags.splice(idx, 1)
}

const onTagInput = async () => {
  const q = tagsInput.value.trim()
  if (tagFetchTimer) clearTimeout(tagFetchTimer)
  tagFetchTimer = setTimeout(async () => {
    if (q.length === 0) {
      tagSuggestions.value = []
      return
    }
    try {
      const res = await axios.get('/api/knowledge/tags', { params: { q } })
      tagSuggestions.value = res.data?.data || []
    } catch {
      tagSuggestions.value = []
    }
  }, 250)
}

const addTagFromSuggestion = (name) => {
  if (!name) return
  if (!form.value.tags.includes(name)) form.value.tags.push(name)
  tagsInput.value = ''
  tagSuggestions.value = []
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

    attachments.value.push(file)
  })
}

function removeAttachment(index) {
  attachments.value.splice(index, 1)
}

const onTitleReady = async (title) => {
  const t = (title || '').trim()
  if (t.length < 5) return
  try {
    aiLoading.value = true
    const res = await axios.post('/api/ai/draft-from-title', { title: t })
    const data = res.data?.data || {}
    if (data.description) form.value.description = data.description
    if (data.content) form.value.content = data.content
    if (Array.isArray(data.tags)) {
      for (const tg of data.tags) {
        if (tg && !form.value.tags.includes(tg)) form.value.tags.push(String(tg))
      }
    }
    toast.success('Draft diisi otomatis oleh AI')
  } catch (e) {
    toast.error(e?.response?.data?.message || 'Gagal membuat draft dari judul')
  } finally {
    aiLoading.value = false
  }
}

function onSubmit() {
  processing.value = true
  errors.value = {}
  
  // Debug: Log form data before sending
  console.log('Form data before submit:', form.value)
  console.log('Attachments:', attachments.value)

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

  // Add attachments
  attachments.value.forEach((file, index) => {
    formData.append(`attachments[${index}]`, file)
  })

  router.post(route('admin.knowledge-versions.store'), formData, {
    onSuccess: () => {
      toast.success('Versi pengetahuan berhasil dibuat!')
      router.visit(route('admin.knowledge-versions.index'))
    },
    onError: (error) => {
      errors.value = error
      toast.error('Gagal membuat versi pengetahuan!')
    },
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>