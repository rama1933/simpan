<template>
    <div class="relative w-full max-w-2xl mx-auto">
        <!-- Search Input -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input
                ref="searchInput"
                v-model="searchQuery"
                @input="handleInput"
                @focus="showSuggestions = true"
                @keydown="handleKeydown"
                type="text"
                placeholder="Cari pengetahuan, topik, atau kata kunci..."
                class="w-full pl-12 pr-12 py-4 text-lg border-2 border-gray-300 rounded-full focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 shadow-lg hover:shadow-xl"
            />
            <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                <button
                    v-if="searchQuery"
                    @click="clearSearch"
                    class="text-gray-400 hover:text-gray-600 transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Search Suggestions Dropdown -->
        <div
            v-if="showSuggestions && (suggestions.length > 0 || recommendations.length > 0 || isLoading)"
            class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-200 z-50 max-h-96 overflow-hidden"
        >
            <!-- Loading State -->
            <div v-if="isLoading" class="p-4 text-center">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mx-auto"></div>
                <p class="text-sm text-gray-500 mt-2">Mencari...</p>
            </div>

            <!-- Search Suggestions -->
            <div v-if="suggestions.length > 0 && !isLoading" class="border-b border-gray-100">
                <div class="px-4 py-2 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wide">
                    Saran Pencarian
                </div>
                <div class="max-h-48 overflow-y-auto">
                    <button
                        v-for="(suggestion, index) in suggestions"
                        :key="`suggestion-${index}`"
                        @click="selectSuggestion(suggestion)"
                        :class="[
                            'w-full text-left px-4 py-3 hover:bg-blue-50 transition-colors border-b border-gray-50 last:border-b-0',
                            selectedIndex === index ? 'bg-blue-50' : ''
                        ]"
                    >
                        <div class="flex items-center space-x-3">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <div>
                                <div class="font-medium text-gray-900" v-html="highlightMatch(suggestion.title)"></div>
                                <div class="text-sm text-gray-500 truncate">{{ suggestion.description }}</div>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Recommendations -->
            <div v-if="recommendations.length > 0 && !isLoading">
                <div class="px-4 py-2 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wide">
                    Rekomendasi untuk Anda
                </div>
                <div class="max-h-48 overflow-y-auto">
                    <button
                        v-for="(rec, index) in recommendations"
                        :key="`rec-${rec.id}`"
                        @click="selectRecommendation(rec)"
                        :class="[
                            'w-full text-left px-4 py-3 hover:bg-green-50 transition-colors border-b border-gray-50 last:border-b-0',
                            selectedIndex === (suggestions.length + index) ? 'bg-green-50' : ''
                        ]"
                    >
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900 truncate">{{ rec.title }}</div>
                                <div class="text-sm text-gray-500 line-clamp-2">{{ rec.description }}</div>
                                <div class="flex items-center mt-1 space-x-2 text-xs text-gray-400">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">{{ rec.category }}</span>
                                    <span>{{ rec.author }}</span>
                                </div>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- No Results -->
            <div v-if="!isLoading && suggestions.length === 0 && recommendations.length === 0 && searchQuery.length > 2" class="p-6 text-center">
                <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.562M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-gray-500">Tidak ada hasil ditemukan untuk "{{ searchQuery }}"</p>
                <p class="text-sm text-gray-400 mt-1">Coba kata kunci yang berbeda atau lebih spesifik</p>
            </div>
        </div>

        <!-- Overlay to close suggestions -->
        <div
            v-if="showSuggestions"
            @click="showSuggestions = false"
            class="fixed inset-0 z-40"
        ></div>
    </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

// Props
const props = defineProps({
    initialValue: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: 'Cari pengetahuan, topik, atau kata kunci...'
    }
})

// Emits
const emit = defineEmits(['search', 'select'])

// Reactive data
const searchQuery = ref(props.initialValue)
const showSuggestions = ref(false)
const suggestions = ref([])
const recommendations = ref([])
const isLoading = ref(false)
const selectedIndex = ref(-1)
const searchInput = ref(null)

// Debounce timer
let searchTimeout = null

// Methods
const handleInput = () => {
    clearTimeout(searchTimeout)
    selectedIndex.value = -1
    
    if (searchQuery.value.length < 2) {
        suggestions.value = []
        loadRecommendations()
        return
    }
    
    searchTimeout = setTimeout(() => {
        searchSuggestions()
    }, 300)
}

const searchSuggestions = async () => {
    if (searchQuery.value.length < 2) return
    
    isLoading.value = true
    
    try {
        const response = await axios.get('/api/knowledge/search-suggestions', {
            params: { q: searchQuery.value }
        })
        
        suggestions.value = response.data.suggestions || []
        recommendations.value = response.data.recommendations || []
    } catch (error) {
        console.error('Error fetching suggestions:', error)
        suggestions.value = []
        recommendations.value = []
    } finally {
        isLoading.value = false
    }
}

const loadRecommendations = async () => {
    try {
        const response = await axios.get('/api/knowledge/recommendations')
        recommendations.value = response.data.recommendations || []
    } catch (error) {
        console.error('Error loading recommendations:', error)
        recommendations.value = []
    }
}

const selectSuggestion = (suggestion) => {
    searchQuery.value = suggestion.title
    showSuggestions.value = false
    emit('search', suggestion.title)
    performSearch(suggestion.title)
}

const selectRecommendation = (recommendation) => {
    showSuggestions.value = false
    emit('select', recommendation)
    // Navigate to knowledge detail
    router.visit(`/knowledge/public/${recommendation.id}`)
}

const clearSearch = () => {
    searchQuery.value = ''
    suggestions.value = []
    selectedIndex.value = -1
    loadRecommendations()
    searchInput.value?.focus()
}

const handleKeydown = (event) => {
    const totalItems = suggestions.value.length + recommendations.value.length
    
    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault()
            selectedIndex.value = Math.min(selectedIndex.value + 1, totalItems - 1)
            break
        case 'ArrowUp':
            event.preventDefault()
            selectedIndex.value = Math.max(selectedIndex.value - 1, -1)
            break
        case 'Enter':
            event.preventDefault()
            if (selectedIndex.value >= 0) {
                if (selectedIndex.value < suggestions.value.length) {
                    selectSuggestion(suggestions.value[selectedIndex.value])
                } else {
                    const recIndex = selectedIndex.value - suggestions.value.length
                    selectRecommendation(recommendations.value[recIndex])
                }
            } else if (searchQuery.value.trim()) {
                performSearch(searchQuery.value)
            }
            break
        case 'Escape':
            showSuggestions.value = false
            searchInput.value?.blur()
            break
    }
}

const performSearch = (query) => {
    showSuggestions.value = false
    emit('search', query)
    
    // Navigate to search results with the query
    router.get('/knowledge/public', { search: query }, {
        preserveState: true,
        preserveScroll: true
    })
}

const highlightMatch = (text) => {
    if (!searchQuery.value || searchQuery.value.length < 2) return text
    
    const regex = new RegExp(`(${searchQuery.value})`, 'gi')
    return text.replace(regex, '<mark class="bg-yellow-200">$1</mark>')
}

// Load initial recommendations
loadRecommendations()

// Watch for external changes to search query
watch(() => props.initialValue, (newValue) => {
    searchQuery.value = newValue
})
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

mark {
    background-color: #fef08a;
    padding: 0;
    border-radius: 2px;
}
</style>