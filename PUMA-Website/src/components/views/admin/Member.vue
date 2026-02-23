<template>
    <AdminLayout>
        <div class="container relative p-4 mx-auto overflow-hidden font-mono">
            <div class="absolute inset-0 z-0 grid-lines opacity-10"></div>
            
            <div class="relative z-10">
                
                <div class="mb-6">
                    <button @click="openAddModal"
                        class="flex items-center px-4 py-3 font-medium transition-all duration-300 bg-white border rounded-lg shadow-md border-black/20 hover:bg-black hover:text-white group">
                        <span
                            class="mr-2 text-xs px-1.5 py-0.5 rounded-sm bg-black/10 text-black/80 group-hover:bg-white group-hover:text-black transition-colors duration-300">
                            01
                        </span>
                        ADD NEW MEMBER
                    </button>
                </div>

                <div class="relative overflow-hidden bg-white border rounded-lg shadow-lg border-black/10">
                    <div class="absolute inset-0 grid-lines opacity-5"></div>

                    <div class="relative p-6">
                        <div class="flex flex-col justify-betweeyn mb-6 md:flex-row md:items-center">
                            <h2 class="mb-4 text-xl font-bold tracking-wider uppercase md:mb-0">Team Members</h2>

                            <div class="flex flex-col gap-4 md:flex-row">
                                <div class="relative">
                                    <select v-model="divisionFilter" 
                                        class="w-full p-2 pl-3 font-mono text-sm border rounded-lg border-black/20 bg-white/80 backdrop-blur-sm">
                                        <option value="">All Divisions</option>
                                        <option v-for="division in divisions" :key="division.id" :value="division.name">
                                            {{ division.title }}
                                        </option>
                                    </select>
                                </div>
                                <div class="relative">
                                    <select v-model="cabinetFilter" 
                                        class="w-full p-2 pl-3 font-mono text-sm border rounded-lg border-black/20 bg-white/80 backdrop-blur-sm">
                                        <option value="">All Cabinets</option>
                                        <option v-for="cabinet in cabinets" :key="cabinet.id" :value="cabinet.name">
                                            {{ cabinet.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="relative">
                                    <input v-model="searchQuery" placeholder="Search members..."
                                        class="w-full p-2 pl-3 font-mono text-sm border rounded-lg md:w-64 border-black/20 bg-white/80 backdrop-blur-sm">
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr>
                                        <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            ID</th>
                                        <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Avatar</th>
                                        <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Name</th>
                                        <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Position</th>
                                        <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Division</th>
                                        <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Cabinet</th>
                                        <th class="p-3 text-xs tracking-wider text-center uppercase border-b border-black/10">
                                            Visible</th>
                                        <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="member in paginatedMembers" :key="member.id"
                                        class="transition-colors border-b border-black/5 hover:bg-black/5">
                                        <td class="p-3">
                                            <span class="inline-block px-2 py-1 text-xs text-white bg-black rounded">
                                                {{ member.id.toString().padStart(2, '0') }}
                                            </span>
                                        </td>
                                        <td class="p-3">
                                            <div class="w-10 h-10 overflow-hidden rounded-full">
                                                <img :src="member.avatar" alt="Avatar" class="object-cover w-full h-full">
                                            </div>
                                        </td>
                                        <td class="p-3 font-medium">{{ member.name }}</td>
                                        <td class="p-3 text-black/70">{{ member.position }}</td>
                                        <td class="p-3">
                                            <span class="px-2 py-1 text-xs bg-white border rounded-full border-black/20">
                                                {{ member.division }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-black/70">{{ member.cabinet }}</td>
                                        <td class="p-3 text-center">
                                            <button @click="toggleVisibility(member.id)" 
                                                :class="member.is_visible !== false ? 'text-green-600 hover:text-green-800' : 'text-gray-400 hover:text-gray-600'"
                                                class="transition-colors duration-200"
                                                :title="member.is_visible !== false ? 'Visible - Click to hide' : 'Hidden - Click to show'">
                                                <svg v-if="member.is_visible !== false" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                </svg>
                                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                                </svg>
                                            </button>
                                        </td>
                                        <td class="p-3">
                                            <div class="flex gap-2">
                                                <button @click="viewDetails(member)"
                                                    class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                    <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                                                    View
                                                </button>
                                                <button @click="editItem(member)"
                                                    class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                    <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">02</span>
                                                    Edit
                                                </button>
                                                <button @click="deleteItem(member.id)"
                                                    class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                    <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">03</span>
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="paginatedMembers.length === 0">
                                        <td colspan="7" class="p-4 text-center text-black/50">No members found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-between pt-4 mt-4 border-t border-black/10">
                            <div class="text-sm text-black/60">
                                Showing {{ startIndex + 1 }}-{{ endIndex }} of {{ filteredMembers.length }} members
                            </div>
                            <div class="flex gap-2">
                                <button @click="pageNum--" :disabled="pageNum === 1"
                                    class="flex items-center px-3 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-black hover:text-white">
                                    Prev
                                </button>
                                <span class="flex items-center px-3 py-1 text-xs font-medium">
                                    {{ pageNum }} / {{ totalPages }}
                                </span>
                                <button @click="pageNum++" :disabled="pageNum === totalPages"
                                    class="flex items-center px-3 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-black hover:text-white">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="showEditModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                <div class="relative w-full max-w-2xl p-0 overflow-hidden bg-white rounded-lg shadow-2xl">
                    <div class="absolute inset-0 grid-lines opacity-10"></div>

                    <div class="relative p-6">
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-black/10">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 mr-3 bg-black rounded-full">
                                    <span class="text-xs font-bold text-white">{{ isEditing ? 'ED' : 'NEW' }}</span>
                                </div>
                                <h3 class="text-xl font-bold tracking-wider uppercase">{{ isEditing ? 'Edit Member' :
                                    'Add New Member' }}</h3>
                            </div>
                            <button @click="showEditModal = false" class="transition-colors text-black/50 hover:text-black">
                                &times;
                            </button>
                        </div>

                        <form @submit.prevent="saveMember">
                            <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
                                <div class="mb-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Name</label>
                                    <input v-model="currentItem.name" type="text"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" required>
                                </div>

                                <div class="mb-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Email</label>
                                    <input v-model="currentItem.email" type="email"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" required>
                                </div>

                                <div class="mb-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Position</label>
                                    <input v-model="currentItem.position" type="text"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" required>
                                </div>

                                <div class="mb-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Batch</label>
                                    <input v-model="currentItem.batch" type="text"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" required>
                                </div>

                                <div class="mb-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Division</label>
                                    <select v-model="currentItem.division_id"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" required>
                                        <option v-for="division in divisions" :key="division.id" :value="division.id">
                                            {{ division.title }}
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Cabinet</label>
                                    <select v-model="currentItem.cabinet_id"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" required>
                                        <option v-for="cabinet in cabinets" :key="cabinet.id" :value="cabinet.id">
                                            {{ cabinet.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-2 md:col-span-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Avatar</label>
                                    <div class="flex items-center gap-4">
                                        <div v-if="currentItem.avatar" class="w-16 h-16 overflow-hidden rounded-full">
                                            <img :src="currentItem.avatar" alt="Avatar" class="object-cover w-full h-full">
                                        </div>
                                        <input type="file" @change="handleAvatarUpload" accept="image/*"
                                            class="flex-1 p-3 font-mono border rounded border-black/20 bg-white/80">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-black/10">
                                <button type="button" @click="showEditModal = false"
                                    class="flex items-center px-4 py-2 text-sm font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black/10">
                                    <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="flex items-center px-4 py-2 text-sm font-medium text-white uppercase transition-all duration-300 bg-black border border-black rounded hover:bg-black/80">
                                    <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-white/20">02</span>
                                    {{ isEditing ? 'Update' : 'Add' }} Member
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div v-if="showDetailsModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                <div class="relative w-full max-w-2xl overflow-hidden bg-white rounded-lg shadow-2xl">
                    <div class="absolute inset-0 grid-lines opacity-10"></div>

                    <div class="relative p-6">
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-black/10">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 mr-3 bg-black rounded-full">
                                    <span class="text-xs font-bold text-white">{{ selectedItem.id?.toString().padStart(2,
                                        '0') || '00' }}</span>
                                </div>
                                <h3 class="text-xl font-bold tracking-wider uppercase">{{ selectedItem.name }}</h3>
                            </div>
                            <button @click="showDetailsModal = false"
                                class="transition-colors text-black/50 hover:text-black">
                                &times;
                            </button>
                        </div>
                        
                        <div class="flex flex-col gap-6 md:flex-row">
                            <div class="flex-shrink-0">
                                <div class="w-32 h-32 mx-auto overflow-hidden rounded-full">
                                    <img :src="selectedItem.avatar" alt="Avatar" class="object-cover w-full h-full">
                                </div>
                            </div>
                            
                            <div class="flex-1">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Email</h4>
                                        <p class="font-medium">{{ selectedItem.email }}</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Position</h4>
                                        <p class="font-medium">{{ selectedItem.position }}</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Division</h4>
                                        <p class="font-medium">{{ getDivisionTitle(selectedItem.division) }}</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Cabinet</h4>
                                        <p class="font-medium">{{ selectedItem.cabinet }}</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Batch</h4>
                                        <p class="font-medium">{{ selectedItem.batch }}</p>
                                    </div>
                                </div>
                                
                                <div v-if="selectedDivision" class="p-4 mt-4 rounded-lg bg-black/5">
                                    <h4 class="mb-2 text-xs font-bold tracking-wider uppercase text-black/70">Division Info</h4>
                                    <p class="mb-2">{{ selectedDivision.title }}</p>
                                    <p class="text-black/80">{{ selectedDivision.description }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 mt-6 border-t border-black/10">
                            <button @click="showDetailsModal = false"
                                class="flex items-center px-4 py-2 text-sm font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="showDeleteModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                <div class="relative w-full max-w-md overflow-hidden bg-white rounded-lg shadow-2xl">
                    <div class="absolute inset-0 grid-lines opacity-10"></div>

                    <div class="relative p-6">
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-black/10">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 mr-3 bg-black rounded-full">
                                    <span class="text-xs font-bold text-white">DEL</span>
                                </div>
                                <h3 class="text-xl font-bold tracking-wider uppercase">Confirm Delete</h3>
                            </div>
                        </div>

                        <p class="mb-6">Are you sure you want to delete this member? This action cannot be undone.</p>
                        
                        <div class="flex justify-end gap-3 pt-4 border-t border-black/10">
                            <button @click="showDeleteModal = false"
                                class="flex items-center px-4 py-2 text-sm font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black/10">
                                <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                                Cancel
                            </button>
                            <button @click="confirmDelete"
                                class="flex items-center px-4 py-2 text-sm font-medium text-white uppercase transition-all duration-300 bg-black border border-black rounded hover:bg-black/80">
                                <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-white/20">02</span>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch, h } from 'vue';
import AdminLayout from './Layout.vue';
import { useMembers } from '@/composables/useMembers';
import { useDivisions } from '@/composables/useDivisions';
import { useCabinets } from '@/composables/useCabinets';
import { toast } from '@/components/ui/toast';
import { ToastAction } from '@/components/ui/toast';

const { members, loading, error, fetchMembers, createMember, updateMember, deleteMember, toggleMemberVisibility, uploadMemberPhoto } = useMembers();
const { divisions, fetchDivisions } = useDivisions();
const { cabinets, fetchCabinets } = useCabinets();

// Fetch data on mount
onMounted(() => {
    fetchMembers();
    fetchDivisions();
    fetchCabinets();
});

const searchQuery = ref('');
const divisionFilter = ref('');
const cabinetFilter = ref('');
const pageNum = ref(1);
const itemsPerPage = 5;

const showEditModal = ref(false);
const showDetailsModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const currentItem = ref({
    name: '',
    email: '',
    position: '',
    cabinet: '',
    batch: '',
    division: '',
    avatar: ''
});
const selectedItem = ref({});
const deleteId = ref(null);
// Avatar file for new members - DO NOT reassign, always use .value
const uploadedAvatarFile = ref(null);

const filteredMembers = computed(() => {
    let result = [...members.value];
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(member => 
            member.name.toLowerCase().includes(query) || 
            member.email.toLowerCase().includes(query) ||
            member.position.toLowerCase().includes(query)
        );
    }
    
    if (divisionFilter.value) {
        result = result.filter(member => member.division === divisionFilter.value);
    }
    
    if (cabinetFilter.value) {
        result = result.filter(member => member.cabinet === cabinetFilter.value);
    }
    
    return result;
});

const totalPages = computed(() => {
    return Math.ceil(filteredMembers.value.length / itemsPerPage);
});

const startIndex = computed(() => {
    return (pageNum.value - 1) * itemsPerPage;
});

const endIndex = computed(() => {
    const calculatedEnd = startIndex.value + itemsPerPage;
    return Math.min(calculatedEnd, filteredMembers.value.length);
});

const paginatedMembers = computed(() => {
    return filteredMembers.value.slice(startIndex.value, endIndex.value);
});

const selectedDivision = computed(() => {
    if (selectedItem.value && selectedItem.value.division) {
        return divisions.value.find(div => div.name === selectedItem.value.division);
    }
    return null;
});

function getDivisionTitle(divisionName) {
    const division = divisions.value.find(d => d.name === divisionName);
    return division ? division.title : divisionName;
}


function openAddModal() {
    console.log('openAddModal called');
    console.log('Type checks:', {
        currentItem: typeof currentItem,
        uploadedAvatarFile: typeof uploadedAvatarFile,
        isEditing: typeof isEditing,
        showEditModal: typeof showEditModal
    });
    
    currentItem.value = {
        name: '',
        email: '',
        position: '',
        cabinet_id: cabinets.value[0]?.id ?? null,
        batch: '',
        division_id: divisions.value[0]?.id ?? null,
        avatar: 'https://i.pinimg.com/736x/f2/96/65/f296659f98543ad0ee11738a62e7652f.jpg',
        user_id: null,
    };
    uploadedAvatarFile.value = null; // Clear any previous file
    isEditing.value = false;
    showEditModal.value = true;
}

function editItem(member) {
    currentItem.value = { ...member };
    isEditing.value = true;
    showEditModal.value = true;
}

function viewDetails(member) {
    selectedItem.value = { ...member };
    showDetailsModal.value = true;
}

function deleteItem(id) {
    deleteId.value = id;
    showDeleteModal.value = true;
}

const deletedMember = ref(null);

async function confirmDelete() {
    if (deleteId.value !== null) {
        try {
            const memberToDelete = members.value.find(m => m.id === deleteId.value);
            deletedMember.value = { ...memberToDelete };
            const deletedId = deleteId.value;

            await deleteMember(deleteId.value);
            await fetchMembers();
            showDeleteModal.value = false;
            deleteId.value = null;

            toast({
                title: 'Member Deleted',
                description: 'The member has been successfully deleted.',
                action: h(ToastAction, {
                    altText: 'Undo',
                    onClick: async () => {
                        if (deletedMember.value) {
                            try {
                                const restoreData = {
                                    user_id: deletedMember.value.user_id,
                                    cabinet_id: deletedMember.value.cabinet_id,
                                    division_id: deletedMember.value.division_id,
                                    position: deletedMember.value.position,
                                };
                                await createMember(restoreData);
                                await fetchMembers();
                                
                                toast({
                                    title: 'Member Restored',
                                    description: 'The member has been restored successfully.',
                                });
                            } catch (err) {
                                console.error('Error restoring member:', err);
                                toast({
                                    title: 'Error',
                                    description: 'Failed to restore member.',
                                    variant: 'destructive',
                                });
                            }
                        }
                    }
                }, () => 'Undo'),
            });

            if (paginatedMembers.value.length === 0 && pageNum.value > 1) {
                pageNum.value = 1;
            }
        } catch (err) {
            console.error('Error deleting member:', err);
            toast({
                title: 'Error',
                description: 'Failed to delete member. Please try again.',
                variant: 'destructive',
            });
        }
    }
}

async function saveMember() {
    try {
        const memberData = {
            name: currentItem.value.name,
            email: currentItem.value.email,
            batch: currentItem.value.batch,
            user_id: currentItem.value.user_id,
            cabinet_id: currentItem.value.cabinet_id,
            division_id: currentItem.value.division_id,
            position: currentItem.value.position,
            // Don't include avatar in data - it will be handled separately
        };

        if (isEditing.value && currentItem.value.id) {
            // For updates, if there's a new file it will be uploaded separately via handleAvatarUpload
            await updateMember(currentItem.value.id, memberData);
            toast({
                title: 'Member Updated',
                description: 'The member has been successfully updated.',
            });
        } else {
            // For new members, pass the file separately
            await createMember(memberData, uploadedAvatarFile.value);
            toast({
                title: 'Member Created',
                description: 'The member has been successfully created.',
            });
            uploadedAvatarFile.value = null; // Clear file after creation
        }

        await fetchMembers();
        showEditModal.value = false;
    } catch (error) {
        console.error('Error saving member:', error);
        toast({
            title: 'Error',
            description: 'Failed to save member. Please try again.',
            variant: 'destructive',
        });
    }
}



// Toggle member visibility
async function toggleVisibility(memberId) {
    try {
        await toggleMemberVisibility(memberId);
        await fetchMembers();
        toast({
            title: 'Visibility Updated',
            description: 'Member visibility has been toggled successfully.',
        });
    } catch (err) {
        console.error('Error toggling visibility:', err);
        toast({
            title: 'Error',
            description: 'Failed to toggle visibility. Please try again.',
            variant: 'destructive',
        });
    }
}

// Handle avatar upload with actual file upload for existing members
const uploadingPhoto = ref(false);

async function handleAvatarUpload(event) {
    const file = event.target.files?.[0];
    if (!file) return;

    // Validate file type
    if (!file.type.startsWith('image/')) {
        toast({
            title: 'Invalid File',
            description: 'Please select an image file.',
            variant: 'destructive',
        });
        return;
    }

    // Validate file size (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
        toast({
            title: 'File Too Large',
            description: 'Please select an image smaller than 2MB.',
            variant: 'destructive',
        });
        return;
    }

    // If editing existing member, upload immediately
    if (isEditing.value && currentItem.value.id) {
        uploadingPhoto.value = true;
        try {
            const result = await uploadMemberPhoto(currentItem.value.id, file);
            currentItem.value.avatar = result.photo_path;
            await fetchMembers();
            toast({
                title: 'Photo Uploaded',
                description: 'Member photo has been updated successfully.',
            });
        } catch (err) {
            console.error('Error uploading photo:', err);
            toast({
                title: 'Upload Failed',
                description: 'Failed to upload photo. Please try again.',
                variant: 'destructive',
            });
        } finally {
            uploadingPhoto.value = false;
        }
    } else {
        // For new members, store the file and create a preview
        uploadedAvatarFile.value = file;
        
        // Create preview URL for display
        const reader = new FileReader();
        reader.onload = (e) => {
            currentItem.value.avatar = e.target?.result;
        };
        reader.readAsDataURL(file);
    }
}

watch([searchQuery, divisionFilter, cabinetFilter], () => {
    pageNum.value = 1;
});

const style = document.createElement('style');
style.textContent = `
.grid-lines {
    background-image: 
        linear-gradient(to right, rgba(0,0,0,0.05) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(0,0,0,0.05) 1px, transparent 1px);
    background-size: 20px 20px;
}
`;
document.head.appendChild(style);
</script>