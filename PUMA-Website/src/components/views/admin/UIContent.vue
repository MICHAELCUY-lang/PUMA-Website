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
                        ADD NEW CONTENT
                    </button>
                </div>

                <div class="relative overflow-hidden bg-white border rounded-lg shadow-lg border-black/10">
                    <div class="absolute inset-0 grid-lines opacity-5"></div>

                    <div class="relative p-6">
                        <div class="flex flex-col justify-between mb-6 md:flex-row md:items-center">
                            <h2 class="mb-4 text-xl font-bold tracking-wider uppercase md:mb-0">UI Content</h2>

                            <div class="flex flex-col gap-4 md:flex-row">
                                <div class="relative">
                                    <select v-model="typeFilter" 
                                        class="w-full p-2 pl-3 font-mono text-sm border rounded-lg border-black/20 bg-white/80 backdrop-blur-sm">
                                        <option value="">All Types</option>
                                        <option value="text">Text</option>
                                        <option value="html">HTML</option>
                                        <option value="image">Image</option>
                                        <option value="video">Video</option>
                                        <option value="section">Section</option>
                                    </select>
                                </div>
                                <div class="relative">
                                    <input v-model="searchQuery" placeholder="Search content..."
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
                                            Key</th>
                                        <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Title</th>
                                        <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Type</th>
                                        <th class="p-3 text-xs tracking-wider text-center uppercase border-b border-black/10">
                                            Active</th>
                                        <th class="p-3 text-xs tracking-wider text-center uppercase border-b border-black/10">
                                            Order</th>
                                        <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="content in paginatedContents" :key="content.id"
                                        class="transition-colors border-b border-black/5 hover:bg-black/5">
                                        <td class="p-3">
                                            <span class="inline-block px-2 py-1 text-xs text-white bg-black rounded">
                                                {{ content.id.toString().padStart(2, '0') }}
                                            </span>
                                        </td>
                                        <td class="p-3 font-mono text-sm text-black/70">{{ content.key }}</td>
                                        <td class="p-3 font-medium">{{ content.title }}</td>
                                        <td class="p-3">
                                            <span class="px-2 py-1 text-xs bg-white border rounded-full border-black/20">
                                                {{ content.type }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-center">
                                            <span v-if="content.is_active" class="text-green-600">●</span>
                                            <span v-else class="text-gray-400">●</span>
                                        </td>
                                        <td class="p-3 text-center text-black/70">{{ content.display_order }}</td>
                                        <td class="p-3">
                                            <div class="flex gap-2">
                                                <button @click="viewDetails(content)"
                                                    class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                    <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                                                    View
                                                </button>
                                                <button @click="editItem(content)"
                                                    class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                    <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">02</span>
                                                    Edit
                                                </button>
                                                <button @click="deleteItem(content.id)"
                                                    class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                    <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">03</span>
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="paginatedContents.length === 0">
                                        <td colspan="7" class="p-4 text-center text-black/50">No content found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-between pt-4 mt-4 border-t border-black/10">
                            <div class="text-sm text-black/60">
                                Showing {{ startIndex + 1 }}-{{ endIndex }} of {{ filteredContents.length }} items
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

            <!-- Edit/Add Modal -->
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
                                <h3 class="text-xl font-bold tracking-wider uppercase">{{ isEditing ? 'Edit Content' :
                                    'Add New Content' }}</h3>
                            </div>
                            <button @click="showEditModal = false" class="transition-colors text-black/50 hover:text-black">
                                &times;
                            </button>
                        </div>

                        <form @submit.prevent="saveContent">
                            <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
                                <div class="mb-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Key</label>
                                    <input v-model="currentItem.key" type="text"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" required>
                                </div>

                                <div class="mb-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Title</label>
                                    <input v-model="currentItem.title" type="text"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" required>
                                </div>

                                <div class="mb-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Type</label>
                                    <select v-model="currentItem.type"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" required>
                                        <option value="text">Text</option>
                                        <option value="html">HTML</option>
                                        <option value="image">Image</option>
                                        <option value="video">Video</option>
                                        <option value="section">Section</option>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Display Order</label>
                                    <input v-model.number="currentItem.display_order" type="number"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80">
                                </div>

                                <div class="mb-2 md:col-span-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Content</label>
                                    <textarea v-model="currentItem.content" rows="4"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80"></textarea>
                                </div>

                                <div class="flex items-center mb-2">
                                    <input v-model="currentItem.is_active" type="checkbox" id="is_active"
                                        class="w-4 h-4 mr-2">
                                    <label for="is_active" class="text-sm font-medium">Active</label>
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
                                    {{ isEditing ? 'Update' : 'Add' }} Content
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Details Modal -->
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
                                <h3 class="text-xl font-bold tracking-wider uppercase">{{ selectedItem.title }}</h3>
                            </div>
                            <button @click="showDetailsModal = false"
                                class="transition-colors text-black/50 hover:text-black">
                                &times;
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Key</h4>
                                <p class="font-mono">{{ selectedItem.key }}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Type</h4>
                                <p class="font-medium">{{ selectedItem.type }}</p>
                            </div>
                            
                            <div>
                                <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Content</h4>
                                <p class="p-3 font-mono text-sm rounded bg-black/5">{{ selectedItem.content || 'No content' }}</p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Status</h4>
                                    <p class="font-medium">{{ selectedItem.is_active ? 'Active' : 'Inactive' }}</p>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Display Order</h4>
                                    <p class="font-medium">{{ selectedItem.display_order }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Modal -->
            <div v-if="showDeleteModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                <div class="relative w-full max-w-md overflow-hidden bg-white rounded-lg shadow-2xl">
                    <div class="p-6">
                        <h3 class="mb-4 text-xl font-bold">Confirm Delete</h3>
                        <p class="mb-6 text-black/70">Are you sure you want to delete this content? This action cannot be undone.</p>
                        <div class="flex justify-end gap-3">
                            <button @click="showDeleteModal = false"
                                class="px-4 py-2 text-sm font-medium border rounded border-black/20 hover:bg-black/10">
                                Cancel
                            </button>
                            <button @click="confirmDelete"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded hover:bg-red-700">
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
import { ref, computed, onMounted, watch } from 'vue';
import AdminLayout from './Layout.vue';
import { useUIContent } from '@/composables/useUIContent';
import { toast } from '@/components/ui/toast';

const { contents, loading, error, fetchUIContents, createUIContent, updateUIContent, deleteUIContent } = useUIContent();

onMounted(() => {
    fetchUIContents();
});

const searchQuery = ref('');
const typeFilter = ref('');
const pageNum = ref(1);
const itemsPerPage = 10;

const showEditModal = ref(false);
const showDetailsModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const currentItem = ref({
    key: '',
    title: '',
    content: '',
    type: 'text',
    is_active: true,
    display_order: 0,
});
const selectedItem = ref({});
const deleteId = ref(null);

const filteredContents = computed(() => {
    let result = [...contents.value];
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(content => 
            content.key.toLowerCase().includes(query) || 
            content.title.toLowerCase().includes(query) ||
            (content.content && content.content.toLowerCase().includes(query))
        );
    }
    
    if (typeFilter.value) {
        result = result.filter(content => content.type === typeFilter.value);
    }
    
    return result;
});

const totalPages = computed(() => {
    return Math.ceil(filteredContents.value.length / itemsPerPage);
});

const startIndex = computed(() => {
    return (pageNum.value - 1) * itemsPerPage;
});

const endIndex = computed(() => {
    const calculatedEnd = startIndex.value + itemsPerPage;
    return Math.min(calculatedEnd, filteredContents.value.length);
});

const paginatedContents = computed(() => {
    return filteredContents.value.slice(startIndex.value, endIndex.value);
});

function openAddModal() {
    currentItem.value = {
        key: '',
        title: '',
        content: '',
        type: 'text',
        is_active: true,
        display_order: 0,
    };
    isEditing.value = false;
    showEditModal.value = true;
}

function editItem(content) {
    currentItem.value = { ...content };
    isEditing.value = true;
    showEditModal.value = true;
}

function viewDetails(content) {
    selectedItem.value = { ...content };
    showDetailsModal.value = true;
}

function deleteItem(id) {
    deleteId.value = id;
    showDeleteModal.value = true;
}

async function confirmDelete() {
    if (deleteId.value !== null) {
        try {
            await deleteUIContent(deleteId.value);
            showDeleteModal.value = false;
            deleteId.value = null;
            toast({
                title: 'Content Deleted',
                description: 'The content has been successfully deleted.',
            });
            if (paginatedContents.value.length === 0 && pageNum.value > 1) {
                pageNum.value = 1;
            }
        } catch (err) {
            console.error('Error deleting content:', err);
            toast({
                title: 'Error',
                description: 'Failed to delete content. Please try again.',
                variant: 'destructive',
            });
        }
    }
}

async function saveContent() {
    try {
        if (isEditing.value && currentItem.value.id) {
            await updateUIContent(currentItem.value.id, currentItem.value);
            toast({
                title: 'Content Updated',
                description: 'The content has been successfully updated.',
            });
        } else {
            await createUIContent(currentItem.value);
            toast({
                title: 'Content Created',
                description: 'The content has been successfully created.',
            });
        }
        showEditModal.value = false;
    } catch (err) {
        console.error('Error saving content:', err);
        toast({
            title: 'Error',
            description: 'Failed to save content. Please try again.',
            variant: 'destructive',
        });
    }
}

watch([searchQuery, typeFilter], () => {
    pageNum.value = 1;
});
</script>

<style scoped>
.grid-lines {
    background-image: linear-gradient(rgba(0, 0, 0, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
    background-size: 20px 20px;
}
</style>
