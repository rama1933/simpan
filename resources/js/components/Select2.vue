<template>
  <div class="relative">
    <!-- Input field -->
    <div
      @click="toggleDropdown"
      class="w-full px-3 py-2.5 border border-gray-300 rounded-lg bg-white cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 transition-colors duration-200"
      :class="{ 'ring-2 ring-blue-500 border-blue-500': showDropdown }"
    >
      <div class="flex items-center justify-between">
        <span
          class="text-gray-900 truncate"
          :class="{ 'text-gray-500': !modelValue }"
        >
          {{ selectedLabel || placeholder }}
        </span>
        <svg
          class="w-5 h-5 text-gray-400 transition-transform duration-200"
          :class="{ 'rotate-180': showDropdown }"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </div>
    </div>

    <!-- Simple Dropdown -->
    <div
      v-show="showDropdown"
      class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-50"
    >
      <!-- Search input -->
      <div class="p-2 border-b border-gray-200">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari..."
          class="w-full px-3 py-2 text-sm border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <!-- Options list -->
      <div class="max-h-48 overflow-y-auto">
        <div
          v-for="option in filteredOptions"
          :key="option.value || option.id"
          @click="selectOption(option)"
          class="px-3 py-2 text-sm text-gray-900 cursor-pointer hover:bg-blue-50 hover:text-blue-900"
          :class="{ 'bg-blue-100 text-blue-900': isSelected(option) }"
        >
          {{ option.label || option.name || option.nama_skpd }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

interface Option {
  value?: string | number
  id?: string | number
  label?: string
  name?: string
  nama_skpd?: string
}

interface Props {
  modelValue?: string | number
  options: Option[]
  placeholder?: string
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Pilih opsi...'
})

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
  'change': [value: string | number]
}>()

const showDropdown = ref(false)
const searchQuery = ref('')

// Toggle dropdown
const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value
  console.log('Toggle dropdown:', showDropdown.value)
  if (showDropdown.value) {
    searchQuery.value = ''
  }
}

// Filter options based on search
const filteredOptions = computed(() => {
  if (!searchQuery.value) return props.options
  const query = searchQuery.value.toLowerCase()
  return props.options.filter(option => {
    const label = option.label || option.name || option.nama_skpd || ''
    return label.toLowerCase().includes(query)
  })
})

// Get selected option label
const selectedLabel = computed(() => {
  if (!props.modelValue) return ''
  const selected = props.options.find(option => {
    const value = option.value || option.id
    return value == props.modelValue
  })
  return selected ? (selected.label || selected.name || selected.nama_skpd) : ''
})

// Check if option is selected
const isSelected = (option: Option) => {
  const value = option.value || option.id
  return value == props.modelValue
}

// Select option
const selectOption = (option: Option) => {
  const value = option.value || option.id
  if (value !== undefined) {
    emit('update:modelValue', value)
    emit('change', value)
    showDropdown.value = false
    searchQuery.value = ''
  }
}

// Close dropdown when clicking outside
const closeDropdown = () => {
  showDropdown.value = false
}

onMounted(() => {
  document.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown)
})
</script>
