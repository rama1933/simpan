<template>
  <AdminLayout page-title="Edit Pengetahuan" :user="user">
    <div class="min-h-screen bg-gray-50">
      <div class="w-full mx-auto py-6 px-6">
        <div class="px-0">
          <!-- Header -->
          <div class="mb-6 flex items-center justify-between">
            <Link :href="route('knowledge.show', knowledge.id)" class="text-brand-700 hover:text-brand-800 mr-4">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </Link>
            <div class="flex-1">
              <h1 class="text-3xl font-bold text-gray-900">Edit Pengetahuan</h1>
              <p class="text-gray-600 mt-2">Perbarui data pengetahuan</p>
            </div>
            <div class="hidden md:flex items-center gap-3">
              <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-sm font-medium text-brand-700 border border-brand-100">Form</span>
            </div>
          </div>

          <!-- Form Full Width -->
          <Form ref="formRef" :validation-schema="schema" @submit="onSubmit" v-slot="{ meta, values }" class="space-y-6">
            <div class="grid grid-cols-1 gap-6">
              <div class="space-y-6 bg-white shadow rounded-lg p-6">
                <!-- Judul -->
                <div>
                  <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul <span class="text-red-500">*</span></label>
                  <Field name="title" v-slot="{ field, meta }" :model-value="initial.title">
                    <div class="relative">
                      <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M4 4a2 2 0 012-2h3l2 2h3a2 2 0 012 2v2H4V4zM4 9h12v5a2 2 0 01-2 2H6a2 2 0 01-2-2V9z"/></svg>
                      </div>
                      <input v-bind="field" id="title" type="text" class="w-full pl-10 pr-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors" :class="meta.touched && meta.invalid ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'" placeholder="Masukkan judul pengetahuan" />
                    </div>
                  </Field>
                  <ErrorMessage name="title" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Deskripsi -->
                <div>
                  <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Singkat</label>
                  <Field name="description" v-slot="{ field, meta }" :model-value="initial.description">
                    <div class="relative">
                      <textarea v-bind="field" id="description" rows="3" class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors" :class="meta.touched && meta.invalid ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'" placeholder="Masukkan deskripsi singkat pengetahuan"></textarea>
                    </div>
                  </Field>
                  <ErrorMessage name="description" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Konten -->
                <div>
                  <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Konten <span class="text-red-500">*</span></label>
                  <Field name="content" v-slot="{ field, meta }" :model-value="initial.content">
                    <textarea v-bind="field" id="content" rows="14" class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors resize-none" :class="meta.touched && meta.invalid ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'" placeholder="Masukkan konten lengkap pengetahuan" @input="autoResize"></textarea>
                  </Field>
                  <ErrorMessage name="content" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Attachments -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran</label>
                  <!-- Existing attachments -->
                  <div v-if="existingAttachments.length" class="mb-3 space-y-2">
                    <div class="text-xs text-gray-500">Lampiran saat ini:</div>
                    <ul class="space-y-2">
                      <li v-for="att in existingAttachments" :key="att.id" class="flex items-center justify-between bg-gray-50 border rounded px-3 py-2 text-sm">
                        <div class="truncate flex items-center gap-2">
                          <svg class="w-4 h-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                          <span class="font-medium truncate max-w-[320px]" :title="att.original_name">{{ att.original_name }}</span>
                          <span class="text-gray-500">({{ formatSize(att.size_bytes) }})</span>
                        </div>
                        <label class="inline-flex items-center gap-2 text-red-600 cursor-pointer">
                          <input type="checkbox" v-model="removeIds" :value="att.id" class="rounded border-gray-300 text-red-600 focus:ring-red-500" />
                          Hapus
                        </label>
                      </li>
                    </ul>
                  </div>
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
                      <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12l-2 2m0 0l-2-2m2 2V4"/></svg>
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

                <!-- Klasifikasi -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <Field name="category_id" v-slot="{ field }" :model-value="initial.category_id">
                      <VueSelect
                        v-model="field.value"
                        :options="categoryOptions"
                        placeholder="Pilih Kategori atau ketik untuk menambah"
                        @change="(v) => field.onChange(v)"
                        @add="(label) => addCategoryQuick(label, (v) => field.onChange(v))"
                      />
                    </Field>
                    <ErrorMessage name="category_id" class="mt-1 text-sm text-red-600" />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SKPD <span class="text-red-500">*</span></label>
                    <Field name="skpd_id" v-slot="{ field }" :model-value="initial.skpd_id">
                      <VueSelect
                        v-model="field.value"
                        :options="skpdOptions"
                        placeholder="Pilih SKPD"
                        @change="(v) => field.onChange(v)"
                      />
                    </Field>
                    <ErrorMessage name="skpd_id" class="mt-1 text-sm text-red-600" />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <Field name="status" v-slot="{ field }" :model-value="initial.status">
                      <VueSelect
                        v-model="field.value"
                        :options="statusOptions"
                        placeholder="Pilih Status"
                        @change="(v) => field.onChange(v)"
                      />
                    </Field>
                    <ErrorMessage name="status" class="mt-1 text-sm text-red-600" />
                  </div>
                </div>

                <!-- Tags -->
                <div>
                  <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                  <div class="space-y-2">
                    <div class="flex gap-2">
                      <input v-model="tagsInput" id="tags" type="text" class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors hover:border-gray-400" placeholder="Ketik tag lalu Enter, atau klik saran di bawah" @focus="loadInitialTags" @input="onTagInput" @keydown.enter.prevent="addTag" />
                    </div>
                    <div v-if="tagSuggestions.length > 0" class="bg-white border border-gray-200 rounded-md shadow-sm divide-y z-10 max-h-56 overflow-auto">
                      <button v-for="s in tagSuggestions" :key="s.id || s.name" type="button" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50" @click="addTagFromSuggestion(s.name)">
                        {{ s.name }}
                      </button>
                    </div>
                    <div v-if="tags.length > 0" class="flex flex-wrap gap-2">
                      <span v-if="tags.length > 0" v-for="(tag, index) in tags" :key="index" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-brand-100 text-brand-800">
                        {{ tag }}
                        <button type="button" @click="removeTag(index)" class="ml-2 text-brand-700 hover:text-brand-900">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Sticky Action Bar -->
            <div class="sticky bottom-0 inset-x-0 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80 border-t border-gray-200 px-6 py-4 flex justify-end gap-3 shadow-lg">
              <Link :href="route('knowledge.show', knowledge.id)" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">Batal</Link>
              <button type="submit" :disabled="shouldDisableSubmit(values, meta.valid)" class="px-5 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-semibold text-white bg-brand-700 hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <span v-if="submitting">Menyimpan...</span>
                <span v-else>Simpan Perubahan</span>
              </button>
            </div>
          </Form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import VueSelect from '@/Components/VueSelect.vue'
