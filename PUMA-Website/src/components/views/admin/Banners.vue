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
                        ADD NEW BANNER
                    </button>
                </div>

                <div class="relative overflow-hidden bg-white border rounded-lg shadow-lg border-black/10">
                    <div class="absolute inset-0 grid-lines opacity-5"></div>

                    <div class="relative p-6">
                        <div class="flex flex-col justify-between mb-6 md:flex-row md:items-center">
                            <h2 class="mb-4 text-xl font-bold tracking-wider uppercase md:mb-0">Banners</h2>

                            <div class="relative">
                                <input v-model="searchQuery" placeholder="Search banners..."
                                    class="w-full p-2 pl-3 font-mono text-sm border rounded-lg md:w-64 border-black/20 bg-white/80 backdrop-blur-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div v-for="banner in paginatedBanners" :key="banner.id"
                                class="relative overflow-hidden transition-all duration-300 bg-white border rounded-lg shadow-md border-black/10 hover:shadow-lg">
                                <div class="relative h-48 overflow-hidden bg-gray-200">
                                    <img :src="banner.image_path" :alt="banner.title" 
                                        class="object-cover w-full h-full"
                                        @error="handleImageError">
                                    <div class="absolute top-2 right-2">
                                        <span v-if="banner.is_active" 
                                            class="px-2 py-1 text-xs font-bold text-white bg-green-600 rounded">
                                            Active
                                        </span>
                                        <span v-else 
                                            class="px-2 py-1 text-xs font-bold text-white bg-gray-600 rounded">
                                            Inactive
                                        </span>
                                    </div>
                                    <div class="absolute top-2 left-2">
                                        <span class="px-2 py-1 text-xs font-bold text-white bg-black rounded">
                                            #{{ banner.display_order }}
                                        </span>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h3 class="mb-2 font-bold">{{ banner.title }}</h3>
                                    <p class="mb-3 text-sm text-black/70 line-clamp-2">{{ banner.description || 'No description' }}</p>
                                    <div class="flex gap-2">
                                        <button @click="viewDetails(banner)"
                                            class="flex-1 px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                            View
                                        </button>
                                        <button @click="editItem(banner)"
                                            class="flex-1 px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                            Edit
                                        </button>
                                        <button @click="deleteItem(banner.id)"
                                            class="flex-1 px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-if="paginatedBanners.length === 0" class="col-span-full">
                                <p class="p-8 text-center text-black/50">No banners found</p>
                            </div>
                        </div>

                        <div v-if="filteredBanners.length > itemsPerPage" class="flex items-center justify-between pt-4 mt-4 border-t border-black/10">
                            <div class="text-sm text-black/60">
                                Showing {{ startIndex + 1 }}-{{ endIndex }} of {{ filteredBanners.length }} banners
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
                <!-- Changed max-w-lg to max-w-4xl for wider layout as requested -->
                <div class="relative w-full max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-2xl">
                    <div class="absolute inset-0 grid-lines opacity-10 pointer-events-none"></div>

                    <div class="relative p-6">
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-black/10">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 mr-3 bg-black rounded-full">
                                    <span class="text-xs font-bold text-white">{{ isEditing ? 'ED' : 'NEW' }}</span>
                                </div>
                                <h3 class="text-xl font-bold tracking-wider uppercase">{{ isEditing ? 'Edit Banner' :
                                    'Add New Banner' }}</h3>
                            </div>
                            <button @click="showEditModal = false" class="transition-colors text-black/50 hover:text-black">
                                &times;
                            </button>
                        </div>

                        <form @submit.prevent="saveBanner">
                            <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
                                <div class="mb-2 md:col-span-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Title</label>
                                    <input v-model="currentItem.title" type="text"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" required>
                                </div>

                                <div class="mb-2 md:col-span-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">
                                        Image <span class="text-red-500">*</span>
                                    </label>
                                    
                                    <!-- Upload Status -->
                                    <div v-if="uploadingImage" class="p-4 mb-3 border-2 border-blue-500 rounded-lg bg-blue-50">
                                        <div class="flex items-center gap-3">
                                            <div class="w-6 h-6 border-4 border-blue-500 rounded-full animate-spin border-t-transparent"></div>
                                            <span class="font-medium text-blue-700">Uploading image... Please wait</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Success Status -->
                                    <div v-else-if="currentItem.image_path" class="p-4 mb-3 border-2 border-green-500 rounded-lg bg-green-50">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-medium text-green-700">✓ Image uploaded successfully!</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Warning if no image -->
                                    <div v-else class="p-4 mb-3 border-2 border-yellow-500 rounded-lg bg-yellow-50">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <span class="font-medium text-yellow-700">Please upload an image before submitting</span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start gap-4">
                                        <!-- Preview -->
                                        <div v-if="currentItem.image_path" class="flex-shrink-0 w-32 h-32 overflow-hidden border-2 border-green-500 rounded shadow-lg">
                                            <img :src="currentItem.image_path" alt="Banner preview" class="object-cover w-full h-full">
                                        </div>
                                        
                                        <!-- File Input -->
                                        <div class="flex-1">
                                            <input 
                                                type="file" 
                                                @change="handleImageUpload" 
                                                accept="image/*"
                                                :disabled="uploadingImage"
                                                class="w-full p-3 font-mono border rounded border-black/20 bg-white/80 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <p class="mt-2 text-xs text-gray-600">
                                                Max size: 5MB. Formats: JPG, PNG, GIF, WebP
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-2 md:col-span-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Link (Optional)</label>
                                    <input v-model="currentItem.link" type="url"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80">
                                </div>

                                <div class="mb-2 md:col-span-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Description</label>
                                    <textarea v-model="currentItem.description" rows="6"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80"></textarea>
                                </div>

                                <div class="mb-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Display Order</label>
                                    <input v-model.number="currentItem.display_order" type="number"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80">
                                </div>

                                <div class="flex items-center mb-2">
                                    <input v-model="currentItem.is_active" type="checkbox" id="is_active"
                                        class="w-4 h-4 mr-2">
                                    <label for="is_active" class="text-sm font-medium">Active</label>
                                </div>

                                <div class="mb-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Start Date (Optional)</label>
                                    <input v-model="currentItem.start_date" type="datetime-local"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80">
                                </div>

                                <div class="mb-2">
                                    <label
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">End Date (Optional)</label>
                                    <input v-model="currentItem.end_date" type="datetime-local"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80">
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
                                    {{ isEditing ? 'Update' : 'Add' }} Banner
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
                            <div v-if="selectedItem.image_path" class="overflow-hidden border rounded border-black/10">
                                <img :src="selectedItem.image_path" :alt="selectedItem.title" class="object-cover w-full h-64">
                            </div>
                            
                            <div>
                                <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Description</h4>
                                <p class="font-medium">{{ selectedItem.description || 'No description' }}</p>
                            </div>
                            
                            <div v-if="selectedItem.link">
                                <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Link</h4>
                                <a :href="selectedItem.link" target="_blank" class="text-blue-600 underline">{{ selectedItem.link }}</a>
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
                                <div v-if="selectedItem.start_date">
                                    <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Start Date</h4>
                                    <p class="font-medium">{{ formatDate(selectedItem.start_date) }}</p>
                                </div>
                                <div v-if="selectedItem.end_date">
                                    <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">End Date</h4>
                                    <p class="font-medium">{{ formatDate(selectedItem.end_date) }}</p>
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
                        <p class="mb-6 text-black/70">Are you sure you want to delete this banner? This action cannot be undone.</p>
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
import { useBanners } from '@/composables/useBanners';
import { toast } from '@/components/ui/toast';

