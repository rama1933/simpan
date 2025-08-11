<template>
  <select
    :value="modelValue"
    @change="$emit('update:modelValue', $event.target.value)"
    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 transition-colors duration-200"
  >
    <option value="">{{ placeholder }}</option>
    <option
      v-for="option in options"
      :key="option.value || option.id"
      :value="option.value || option.id"
    >
      {{ option.label || option.name }}
    </option>
  </select>
</template>

<script setup lang="ts">
interface Option {
  value?: string | number
  id?: string | number
  label?: string
  name?: string
}

interface Props {
  modelValue?: string | number
  options: Option[]
  placeholder?: string
}

defineProps<Props>()
defineEmits<{
  'update:modelValue': [value: string | number]
}>()
</script>