import { Form, Field, ErrorMessage, useForm } from 'vee-validate'
import * as yup from 'yup'
import axios from 'axios'
import { toast } from 'vue3-toastify'

const props = defineProps<{ knowledge: any, categories: any[], skpds: any[] }>()
const user = null

const initial = {
  title: props.knowledge?.title || '',
  description: props.knowledge?.description || '',
  content: props.knowledge?.content || '',
  category_id: props.knowledge?.category_id || '',
  skpd_id: props.knowledge?.skpd_id || '',
  status: props.knowledge?.status || 'draft',
}

const formRef = ref<any>(null)
const { setFieldValue } = useForm()

// Attachments
const fileInput = ref<HTMLInputElement | null>(null)
const attachments = ref<File[]>([])
const existingAttachments = ref<any[]>(props.knowledge?.attachments || [])
const removeIds = ref<number[]>([])
const dragOver = ref(false)
const onFilesSelected = (e: Event) => {
  const input = e.target as HTMLInputElement
  const files = Array.from(input.files || [])
  if (files.length) {
    const allowed = files.filter((f) => /^(application\/(pdf|vnd\.openxmlformats-officedocument\.(wordprocessingml\.document|spreadsheetml\.sheet|presentationml\.presentation)|msword|vnd\.ms-excel|vnd\.ms-powerpoint)|image\/(jpeg|jpg|png))$/.test(f.type))
    if (allowed.length !== files.length) toast.error('Hanya dokumen (PDF/Office) atau gambar (JPG/PNG) yang diizinkan')
    const next = [...attachments.value, ...allowed]
    const total = next.reduce((s, f) => s + (f?.size || 0), 0)
    if (total > 5 * 1024 * 1024) toast.error('Total ukuran lampiran maksimal 5MB')
    else attachments.value = next
  }
  if (fileInput.value) fileInput.value.value = ''
}
const onDragOver = () => { dragOver.value = true }
const onDragLeave = () => { dragOver.value = false }
const onDrop = (e: DragEvent) => {
  dragOver.value = false
  const files = Array.from(e.dataTransfer?.files || [])
  if (!files.length) return
  const allowed = files.filter((f) => /^(application\/(pdf|vnd\.openxmlformats-officedocument\.(wordprocessingml\.document|spreadsheetml\.sheet|presentationml\.presentation)|msword|vnd\.ms-excel|vnd\.ms-powerpoint)|image\/(jpeg|jpg|png))$/.test(f.type))
  if (allowed.length !== files.length) toast.error('Hanya dokumen (PDF/Office) atau gambar (JPG/PNG) yang diizinkan')
  const next = [...attachments.value, ...allowed]
  const total = next.reduce((s, f) => s + (f?.size || 0), 0)
  if (total > 5 * 1024 * 1024) toast.error('Total ukuran lampiran maksimal 5MB')
  else attachments.value = next
}
const removeAttachment = (idx: number) => { attachments.value.splice(idx, 1) }
const formatSize = (bytes?: number) => {
  if (!bytes || bytes <= 0) return '0 B'
  const mb = bytes / 1024 / 1024
  return `${mb.toFixed(2)} MB`
}