const { banners, loading, error, fetchBanners, createBanner, updateBanner, deleteBanner, uploadBannerImage } = useBanners();

onMounted(() => {
    fetchBanners();
});

const searchQuery = ref('');
const pageNum = ref(1);
const itemsPerPage = 6;

const showEditModal = ref(false);
const showDetailsModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const uploadingImage = ref(false);
const currentItem = ref({
    title: '',
    image_path: '',
    link: '',
    description: '',
    is_active: true,
    display_order: 0,
    start_date: null,
    end_date: null,
});
const selectedItem = ref({});
const deleteId = ref(null);

const filteredBanners = computed(() => {
    let result = [...banners.value];
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(banner => 
            banner.title.toLowerCase().includes(query) ||
            (banner.description && banner.description.toLowerCase().includes(query))
        );
    }
    
    return result;
});

const totalPages = computed(() => {
    return Math.ceil(filteredBanners.value.length / itemsPerPage);
});

const startIndex = computed(() => {
    return (pageNum.value - 1) * itemsPerPage;
});

const endIndex = computed(() => {
    const calculatedEnd = startIndex.value + itemsPerPage;
    return Math.min(calculatedEnd, filteredBanners.value.length);
});

const paginatedBanners = computed(() => {
    return filteredBanners.value.slice(startIndex.value, endIndex.value);
});

