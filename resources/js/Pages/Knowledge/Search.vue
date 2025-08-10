<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Cari Pengetahuan</h1>
          <p class="text-gray-600 mt-2">Temukan pengetahuan yang Anda butuhkan</p>
        </div>

        <!-- Search Form -->
        <div class="bg-white shadow rounded-lg mb-8">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Form Pencarian</h3>
          </div>
          <div class="p-6">
            <form @submit.prevent="performSearch" class="space-y-6">
              <!-- Basic Search -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label for="query" class="block text-sm font-medium text-gray-700 mb-2">
                    Kata Kunci
                  </label>
                  <input
                    id="query"
                    v-model="searchForm.query"
                    type="text"
                    placeholder="Masukkan kata kunci pencarian..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>

                <div>
                  <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                    Kategori
                  </label>
                  <select
                    id="category"
                    v-model="searchForm.category"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
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
                  <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status
                  </label>
                  <select
                    id="status"
                    v-model="searchForm.status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="">Semua Status</option>
                    <option value="published">Dipublikasi</option>
                    <option value="draft">Draft</option>
                    <option value="archived">Diarsipkan</option>
                  </select>
                </div>
              </div>

              <!-- Advanced Search -->
              <div>
                <button
                  type="button"
                  @click="toggleAdvancedSearch"
                  class="flex items-center text-sm text-indigo-600 hover:text-indigo-500"
                >
                  <svg
                    :class="advancedSearch ? 'rotate-90' : ''"
                    class="w-4 h-4 mr-2 transition-transform duration-200"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                  Pencarian Lanjutan
                </button>

                <div v-if="advancedSearch" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label for="dateFrom" class="block text-sm font-medium text-gray-700 mb-2">
                      Dari Tanggal
                    </label>
                    <input
                      id="dateFrom"
                      v-model="searchForm.dateFrom"
                      type="date"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>

                  <div>
                    <label for="dateTo" class="block text-sm font-medium text-gray-700 mb-2">
                      Sampai Tanggal
                    </label>
                    <input
                      id="dateTo"
                      v-model="searchForm.dateTo"
                      type="date"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>

                  <div>
                    <label for="author" class="block text-sm font-medium text-gray-700 mb-2">
                      Penulis
                    </label>
                    <input
                      id="author"
                      v-model="searchForm.author"
                      type="text"
                      placeholder="Nama penulis..."
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    />
                  </div>
                </div>
              </div>

              <!-- Search Actions -->
              <div class="flex justify-between items-center">
                <button
                  type="button"
                  @click="clearSearch"
                  class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Bersihkan
                </button>

                <button
                  type="submit"
                  :disabled="searching"
                  class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                >
                  <svg
                    v-if="searching"
                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ searching ? 'Mencari...' : 'Cari' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Search Results -->
        <div v-if="hasSearched">
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">
                  Hasil Pencarian
                  <span v-if="searchResults.length > 0" class="text-sm font-normal text-gray-500 ml-2">
                    ({{ searchResults.length }} hasil)
                  </span>
                </h3>

                <div v-if="searchResults.length > 0" class="flex items-center space-x-4">
                  <label for="sortBy" class="text-sm font-medium text-gray-700">Urutkan:</label>
                  <select
                    id="sortBy"
                    v-model="sortBy"
                    @change="applyFilters"
                    class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="relevance">Relevansi</option>
                    <option value="newest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="title">Judul A-Z</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="p-6">
              <!-- No Results -->
              <div v-if="searchResults.length === 0 && !searching" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada hasil</h3>
                <p class="mt-1 text-sm text-gray-500">
                  Coba ubah kata kunci atau filter pencarian Anda.
                </p>
              </div>

              <!-- Search Results List -->
              <div v-else-if="searchResults.length > 0" class="space-y-6">
                <div
                  v-for="result in searchResults"
                  :key="result.id"
                  class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
                >
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <!-- Status and Category -->
                      <div class="flex items-center space-x-3 mb-2">
                        <span
                          :class="getStatusBadgeClass(result.status)"
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        >
                          {{ getStatusText(result.status) }}
                        </span>
                        <span class="text-sm text-gray-500">{{ result.category }}</span>
                      </div>

                      <h3 class="text-lg font-medium text-gray-900 mb-2">
                        <Link
                          :href="route('knowledge.show', result.id)"
                          class="hover:text-indigo-600 transition-colors"
                        >
                          {{ result.title }}
                        </Link>
                      </h3>

                      <p v-if="result.description" class="text-gray-600 mb-3 line-clamp-2">
                        {{ result.description }}
                      </p>

                      <div class="flex items-center text-sm text-gray-500 space-x-4">
                        <span class="flex items-center">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                          </svg>
                          {{ result.author?.name || 'Unknown' }}
                        </span>
                        <span class="flex items-center">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 6v6m-4-6h8m-8 6h8"></path>
                          </svg>
                          {{ formatDate(result.created_at) }}
                        </span>
                      </div>

                      <!-- Tags -->
                      <div v-if="result.tags && result.tags.length > 0" class="mt-3">
                        <div class="flex flex-wrap gap-2">
                          <span
                            v-for="tag in result.tags"
                            :key="tag"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                          >
                            {{ tag }}
                          </span>
                        </div>
                      </div>
                    </div>

                    <div class="ml-4 flex-shrink-0">
                      <Link
                        :href="route('knowledge.show', result.id)"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                      >
                        Lihat Detail
                      </Link>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Pagination -->
              <div v-if="searchResults.length > 0" class="mt-8">
                <!-- Pagination controls would go here -->
                <div class="text-center text-sm text-gray-500">
                  Pagination akan diimplementasikan nanti
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Search Suggestions -->
        <div v-else class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Saran Pencarian</h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                <h4 class="font-medium text-gray-900 mb-2">Teknologi</h4>
                <p class="text-sm text-gray-600">Cari artikel tentang teknologi terbaru</p>
              </div>
              <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                <h4 class="font-medium text-gray-900 mb-2">Bisnis</h4>
                <p class="text-sm text-gray-600">Strategi dan tips bisnis</p>
              </div>
              <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                <h4 class="font-medium text-gray-900 mb-2">Kesehatan</h4>
                <p class="text-sm text-gray-600">Informasi kesehatan dan gaya hidup</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const searching = ref(false);