// Tags state & suggestion
const tagsInput = ref('')
const tags = ref<string[]>(Array.isArray(props.knowledge?.tags) ? props.knowledge.tags : [])
const tagSuggestions = ref<any[]>([])
let tagFetchTimer: any = null
const loadInitialTags = async () => {
  if (tagsInput.value.trim().length > 0) return
  try {
    const res = await axios.get('/api/knowledge/tags')
    tagSuggestions.value = res.data?.data || []
  } catch { tagSuggestions.value = [] }
}
const onTagInput = () => {
  const q = tagsInput.value.trim()
  if (tagFetchTimer) clearTimeout(tagFetchTimer)
  tagFetchTimer = setTimeout(async () => {
    if (q.length === 0) { tagSuggestions.value = []; return }
    try {
      const res = await axios.get('/api/knowledge/tags', { params: { q } })
      tagSuggestions.value = res.data?.data || []
    } catch { tagSuggestions.value = [] }
  }, 250)
}
const addTag = () => {
  const raw = tagsInput.value.trim()
  if (!raw) return
  raw.split(',').map(t => t.trim()).filter(Boolean).forEach((t) => {
    if (!tags.value.includes(t)) tags.value.push(t)
  })
  tagsInput.value = ''
}
const removeTag = (idx: number) => tags.value.splice(idx, 1)
const addTagFromSuggestion = (name: string) => {
  if (!name) return
  if (!tags.value.includes(name)) tags.value.push(name)
  tagsInput.value = ''
  tagSuggestions.value = []
}

// Options
const localCategories = ref<any[]>([...props.categories])
const categoryOptions = computed(() => localCategories.value.map(c => ({ value: c.id, label: c.name })))
const skpdOptions = computed(() => props.skpds.map(s => ({ value: s.id, label: s.nama_skpd })))
const statusOptions = computed(() => ([
  { value: 'draft', label: 'Draft' },
  { value: 'published', label: 'Dipublikasi' },
  { value: 'archived', label: 'Diarsipkan' },
]))

// Quick add category
const addCategoryQuick = async (label: string, setValue: (v: number|string) => void) => {
  try {
    const res = await axios.post('/api/knowledge/categories/quick-create', { name: label })
    if (res.data?.success && res.data?.data) {
      const cat = res.data.data
      const exists = localCategories.value.some((c: any) => c.id === cat.id)
      if (!exists) localCategories.value = [...localCategories.value, cat]
      setValue(cat.id)
      toast.success('Kategori ditambahkan')
    } else toast.error('Gagal menambah kategori')
  } catch (e: any) {
    toast.error(e?.response?.data?.message || 'Gagal menambah kategori')
  }
}

// Schema
const schema = yup.object({
  title: yup.string().required('Judul wajib diisi').max(255),
  description: yup.string().nullable().max(500),
  content: yup.string().required('Konten wajib diisi').min(10, 'Minimal 10 karakter'),
  category_id: yup.number().typeError('Pilih kategori').required('Kategori wajib diisi'),
  skpd_id: yup.number().typeError('Pilih SKPD').required('SKPD wajib diisi'),
  status: yup.mixed().oneOf(['draft', 'published', 'archived']).required('Status wajib diisi'),
})

// Disable submit helper
const submitting = ref(false)
const shouldDisableSubmit = (values: any, isValid: boolean) => {
  const emptyTitle = !values?.title || String(values.title).trim() === ''
  const emptyContent = !values?.content || String(values.content).trim() === ''
  const emptyCategory = !values?.category_id
  const emptySkpd = !values?.skpd_id
  const emptyStatus = !values?.status
  const emptyDesc = !values?.description || String(values.description).trim() === ''
  const emptyTags = tags.value.length === 0
  const isCompletelyEmpty = emptyTitle && emptyContent && emptyCategory && emptySkpd && emptyStatus && emptyDesc && emptyTags
  return submitting.value || !isValid || isCompletelyEmpty
}

// Auto resize
const autoResize = (e: any) => {
  const el = e?.target
  if (!el) return
  el.style.height = 'auto'
  el.style.height = el.scrollHeight + 'px'
}

// Submit
const onSubmit = async (values: any) => {
  submitting.value = true
  const leftover = (tagsInput.value || '').trim()
  if (leftover) {
    leftover.split(',').map(t => t.trim()).filter(Boolean).forEach((t) => { if (!tags.value.includes(t)) tags.value.push(t) })
    tagsInput.value = ''
  }

  const fd = new FormData()
  fd.append('title', values.title || '')
  fd.append('description', values.description || '')
  fd.append('content', values.content || '')
  fd.append('category_id', String(values.category_id || ''))
  fd.append('skpd_id', String(values.skpd_id || ''))
  fd.append('status', values.status || 'draft')
  tags.value.forEach(t => fd.append('tags[]', t))
  attachments.value.forEach(f => fd.append('attachments[]', f))
  removeIds.value.forEach(id => fd.append('remove_attachment_ids[]', String(id)))
  fd.append('_method', 'PUT')

  router.post(`/knowledge/${props.knowledge.id}`, fd, {
    forceFormData: true,
    onSuccess: () => {
      toast.success('Berhasil mengubah pengetahuan')
      window.scrollTo({ top: 0, behavior: 'auto' })
    },
    onFinish: () => { submitting.value = false }
  })
}
</script>