function openAddModal() {
    currentItem.value = {
        title: '',
        image_path: '',
        link: '',
        description: '',
        is_active: true,
        display_order: 0,
        start_date: null,
        end_date: null,
    };
    isEditing.value = false;
    showEditModal.value = true;
}

function editItem(banner) {
    currentItem.value = { ...banner };
    isEditing.value = true;
    showEditModal.value = true;
}

function viewDetails(banner) {
    selectedItem.value = { ...banner };
    showDetailsModal.value = true;
}

function deleteItem(id) {
    deleteId.value = id;
    showDeleteModal.value = true;
}

async function confirmDelete() {
    if (deleteId.value !== null) {
        try {
            await deleteBanner(deleteId.value);
            showDeleteModal.value = false;
            deleteId.value = null;
            toast({
                title: 'Banner Deleted',
                description: 'The banner has been successfully deleted.',
            });
            if (paginatedBanners.value.length === 0 && pageNum.value > 1) {
                pageNum.value = 1;
            }
        } catch (err) {
            console.error('Error deleting banner:', err);
            toast({
                title: 'Error',
                description: 'Failed to delete banner. Please try again.',
                variant: 'destructive',
            });
        }
    }
}

async function handleImageUpload(event) {
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

    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        toast({
            title: 'File Too Large',
            description: 'Please select an image smaller than 5MB.',
            variant: 'destructive',
        });
        return;
    }

    uploadingImage.value = true;
    try {
        const result = await uploadBannerImage(file);
        currentItem.value.image_path = result.image_path;
        toast({
            title: 'Image Uploaded',
            description: 'Banner image has been uploaded successfully.',
        });
    } catch (err) {
        console.error('Error uploading image:', err);
        toast({
            title: 'Upload Failed',
            description: 'Failed to upload image. Please try again.',
            variant: 'destructive',
        });
    } finally {
        uploadingImage.value = false;
    }
}

async function saveBanner() {
    console.log('Saving banner...', currentItem.value);
    
    try {
        if (!currentItem.value.image_path) {
            console.error('No image path found');
            toast({
                title: 'Image Required',
                description: 'Please upload a banner image first.',
                variant: 'destructive',
            });
            return;
        }

        console.log('Submitting banner data:', {
            isEditing: isEditing.value,
            id: currentItem.value.id,
            data: currentItem.value
        });

        if (isEditing.value && currentItem.value.id) {
            console.log('Updating banner ID:', currentItem.value.id);
            await updateBanner(currentItem.value.id, currentItem.value);
            toast({
                title: 'Banner Updated',
                description: 'The banner has been successfully updated.',
            });
        } else {
            console.log('Creating new banner');
            await createBanner(currentItem.value);
            toast({
                title: 'Banner Created',
                description: 'The banner has been successfully created.',
            });
        }
        
        console.log('Banner saved successfully');
        showEditModal.value = false;
        await fetchBanners(); // Refresh the list
    } catch (err) {
        console.error('Error saving banner:', err);
        console.error('Error details:', err.response?.data);
        toast({
            title: 'Error',
            description: err.response?.data?.message || 'Failed to save banner. Please try again.',
            variant: 'destructive',
        });
    }
}

function formatDate(dateString) {
    if (!dateString) return '';
    return new Date(dateString).toLocaleString();
}

function handleImageError(event) {
    event.target.src = '/placeholder-banner.jpg';
}

watch(searchQuery, () => {
    pageNum.value = 1;
});
</script>

<style scoped>
.grid-lines {
    background-image: linear-gradient(rgba(0, 0, 0, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
    background-size: 20px 20px;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
