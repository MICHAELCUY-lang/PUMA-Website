<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import AdminLayout from './Layout.vue';
import { useCabinets } from '@/composables/useCabinets';
import { useMembers } from '@/composables/useMembers';
import { useDivisions } from '@/composables/useDivisions';

interface Cabinet {
  id: number;
  name: string;
  description?: string;
  year?: string;
  status?: string;
  divisions?: any[];
  divisions_count?: number;
}

const { cabinets, loading: cabinetsLoading, fetchCabinets, updateCabinet, deleteCabinet } = useCabinets();
const { members, fetchMembers } = useMembers();
const { divisions, fetchDivisions } = useDivisions();

const selectedCabinet = ref<Cabinet | null>(null);
const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const isViewModalOpen = ref(false);
const deletePassword = ref('');
const deletePasswordError = ref('');
const editForm = ref({
  name: '',
  description: '',
  year: '',
  status: 'active',
});

const searchQuery = ref('');
const filterStatus = ref('all');

const filteredCabinets = computed(() => {
  let result = cabinets.value;
  
  if (filterStatus.value !== 'all') {
    result = result.filter((c: any) => c.status === filterStatus.value);
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter((c: any) => 
      c.name.toLowerCase().includes(query) ||
      (c.description && c.description.toLowerCase().includes(query))
    );
  }
  
  return result;
});

const getCabinetMembers = (cabinetName: string) => {
  return members.value.filter((m: any) => 
    m.cabinet === cabinetName || m.cabinet === `${cabinetName} Cabinet`
  );
};

const getCabinetDivisions = (cabinetId: number) => {
  const cabinet = cabinets.value.find((c: any) => c.id === cabinetId);
  return cabinet?.divisions || [];
};

const getCabinetStats = (cabinet: any) => {
  const cabinetMembers = getCabinetMembers(cabinet.name);
  const cabinetDivisions = getCabinetDivisions(cabinet.id);
  
  return {
    totalMembers: cabinetMembers.length,
    activeMembers: cabinetMembers.filter((m: any) => m.is_visible).length,
    totalDivisions: cabinetDivisions.length,
  };
};

const openEditModal = (cabinet: Cabinet) => {
  selectedCabinet.value = cabinet;
  editForm.value = {
    name: cabinet.name,
    description: cabinet.description || '',
    year: cabinet.year || '',
    status: cabinet.status || 'active',
  };
  isEditModalOpen.value = true;
};

const openViewModal = (cabinet: Cabinet) => {
  selectedCabinet.value = cabinet;
  isViewModalOpen.value = true;
};

const openDeleteModal = (cabinet: Cabinet) => {
  selectedCabinet.value = cabinet;
  deletePassword.value = '';
  deletePasswordError.value = '';
  isDeleteModalOpen.value = true;
};

const closeModals = () => {
  isEditModalOpen.value = false;
  isDeleteModalOpen.value = false;
  isViewModalOpen.value = false;
  selectedCabinet.value = null;
  deletePassword.value = '';
  deletePasswordError.value = '';
};

const handleSave = async () => {
  if (!selectedCabinet.value) return;
  
  try {
    await updateCabinet(selectedCabinet.value.id, editForm.value);
    await fetchCabinets();
    closeModals();
  } catch (error) {
    console.error('Error updating cabinet:', error);
    alert('Failed to update cabinet');
  }
};

const handleDelete = async () => {
  if (!selectedCabinet.value) return;
  
  // Verify confirmation text is entered
  if (!deletePassword.value) {
    deletePasswordError.value = 'Confirmation is required';
    return;
  }
  
  // Check if user typed the cabinet name correctly
  if (deletePassword.value.toLowerCase() !== selectedCabinet.value.name.toLowerCase()) {
    deletePasswordError.value = `Please type "${selectedCabinet.value.name}" to confirm`;
    return;
  }
  
  try {
    // Proceed with deletion
    await deleteCabinet(selectedCabinet.value.id);
    await fetchCabinets();
    closeModals();
  } catch (error: any) {
    console.error('Error deleting cabinet:', error);
    alert('Failed to delete cabinet');
  }
};

const getStatusColor = (status: string) => {
  return status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
};

const getStatusDot = (status: string) => {
  return status === 'active' ? 'bg-green-500' : 'bg-gray-400';
};

onMounted(async () => {
  await Promise.all([
    fetchCabinets(),
    fetchMembers(),
    fetchDivisions(),
  ]);
});
</script>