const hasSearched = ref(false);
const advancedSearch = ref(false);
const sortBy = ref('relevance');

const searchForm = reactive({
  query: '',
  category: '',
  status: '',
  dateFrom: '',
  dateTo: '',
  author: ''
});

const searchResults = ref([]);

const performSearch = () => {
  searching.value = true;
  hasSearched.value = true;

  const searchParams = {
    ...searchForm,
    sort: sortBy.value
  };

  router.get(route('knowledge.search'), searchParams, {
    preserveState: true,
    onSuccess: (page) => {
      searchResults.value = page.props.searchResults || [];
      searching.value = false;
    },
    onError: () => {
      searching.value = false;
    }
  });
};

const clearSearch = () => {
  Object.keys(searchForm).forEach(key => {
    searchForm[key] = '';
  });
  sortBy.value = 'relevance';
  searchResults.value = [];
  hasSearched.value = false;
};

const toggleAdvancedSearch = () => {
  advancedSearch.value = !advancedSearch.value;
};

const applyFilters = () => {
  if (hasSearched.value) {
    performSearch();
  }
};

const getStatusBadgeClass = (status) => {
  const classes = {
    published: 'bg-green-100 text-green-800',
    draft: 'bg-yellow-100 text-yellow-800',
    archived: 'bg-gray-100 text-gray-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
  const texts = {
    published: 'Dipublikasi',
    draft: 'Draft',
    archived: 'Diarsipkan'
  };
  return texts[status] || status;
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

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
