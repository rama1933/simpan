<template>
  <AdminLayout page-title="Dashboard" :user="user">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
      <KpiCard title="Total Pengetahuan" :value="stats.knowledge" icon="doc" color="from-sky-500 to-sky-600" />
      <KpiCard title="Interaksi AI" :value="stats.ai_interactions" icon="ai" color="from-emerald-500 to-emerald-600" />
      <KpiCard title="Total Pengguna" :value="stats.users" icon="users" color="from-indigo-500 to-indigo-600" />
      <KpiCard title="Dipublikasi" :value="statusDistribution.published.count" subtitle="{{ statusDistribution.published.percentage }}%" icon="check" color="from-violet-500 to-violet-600" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Tren Bulanan -->
      <div class="bg-white rounded-lg shadow p-4 lg:col-span-2">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-semibold text-gray-900">Tren Pengetahuan per Bulan ({{ currentYear }})</h3>
        </div>
        <Bar :data="monthlyBarData" :options="barOptions" />
      </div>

      <!-- Distribusi Status -->
      <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-semibold text-gray-900 mb-2">Distribusi Status</h3>
        <Doughnut :data="statusDoughnutData" :options="doughnutOptions" />
        <div class="mt-4 grid grid-cols-3 text-center text-sm text-gray-600">
          <div>
            <div class="font-semibold text-green-600">Published</div>
            <div>{{ statusDistribution.published.count }} ({{ statusDistribution.published.percentage }}%)</div>
          </div>
          <div>
            <div class="font-semibold text-amber-600">Draft</div>
            <div>{{ statusDistribution.draft.count }} ({{ statusDistribution.draft.percentage }}%)</div>
          </div>
          <div>
            <div class="font-semibold text-gray-600">Archived</div>
            <div>{{ statusDistribution.archived.count }} ({{ statusDistribution.archived.percentage }}%)</div>
          </div>
        </div>
      </div>

      <!-- Distribusi Kategori -->
      <div class="bg-white rounded-lg shadow p-4 lg:col-span-2">
        <h3 class="font-semibold text-gray-900 mb-2">Kategori Teratas</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500">
                <th class="py-2">Kategori</th>
                <th class="py-2">Jumlah</th>
                <th class="py-2">% dari Total</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="cat in topCategories" :key="cat.name" class="border-t">
                <td class="py-2">{{ cat.name }}</td>
                <td class="py-2">{{ cat.count }}</td>
                <td class="py-2">{{ cat.percentage }}%</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Aktivitas Terbaru -->
      <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-semibold text-gray-900 mb-2">Aktivitas Terbaru</h3>
        <ul class="divide-y">
          <li v-for="a in recentActivities" :key="a.id" class="py-3">
            <div class="flex items-start gap-3">
              <div class="h-8 w-8 rounded bg-brand-100 flex items-center justify-center">
                <i class="pi pi-clock text-brand-700"></i>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-900">{{ a.action }} - {{ a.subject_title }}</div>
                <div class="text-xs text-gray-500">oleh {{ a.causer_name }} • {{ formatDate(a.created_at) }}</div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed } from 'vue'
import { Bar, Doughnut } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title, Tooltip, Legend,
  BarElement, ArcElement,
  CategoryScale, LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, ArcElement, CategoryScale, LinearScale)

const props = defineProps({
  stats: { type: Object, default: () => ({ knowledge: 0, ai_interactions: 0, users: 0 }) },
  user: { type: Object, default: null },
  statusDistribution: { type: Object, default: () => ({ published: {count:0,percentage:0}, draft:{count:0,percentage:0}, archived:{count:0,percentage:0} }) },
  monthlyTrends: { type: Array, default: () => [] },
  topCategories: { type: Array, default: () => [] },
  recentActivities: { type: Array, default: () => [] }
})

const currentYear = new Date().getFullYear()

const monthlyBarData = computed(() => ({
  labels: props.monthlyTrends.map(m => m.month),
  datasets: [{
    label: 'Pengetahuan Dibuat',
    data: props.monthlyTrends.map(m => m.count),
    backgroundColor: '#2563eb',
    borderRadius: 6,
  }]
}))

const statusDoughnutData = computed(() => ({
  labels: ['Published', 'Draft', 'Archived'],
  datasets: [{
    data: [props.statusDistribution.published.count, props.statusDistribution.draft.count, props.statusDistribution.archived.count],
    backgroundColor: ['#16a34a', '#f59e0b', '#6b7280']
  }]
}))

const barOptions = { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
const doughnutOptions = { responsive: true, maintainAspectRatio: false }

function formatDate(d) {
  try {
    return new Date(d).toLocaleString()
  } catch { return d }
}
</script>

<script>
export default {
  components: {
    KpiCard: {
      props: { title: String, value: [String, Number], subtitle: String, icon: String, color: String },
      template: `
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br text-white flex items-center justify-center" :class="color">
                  <i class="pi" :class="iconMap[icon] || 'pi-chart-bar' "></i>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">{{ title }}</dt>
                  <dd class="text-2xl font-bold text-gray-900">{{ value }}</dd>
                  <div v-if="subtitle" class="text-xs text-gray-500">{{ subtitle }}</div>
                </dl>
              </div>
            </div>
          </div>
        </div>
      `,
      data() { return { iconMap: { doc: 'pi-file', ai: 'pi-bolt', users: 'pi-users', check: 'pi-check-circle' } } }
    }
  }
}
</script>

<style scoped>
/***** ensure charts have height *****/
:deep(canvas) { height: 280px !important; }
</style>
