<template>
  <AdminLayout page-title="Edit Pengetahuan" :user="user">
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Edit: {{ form.title }}</h1>
      <Link :href="`/knowledge/${knowledge.id}`" class="px-3 py-2 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">Kembali</Link>
    </div>

    <form @submit.prevent="onSubmit" class="space-y-6">
      <div class="bg-white rounded-lg border p-6 grid grid-cols-1 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
          <input v-model="form.title" type="text" class="w-full px-3 py-2 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-500" required />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
          <textarea v-model="form.description" rows="3" class="w-full px-3 py-2 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-500"></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Konten</label>
          <textarea v-model="form.content" rows="12" class="w-full px-3 py-2 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-500" required></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
            <VueSelect v-model="form.category_id" :options="categoryOptions" placeholder="Pilih kategori..." />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">SKPD</label>
            <VueSelect v-model="form.skpd_id" :options="skpdOptions" placeholder="Pilih SKPD..." />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <VueSelect v-model="form.status" :options="statusOptions" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
          <div class="flex flex-wrap gap-2 mb-2">
            <span v-for="(t, i) in form.tags" :key="i" class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 px-2 py-1 rounded-full text-xs">
              {{ t }}
              <button type="button" @click="removeTag(i)" class="text-indigo-600 hover:text-indigo-800">×</button>
            </span>
          </div>
          <input v-model="tagInput" @keydown.enter.prevent="addTag" type="text" placeholder="Ketik tag lalu Enter" class="w-full px-3 py-2 rounded-md border border-gray-300 focus:ring-2 focus:ring-indigo-500" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Lampiran (opsional) — Dokumen/Gambar, total maks 5MB</label>
          <input ref="fileInput" type="file" multiple class="hidden" @change="onFilesSelected" />
          <div @click="() => fileInput?.click()" class="border-2 border-dashed rounded-lg p-4 text-center cursor-pointer bg-white hover:bg-gray-50">
            <p class="text-sm text-gray-600">Klik untuk pilih file atau seret ke sini</p>
          </div>
          <ul class="mt-2 text-sm text-gray-700 list-disc pl-5">
            <li v-for="(f, idx) in attachments" :key="idx" class="flex items-center justify-between">
              <span>{{ f.name }} ({{ (f.size/1024).toFixed(1) }} KB)</span>
              <button type="button" @click="removeFile(idx)" class="text-rose-600 hover:text-rose-800">Hapus</button>
            </li>
          </ul>
        </div>
      </div>

      <div class="flex items-center justify-end gap-3">
        <Link :href="`/knowledge/${knowledge.id}`" class="px-4 py-2 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">Batal</Link>
        <button :disabled="submitting" type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-60">
          <span v-if="submitting" class="animate-spin inline-block h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
          Simpan Perubahan
        </button>
      </div>
    </form>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import VueSelect from '@/Components/VueSelect.vue'
import { toast } from 'vue3-toastify'

const props = defineProps<{ knowledge: any, categories: any[], skpds: any[] }>()
const user = null

const statusOptions = [
  { value: 'draft', label: 'Draft' },
  { value: 'published', label: 'Published' },
  { value: 'archived', label: 'Archived' }
]

const categoryOptions = computed(() => props.categories.map(c => ({ value: c.id, label: c.name })))
const skpdOptions = computed(() => props.skpds.map(s => ({ value: s.id, label: s.nama_skpd })))

const form = ref({
  title: props.knowledge.title,
  description: props.knowledge.description,
  content: props.knowledge.content,
  category_id: props.knowledge.category_id,
  skpd_id: props.knowledge.skpd_id,
  status: props.knowledge.status,
  tags: Array.isArray(props.knowledge.tags) ? props.knowledge.tags : []
})

const tagInput = ref('')
const addTag = () => {
  const v = tagInput.value.trim()
  if (!v) return
  if (!form.value.tags.includes(v)) form.value.tags.push(v)
  tagInput.value = ''
}
const removeTag = (i: number) => form.value.tags.splice(i, 1)

const fileInput = ref<HTMLInputElement | null>(null)
const attachments = ref<File[]>([])
const onFilesSelected = (e: Event) => {
  const files = Array.from((e.target as HTMLInputElement).files || [])
  if (!files.length) return
  const next = [...attachments.value, ...files]
  const total = next.reduce((s, f) => s + (f?.size || 0), 0)
  if (total > 5 * 1024 * 1024) {
    return toast.error('Total ukuran lampiran maksimal 5MB')
  }
  const allowed = ['application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation','image/jpeg','image/png']
  if (next.some(f => !allowed.includes(f.type))) {
    return toast.error('Hanya dokumen/gambar yang diperbolehkan')
  }
  attachments.value = next
  if (fileInput.value) fileInput.value.value = ''
}
const removeFile = (i: number) => attachments.value.splice(i, 1)

const submitting = ref(false)
const onSubmit = async () => {
  submitting.value = true
  try {
    const fd = new FormData()
    Object.entries(form.value).forEach(([k, v]) => {
      if (k === 'tags') {
        (v as string[]).forEach((t, idx) => fd.append('tags['+idx+']', t))
      } else {
        fd.append(k, String(v ?? ''))
      }
    })
    attachments.value.forEach(f => fd.append('attachments[]', f))
    fd.append('_method', 'PUT')

    await router.post(`/knowledge/${props.knowledge.id}`, fd, { forceFormData: true })
    toast.success('Berhasil mengubah pengetahuan')
  } catch (e) {
    toast.error('Gagal menyimpan perubahan')
  } finally {
    submitting.value = false
  }
}
</script>

