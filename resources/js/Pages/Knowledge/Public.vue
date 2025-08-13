<template>
    <PublicLayout>
        <!-- Page Title -->
        <PublicPageHeader 
            title="Temukan Pengetahuan"
            description="Jelajahi koleksi pengetahuan yang telah terverifikasi dari Pemerintah Kabupaten Hulu Sungai Selatan"
        />

            <!-- Filter Section -->
            <div class="bg-white p-6 rounded-xl shadow-md ring-1 ring-gray-100 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Filter Data</h3>
                        <p class="text-sm text-gray-500 mt-1">Saring data berdasarkan kriteria yang diinginkan</p>
                    </div>
                    <div class="flex space-x-3">
                        <button
                            @click="toggleAIAssistant"
                            class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-sm"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            🤖 AI Assistant
                        </button>
                        <button
                            @click="search"
                            class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                            </svg>
                            Filter Data
                        </button>
                        <button
                            @click="resetFilters"
                            :disabled="!hasActiveFilters"
                            :class="[
                                'inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition-all duration-200',
                                hasActiveFilters
                                    ? 'text-gray-600 bg-gray-50 border-gray-300 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 shadow-sm'
                                    : 'text-gray-400 bg-gray-100 border-gray-200 cursor-not-allowed'
                            ]"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Hapus Filter
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Cari</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="Cari judul atau konten..."
                                class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 transition-colors duration-200"
                            />
                        </div>
                    </div>
                    
                    <!-- Category -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                        <VueSelect
                            v-model="searchForm.category_id"
                            :options="categoryOptions"
                            placeholder="Pilih Kategori..."
                        />
                    </div>
                    
                    <!-- SKPD -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">SKPD</label>
                        <VueSelect
                            v-model="searchForm.skpd_id"
                            :options="skpdOptions"
                            placeholder="Pilih SKPD..."
                        />
                    </div>

                    <!-- Tags -->
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Tags</label>
                        <input
                            v-model="tagsQuery"
                            @input="debouncedSearchTags"
                            type="text"
                            placeholder="Cari tag..."
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 transition-colors duration-200"
                        />
                        <div v-if="tagOptions.length > 0" class="mt-2">
                            <div class="text-xs text-gray-500 mb-2">Pilih tag:</div>
                            <div class="flex flex-wrap gap-2 max-h-32 overflow-y-auto">
                                <button
                                    v-for="tag in tagOptions"
                                    :key="tag.id"
                                    type="button"
                                    @click="toggleTag(tag)"
                                    :class="selectedTagIds.includes(tag.id) ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border-gray-200'"
                                    class="px-2.5 py-1.5 text-xs rounded-md border transition-colors duration-200"
                                >
                                    {{ tag.name }}
                                    <span v-if="selectedTagIds.includes(tag.id)" class="ml-1">✓</span>
                                </button>
                            </div>
                        </div>
                        <div v-if="selectedTagIds.length" class="mt-3">
                            <div class="text-xs text-gray-500 mb-2">Tag terpilih ({{ selectedTagIds.length }}):</div>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="id in selectedTagIds" :key="`sel-${id}`" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-indigo-100 text-indigo-800">
                                    {{ tagNameById(id) }}
                                    <button class="ml-1.5 text-indigo-600 hover:text-indigo-800 font-medium" @click="removeTag(id)">×</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active filters display -->
                <div v-if="hasActiveFilters" class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">Filter aktif:</span>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-if="searchForm.search"
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                            >
                                Search: {{ searchForm.search }}
                                <button
                                    @click="searchForm.search = ''"
                                    class="ml-1.5 text-blue-700 hover:text-blue-900"
                                >
                                    ×
                                </button>
                            </span>
                            <span
                                v-if="searchForm.category_id"
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                            >
                                Kategori: {{ getCategoryName(searchForm.category_id) }}
                                <button
                                    @click="searchForm.category_id = ''"
                                    class="ml-1.5 text-green-600 hover:text-green-800"
                                >
                                    ×
                                </button>
                            </span>
                            <span
                                v-if="searchForm.skpd_id"
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
                            >
                                SKPD: {{ getSKPDName(searchForm.skpd_id) }}
                                <button
                                    @click="searchForm.skpd_id = ''"
                                    class="ml-1.5 text-purple-600 hover:text-purple-800"
                                >
                                    ×
                                </button>
                            </span>
                            <span
                                v-if="selectedTagIds.length > 0"
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                            >
                                Tags: {{ selectedTagIds.length }} dipilih
                                <button
                                    @click="selectedTagIds = []; selectedTags = []"
                                    class="ml-1.5 text-indigo-600 hover:text-indigo-800"
                                >
                                    ×
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Knowledge Grid -->
            <div v-if="knowledge && knowledge.data && knowledge.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="item in knowledge.data"
                    :key="item.id"
                    class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow cursor-pointer"
                    @click="viewKnowledge(item.id)"
                >
                    <div class="p-6">
                        <!-- Category Badge -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ item.category?.name || 'Umum' }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Terverifikasi
                            </span>
                        </div>
                        
                        <!-- Title -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                            {{ item.title }}
                        </h3>
                        
                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {{ item.description || 'Tidak ada deskripsi' }}
                        </p>
                        
                        <!-- Meta Info -->
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <div class="flex items-center space-x-4">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ item.author?.name || 'Unknown' }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    {{ item.skpd?.nama_skpd || 'SKPD' }}
                                </span>
                            </div>
                            <div>
                                {{ formatDate(item.created_at) }}
                            </div>
                        </div>
                        
                        <!-- Tags -->
                        <div v-if="item.tags_relation && item.tags_relation.length > 0" class="mt-3 flex flex-wrap gap-1">
                            <span
                                v-for="tag in item.tags_relation.slice(0, 3)"
                                :key="tag.id"
                                class="inline-flex items-center px-2 py-1 rounded text-xs bg-blue-100 text-blue-800"
                            >
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                {{ tag.name }}
                            </span>
                            <span v-if="item.tags_relation.length > 3" class="text-xs text-gray-500">
                                +{{ item.tags_relation.length - 3 }} lainnya
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <div class="w-24 h-24 mx-auto mb-4 text-gray-300">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pengetahuan ditemukan</h3>
                <p class="text-gray-600">Coba ubah filter pencarian atau kata kunci Anda.</p>
            </div>

            <!-- Pagination -->
            <div v-if="knowledge && knowledge.data && knowledge.data.length > 0 && knowledge.links && knowledge.links.length > 0" class="mt-8">
                <nav class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link
                            v-if="knowledge.prev_page_url"
                            :href="knowledge.prev_page_url"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Sebelumnya
                        </Link>
                        <Link
                            v-if="knowledge.next_page_url"
                            :href="knowledge.next_page_url"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Selanjutnya
                        </Link>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-medium">{{ knowledge.from }}</span>
                                sampai
                                <span class="font-medium">{{ knowledge.to }}</span>
                                dari
                                <span class="font-medium">{{ knowledge.total }}</span>
                                hasil
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                <template v-for="link in knowledge.links" :key="link.label">
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                            link.active
                                                ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                        ]"
                                        :preserve-scroll="true"
                                        v-html="link.label"
                                    >
                                    </Link>
                                    <span
                                        v-else
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                            'bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed'
                                        ]"
                                        v-html="link.label"
                                    >
                                    </span>
                                </template>
                            </nav>
                        </div>
                    </div>
                </nav>
            </div>

        <!-- AI Assistant Modal -->
        <div v-if="showAIModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAIModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <span class="text-2xl">🤖</span>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">AI Assistant - Gemini</h3>
                                    <p class="text-sm text-gray-500">Tanyakan tentang sistem manajemen pengetahuan</p>
                                </div>
                            </div>
                            <button @click="closeAIModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Chat Messages -->
                        <div class="h-64 overflow-y-auto border rounded-lg p-3 mb-4 bg-gray-50">
                            <div v-for="(message, index) in chatMessages" :key="index" class="mb-3">
                                <div v-if="message.type === 'user'" class="flex justify-end">
                                    <div class="bg-blue-500 text-white px-3 py-2 rounded-lg max-w-xs">
                                        {{ message.content }}
                                    </div>
                                </div>
                                <div v-else class="flex justify-start">
                                    <div class="bg-white border px-3 py-2 rounded-lg max-w-xs">
                                        {{ message.content }}
                                    </div>
                                </div>
                            </div>
                            <div v-if="aiLoading" class="flex justify-start">
                                <div class="bg-white border px-3 py-2 rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-green-600"></div>
                                        <span class="text-sm text-gray-500">AI sedang mengetik...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Message Input -->
                        <div class="flex space-x-2">
                            <input
                                v-model="currentMessage"
                                @keyup.enter="sendMessage"
                                type="text"
                                placeholder="Tanyakan tentang sistem manajemen pengetahuan..."
                                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                :disabled="aiLoading"
                            />
                            <button
                                @click="sendMessage"
                                :disabled="!currentMessage.trim() || aiLoading"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import VueSelect from '@/Components/VueSelect.vue'
