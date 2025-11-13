<template>
    <AdminLayout>
        <div class="container relative p-4 mx-auto overflow-hidden font-mono">
            <div class="absolute inset-0 z-0 grid-lines opacity-10"></div>
            
            <div class="relative z-10">
                <div class="relative overflow-hidden bg-white border rounded-lg shadow-lg border-black/10">
                    <div class="absolute inset-0 grid-lines opacity-5"></div>

                    <div class="relative p-6">
                        <div class="flex flex-col justify-between mb-6 md:flex-row md:items-center">
                            <h2 class="mb-4 text-xl font-bold tracking-wider uppercase md:mb-0">User Accounts</h2>

                            <div class="flex flex-col gap-4 md:flex-row">
                                <div class="relative">
                                    <input v-model="searchQuery" placeholder="Search users..."
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
                                            Email</th>
                                        <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Role</th>
                                        <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Status</th>
                                        <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Joined</th>
                                        <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="loading">
                                        <td colspan="8" class="p-4 text-center text-black/50">Loading users...</td>
                                    </tr>
                                    <tr v-else-if="error">
                                        <td colspan="8" class="p-4 text-center text-red-500">{{ error }}</td>
                                    </tr>
                                    <template v-else>
                                        <tr v-for="guest in paginatedGuests" :key="guest.id"
                                            class="transition-colors border-b border-black/5 hover:bg-black/5">
                                            <td class="p-3">
                                                <span class="inline-block px-2 py-1 text-xs text-white bg-black rounded">
                                                    {{ guest.id.toString().padStart(2, '0') }}
                                                </span>
                                            </td>
                                            <td class="p-3">
                                                <div class="w-10 h-10 overflow-hidden rounded-full">
                                                    <img :src="guest.avatar || getDefaultAvatar()" alt="Avatar" class="object-cover w-full h-full">
                                                </div>
                                            </td>
                                            <td class="p-3 font-medium">{{ guest.name }}</td>
                                            <td class="p-3 text-black/70">{{ guest.email }}</td>
                                            <td class="p-3">
                                                <span :class="[
                                                    'inline-block px-2 py-1 text-xs font-semibold uppercase rounded',
                                                    guest.role === 'admin' ? 'bg-purple-100 text-purple-800' :
                                                    guest.role === 'member' ? 'bg-blue-100 text-blue-800' :
                                                    guest.role === 'instructor' ? 'bg-green-100 text-green-800' :
                                                    'bg-gray-100 text-gray-800'
                                                ]">
                                                    {{ guest.role }}
                                                </span>
                                            </td>
                                            <td class="p-3">
                                                <span :class="[
                                                    'inline-block px-2 py-1 text-xs font-semibold uppercase rounded',
                                                    guest.status === 'active' ? 'bg-green-100 text-green-800' :
                                                    guest.status === 'inactive' ? 'bg-red-100 text-red-800' :
                                                    'bg-yellow-100 text-yellow-800'
                                                ]">
                                                    {{ guest.status }}
                                                </span>
                                            </td>
                                            <td class="p-3 text-black/70">{{ formatDate(guest.created_at) }}</td>
                                            <td class="p-3">
                                                <div class="flex gap-2">
                                                    <button @click="viewDetails(guest)"
                                                        class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                        <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                                                        View
                                                    </button>
                                                    <button @click="openRoleChangeModal(guest)"
                                                        class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                        <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">02</span>
                                                        Role
                                                    </button>
                                                    <button @click="deleteItem(guest.id)"
                                                        class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                        <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">03</span>
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="paginatedGuests.length === 0">
                                            <td colspan="8" class="p-4 text-center text-black/50">No users found</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-between pt-4 mt-4 border-t border-black/10">
                            <div class="text-sm text-black/60">
                                Showing {{ startIndex + 1 }}-{{ endIndex }} of {{ totalUsers }} users
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

            <div v-if="showDetailsModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                <div class="relative w-full max-w-2xl overflow-hidden bg-white rounded-lg shadow-2xl">
                    <div class="absolute inset-0 grid-lines opacity-10"></div>

                    <div class="relative p-6">
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-black/10">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 mr-3 bg-black rounded-full">
                                    <span class="text-xs font-bold text-white">{{ selectedGuest.id?.toString().padStart(2,
                                        '0') || '00' }}</span>
                                </div>
                                <h3 class="text-xl font-bold tracking-wider uppercase">{{ selectedGuest.name }}</h3>
                            </div>
                            <button @click="showDetailsModal = false"
                                class="transition-colors text-black/50 hover:text-black">
                                &times;
                            </button>
                        </div>
                        
                        <div class="flex flex-col gap-6 md:flex-row">
                            <div class="flex-shrink-0">
                                <div class="w-32 h-32 mx-auto overflow-hidden rounded-full">
                                    <img :src="selectedGuest.avatar" alt="Avatar" class="object-cover w-full h-full">
                                </div>
                            </div>
                            
                            <div class="flex-1">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Email</h4>
                                        <p class="font-medium">{{ selectedGuest.email }}</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Role</h4>
                                        <p class="font-medium capitalize">{{ selectedGuest.role }}</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Status</h4>
                                        <p class="font-medium capitalize">{{ selectedGuest.status }}</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Joined</h4>
                                        <p class="font-medium">{{ formatDate(selectedGuest.created_at) }}</p>
                                    </div>
                                    
                                    <div v-if="selectedGuest.batch" class="col-span-2">
                                        <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Batch</h4>
                                        <p class="font-medium">{{ selectedGuest.batch }}</p>
                                    </div>
                                    
                                    <div v-if="selectedGuest.personal_description" class="col-span-2">
                                        <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Description</h4>
                                        <p class="font-medium">{{ selectedGuest.personal_description }}</p>
                                    </div>
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

            <!-- Role Change Modal -->
            <div v-if="showRoleChangeModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                <div class="relative w-full max-w-md overflow-hidden bg-white rounded-lg shadow-2xl">
                    <div class="absolute inset-0 grid-lines opacity-10"></div>

                    <div class="relative p-6">
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-black/10">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 mr-3 bg-black rounded-full">
                                    <span class="text-xs font-bold text-white">ROLE</span>
                                </div>
                                <h3 class="text-xl font-bold tracking-wider uppercase">Change User Role</h3>
                            </div>
                        </div>

                        <div class="mb-6 space-y-4">
                            <div>
                                <p class="mb-2 text-sm font-medium text-black/70">User: <span class="font-bold">{{ roleChangeUser?.name }}</span></p>
                                <p class="mb-4 text-sm text-black/70">Current Role: <span class="font-semibold uppercase">{{ roleChangeUser?.role }}</span></p>
                            </div>

                            <div>
                                <label class="block mb-2 text-xs font-bold tracking-wider uppercase text-black/70">New Role</label>
                                <select v-model="newRole"
                                    class="w-full p-2 font-mono text-sm border rounded-lg border-black/20 bg-white/80 focus:outline-none focus:ring-2 focus:ring-black/50">
                                    <option value="guest">Guest</option>
                                    <option value="student">Student</option>
                                    <option value="member">Member</option>
                                    <option value="instructor">Instructor</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <div>
                                <label class="block mb-2 text-xs font-bold tracking-wider uppercase text-black/70">Admin Password</label>
                                <input v-model="adminPassword" type="password" required
                                    class="w-full p-2 font-mono text-sm border rounded-lg border-black/20 bg-white/80 focus:outline-none focus:ring-2 focus:ring-black/50"
                                    placeholder="Enter your admin password">
                                <p class="mt-1 text-xs text-black/50">Password verification required for security</p>
                            </div>

                            <div v-if="roleChangeError" class="p-3 text-sm text-red-700 border border-red-300 rounded-lg bg-red-50">
                                {{ roleChangeError }}
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-4 border-t border-black/10">
                            <button @click="closeRoleChangeModal"
                                class="flex items-center px-4 py-2 text-sm font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black/10">
                                <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                                Cancel
                            </button>
                            <button @click="confirmRoleChange" :disabled="!adminPassword || !newRole || roleChangeLoading"
                                class="flex items-center px-4 py-2 text-sm font-medium text-white uppercase transition-all duration-300 bg-black border border-black rounded hover:bg-black/80 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-white/20">02</span>
                                {{ roleChangeLoading ? 'Updating...' : 'Change Role' }}
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

                        <p class="mb-6">Are you sure you want to delete this guest account? This action cannot be undone.</p>
                        
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
import { ref, computed, watch, onMounted } from 'vue';
import AdminLayout from './Layout.vue';
import { useUsers } from '@/composables/useUsers';

