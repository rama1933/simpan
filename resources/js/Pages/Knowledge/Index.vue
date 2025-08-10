<template>
  <AdminLayout page-title="Knowledge Management" :user="user">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Pengetahuan</h1>
            <p class="text-gray-600 mt-2">Kelola dan atur semua pengetahuan dalam sistem</p>
          </div>
          <Link
            :href="route('knowledge.create')"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Pengetahuan
          </Link>
        </div>

        <!-- Filters -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Cari pengetahuan..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                @input="debounceSearch"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
              <select
                v-model="filters.category"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <option value="">Semua Kategori</option>
                <option value="Teknologi">Teknologi</option>
                <option value="Bisnis">Bisnis</option>
                <option value="Kesehatan">Kesehatan</option>
                <option value="Pendidikan">Pendidikan</option>
                <option value="Seni & Budaya">Seni & Budaya</option>
                <option value="Olahraga">Olahraga</option>
                <option value="Sains">Sains</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select
                v-model="filters.status"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
              >
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="published">Dipublikasi</option>
                <option value="archived">Diarsipkan</option>
              </select>
            </div>
            <div class="flex items-end">
              <button
                @click="applyFilters"
                class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Terapkan Filter
              </button>
            </div>
          </div>
        </div>

        <!-- Knowledge Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Judul
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Kategori
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Penulis
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tanggal
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Aksi
                  </th>
                </tr>
              </thead>
              <tbody v-if="knowledge && knowledge.data && knowledge.data.length > 0" class="bg-white divide-y divide-gray-200">
                <tr v-for="item in knowledge.data" :key="item.id" class="hover:bg-gray-50 transition-colors duration-150">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                          <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                          </svg>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ item.title }}</div>
                        <div class="text-sm text-gray-500 truncate max-w-xs">{{ item.description }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="item.category" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ item.category.name }}
                    </span>
                    <span v-else class="text-gray-400">-</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      item.status === 'published' ? 'bg-green-100 text-green-800' : '',
                      item.status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '',
                      item.status === 'archived' ? 'bg-gray-100 text-gray-800' : ''
                    ]">
                      {{ item.status === 'published' ? 'Dipublikasi' : item.status === 'draft' ? 'Draft' : 'Diarsipkan' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ item.author ? item.author.name : '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(item.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <Link :href="route('knowledge.show', item.id)" class="text-indigo-600 hover:text-indigo-900">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                      </Link>
                      <Link :href="route('knowledge.edit', item.id)" class="text-yellow-600 hover:text-yellow-900">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </Link>
                      <button
                        @click="deleteKnowledge(item.id)"
                        class="text-red-600 hover:text-red-900"
                      >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Empty State -->
          <div v-if="!knowledge || !knowledge.data || knowledge.data.length === 0" class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pengetahuan</h3>
            <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan pengetahuan baru.</p>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="knowledge.links && knowledge.links.length > 1" class="mt-6">
          <nav class="flex justify-center">
            <div class="flex space-x-1">
              <Link
                v-for="(link, index) in knowledge.links"
                :key="index"
                :href="link.url || '#'"
                :class="[
                  'px-3 py-2 text-sm font-medium rounded-md',
                  !link.url || link.url === '#'
                    ? 'text-gray-400 cursor-not-allowed'
                    : link.active === true
                    ? 'bg-indigo-600 text-white'
                    : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'
                ]"
                v-html="link.label || ''"
              />
            </div>
          </nav>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { route } from '@/core/helpers/route';
// import { debounce } from 'lodash';

const props = defineProps({
  knowledge: {
    type: Object,
    default: () => ({ data: [], links: [] })
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  perPage: {
    type: Number,
    default: 15
  },
  user: {
    type: Object,
    default: null
  }
});

const filters = ref({
  search: props.filters.search || '',
  category: props.filters.category || '',
  status: props.filters.status || ''
});

const debounceSearch = (() => {
  let timeout;
  return () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      applyFilters();
    }, 500);
  };
})();

const applyFilters = () => {
  router.get(route('knowledge.index'), filters.value, {
    preserveState: true,
    replace: true
  });
};

const deleteKnowledge = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus pengetahuan ini?')) {
    router.delete(route('knowledge.destroy', id));
  }
};

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
    day: 'numeric'
  });
};
</script>
