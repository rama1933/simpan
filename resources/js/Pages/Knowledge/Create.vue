<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center">
            <Link
              :href="route('knowledge.index')"
              class="text-indigo-600 hover:text-indigo-500 mr-4"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </Link>
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Tambah Pengetahuan Baru</h1>
              <p class="text-gray-600 mt-2">Buat pengetahuan baru untuk sistem manajemen pengetahuan</p>
            </div>
          </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
          <form @submit.prevent="submitForm" class="p-6 space-y-6">
            <!-- Title -->
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                Judul <span class="text-red-500">*</span>
              </label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                :class="{ 'border-red-500': errors.title }"
                placeholder="Masukkan judul pengetahuan"
              />
              <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
            </div>

            <!-- Description -->
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Deskripsi Singkat
              </label>
              <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                :class="{ 'border-red-500': errors.description }"
                placeholder="Masukkan deskripsi singkat pengetahuan"
              ></textarea>
              <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
            </div>

            <!-- Content -->
            <div>
              <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                Konten <span class="text-red-500">*</span>
              </label>
              <textarea
                id="content"
                v-model="form.content"
                rows="10"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                :class="{ 'border-red-500': errors.content }"
                placeholder="Masukkan konten lengkap pengetahuan"
              ></textarea>
              <p v-if="errors.content" class="mt-1 text-sm text-red-600">{{ errors.content }}</p>
            </div>

            <!-- Category -->
            <div>
              <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                Kategori <span class="text-red-500">*</span>
              </label>
              <select
                id="category"
                v-model="form.category"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                :class="{ 'border-red-500': errors.category }"
              >
                <option value="">Pilih Kategori</option>
                <option value="Teknologi">Teknologi</option>
                <option value="Bisnis">Bisnis</option>
                <option value="Kesehatan">Kesehatan</option>
                <option value="Pendidikan">Pendidikan</option>
                <option value="Seni & Budaya">Seni & Budaya</option>
                <option value="Olahraga">Olahraga</option>
                <option value="Sains">Sains</option>
                <option value="Lainnya">Lainnya</option>
              </select>
              <p v-if="errors.category" class="mt-1 text-sm text-red-600">{{ errors.category }}</p>
            </div>

            <!-- Tags -->
            <div>
              <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                Tags
              </label>
              <input
                id="tags"
                v-model="tagsInput"
                type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Masukkan tags dipisahkan dengan koma (contoh: Laravel, PHP, Web)"
                @keydown.enter.prevent="addTag"
              />
              <p class="mt-1 text-sm text-gray-500">Tekan Enter untuk menambah tag</p>
              <div v-if="form.tags && form.tags.length > 0" class="mt-2 flex flex-wrap gap-2">
                <span
                  v-for="(tag, index) in form.tags"
                  :key="index"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800"
                >
                  {{ tag }}
                  <button
                    type="button"
                    @click="removeTag(index)"
                    class="ml-2 text-indigo-600 hover:text-indigo-800"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </span>
              </div>
              <p v-if="errors.tags" class="mt-1 text-sm text-red-600">{{ errors.tags }}</p>
            </div>

            <!-- Status -->
            <div>
              <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                Status <span class="text-red-500">*</span>
              </label>
              <select
                id="status"
                v-model="form.status"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                :class="{ 'border-red-500': errors.status }"
              >
                <option value="draft">Draft</option>
                <option value="published">Dipublikasi</option>
                <option value="archived">Diarsipkan</option>
              </select>
              <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</p>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
              <Link
                :href="route('knowledge.index')"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Batal
              </Link>
              <button
                type="submit"
                :disabled="processing"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="processing">Menyimpan...</span>
                <span v-else>Simpan Pengetahuan</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  errors: {
    type: Object,
    default: () => ({})
  }
});

const form = useForm({
  title: '',
  description: '',
  content: '',
  category: '',
  tags: [],
  status: 'draft'
});

const tagsInput = ref('');
const processing = ref(false);

const addTag = () => {
  const tag = tagsInput.value.trim();
  if (tag && !form.tags.includes(tag)) {
    form.tags.push(tag);
    tagsInput.value = '';
  }
};

const removeTag = (index) => {
  form.tags.splice(index, 1);
};

const submitForm = () => {
  processing.value = true;
  form.post(route('knowledge.store'), {
    onSuccess: () => {
      processing.value = false;
    },
    onError: () => {
      processing.value = false;
    }
  });
};
</script>

