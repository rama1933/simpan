<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Statistik Pengetahuan</h1>
              <p class="text-gray-600 mt-2">Analisis mendalam dan laporan statistik</p>
            </div>
            <div class="flex space-x-3">
              <button
                @click="exportToCSV"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Ekspor CSV
              </button>
              <button
                @click="exportToPDF"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Ekspor PDF
              </button>
            </div>
          </div>
        </div>

        <!-- Overview Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Knowledge -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Pengetahuan</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ statistics.total }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Published Knowledge -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Dipublikasi</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ statistics.published }}</dd>
                    <dd class="text-sm text-gray-500">{{ getPercentage(statistics.published, statistics.total) }}%</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Draft Knowledge -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Draft</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ statistics.draft }}</dd>
                    <dd class="text-sm text-gray-500">{{ getPercentage(statistics.draft, statistics.total) }}%</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Archived Knowledge -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-gray-100 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Diarsipkan</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ statistics.archived }}</dd>
                    <dd class="text-sm text-gray-500">{{ getPercentage(statistics.archived, statistics.total) }}%</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Detailed Statistics Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
          <!-- Category Distribution -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Distribusi Kategori</h3>
            </div>
            <div class="p-6">
              <div class="space-y-4">
                <div
                  v-for="category in categoryDistribution"
                  :key="category.name"
                  class="flex items-center justify-between"
                >
                  <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full mr-3" :style="{ backgroundColor: category.color }"></div>
                    <span class="text-sm font-medium text-gray-700">{{ category.name }}</span>
                  </div>
                  <div class="flex items-center space-x-3">
                    <div class="w-32 bg-gray-200 rounded-full h-2">
                      <div
                        class="h-2 rounded-full"
                        :style="{
                          width: category.percentage + '%',
                          backgroundColor: category.color
                        }"
                      ></div>
                    </div>
                    <span class="text-sm text-gray-500 w-12 text-right">{{ category.count }}</span>
                    <span class="text-sm text-gray-400 w-12 text-right">{{ category.percentage }}%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Status Distribution -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Distribusi Status</h3>
            </div>
            <div class="p-6">
              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-gray-700">Dipublikasi</span>
                  </div>
                  <div class="flex items-center space-x-3">
                    <div class="w-32 bg-gray-200 rounded-full h-2">
                      <div
                        class="bg-green-500 h-2 rounded-full"
                        :style="{ width: getPercentage(statistics.published, statistics.total) + '%' }"
                      ></div>
                    </div>
                    <span class="text-sm text-gray-500 w-12 text-right">{{ statistics.published }}</span>
                    <span class="text-sm text-gray-400 w-12 text-right">{{ getPercentage(statistics.published, statistics.total) }}%</span>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <div class="w-4 h-4 bg-yellow-500 rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-gray-700">Draft</span>
                  </div>
                  <div class="flex items-center space-x-3">
                    <div class="w-32 bg-gray-200 rounded-full h-2">
                      <div
                        class="bg-yellow-500 h-2 rounded-full"
                        :style="{ width: getPercentage(statistics.draft, statistics.total) + '%' }"
                      ></div>
                    </div>
                    <span class="text-sm text-gray-500 w-12 text-right">{{ statistics.draft }}</span>
                    <span class="text-sm text-gray-400 w-12 text-right">{{ getPercentage(statistics.draft, statistics.total) }}%</span>
                  </div>
                </div>

                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <div class="w-4 h-4 bg-gray-500 rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-gray-700">Diarsipkan</span>
                  </div>
                  <div class="flex items-center space-x-3">
                    <div class="w-32 bg-gray-200 rounded-full h-2">
                      <div
                        class="bg-gray-500 h-2 rounded-full"
                        :style="{ width: getPercentage(statistics.archived, statistics.total) + '%' }"
                      ></div>
                    </div>
                    <span class="text-sm text-gray-500 w-12 text-right">{{ statistics.archived }}</span>
                    <span class="text-sm text-gray-400 w-12 text-right">{{ getPercentage(statistics.archived, statistics.total) }}%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Author Statistics -->
        <div class="bg-white shadow rounded-lg mb-8">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Statistik Penulis</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Penulis
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Total Artikel
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Dipublikasi
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Draft
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Diarsipkan
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    % dari Total
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr
                  v-for="author in authorStatistics"
                  :key="author.id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                          <span class="text-sm font-medium text-indigo-600">
                            {{ getInitials(author.name) }}
                          </span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ author.name }}</div>
                        <div class="text-sm text-gray-500">{{ author.email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ author.total }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ author.published }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ author.draft }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ author.archived }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ getPercentage(author.total, statistics.total) }}%
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Monthly Trends -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Tren Bulanan</h3>
            <p class="text-sm text-gray-500 mt-1">Jumlah pengetahuan yang dibuat per bulan</p>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div
                v-for="month in monthlyTrends"
                :key="month.month"
                class="flex items-center justify-between"
              >
                <div class="flex items-center">
                  <span class="text-sm font-medium text-gray-700 w-24">{{ month.month }}</span>
                </div>
                <div class="flex items-center space-x-3">
                  <div class="w-48 bg-gray-200 rounded-full h-3">
                    <div
                      class="bg-indigo-600 h-3 rounded-full"
                      :style="{ width: getTrendPercentage(month.count) + '%' }"
                    ></div>
                  </div>
                  <span class="text-sm text-gray-500 w-12 text-right">{{ month.count }}</span>
                  <span class="text-sm text-gray-400 w-12 text-right">{{ getTrendPercentage(month.count) }}%</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  statistics: {
    type: Object,
    default: () => ({
      total: 0,
      published: 0,
      draft: 0,
      archived: 0
    })
  },
  categoryDistribution: {
    type: Array,
    default: () => []
  },
  authorStatistics: {
    type: Array,
    default: () => []
  },
  monthlyTrends: {
    type: Array,
    default: () => []
  }
});

const getPercentage = (value, total) => {
  if (total === 0) return 0;
  return Math.round((value / total) * 100);
};

const getTrendPercentage = (count) => {
  if (props.monthlyTrends.length === 0) return 0;
  const maxCount = Math.max(...props.monthlyTrends.map(m => m.count));
  if (maxCount === 0) return 0;
  return Math.round((count / maxCount) * 100);
};

const getInitials = (name) => {
  if (!name) return '?';
  return name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2);
};

const exportToCSV = () => {
  // Simulate CSV export
  alert('Ekspor CSV akan segera tersedia!');
};

const exportToPDF = () => {
  // Simulate PDF export
  alert('Ekspor PDF akan segera tersedia!');
};
</script>
