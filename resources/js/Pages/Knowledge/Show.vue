<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="mb-6">
          <div class="flex items-center justify-between">
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
                <h1 class="text-3xl font-bold text-gray-900">{{ knowledge.title }}</h1>
                <div class="flex items-center mt-2 space-x-4">
                  <span
                    :class="getStatusBadgeClass(knowledge.status)"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  >
                    {{ getStatusText(knowledge.status) }}
                  </span>
                  <span class="text-gray-600">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    {{ knowledge.category }}
                  </span>
                </div>
              </div>
            </div>
            <div class="flex space-x-3">
              <Link
                :href="route('knowledge.edit', knowledge.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
              </Link>
              <button
                @click="deleteKnowledge"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Hapus
              </button>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
          <!-- Meta Information -->
          <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-wrap items-center text-sm text-gray-600 space-x-6">
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Oleh: {{ knowledge.author?.name || 'Unknown' }}</span>
              </div>
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Dibuat: {{ formatDate(knowledge.created_at) }}</span>
              </div>
              <div v-if="knowledge.updated_at !== knowledge.created_at" class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Diupdate: {{ formatDate(knowledge.updated_at) }}</span>
              </div>
            </div>
          </div>

          <!-- Description -->
          <div v-if="knowledge.description" class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Deskripsi</h3>
            <p class="text-gray-700">{{ knowledge.description }}</p>
          </div>

          <!-- Tags -->
          <div v-if="knowledge.tags && knowledge.tags.length > 0" class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tags</h3>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="tag in knowledge.tags"
                :key="tag"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800"
              >
                {{ tag }}
              </span>
            </div>
          </div>

          <!-- Main Content -->
          <div class="px-6 py-6">
            <div class="prose max-w-none">
              <div class="whitespace-pre-wrap text-gray-700 leading-relaxed">{{ knowledge.content }}</div>
            </div>
          </div>
        </div>

        <!-- Related Knowledge -->
        <div v-if="relatedKnowledge && relatedKnowledge.length > 0" class="mt-8">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Pengetahuan Terkait</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="item in relatedKnowledge"
              :key="item.id"
              class="bg-white rounded-lg shadow p-4 hover:shadow-md transition-shadow"
            >
              <h4 class="font-medium text-gray-900 mb-2">
                <Link :href="route('knowledge.show', item.id)" class="hover:text-indigo-600">
                  {{ item.title }}
                </Link>
              </h4>
              <p class="text-sm text-gray-600 mb-2">{{ item.description }}</p>
              <div class="flex items-center justify-between text-xs text-gray-500">
                <span>{{ item.category }}</span>
                <span>{{ formatDate(item.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
  knowledge: {
    type: Object,
    required: true
  },
  relatedKnowledge: {
    type: Array,
    default: () => []
  }
});

const getStatusBadgeClass = (status) => {
  const classes = {
    draft: 'bg-yellow-100 text-yellow-800',
    published: 'bg-green-100 text-green-800',
    archived: 'bg-gray-100 text-gray-800'
  };
  return classes[status] || 'bg-blue-100 text-blue-800';
};

const getStatusText = (status) => {
  const texts = {
    draft: 'Draft',
    published: 'Dipublikasi',
    archived: 'Diarsipkan'
  };
  return texts[status] || 'Tidak Diketahui';
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const deleteKnowledge = () => {
  if (confirm('Apakah Anda yakin ingin menghapus pengetahuan ini?')) {
    router.delete(route('knowledge.destroy', props.knowledge.id));
  }
};
</script>

