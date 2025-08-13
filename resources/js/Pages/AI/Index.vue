<template>
  <AdminLayout page-title="AI Assistant" :user="user">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">AI Assistant</h1>
        <p class="mt-2 text-gray-600">Gunakan AI untuk menganalisis dan menghasilkan konten</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Content Analysis -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold mb-4">Analisis Konten</h2>
          <form @submit.prevent="analyzeContent">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Konten yang akan dianalisis
              </label>
              <textarea
                v-model="analysisForm.content"
                rows="6"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Masukkan konten yang ingin dianalisis..."
                required
              ></textarea>
            </div>
            <button
              type="submit"
              :disabled="analyzing"
              class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 disabled:opacity-50"
            >
              {{ analyzing ? 'Menganalisis...' : 'Analisis Konten' }}
            </button>
          </form>

          <div v-if="analysisResult" class="mt-6 p-4 bg-gray-50 rounded-md">
            <h3 class="font-medium mb-2">Hasil Analisis:</h3>
            <div class="whitespace-pre-line text-sm text-gray-700">{{ analysisResult }}</div>
          </div>
        </div>

        <!-- Tag Suggestion -->
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold mb-4">Saran Tag</h2>
          <form @submit.prevent="suggestTags">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Konten untuk saran tag
              </label>
              <textarea
                v-model="tagForm.content"
                rows="6"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Masukkan konten untuk mendapatkan saran tag..."
                required
              ></textarea>
            </div>
            <button
              type="submit"
              :disabled="suggesting"
              class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 disabled:opacity-50"
            >
              {{ suggesting ? 'Menganalisis...' : 'Dapatkan Saran Tag' }}
            </button>
          </form>

          <div v-if="tagResult" class="mt-6 p-4 bg-gray-50 rounded-md">
            <h3 class="font-medium mb-2">Saran Tag:</h3>
            <div class="whitespace-pre-line text-sm text-gray-700">{{ tagResult }}</div>
          </div>
        </div>
      </div>

      <!-- Custom Generation -->
      <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Generate Konten Custom</h2>
        <form @submit.prevent="generateContent">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Prompt
            </label>
            <textarea
              v-model="generationForm.prompt"
              rows="4"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Masukkan prompt untuk generate konten..."
              required
            ></textarea>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Temperature (0.1 - 1.0)
            </label>
            <input
              v-model="generationForm.temperature"
              type="range"
              min="0.1"
              max="1.0"
              step="0.1"
              class="w-full"
            />
            <span class="text-sm text-gray-500">{{ generationForm.temperature }}</span>
          </div>

          <button
            type="submit"
            :disabled="generating"
            class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 disabled:opacity-50"
          >
            {{ generating ? 'Generating...' : 'Generate Konten' }}
          </button>
        </form>

        <div v-if="generationResult" class="mt-6 p-4 bg-gray-50 rounded-md">
          <h3 class="font-medium mb-2">Hasil Generate:</h3>
          <div class="whitespace-pre-line text-sm text-gray-700">{{ generationResult }}</div>
        </div>
      </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import AdminLayout from '@/Layouts/AdminLayout.vue'

interface Props {
  user?: any
}

const props = defineProps<Props>()

const analyzing = ref(false)
const suggesting = ref(false)
const generating = ref(false)

const analysisForm = ref({
  content: ''
})

const tagForm = ref({
  content: ''
})

const generationForm = ref({
  prompt: '',
  temperature: 0.7
})

const analysisResult = ref('')
const tagResult = ref('')
const generationResult = ref('')

const analyzeContent = async () => {
  analyzing.value = true
  try {
    const response = await axios.post('/ai/analyze', analysisForm.value)
    if (response.data.success) {
      analysisResult.value = response.data.data.content
    } else {
      alert('Error: ' + response.data.message)
    }
  } catch (error) {
    alert('Terjadi kesalahan saat menganalisis konten')
  } finally {
    analyzing.value = false
  }
}

const suggestTags = async () => {
  suggesting.value = true
  try {
    const response = await axios.post('/ai/suggest-tags', tagForm.value)
    if (response.data.success) {
      tagResult.value = response.data.data.content
    } else {
      alert('Error: ' + response.data.message)
    }
  } catch (error) {
    alert('Terjadi kesalahan saat mendapatkan saran tag')
  } finally {
    suggesting.value = false
  }
}

const generateContent = async () => {
  generating.value = true
  try {
    const response = await axios.post('/ai/generate', {
      prompt: generationForm.value.prompt,
      options: {
        temperature: parseFloat(generationForm.value.temperature)
      }
    })
    if (response.data.success) {
      generationResult.value = response.data.data.content
    } else {
      alert('Error: ' + response.data.message)
    }
  } catch (error) {
    alert('Terjadi kesalahan saat generate konten')
  } finally {
    generating.value = false
  }
}
</script>