import axios from 'axios'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import PublicPageHeader from '@/Components/Public/PublicPageHeader.vue'
import { route } from '@/core/helpers/route'



// Props
const props = defineProps({
    knowledge: Object,
    filters: Object,
    categories: Array,
    skpds: Array
})

// Search form
const searchForm = reactive({
    search: props.filters?.search || '',
    category_id: props.filters?.category_id || '',
    skpd_id: props.filters?.skpd_id || ''
})

// AI Assistant state
const showAIModal = ref(false)
const chatMessages = ref([])
const currentMessage = ref('')
const aiLoading = ref(false)

// Options for VueSelect components
const categoryOptions = computed(() => {
    const options = [{ value: '', label: 'Semua Kategori' }]
    if (props.categories) {
        options.push(...props.categories.map(category => ({
            value: category.id,
            label: category.name
        })))
    }
    return options
})

const skpdOptions = computed(() => {
    const options = [{ value: '', label: 'Semua SKPD' }]
    if (props.skpds) {
        options.push(...props.skpds.map(skpd => ({
            value: skpd.id,
            label: skpd.nama_skpd
        })))
    }
    return options
})

// Tags state
const tagsQuery = ref('')
const selectedTagIds = ref(Array.isArray(props.filters?.tags) ? props.filters.tags : [])
const tagOptions = ref([])
const selectedTags = ref([]) // Cache untuk menyimpan data tag yang dipilih