const { users, loading, error, pagination, fetchUsers, deleteUser, updateUser } = useUsers();

const searchQuery = ref('');
const pageNum = ref(1);
const itemsPerPage = 10;

const showDetailsModal = ref(false);
const showDeleteModal = ref(false);
const showRoleChangeModal = ref(false);
const selectedGuest = ref({});
const deleteId = ref(null);
const roleChangeUser = ref(null);
const newRole = ref('');
const adminPassword = ref('');
const roleChangeError = ref(null);
const roleChangeLoading = ref(false);

// Fetch users on component mount
onMounted(() => {
    loadUsers();
});

// Load users from API
async function loadUsers() {
    await fetchUsers({
        search: searchQuery.value || undefined,
        page: pageNum.value,
        per_page: itemsPerPage,
    });
}

// Watch for search query changes
watch(searchQuery, () => {
    pageNum.value = 1;
    loadUsers();
});

// Watch for page changes
watch(pageNum, () => {
    loadUsers();
});

const filteredGuests = computed(() => {
    return users.value || [];
});

const totalPages = computed(() => {
    if (pagination.value) {
        return pagination.value.last_page;
    }
    return Math.max(1, Math.ceil(filteredGuests.value.length / itemsPerPage));
});

const startIndex = computed(() => {
    if (pagination.value) {
        return pagination.value.from - 1;
    }
    return (pageNum.value - 1) * itemsPerPage;
});

