<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AdminLayout from './Layout.vue';
import { useActivityLogs } from '@/composables/useActivityLogs';
import type { ActivityLogFilters } from '@/composables/useActivityLogs';

const { logs, stats, models, loading, pagination, fetchLogs, fetchStats, fetchModels } = useActivityLogs();

const filters = ref<ActivityLogFilters>({
  action: 'all',
  model: 'all',
  search: '',
  per_page: 20,
});

const currentPage = ref(1);
const selectedLog = ref<any>(null);
const showDetailsModal = ref(false);

const actionColors = {
  create: 'bg-green-100 text-green-800 border-green-300',
  update: 'bg-blue-100 text-blue-800 border-blue-300',
  delete: 'bg-red-100 text-red-800 border-red-300',
};

const actionIcons = {
  create: 'M12 4v16m8-8H4',
  update: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
  delete: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
};

const applyFilters = async () => {
  currentPage.value = 1;
  const filterParams = { ...filters.value };
  if (filterParams.action === 'all') delete filterParams.action;
  if (filterParams.model === 'all') delete filterParams.model;
  if (!filterParams.search) delete filterParams.search;
  
  await fetchLogs(filterParams);
};

const changePage = async (page: number) => {
  currentPage.value = page;
  const filterParams = { ...filters.value, page };
  if (filterParams.action === 'all') delete filterParams.action;
  if (filterParams.model === 'all') delete filterParams.model;
  if (!filterParams.search) delete filterParams.search;
  
  await fetchLogs(filterParams);
};

const viewDetails = (log: any) => {
  selectedLog.value = log;
  showDetailsModal.value = true;
};

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffMs = now.getTime() - date.getTime();
  const diffMins = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMs / 3600000);
  const diffDays = Math.floor(diffMs / 86400000);

  if (diffMins < 1) return 'Just now';
  if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
  if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
  if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
  
  return date.toLocaleString();
};

const getActionBadgeClass = (action: string) => {
  return actionColors[action as keyof typeof actionColors] || 'bg-gray-100 text-gray-800';
};

const getActionIcon = (action: string) => {
  return actionIcons[action as keyof typeof actionIcons] || actionIcons.update;
};

onMounted(async () => {
  await Promise.all([
    fetchLogs(filters.value),
    fetchStats(),
    fetchModels(),
  ]);
});
</script>

