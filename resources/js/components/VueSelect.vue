<template>
  <div class="relative">
    <Listbox v-model="selectedValue" @update:modelValue="handleChange">
      <div class="relative">
        <ListboxButton
          class="relative w-full cursor-pointer rounded-lg bg-white py-2.5 pl-3 pr-10 text-left border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 transition-colors duration-200"
        >
          <span class="block truncate" :class="{ 'text-gray-500': !selectedValue }">
            {{ selectedLabel || placeholder }}
          </span>
          <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
            <svg
              class="h-5 w-5 text-gray-400"
              viewBox="0 0 20 20"
              fill="currentColor"
              aria-hidden="true"
            >
              <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
            </svg>
          </span>
        </ListboxButton>

        <transition
          leave-active-class="transition duration-100 ease-in"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <ListboxOptions
            class="absolute z-50 w-full mt-1 max-h-60 overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
          >
            <!-- Search input -->
            <div class="sticky top-0 bg-white border-b border-gray-100 p-2">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari..."
                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                @focus="$event.target.select()"
              />
            </div>

            <ListboxOption
              v-for="option in filteredOptions"
              :key="option.value || option.id"
              :value="option.value || option.id"
              v-slot="{ active, selected }"
            >
              <li
                :class="[
                  active ? 'bg-blue-600 text-white' : 'text-gray-900',
                  'relative cursor-pointer select-none py-2 pl-3 pr-9'
                ]"
              >
                <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                  {{ option.label || option.name || option.nama_skpd }}
                </span>
                <span
                  v-if="selected"
                  :class="[
                    active ? 'text-white' : 'text-blue-600',
                    'absolute inset-y-0 right-0 flex items-center pr-4'
                  ]"
                >
                  <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                  </svg>
                </span>
              </li>
            </ListboxOption>

            <!-- No results / Add new option -->
            <div v-if="filteredOptions.length === 0 && searchQuery.trim()" class="border-t border-gray-100">
              <button
                type="button"
                @click="addNewOption"
                class="w-full px-3 py-2 text-left text-sm text-blue-600 hover:bg-blue-50 focus:outline-none focus:bg-blue-50 transition-colors"
              >
                <span class="flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                  Tambah "{{ searchQuery.trim() }}"
                </span>
              </button>
            </div>
            <div v-else-if="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-500 text-center">
              Tidak ada hasil
            </div>


          </ListboxOptions>
        </transition>
      </div>
    </Listbox>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from '@headlessui/vue'

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
  'add': [label: string]
}>()

const searchQuery = ref('')
const selectedValue = ref(props.modelValue)

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
  selectedValue.value = newValue
})

// Watch for internal changes
watch(selectedValue, (newValue) => {
  if (newValue !== undefined) {
    emit('update:modelValue', newValue)
    emit('change', newValue)
  }
})

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
  if (!selectedValue.value) return ''
  const selected = props.options.find(option => {
    const value = option.value || option.id
    return value == selectedValue.value
  })
  return selected ? (selected.label || selected.name || selected.nama_skpd) : ''
})

// Handle change
const handleChange = (value: string | number) => {
  selectedValue.value = value
  searchQuery.value = ''
}

// Handle adding new option
const addNewOption = () => {
  const label = searchQuery.value.trim()
  if (label) {
    emit('add', label)
    searchQuery.value = ''
  }
}


</script>