<template>
  <AdminLayout>
    <div class="min-h-screen p-6 bg-gray-50">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="mb-2 text-2xl font-bold text-gray-900">Cabinet Management</h1>
        <p class="text-gray-600">Manage cabinets, members, and divisions</p>
      </div>

      <!-- Filters -->
      <div class="flex flex-col gap-4 p-4 mb-6 bg-white border border-gray-200 shadow-sm rounded-xl sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search cabinets..."
            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
          />
          <select
            v-model="filterStatus"
            class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
          >
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="text-sm text-gray-600">
          {{ filteredCabinets.length }} cabinet(s) found
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="cabinetsLoading" class="flex items-center justify-center py-20">
        <div class="relative">
          <div class="w-16 h-16 border-4 border-gray-200 rounded-full"></div>
          <div class="absolute top-0 left-0 w-16 h-16 border-4 border-black rounded-full animate-spin border-t-transparent"></div>
        </div>
      </div>

      <!-- Cabinet Cards -->
      <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="cabinet in filteredCabinets"
          :key="cabinet.id"
          class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-xl hover:shadow-md"
        >
          <!-- Cabinet Header -->
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="text-xl font-bold text-gray-900">{{ cabinet.name }}</h3>
              <p class="text-sm text-gray-500">Cabinet {{ cabinet.year || 'N/A' }}</p>
            </div>
            <span :class="['px-3 py-1 text-xs font-medium rounded-full', getStatusColor(cabinet.status || 'active')]">
              <span :class="['inline-block w-2 h-2 mr-1 rounded-full', getStatusDot(cabinet.status || 'active')]"></span>
              {{ cabinet.status || 'active' }}
            </span>
          </div>

          <!-- Description -->
          <p class="mb-4 text-sm text-gray-600 line-clamp-2">
            {{ cabinet.description || 'No description available' }}
          </p>

          <!-- Statistics -->
          <div class="grid grid-cols-3 gap-4 p-4 mb-4 border border-gray-200 rounded-lg bg-gray-50">
            <div class="text-center">
              <div class="text-2xl font-bold text-gray-900">{{ getCabinetStats(cabinet).totalMembers }}</div>
              <div class="text-xs text-gray-500">Members</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600">{{ getCabinetStats(cabinet).activeMembers }}</div>
              <div class="text-xs text-gray-500">Active</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-purple-600">{{ getCabinetStats(cabinet).totalDivisions }}</div>
              <div class="text-xs text-gray-500">Divisions</div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-2">
            <button
              @click="openViewModal(cabinet)"
              class="flex-1 px-4 py-2 text-sm font-medium text-white transition-colors bg-black rounded-lg hover:bg-gray-800"
            >
              View Details
            </button>
            <button
              @click="openEditModal(cabinet)"
              class="px-4 py-2 text-sm font-medium transition-colors bg-gray-100 rounded-lg hover:bg-gray-200"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </button>
            <button
              @click="openDeleteModal(cabinet)"
              class="px-4 py-2 text-sm font-medium text-red-600 transition-colors bg-red-50 rounded-lg hover:bg-red-100"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!cabinetsLoading && filteredCabinets.length === 0" class="py-20 text-center">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <h3 class="mb-2 text-lg font-medium text-gray-900">No cabinets found</h3>
        <p class="text-gray-500">Try adjusting your search or filters</p>
      </div>

      <!-- Edit Modal -->
      <div v-if="isEditModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-md p-6 bg-white rounded-xl">
          <h2 class="mb-4 text-xl font-bold">Edit Cabinet</h2>
          <div class="space-y-4">
            <div>
              <label class="block mb-1 text-sm font-medium text-gray-700">Name</label>
              <input
                v-model="editForm.name"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
              />
            </div>
            <div>
              <label class="block mb-1 text-sm font-medium text-gray-700">Year</label>
              <input
                v-model="editForm.year"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
              />
            </div>
            <div>
              <label class="block mb-1 text-sm font-medium text-gray-700">Description</label>
              <textarea
                v-model="editForm.description"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
              ></textarea>
            </div>
            <div>
              <label class="block mb-1 text-sm font-medium text-gray-700">Status</label>
              <select
                v-model="editForm.status"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black"
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="flex gap-2 mt-6">
            <button
              @click="handleSave"
              class="flex-1 px-4 py-2 text-white bg-black rounded-lg hover:bg-gray-800"
            >
              Save Changes
            </button>
            <button
              @click="closeModals"
              class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>

      <!-- View Modal -->
      <div v-if="isViewModalOpen && selectedCabinet" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-2xl p-6 bg-white rounded-xl max-h-[90vh] overflow-y-auto">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h2 class="text-2xl font-bold">{{ selectedCabinet.name }}</h2>
              <p class="text-gray-500">Cabinet {{ selectedCabinet.year }}</p>
            </div>
            <button @click="closeModals" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Description -->
          <p class="mb-6 text-gray-600">{{ selectedCabinet.description }}</p>

          <!-- Stats -->
          <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="p-4 text-center border border-gray-200 rounded-lg">
              <div class="text-3xl font-bold">{{ getCabinetStats(selectedCabinet).totalMembers }}</div>
              <div class="text-sm text-gray-500">Total Members</div>
            </div>
            <div class="p-4 text-center border border-gray-200 rounded-lg">
              <div class="text-3xl font-bold text-green-600">{{ getCabinetStats(selectedCabinet).activeMembers }}</div>
              <div class="text-sm text-gray-500">Active Members</div>
            </div>
            <div class="p-4 text-center border border-gray-200 rounded-lg">
              <div class="text-3xl font-bold text-purple-600">{{ getCabinetStats(selectedCabinet).totalDivisions }}</div>
              <div class="text-sm text-gray-500">Divisions</div>
            </div>
          </div>

          <!-- Divisions -->
          <div class="mb-6">
            <h3 class="mb-3 font-bold">Divisions</h3>
            <div class="grid grid-cols-2 gap-2">
              <div
                v-for="division in getCabinetDivisions(selectedCabinet.id)"
                :key="division.id"
                class="p-3 border border-gray-200 rounded-lg"
              >
                <div class="font-medium">{{ division.title }}</div>
                <div class="text-xs text-gray-500">{{ division.code }}</div>
              </div>
            </div>
            <div v-if="getCabinetDivisions(selectedCabinet.id).length === 0" class="py-8 text-center text-gray-500">
              No divisions assigned
            </div>
          </div>

          <!-- Members -->
          <div>
            <h3 class="mb-3 font-bold">Members</h3>
            <div class="space-y-2 max-h-60 overflow-y-auto">
              <div
                v-for="member in getCabinetMembers(selectedCabinet.name)"
                :key="member.id"
                class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg"
              >
                <img
                  :src="member.avatar || 'https://via.placeholder.com/40'"
                  :alt="member.name"
                  class="object-cover w-10 h-10 rounded-full"
                />
                <div class="flex-1">
                  <div class="font-medium">{{ member.name }}</div>
                  <div class="text-xs text-gray-500">{{ member.position }} - {{ member.division }}</div>
                </div>
              </div>
            </div>
            <div v-if="getCabinetMembers(selectedCabinet.name).length === 0" class="py-8 text-center text-gray-500">
              No members assigned
            </div>
          </div>
        </div>
      </div>

      <!-- Delete Modal -->
      <div v-if="isDeleteModalOpen && selectedCabinet" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-md p-6 bg-white rounded-xl">
          <div class="flex items-center gap-3 mb-4">
            <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <h2 class="text-xl font-bold text-red-600">Delete Cabinet</h2>
          </div>
          
          <div class="mb-6">
            <p class="mb-4 text-gray-600">
              Are you sure you want to delete <strong class="text-red-600">{{ selectedCabinet.name }}</strong>? 
            </p>
            <div class="p-3 mb-4 border-l-4 border-red-500 bg-red-50">
              <p class="text-sm font-medium text-red-800">⚠️ Warning:</p>
              <ul class="mt-2 ml-4 text-sm text-red-700 list-disc">
                <li>This action cannot be undone</li>
                <li>All associated data will be permanently deleted</li>
                <li>Members and divisions linked to this cabinet may be affected</li>
              </ul>
            </div>
            
            <div>
              <label class="block mb-2 text-sm font-medium text-gray-700">
                Type <strong class="text-red-600">{{ selectedCabinet.name }}</strong> to confirm:
              </label>
              <input
                v-model="deletePassword"
                type="text"
                :placeholder="`Type '${selectedCabinet.name}' here`"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                @keyup.enter="handleDelete"
              />
              <p v-if="deletePasswordError" class="mt-2 text-sm text-red-600">
                {{ deletePasswordError }}
              </p>
            </div>
          </div>
          
          <div class="flex gap-2">
            <button
              @click="handleDelete"
              :disabled="!deletePassword"
              class="flex-1 px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Delete Cabinet
            </button>
            <button
              @click="closeModals"
              class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
