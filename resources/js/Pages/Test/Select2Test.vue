<template>
  <div class="p-8">
    <h1 class="text-2xl font-bold mb-6">Test Select2 Component</h1>

    <div class="space-y-6">
      <!-- Test Category Select2 -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
        <Select2
          v-model="selectedCategory"
          :options="categoryOptions"
          placeholder="Pilih Kategori..."
          @change="onCategoryChange"
        />
        <p class="text-sm text-gray-500 mt-1">Selected: {{ selectedCategory }}</p>
      </div>

      <!-- Test SKPD Select2 -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">SKPD</label>
        <Select2
          v-model="selectedSKPD"
          :options="skpdOptions"
          placeholder="Pilih SKPD..."
          @change="onSKPDChange"
        />
        <p class="text-sm text-gray-500 mt-1">Selected: {{ selectedSKPD }}</p>
      </div>

      <!-- Debug Info -->
      <div class="bg-gray-100 p-4 rounded-lg">
        <h3 class="font-semibold mb-2">Debug Info:</h3>
        <pre class="text-sm">{{ debugInfo }}</pre>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import Select2 from '@/Components/Select2.vue'

const selectedCategory = ref('')
const selectedSKPD = ref('')

const categoryOptions = ref([
  { value: '', label: 'Semua Kategori' },
  { value: '1', label: 'Teknologi' },
  { value: '2', label: 'Bisnis' },
  { value: '3', label: 'Kesehatan' }
])

const skpdOptions = ref([
  { id: '', label: 'Semua SKPD' },
  { id: '1', label: 'SKPD001 - Dinas Teknologi' },
  { id: '2', label: 'SKPD002 - Dinas Ekonomi' },
  { id: '3', label: 'SKPD003 - Dinas Kesehatan' }
])

const onCategoryChange = (value: string | number) => {
  console.log('Category changed:', value)
}

const onSKPDChange = (value: string | number) => {
  console.log('SKPD changed:', value)
}

const debugInfo = computed(() => ({
  selectedCategory: selectedCategory.value,
  selectedSKPD: selectedSKPD.value,
  categoryOptions: categoryOptions.value,
  skpdOptions: skpdOptions.value
}))
</script>