const endIndex = computed(() => {
    if (pagination.value) {
        return pagination.value.to;
    }
    const calculatedEnd = startIndex.value + itemsPerPage;
    return Math.min(calculatedEnd, filteredGuests.value.length);
});

const paginatedGuests = computed(() => {
    // If using API pagination, return all users (already paginated by server)
    if (pagination.value) {
        return users.value;
    }
    // Otherwise, paginate locally
    return filteredGuests.value.slice(startIndex.value, endIndex.value);
});

const totalUsers = computed(() => {
    if (pagination.value) {
        return pagination.value.total;
    }
    return filteredGuests.value.length;
});

function viewDetails(guest) {
    selectedGuest.value = { ...guest };
    showDetailsModal.value = true;
}

function deleteItem(id) {
    deleteId.value = id;
    showDeleteModal.value = true;
}

async function confirmDelete() {
    if (deleteId.value !== null) {
        const result = await deleteUser(deleteId.value);
        
        if (result.success) {
            showDeleteModal.value = false;
            deleteId.value = null;
            
            // Reload users
            await loadUsers();
            
            // If current page is empty and not first page, go to previous page
            if (paginatedGuests.value.length === 0 && pageNum.value > 1) {
                pageNum.value--;
            }
        } else {
            alert(result.message || 'Failed to delete user');
        }
    }
}

function openRoleChangeModal(user) {
    roleChangeUser.value = user;
    newRole.value = user.role;
    adminPassword.value = '';
    roleChangeError.value = null;
    showRoleChangeModal.value = true;
}

function closeRoleChangeModal() {
    showRoleChangeModal.value = false;
    roleChangeUser.value = null;
    newRole.value = '';
    adminPassword.value = '';
    roleChangeError.value = null;
    roleChangeLoading.value = false;
}

async function confirmRoleChange() {
    if (!roleChangeUser.value || !adminPassword.value || !newRole.value) {
        roleChangeError.value = 'Please fill in all fields';
        return;
    }

    roleChangeLoading.value = true;
    roleChangeError.value = null;

    try {
        // Verify with universal admin password
        const ADMIN_PASSWORD = 'admin123'; // Universal password for role changes
        
        if (adminPassword.value !== ADMIN_PASSWORD) {
            roleChangeError.value = 'Invalid admin password';
            roleChangeLoading.value = false;
            return;
        }

        // Password verified, now update the role
        const result = await updateUser(roleChangeUser.value.id, { role: newRole.value });
        
        if (result.success) {
            closeRoleChangeModal();
            // Reload users to reflect changes
            await loadUsers();
        } else {
            roleChangeError.value = result.message || 'Failed to update role';
        }
    } catch (error) {
        console.error('Error changing role:', error);
        roleChangeError.value = 'An error occurred while changing role';
    } finally {
        roleChangeLoading.value = false;
    }
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function getDefaultAvatar() {
    return 'https://i.pinimg.com/474x/2d/66/90/2d669016a3e3f5316b0589741e8ca834.jpg';
}

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