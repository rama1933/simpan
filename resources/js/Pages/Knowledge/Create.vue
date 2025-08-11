<template>
  <AdminLayout page-title="Tambah Pengetahuan" :user="user">
    <div class="min-h-screen bg-gray-50">
      <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
          <!-- Flash Success -->
          <div v-if="$page.props.flash?.success" class="mb-4">
            <div class="rounded-md bg-green-50 p-4 border border-green-200">
              <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
            </div>
          </div>

          <!-- Header -->
          <div class="mb-6 flex items-center">
            <Link href="/knowledge" class="text-indigo-600 hover:text-indigo-500 mr-4">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </Link>
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Tambah Pengetahuan Baru</h1>
              <p class="text-gray-600 mt-2">Buat pengetahuan baru untuk sistem manajemen pengetahuan</p>
            </div>
          </div>

          <!-- Form -->
          <div class="bg-white shadow rounded-lg">
            <Form :validation-schema="schema" @submit="onSubmit" class="p-6 space-y-6">
              <!-- Title -->
              <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul <span class="text-red-500">*</span></label>
                <Field name="title" v-slot="{ field, meta }">
                  <input v-bind="field" id="title" type="text" class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 border transition-colors" :class="meta.touched && meta.invalid ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'" placeholder="Masukkan judul pengetahuan" />
                </Field>
                <ErrorMessage name="title" class="mt-1 text-sm text-red-600" />
              </div>

              <!-- Description -->
              <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Singkat</label>
                <Field name="description" v-slot="{ field, meta }">
                  <textarea v-bind="field" id="description" rows="3" class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 border transition-colors" :class="meta.touched && meta.invalid ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'" placeholder="Masukkan deskripsi singkat pengetahuan"></textarea>
                </Field>
                <ErrorMessage name="description" class="mt-1 text-sm text-red-600" />
              </div>

              <!-- Content -->
              <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Konten <span class="text-red-500">*</span></label>
                <Field name="content" v-slot="{ field, meta }">
                  <textarea v-bind="field" id="content" rows="10" class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 border transition-colors" :class="meta.touched && meta.invalid ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'" placeholder="Masukkan konten lengkap pengetahuan"></textarea>
                </Field>
                <ErrorMessage name="content" class="mt-1 text-sm text-red-600" />
              </div>

              <!-- Category -->
              <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                <Field name="category_id" v-slot="{ field, meta }">
                  <select v-bind="field" id="category_id" class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 border transition-colors bg-white" :class="meta.touched && meta.invalid ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'">
                    <option value="">Pilih Kategori</option>
                    <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                  </select>
                </Field>
                <ErrorMessage name="category_id" class="mt-1 text-sm text-red-600" />
              </div>

              <!-- SKPD -->
              <div>
                <label for="skpd_id" class="block text-sm font-medium text-gray-700 mb-2">SKPD <span class="text-red-500">*</span></label>
                <Field name="skpd_id" v-slot="{ field, meta }">
                  <select v-bind="field" id="skpd_id" class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 border transition-colors bg-white" :class="meta.touched && meta.invalid ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'">
                    <option value="">Pilih SKPD</option>
                    <option v-for="s in skpds" :key="s.id" :value="s.id">{{ s.nama_skpd }}</option>
                  </select>
                </Field>
                <ErrorMessage name="skpd_id" class="mt-1 text-sm text-red-600" />
              </div>

              <!-- Tags -->
              <div>
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                <div class="space-y-2">
                  <input v-model="tagsInput" id="tags" type="text" class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 border transition-colors hover:border-gray-400" placeholder="Masukkan tags dipisahkan dengan koma (contoh: Laravel, PHP, Web)" @keydown.enter.prevent="addTag" />
                  <div v-if="tags.length > 0" class="flex flex-wrap gap-2">
                    <span v-for="(tag, index) in tags" :key="index" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                      {{ tag }}
                      <button type="button" @click="removeTag(index)" class="ml-2 text-indigo-600 hover:text-indigo-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                      </button>
                    </span>
                  </div>
                </div>
              </div>

              <!-- Status -->
              <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                <Field name="status" v-slot="{ field, meta }">
                  <select v-bind="field" id="status" class="w-full px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 border transition-colors bg-white" :class="meta.touched && meta.invalid ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'">
                    <option value="draft">Draft</option>
                    <option value="published">Dipublikasi</option>
                    <option value="archived">Diarsipkan</option>
                  </select>
                </Field>
                <ErrorMessage name="status" class="mt-1 text-sm text-red-600" />
              </div>

              <!-- Actions -->
              <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <Link href="/knowledge" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Batal</Link>
                <button type="submit" :disabled="submitting" class="px-5 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                  <span v-if="submitting">Menyimpan...</span>
                  <span v-else>Simpan Pengetahuan</span>
                </button>
              </div>
            </Form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Form, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'
import { toast } from 'vue3-toastify'

const props = defineProps({
  categories: { type: Array, default: () => [] },
  skpds: { type: Array, default: () => [] },
  user: { type: Object, default: null }
})

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
const submitting = ref(false)

// Local state for tags
const tagsInput = ref('')
const tags = ref([])
const addTag = () => {
  const raw = tagsInput.value.trim()
  if (!raw) return
  raw
    .split(',')
    .map((t) => t.trim())
    .filter(Boolean)
    .forEach((tag) => {
      if (!tags.value.includes(tag)) tags.value.push(tag)
    })
  tagsInput.value = ''
}
const removeTag = (idx) => {
  tags.value.splice(idx, 1)
}

// Yup schema
const schema = yup.object({
  title: yup.string().required('Judul wajib diisi').max(255),
  description: yup.string().nullable().max(500),
  content: yup.string().required('Konten wajib diisi').min(10, 'Minimal 10 karakter'),
  category_id: yup
    .number()
    .typeError('Pilih kategori')
    .required('Kategori wajib diisi'),
  skpd_id: yup.number().typeError('Pilih SKPD').required('SKPD wajib diisi'),
  status: yup.mixed().oneOf(['draft', 'published', 'archived']).required('Status wajib diisi')
})

// Submit handler
const onSubmit = async (values) => {
  submitting.value = true
  const payload = { ...values, tags: tags.value }
  router.post('/knowledge', payload, {
    onSuccess: () => {
      toast.success('Pengetahuan berhasil disimpan')
      window.scrollTo({ top: 0, behavior: 'auto' })
    },
    onFinish: () => { submitting.value = false }
  })
}
</script>

