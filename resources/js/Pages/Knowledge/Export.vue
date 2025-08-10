<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Ekspor Data</h1>
          <p class="text-gray-600 mt-2">Ekspor data pengetahuan dalam berbagai format</p>
        </div>

        <!-- Export Options Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
          <!-- Export to CSV -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Ekspor ke CSV</h3>
              <p class="text-sm text-gray-500 mt-1">Unduh data dalam format spreadsheet</p>
            </div>
            <div class="p-6">
              <form @submit.prevent="exportToCSV" class="space-y-4">
                <!-- Data Scope -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Cakupan Data
                  </label>
                  <div class="space-y-2">
                    <label class="flex items-center">
                      <input
                        type="radio"
                        v-model="csvExport.scope"
                        value="all"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      />
                      <span class="ml-2 text-sm text-gray-700">Semua Data</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        type="radio"
                        v-model="csvExport.scope"
                        value="published"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      />
                      <span class="ml-2 text-sm text-gray-700">Hanya yang Dipublikasi</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        type="radio"
                        v-model="csvExport.scope"
                        value="draft"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      />
                      <span class="ml-2 text-sm text-gray-700">Hanya Draft</span>
                    </label>
                  </div>
                </div>

                <!-- Fields to Include -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Field yang Diikutsertakan
                  </label>
                  <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center">
                      <input
                        type="checkbox"
                        v-model="csvExport.fields"
                        value="title"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      />
                      <span class="ml-2 text-sm text-gray-700">Judul</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        type="checkbox"
                        v-model="csvExport.fields"
                        value="description"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      />
                      <span class="ml-2 text-sm text-gray-700">Deskripsi</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        type="checkbox"
                        v-model="csvExport.fields"
                        value="category"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      />
                      <span class="ml-2 text-sm text-gray-700">Kategori</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        type="checkbox"
                        v-model="csvExport.fields"
                        value="status"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      />
                      <span class="ml-2 text-sm text-gray-700">Status</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        type="checkbox"
                        v-model="csvExport.fields"
                        value="author"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      />
                      <span class="ml-2 text-sm text-gray-700">Penulis</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        type="checkbox"
                        v-model="csvExport.fields"
                        value="created_at"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      />
                      <span class="ml-2 text-sm text-gray-700">Tanggal Dibuat</span>
                    </label>
                  </div>
                </div>

                <!-- Export Button -->
                <button
                  type="submit"
                  :disabled="csvExporting"
                  class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                >
                  <svg
                    v-if="csvExporting"
                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ csvExporting ? 'Mengekspor...' : 'Ekspor ke CSV' }}
                </button>
              </form>
            </div>
          </div>

          <!-- Export to PDF -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Ekspor ke PDF</h3>
              <p class="text-sm text-gray-500 mt-1">Buat laporan dalam format dokumen</p>
            </div>
            <div class="p-6">
              <form @submit.prevent="exportToPDF" class="space-y-4">
                <!-- Data Scope -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Cakupan Data
                  </label>
                  <div class="space-y-2">
                    <label class="flex items-center">
                      <input
                        type="radio"
                        v-model="pdfExport.scope"
                        value="all"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      />
                      <span class="ml-2 text-sm text-gray-700">Semua Data</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        type="radio"
                        v-model="pdfExport.scope"
                        value="published"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      />
                      <span class="ml-2 text-sm text-gray-700">Hanya yang Dipublikasi</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        type="radio"
                        v-model="pdfExport.scope"
                        value="draft"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                      />
                      <span class="ml-2 text-sm text-gray-700">Hanya Draft</span>
                    </label>
                  </div>
                </div>

                <!-- Layout Options -->
                <div>
                  <label for="pdfLayout" class="block text-sm font-medium text-gray-700 mb-2">
                    Layout
                  </label>
                  <select
                    id="pdfLayout"
                    v-model="pdfExport.layout"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="portrait">Portrait</option>
                    <option value="landscape">Landscape</option>
                  </select>
                </div>

                <!-- Page Size -->
                <div>
                  <label for="pdfPageSize" class="block text-sm font-medium text-gray-700 mb-2">
                    Ukuran Halaman
                  </label>
                  <select
                    id="pdfPageSize"
                    v-model="pdfExport.pageSize"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="a4">A4</option>
                    <option value="letter">Letter</option>
                    <option value="legal">Legal</option>
                  </select>
                </div>

                <!-- Content Options -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Konten
                  </label>
                  <div class="space-y-2">
                    <label class="flex items-center">
                      <input
                        type="checkbox"
                        v-model="pdfExport.includeHeader"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      />
                      <span class="ml-2 text-sm text-gray-700">Sertakan Header</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        type="checkbox"
                        v-model="pdfExport.includeFooter"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      />
                      <span class="ml-2 text-sm text-gray-700">Sertakan Footer</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        type="checkbox"
                        v-model="pdfExport.includeTableOfContents"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                      />
                      <span class="ml-2 text-sm text-gray-700">Daftar Isi</span>
                    </label>
                  </div>
                </div>

                <!-- Export Button -->
                <button
                  type="submit"
                  :disabled="pdfExporting"
                  class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
                >
                  <svg
                    v-if="pdfExporting"
                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ pdfExporting ? 'Mengekspor...' : 'Ekspor ke PDF' }}
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- Export History -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Riwayat Ekspor</h3>
            <p class="text-sm text-gray-500 mt-1">Daftar ekspor yang telah dilakukan</p>
          </div>
          <div class="p-6">
            <div v-if="exportHistory.length === 0" class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada riwayat ekspor</h3>
              <p class="mt-1 text-sm text-gray-500">
                Riwayat ekspor akan muncul di sini setelah Anda melakukan ekspor.
              </p>
            </div>
            <div v-else class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Jenis
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Nama File
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Tanggal
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Jumlah Record
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr
                    v-for="exportItem in exportHistory"
                    :key="exportItem.id"
                    class="hover:bg-gray-50"
                  >
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8">
                          <div :class="getExportIconClass(exportItem.type)" class="h-8 w-8 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path v-if="exportItem.type === 'csv'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                          </div>
                        </div>
                        <div class="ml-3">
                          <div class="text-sm font-medium text-gray-900">
                            {{ exportItem.type === 'csv' ? 'CSV' : 'PDF' }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ exportItem.filename }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(exportItem.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ exportItem.record_count }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="exportItem.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      >
                        {{ exportItem.status === 'completed' ? 'Selesai' : 'Sedang Diproses' }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';

const csvExporting = ref(false);
const pdfExporting = ref(false);

const csvExport = reactive({
  scope: 'all',
  fields: ['title', 'description', 'category', 'status', 'author', 'created_at']
});

const pdfExport = reactive({
  scope: 'all',
  layout: 'portrait',
  pageSize: 'a4',
  includeHeader: true,
  includeFooter: true,
  includeTableOfContents: false
});

const exportHistory = ref([
  {
    id: 1,
    type: 'csv',
    filename: 'knowledge_export_2024_01.csv',
    created_at: '2024-01-15T10:30:00Z',
    record_count: 150,
    status: 'completed'
  },
  {
    id: 2,
    type: 'pdf',
    filename: 'knowledge_report_2024_01.pdf',
    created_at: '2024-01-14T14:20:00Z',
    record_count: 150,
    status: 'completed'
  }
]);

const exportToCSV = () => {
  csvExporting.value = true;

  // Simulate CSV export
  setTimeout(() => {
    alert('Ekspor CSV berhasil! File akan diunduh.');
    csvExporting.value = false;

    // Add to history
    exportHistory.value.unshift({
      id: Date.now(),
      type: 'csv',
      filename: `knowledge_export_${new Date().toISOString().split('T')[0]}.csv`,
      created_at: new Date().toISOString(),
      record_count: 150,
      status: 'completed'
    });
  }, 2000);
};

const exportToPDF = () => {
  pdfExporting.value = true;

  // Simulate PDF export
  setTimeout(() => {
    alert('Ekspor PDF berhasil! File akan diunduh.');
    pdfExporting.value = false;

    // Add to history
    exportHistory.value.unshift({
      id: Date.now(),
      type: 'pdf',
      filename: `knowledge_report_${new Date().toISOString().split('T')[0]}.pdf`,
      created_at: new Date().toISOString(),
      record_count: 150,
      status: 'completed'
    });
  }, 3000);
};

const getExportIconClass = (type) => {
  return type === 'csv' ? 'bg-green-500' : 'bg-red-500';
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
</script>