<template>
  <AdminLayout>
    <div class="min-h-screen p-6 bg-gray-50">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="mb-2 text-2xl font-bold text-gray-900">Activity Logs</h1>
        <p class="text-gray-600">Track all admin actions and changes</p>
      </div>

      <!-- Statistics Cards -->
      <div v-if="stats" class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-4">
        <div class="p-4 bg-white border border-gray-200 shadow-sm rounded-xl">
          <div class="text-sm text-gray-500">Total Activities</div>
          <div class="text-2xl font-bold text-gray-900">{{ stats.total_activities }}</div>
        </div>
        <div class="p-4 bg-white border border-gray-200 shadow-sm rounded-xl">
          <div class="text-sm text-gray-500">Today</div>
          <div class="text-2xl font-bold text-green-600">{{ stats.today }}</div>
        </div>
        <div class="p-4 bg-white border border-gray-200 shadow-sm rounded-xl">
          <div class="text-sm text-gray-500">This Week</div>
          <div class="text-2xl font-bold text-blue-600">{{ stats.this_week }}</div>
        </div>
        <div class="p-4 bg-white border border-gray-200 shadow-sm rounded-xl">
          <div class="text-sm text-gray-500">This Month</div>
          <div class="text-2xl font-bold text-purple-600">{{ stats.this_month }}</div>
        </div>
      </div>

      <!-- Filters -->
      <div class="p-4 mb-6 bg-white border border-gray-200 shadow-sm rounded-xl">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
          <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Action</label>
            <select
              v-model="filters.action"
              @change="applyFilters"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
            >
              <option value="all">All Actions</option>
              <option value="create">Create</option>
              <option value="update">Update</option>
              <option value="delete">Delete</option>
            </select>
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Model</label>
            <select
              v-model="filters.model"
              @change="applyFilters"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
            >
              <option value="all">All Models</option>
              <option v-for="model in models" :key="model" :value="model">{{ model }}</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="block mb-1 text-sm font-medium text-gray-700">Search</label>
            <input
              v-model="filters.search"
              @keyup.enter="applyFilters"
              type="text"
              placeholder="Search in descriptions..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
            />
          </div>
        </div>
        <div class="mt-3">
          <button
            @click="applyFilters"
            class="px-4 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800"
          >
            Apply Filters
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="relative">
          <div class="w-16 h-16 border-4 border-gray-200 rounded-full"></div>
          <div class="absolute top-0 left-0 w-16 h-16 border-4 border-black rounded-full animate-spin border-t-transparent"></div>
        </div>
      </div>

      <!-- Activity Timeline -->
      <div v-else class="space-y-4">
        <div
          v-for="log in logs"
          :key="log.id"
          class="p-4 transition-shadow bg-white border border-gray-200 shadow-sm cursor-pointer rounded-xl hover:shadow-md"
          @click="viewDetails(log)"
        >
          <div class="flex items-start gap-4">
            <!-- Icon -->
            <div :class="['flex items-center justify-center w-10 h-10 rounded-full', getActionBadgeClass(log.action)]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getActionIcon(log.action)" />
              </svg>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <p class="font-medium text-gray-900">{{ log.description }}</p>
                  <div class="flex items-center gap-2 mt-1 text-sm text-gray-500">
                    <span class="font-medium">{{ log.user?.name || 'Unknown User' }}</span>
                    <span>•</span>
                    <span>{{ log.user?.email }}</span>
                  </div>
                </div>
                <div class="flex flex-col items-end gap-2 ml-4">
                  <span :class="['px-2 py-1 text-xs font-medium border rounded-full', getActionBadgeClass(log.action)]">
                    {{ log.action }}
                  </span>
                  <span class="text-xs text-gray-500">{{ formatDate(log.created_at) }}</span>
                </div>
              </div>
              <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                <span class="px-2 py-1 bg-gray-100 rounded">{{ log.model }}</span>
                <span v-if="log.model_id">#{{ log.model_id }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="logs.length === 0" class="py-20 text-center">
          <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="mb-2 text-lg font-medium text-gray-900">No activity logs found</h3>
          <p class="text-gray-500">Try adjusting your filters</p>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="flex items-center justify-between p-4 mt-6 bg-white border border-gray-200 rounded-xl">
        <div class="text-sm text-gray-600">
          Showing {{ ((pagination.current_page - 1) * pagination.per_page) + 1 }} - 
          {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} 
          of {{ pagination.total }} logs
        </div>
        <div class="flex gap-2">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-3 py-1 text-sm font-medium border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
          >
            Previous
          </button>
          <span class="flex items-center px-3 py-1 text-sm font-medium">
            {{ pagination.current_page }} / {{ pagination.last_page }}
          </span>
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1 text-sm font-medium border border-gray-300 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
          >
            Next
          </button>
        </div>
      </div>

      <!-- Details Modal -->
      <div v-if="showDetailsModal && selectedLog" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-2xl p-6 bg-white rounded-xl max-h-[90vh] overflow-y-auto">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h2 class="text-xl font-bold">Activity Details</h2>
              <p class="text-sm text-gray-500">{{ formatDate(selectedLog.created_at) }}</p>
            </div>
            <button @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="space-y-4">
            <div>
              <h3 class="text-sm font-medium text-gray-700">Description</h3>
              <p class="text-gray-900">{{ selectedLog.description }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <h3 class="text-sm font-medium text-gray-700">User</h3>
                <p class="text-gray-900">{{ selectedLog.user?.name }}</p>
                <p class="text-sm text-gray-500">{{ selectedLog.user?.email }}</p>
              </div>
              <div>
                <h3 class="text-sm font-medium text-gray-700">Action</h3>
                <span :class="['inline-block px-2 py-1 text-xs font-medium border rounded-full', getActionBadgeClass(selectedLog.action)]">
                  {{ selectedLog.action }}
                </span>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <h3 class="text-sm font-medium text-gray-700">Model</h3>
                <p class="text-gray-900">{{ selectedLog.model }}</p>
              </div>
              <div>
                <h3 class="text-sm font-medium text-gray-700">Model ID</h3>
                <p class="text-gray-900">{{ selectedLog.model_id || 'N/A' }}</p>
              </div>
            </div>

            <div v-if="selectedLog.old_values" class="p-3 border border-gray-200 rounded-lg bg-red-50">
              <h3 class="mb-2 text-sm font-medium text-gray-700">Old Values</h3>
              <pre class="text-xs text-gray-900 whitespace-pre-wrap">{{ JSON.stringify(selectedLog.old_values, null, 2) }}</pre>
            </div>

            <div v-if="selectedLog.new_values" class="p-3 border border-gray-200 rounded-lg bg-green-50">
              <h3 class="mb-2 text-sm font-medium text-gray-700">New Values</h3>
              <pre class="text-xs text-gray-900 whitespace-pre-wrap">{{ JSON.stringify(selectedLog.new_values, null, 2) }}</pre>
            </div>

            <div class="grid grid-cols-2 gap-4 text-xs text-gray-500">
              <div>
                <h3 class="font-medium">IP Address</h3>
                <p>{{ selectedLog.ip_address || 'N/A' }}</p>
              </div>
              <div>
                <h3 class="font-medium">User Agent</h3>
                <p class="truncate">{{ selectedLog.user_agent || 'N/A' }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
