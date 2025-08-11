<template>
  <div class="bg-white shadow-sm rounded-lg border border-gray-200">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-gray-200">
      <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
      <p v-if="subtitle" class="text-sm text-gray-500 mt-1">{{ subtitle }}</p>
    </div>

    <!-- Table Content -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              :class="column.class"
            >
              {{ column.label }}
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="(item, index) in data"
            :key="getItemKey(item, index)"
            class="hover:bg-gray-50 transition-colors duration-150"
          >
            <td
              v-for="column in columns"
              :key="column.key"
              class="px-6 py-4 whitespace-nowrap"
              :class="column.cellClass"
            >
              <slot
                :name="column.key"
                :item="item"
                :value="getItemValue(item, column.key)"
                :index="index"
              >
                {{ formatCellValue(item, column) }}
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Empty State -->
    <div v-if="!data || data.length === 0" class="text-center py-12">
      <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
      </div>
      <h3 class="mt-2 text-sm font-medium text-gray-900">{{ emptyMessage }}</h3>
      <p class="mt-1 text-sm text-gray-500">{{ emptyDescription }}</p>
      <slot name="empty-actions"></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Column {
  key: string
  label: string
  class?: string
  cellClass?: string
  formatter?: (value: any, item: any) => string
  type?: 'text' | 'badge' | 'date' | 'actions' | 'custom'
}

interface Props {
  data: any[]
  columns: Column[]
  title?: string
  subtitle?: string
  emptyMessage?: string
  emptyDescription?: string
  itemKey?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Data Table',
  emptyMessage: 'Tidak ada data',
  emptyDescription: 'Belum ada data yang tersedia.',
  itemKey: 'id'
})

// Get unique key for each item
const getItemKey = (item: any, index: number) => {
  return item?.[props.itemKey] || `item-${index}`
}

// Get value from item based on column key
const getItemValue = (item: any, key: string) => {
  return key.split('.').reduce((obj, k) => obj?.[k], item)
}

// Format cell value based on column type
const formatCellValue = (item: any, column: Column) => {
  const value = getItemValue(item, column.key)

  if (column.formatter) {
    return column.formatter(value, item)
  }

  if (column.type === 'date' && value) {
    return new Date(value).toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  }

  if (column.type === 'badge') {
    return value || 'N/A'
  }

  return value || 'N/A'
}
</script>