// Methods
const search = () => {
    const filterParams = {
        ...searchForm,
        tags: selectedTagIds.value.length > 0 ? selectedTagIds.value : undefined
    }
    
    // Remove empty values
    Object.keys(filterParams).forEach(key => {
        if (filterParams[key] === '' || filterParams[key] === null || filterParams[key] === undefined) {
            delete filterParams[key]
        }
    })
    
    router.get('/knowledge/public', filterParams, {
        preserveState: true,
        preserveScroll: true
    })
}

const resetFilters = () => {
    searchForm.search = ''
    searchForm.category_id = ''
    searchForm.skpd_id = ''
    selectedTagIds.value = []
    selectedTags.value = []
    tagsQuery.value = ''
    tagOptions.value = []
    search()
}

// Tags methods
const tagNameById = (id) => {
    // Cari di tagOptions terlebih dahulu
    const tag = tagOptions.value.find(t => t.id === id)
    if (tag) return tag.name
    
    // Jika tidak ditemukan, cari di selectedTags cache
    const selectedTag = selectedTags.value.find(t => t.id === id)
    return selectedTag?.name || `Tag ${id}`
}

const removeTag = (id) => {
    selectedTagIds.value = selectedTagIds.value.filter(t => t !== id)
    // Hapus juga dari cache selectedTags
    selectedTags.value = selectedTags.value.filter(t => t.id !== id)
    search()
}

const toggleTag = (tag) => {
    if (selectedTagIds.value.includes(tag.id)) {
        removeTag(tag.id)
    } else {
        selectedTagIds.value.push(tag.id)
        // Simpan data tag ke cache jika belum ada
        if (!selectedTags.value.find(t => t.id === tag.id)) {
            selectedTags.value.push(tag)
        }
        search()
    }
}

const searchTags = async () => {
    if (tagsQuery.value.length < 2) {
        tagOptions.value = []
        return
    }
    
    try {
        const response = await axios.get('/api/knowledge/tags', {
            params: { q: tagsQuery.value }
        })
        tagOptions.value = response.data.data || []
    } catch (error) {
        console.error('Error searching tags:', error)
        tagOptions.value = []
    }
}

// Debounced search function
let searchTimeout = null
const debouncedSearchTags = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        searchTags()
    }, 300)
}

const viewKnowledge = (id) => {
    router.visit(route('knowledge.public.show', id))
}

const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

// Computed properties for filter state
const hasActiveFilters = computed(() => {
    return searchForm.search || 
           searchForm.category_id || 
           searchForm.skpd_id || 
           selectedTagIds.value.length > 0
})

const getCategoryName = (id) => {
    const category = props.categories?.find(c => c.id == id)
    return category?.name || 'Unknown'
}

const getSKPDName = (id) => {
    const skpd = props.skpds?.find(s => s.id == id)
    return skpd?.nama_skpd || 'Unknown'
}

// AI Assistant functions
const toggleAIAssistant = () => {
    showAIModal.value = true
    if (chatMessages.value.length === 0) {
        chatMessages.value.push({
            type: 'assistant',
            content: 'Halo! Saya AI Assistant untuk sistem manajemen pengetahuan. Saya dapat membantu Anda dengan informasi tentang pengetahuan yang tersedia, cara menggunakan sistem, dan pertanyaan umum lainnya. Ada yang bisa saya bantu?'
        })
    }
}

const closeAIModal = () => {
    showAIModal.value = false
}

const sendMessage = async () => {
    if (!currentMessage.value.trim() || aiLoading.value) return
    
    const userMessage = currentMessage.value.trim()
    chatMessages.value.push({
        type: 'user',
        content: userMessage
    })
    
    currentMessage.value = ''
    aiLoading.value = true
    
    try {
        const response = await axios.post('/api/ai/chat', {
            message: userMessage,
            context: 'public_knowledge_list'
        })
        
        chatMessages.value.push({
            type: 'assistant',
            content: response.data.response
        })
    } catch (error) {
        console.error('Error:', error)
        chatMessages.value.push({
            type: 'assistant',
            content: 'Maaf, terjadi kesalahan. Silakan coba lagi nanti.'
        })
    } finally {
        aiLoading.value = false
    }
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>