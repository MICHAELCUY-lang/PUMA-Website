<script setup>
import { ref, computed, onMounted, h } from 'vue';
import AdminLayout from './Layout.vue';
import { useEvents } from '@/composables/useEvents';
import { toast } from '@/components/ui/toast';
import { ToastAction } from '@/components/ui/toast';

const { events, loading, error, fetchEvents, createEvent, updateEvent, deleteEvent } = useEvents();

const currentItem = ref({
    title: '',
    date: '',
    description: '',
    images: [],
    status: 'upcoming'
});

const imagesInput = ref('');
const isEditing = ref(false);
const editIndex = ref(-1);
const editingEventId = ref(null);
const searchQuery = ref('');
const showDetailsModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedItem = ref({});
const itemToDeleteId = ref(null);
const galleryIndex = ref(0);
const uploadedFiles = ref([]);
const dragover = ref(false);
const fileCounter = ref(0);
const eventStatuses = ['upcoming', 'completed', 'postponed', 'cancelled', 'planned'];
const deletedEvent = ref(null);
const previousEventState = ref(null);
const createdEventId = ref(null);
const isSaving = ref(false);

// Fetch events on mount
onMounted(() => {
    fetchEvents();
});

const filteredEvents = computed(() => {
    if (!searchQuery.value) {
        return events.value;
    }
    const query = searchQuery.value.toLowerCase();
    return events.value.filter(event =>
        event.title.toLowerCase().includes(query) ||
        event.description.toLowerCase().includes(query) ||
        event.status.toLowerCase().includes(query)
    );
});

const currentGalleryImage = computed(() => {
    if (!selectedItem.value.images || selectedItem.value.images.length === 0) {
        return 'https://i.pinimg.com/474x/39/2a/26/392a261b73dbcd361a0dac2e93a05284.jpg';
    }
    const images = selectedItem.value.images;
    if (galleryIndex.value >= images.length || galleryIndex.value < 0) {
        galleryIndex.value = 0;
    }
    return images[galleryIndex.value];
});

const urlPreviews = computed(() => {
    return imagesInput.value.split('\n')
        .map(url => url.trim())
        .filter(url => url)
        .map(url => ({
            url: url,
            type: 'url'
        }));
});

const filePreviews = computed(() => {
    return uploadedFiles.value.map(fileData => ({
        url: URL.createObjectURL(fileData.file),
        id: fileData.id,
        type: 'file'
    }));
});

const combinedPreviews = computed(() => {
    return [...filePreviews.value, ...urlPreviews.value];
});

function openAddModal() {
    resetForm();
    isEditing.value = false;
    showEditModal.value = true;
}

function getImageSrc(src) {
    if (typeof src === 'object' && src.hasOwnProperty('dataUrl')) {
        return src.dataUrl;
    }
    return src;
}

function handleFileUpload(event) {
    const files = event.target.files;
    if (!files.length) return;

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (file.type.startsWith('image/')) {
            uploadedFiles.value.push({
                id: fileCounter.value++,
                file: file,
                name: file.name
            });
        }
    }
    event.target.value = '';
}

function onDrop(event) {
    dragover.value = false;
    const files = event.dataTransfer.files;

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (file.type.startsWith('image/')) {
            uploadedFiles.value.push({
                id: fileCounter.value++,
                file: file,
                name: file.name
            });
        }
    }
}

async function saveEvent() {
    isSaving.value = true;
    try {
        // Convert date to YYYY-MM-DD format
        let eventDate = currentItem.value.date;
        if (eventDate && !/^\d{4}-\d{2}-\d{2}$/.test(eventDate)) {
            const parsedDate = new Date(eventDate);
            if (!isNaN(parsedDate.getTime())) {
                eventDate = parsedDate.toISOString().split('T')[0];
            }
        }

        // Create FormData for file uploads
        const formData = new FormData();
        formData.append('title', currentItem.value.title);
        formData.append('event_date', eventDate);
        formData.append('description', currentItem.value.description);
        formData.append('status', currentItem.value.status);

        // Handle URL inputs from textarea
        const urlsFromTextarea = imagesInput.value.split('\n')
            .map(url => url.trim())
            .filter(url => url && url.startsWith('http'));

        // Add URLs as images array
        urlsFromTextarea.forEach((url, index) => {
            formData.append(`images[${index}]`, url);
        });

        // Add file uploads
        uploadedFiles.value.forEach((fileData) => {
            formData.append('image_files[]', fileData.file);
        });

        if (isEditing.value && editingEventId.value) {
            // Store previous state before updating
            const eventBeforeUpdate = events.value.find(e => e.id === editingEventId.value);
            previousEventState.value = { ...eventBeforeUpdate };
            const updatedId = editingEventId.value;
            
            const result = await updateEvent(editingEventId.value, formData);
            await fetchEvents();
            showEditModal.value = false;
            resetForm();
            
            toast({
                title: 'Event Updated',
                description: 'The event has been successfully updated.',
                action: h(ToastAction, {
                    altText: 'Undo',
                    onClick: async () => {
                        if (previousEventState.value) {
                            try {
                                // Restore previous state
                                const restoreFormData = new FormData();
                                restoreFormData.append('title', previousEventState.value.title);
                                restoreFormData.append('event_date', new Date(previousEventState.value.date).toISOString().split('T')[0]);
                                restoreFormData.append('description', previousEventState.value.description);
                                restoreFormData.append('status', previousEventState.value.status);
                                
                                if (previousEventState.value.images && previousEventState.value.images.length > 0) {
                                    previousEventState.value.images.forEach((url, index) => {
                                        restoreFormData.append(`images[${index}]`, url);
                                    });
                                }
                                
                                await updateEvent(updatedId, restoreFormData);
                                await fetchEvents();
                                
                                toast({
                                    title: 'Update Undone',
                                    description: 'The event has been restored to its previous state.',
                                });
                            } catch (err) {
                                console.error('Error undoing update:', err);
                                toast({
                                    title: 'Error',
                                    description: 'Failed to undo the update.',
                                    variant: 'destructive',
                                });
                            }
                        }
                    }
                }, () => 'Undo'),
            });
        } else {
            const result = await createEvent(formData);
            createdEventId.value = result?.id;
            await fetchEvents();
            showEditModal.value = false;
            resetForm();
            
            toast({
                title: 'Event Created',
                description: 'The event has been successfully created.',
                action: h(ToastAction, {
                    altText: 'Undo',
                    onClick: async () => {
                        if (createdEventId.value) {
                            try {
                                await deleteEvent(createdEventId.value);
                                await fetchEvents();
                                
                                toast({
                                    title: 'Creation Undone',
                                    description: 'The newly created event has been deleted.',
                                });
                                createdEventId.value = null;
                            } catch (err) {
                                console.error('Error undoing creation:', err);
                                toast({
                                    title: 'Error',
                                    description: 'Failed to undo the creation.',
                                    variant: 'destructive',
                                });
                            }
                        }
                    }
                }, () => 'Undo'),
            });
        }
    } catch (err) {
        console.error('Error saving event:', err);
        toast({
            title: 'Error',
            description: 'Failed to save event. Please try again.',
            variant: 'destructive',
        });
    } finally {
        isSaving.value = false;
    }
}

function editItem(eventItem, index) {
    isEditing.value = true;
    editIndex.value = index;
    editingEventId.value = eventItem.id;
    currentItem.value = JSON.parse(JSON.stringify(eventItem));
    
    // Convert date from "1 September 2024" to "2024-09-01" for date input
    if (currentItem.value.date) {
        const parsedDate = new Date(currentItem.value.date);
        if (!isNaN(parsedDate.getTime())) {
            currentItem.value.date = parsedDate.toISOString().split('T')[0];
        }
    }
    
    imagesInput.value = (currentItem.value.images || []).join('\n');
    uploadedFiles.value = [];
    showEditModal.value = true;
}

function deleteItem(eventItem) {
    itemToDeleteId.value = eventItem.id;
    showDeleteModal.value = true;
}

async function confirmDelete() {
    if (itemToDeleteId.value) {
        try {
            // Store the event data before deleting
            const eventToDelete = events.value.find(e => e.id === itemToDeleteId.value);
            deletedEvent.value = { ...eventToDelete };
            const deletedId = itemToDeleteId.value;
            
            await deleteEvent(itemToDeleteId.value);
            await fetchEvents(); // Refresh the list
            showDeleteModal.value = false;
            itemToDeleteId.value = null;
            
            toast({
                title: 'Event Deleted',
                description: 'The event has been successfully deleted.',
                action: h(ToastAction, {
                    altText: 'Undo',
                    onClick: async () => {
                        if (deletedEvent.value) {
                            try {
                                // Recreate the event
                                const formData = new FormData();
                                formData.append('title', deletedEvent.value.title);
                                formData.append('event_date', new Date(deletedEvent.value.date).toISOString().split('T')[0]);
                                formData.append('description', deletedEvent.value.description);
                                formData.append('status', deletedEvent.value.status);
                                
                                // Add images
                                if (deletedEvent.value.images && deletedEvent.value.images.length > 0) {
                                    deletedEvent.value.images.forEach((url, index) => {
                                        formData.append(`images[${index}]`, url);
                                    });
                                }
                                
                                await createEvent(formData);
                                await fetchEvents();
                                
                                toast({
                                    title: 'Event Restored',
                                    description: 'The event has been restored successfully.',
                                });
                            } catch (err) {
                                console.error('Error restoring event:', err);
                                toast({
                                    title: 'Error',
                                    description: 'Failed to restore event.',
                                    variant: 'destructive',
                                });
                            }
                        }
                    }
                }, () => 'Undo'),
            });
        } catch (err) {
            console.error('Error deleting event:', err);
            toast({
                title: 'Error',
                description: 'Failed to delete event. Please try again.',
                variant: 'destructive',
            });
        }
    }
}

function viewDetails(eventItem) {
    selectedItem.value = { ...eventItem };
    galleryIndex.value = 0;
    showDetailsModal.value = true;
}

function resetForm() {
    currentItem.value = {
        title: '',
        date: '',
        description: '',
        images: [],
        status: 'upcoming'
    };
    imagesInput.value = '';
    uploadedFiles.value = [];
    dragover.value = false;
}

function closeEditModal() {
    showEditModal.value = false;
    resetForm();
}

function nextImage() {
    if (!selectedItem.value.images || selectedItem.value.images.length === 0) return;
    galleryIndex.value = (galleryIndex.value + 1) % selectedItem.value.images.length;
}

function prevImage() {
    if (!selectedItem.value.images || selectedItem.value.images.length === 0) return;
    galleryIndex.value = (galleryIndex.value - 1 + selectedItem.value.images.length) % selectedItem.value.images.length;
}

function removeImagePreview(indexToRemove, type) {
    if (type === 'file') {
        const fileIdToRemove = filePreviews.value[indexToRemove]?.id;
        if (fileIdToRemove !== undefined) {
            uploadedFiles.value = uploadedFiles.value.filter(fileData => fileData.id !== fileIdToRemove);
        }
    } else if (type === 'url') {
        const urlIndexInUrlPreviews = indexToRemove - filePreviews.value.length;
        if (urlIndexInUrlPreviews >= 0) {
            const urls = imagesInput.value.split('\n')
                .map(url => url.trim())
                .filter(url => url);

            if (urlIndexInUrlPreviews < urls.length) {
                urls.splice(urlIndexInUrlPreviews, 1);
                imagesInput.value = urls.join('\n');
            }
        }
    }
}
</script>

<template>
    <AdminLayout>
    <div class="container relative p-4 mx-auto overflow-hidden font-mono">
        <div class="absolute inset-0 z-0 grid-lines opacity-10"></div>

        <div class="relative z-10">
            <div class="mb-6">
                <button @click="openAddModal"
                    class="flex items-center px-4 py-3 font-medium text-gray-800 transition-all duration-300 bg-white border rounded-lg shadow-md border-black/20 hover:bg-black hover:text-white group">
                    <span
                        class="mr-2 text-xs px-1.5 py-0.5 rounded-sm bg-black/10 text-black/80 group-hover:bg-white group-hover:text-black transition-colors duration-300">
                        01
                    </span>
                    ADD NEW EVENT
                </button>
            </div>

            <div class="relative overflow-hidden bg-white border rounded-lg shadow-lg border-black/10">
                <div class="absolute inset-0 grid-lines opacity-5"></div>

                <div class="relative p-6">
                    <div class="flex flex-col items-start justify-between mb-6 sm:flex-row sm:items-center">
                        <h2 class="mb-3 text-xl font-bold tracking-wider text-gray-800 uppercase sm:mb-0">Event Lineup</h2>
                        <div class="relative w-full sm:w-auto">
                            <input v-model="searchQuery" placeholder="Search events (title, desc, status)..."
                                class="w-full p-2 pl-3 font-mono text-sm text-gray-700 border rounded-lg md:w-64 border-black/20 bg-white/80 backdrop-blur-sm focus:ring-2 focus:ring-black focus:border-transparent">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2 lg:grid-cols-3">
                        <div v-for="(event, index) in filteredEvents" :key="event.title + '-' + index + '-' + event.date" 
                            class="flex flex-col overflow-hidden transition-all duration-300 bg-white border rounded-lg shadow-md border-black/10 hover:shadow-xl group">

                            <div class="relative overflow-hidden aspect-video bg-black/5">
                                <img :src="getImageSrc(event.images[0] || 'https://i.pinimg.com/474x/39/2a/26/392a261b73dbcd361a0dac2e93a05284.jpg')" alt="Event cover" class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute flex gap-1 top-2 right-2">
                                    <span class="px-2 py-1 text-xs font-semibold text-white capitalize bg-blue-500 rounded shadow">
                                        {{ event.status }}
                                    </span>
                                    <span class="px-2 py-1 text-xs font-semibold text-white bg-black rounded shadow">
                                        #{{ (index + 1).toString().padStart(2, '0') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-col flex-grow p-4">
                                <div class="flex items-start justify-between mb-2">
                                    <h3 class="text-lg font-bold text-gray-800">{{ event.title }}</h3>
                                    <span class="px-2 py-1 ml-2 text-xs text-gray-600 bg-white border rounded-full whitespace-nowrap border-black/20">{{
                                        event.date }}</span>
                                </div>
                                <p class="flex-grow mb-4 text-sm text-gray-600 line-clamp-2">{{ event.description }}</p>
                                <div class="flex flex-wrap gap-2 mt-auto">
                                    <button @click="viewDetails(event)"
                                        class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                        <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">01</span> View
                                    </button>
                                    <button @click="editItem(event, index)"
                                        class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                        <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">02</span> Edit
                                    </button>
                                    <button @click="deleteItem(event)"
                                        class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                        <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">03</span> Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-if="loading" class="p-8 text-center col-span-full text-black/50">
                            Loading events...
                        </div>

                        <div v-else-if="error" class="p-8 text-center col-span-full text-red-500">
                            {{ error }}
                        </div>

                        <div v-else-if="filteredEvents.length === 0" class="p-8 text-center col-span-full text-black/50">
                            No events found matching your search criteria.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showEditModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white rounded-lg w-full max-w-2xl p-0 shadow-2xl relative overflow-hidden max-h-[90vh] flex flex-col">
                <div class="absolute inset-0 grid-lines opacity-10"></div>

                <div class="relative flex items-center justify-between p-6 pb-4 border-b border-black/10">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 mr-3 bg-black rounded-full">
                            <span class="text-xs font-bold text-white">{{ isEditing ? 'ED' : 'NEW' }}</span>
                        </div>
                        <h3 class="text-xl font-bold tracking-wider text-gray-800 uppercase">{{ isEditing ? 'Edit Event' : 'Add New Event' }}</h3>
                    </div>
                    <button @click="closeEditModal" class="text-2xl leading-none transition-colors text-black/50 hover:text-black">
                        &times;
                    </button>
                </div>

                <form @submit.prevent="saveEvent" class="relative flex-grow p-6 overflow-y-auto">
                    <div v-if="isSaving" class="absolute inset-0 z-20 flex items-center justify-center bg-white/60 backdrop-blur-sm">
                        <div class="flex items-center gap-3 px-4 py-2 bg-white border rounded shadow border-black/10">
                            <span class="inline-block w-5 h-5 border-2 border-black rounded-full animate-spin border-t-transparent"></span>
                            <span class="text-sm font-medium text-black/80">{{ isEditing ? 'Updating event...' : 'Creating event...' }}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-5 mb-6">
                        <div>
                            <label class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Event Title</label>
                            <input v-model="currentItem.title" type="text"
                                class="w-full p-3 font-mono text-sm border rounded border-black/20 bg-white/90 focus:ring-2 focus:ring-black" required>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Event Date</label>
                                <input v-model="currentItem.date" type="date"
                                    class="w-full p-3 font-mono text-sm border rounded border-black/20 bg-white/90 focus:ring-2 focus:ring-black" required>
                                <p class="mt-1 text-xs text-black/50">Select the event date</p>
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Event Status</label>
                                <select v-model="currentItem.status" class="w-full p-3 font-mono text-sm border rounded border-black/20 bg-white/90 focus:ring-2 focus:ring-black" required>
                                    <option disabled value="">Select status</option>
                                    <option v-for="statusOpt in eventStatuses" :key="statusOpt" :value="statusOpt" class="capitalize">{{ statusOpt }}</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Description</label>
                            <textarea v-model="currentItem.description" rows="4"
                                class="w-full p-3 font-mono text-sm border rounded border-black/20 bg-white/90 focus:ring-2 focus:ring-black"
                                required></textarea>
                        </div>

                        <div>
                            <label class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Upload Images</label>
                            <div class="flex items-center mb-2">
                                <input type="file" ref="fileInput" @change="handleFileUpload" accept="image/*" multiple class="hidden">
                                <button type="button" @click="$refs.fileInput.click()"
                                    class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 transition-all duration-300 border rounded border-black/20 hover:bg-black/5">
                                    <span class="mr-1.5 text-base leading-none">+</span> Select Files
                                </button>
                                <span class="ml-3 text-xs text-black/60">or drag & drop into area below</span>
                            </div>
                            <div @dragover.prevent="dragover = true" @dragleave.prevent="dragover = false" @drop.prevent="onDrop"
                                :class="{ 'bg-black/5 border-black/40 ring-2 ring-black/30': dragover, 'border-black/20': !dragover }"
                                class="flex flex-col items-center justify-center min-h-[80px] p-6 transition-colors border-2 border-dashed rounded">
                                <span v-if="!dragover && uploadedFiles.length === 0" class="text-sm text-black/60">Drop images here</span>
                                <span v-if="dragover" class="text-sm font-semibold text-black/70">Drop to upload!</span>
                                <span v-if="!dragover && uploadedFiles.length > 0" class="text-sm text-black/80">{{ uploadedFiles.length }} file(s) selected for upload</span>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">External Image URLs (or Public Paths)</label>
                            <textarea v-model="imagesInput" rows="3"
                                class="w-full p-3 font-mono text-sm border rounded border-black/20 bg-white/90 focus:ring-2 focus:ring-black"
                                placeholder="https://example.com/image1.jpg&#10;/images/public-image.png&#10;data:image/jpeg;base64,..."></textarea>
                            <p class="mt-1 text-xs text-black/60">One URL/path per line. For files in 'public' folder, use paths like '/images/foo.jpg'.</p>
                        </div>

                        <div v-if="combinedPreviews.length > 0" class="mb-2">
                            <label class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Image Previews (Staged & URLs)</label>
                            <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-5">
                                <div v-for="(preview, idx) in combinedPreviews" :key="preview.type + '-' + idx + '-' + (preview.url ? preview.url.slice(-20) : '')"
                                    class="relative overflow-hidden rounded aspect-video bg-black/10">
                                    <img :src="preview.url" alt="Preview" class="object-cover w-full h-full">
                                    <button type="button" @click="removeImagePreview(idx, preview.type)"
                                        class="absolute flex items-center justify-center w-5 h-5 text-xs text-white bg-red-500 rounded-full opacity-75 top-1 right-1 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-red-600"
                                        aria-label="Remove image preview">
                                        &times;
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 mt-auto border-t border-black/10"> <button type="button" @click="closeEditModal"
                            class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black/10">
                            Cancel
                        </button>
                        <button type="submit" :disabled="isSaving"
                            class="flex items-center px-4 py-2 text-sm font-medium text-white uppercase transition-all duration-300 bg-black border border-black rounded hover:bg-black/80 disabled:opacity-60 disabled:cursor-not-allowed">
                            <span v-if="isSaving" class="inline-block w-4 h-4 mr-2 border-2 border-white rounded-full animate-spin border-t-transparent"></span>
                            {{ isSaving ? (isEditing ? 'Updating...' : 'Saving...') : (isEditing ? 'Update' : 'Save') }} Event
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showDetailsModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white rounded-lg w-full max-w-3xl shadow-2xl relative overflow-hidden max-h-[90vh] flex flex-col">
                <div class="absolute inset-0 grid-lines opacity-10"></div>

                <div class="relative bg-gray-900 aspect-video">
                    <img :src="getImageSrc(currentGalleryImage || 'https://i.pinimg.com/474x/39/2a/26/392a261b73dbcd361a0dac2e93a05284.jpg')" alt="Selected event image" class="object-contain w-full h-full">
                    <div class="absolute bottom-0 left-0 right-0 p-4 text-white bg-gradient-to-t from-black/80 via-black/50 to-transparent">
                        <h2 class="text-2xl font-bold">{{ selectedItem.title }}</h2>
                        <p class="text-sm text-white/90">{{ selectedItem.date }}</p>
                    </div>
                    <button v-if="selectedItem.images && selectedItem.images.length > 1" @click="prevImage" aria-label="Previous image"
                        class="absolute flex items-center justify-center w-10 h-10 text-2xl text-white transition-colors transform -translate-y-1/2 rounded-full left-3 top-1/2 bg-black/40 hover:bg-black/70 focus:outline-none focus:ring-2 focus:ring-white">
                        &lt;
                    </button>
                    <button v-if="selectedItem.images && selectedItem.images.length > 1" @click="nextImage" aria-label="Next image"
                        class="absolute flex items-center justify-center w-10 h-10 text-2xl text-white transition-colors transform -translate-y-1/2 rounded-full right-3 top-1/2 bg-black/40 hover:bg-black/70 focus:outline-none focus:ring-2 focus:ring-white">
                        &gt;
                    </button>
                    <button @click="showDetailsModal = false" aria-label="Close details"
                        class="absolute flex items-center justify-center w-8 h-8 text-xl text-white transition-colors rounded-full top-3 right-3 bg-black/40 hover:bg-black/70 focus:outline-none focus:ring-2 focus:ring-white">
                        &times;
                    </button>
                </div>

                <div class="flex-grow p-6 overflow-y-auto">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-2xl font-bold text-gray-800">{{ selectedItem.title }}</h3>
                        <span class="px-3 py-1 text-sm font-semibold text-white capitalize bg-blue-500 rounded-full shadow">{{ selectedItem.status }}</span>
                    </div>
                    <div class="mb-4">
                        <span class="inline-block px-2 py-1 mt-1 text-xs text-gray-600 bg-gray-100 border rounded-full border-black/10">{{ selectedItem.date }}</span>
                    </div>

                    <div v-if="selectedItem.images && selectedItem.images.length > 1" class="flex gap-2 pb-3 mb-4 overflow-x-auto border-b border-black/10">
                        <div v-for="(img, idx) in selectedItem.images" :key="idx + '-thumb-' + (img ? img.slice(-10) : '')" @click="galleryIndex = idx"
                            class="flex-shrink-0 w-20 h-20 overflow-hidden transition-all duration-150 border-2 rounded-md cursor-pointer hover:opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-black"
                            :class="{ 'ring-2 ring-offset-1 ring-black border-transparent': galleryIndex === idx, 'border-black/10': galleryIndex !== idx }">
                            <img :src="getImageSrc(img)" alt="Thumbnail" class="object-cover w-full h-full">
                        </div>
                    </div>
                    
                    <h4 class="mb-2 text-sm font-semibold tracking-wider uppercase text-black/70">Event Description</h4>
                    <p class="mb-6 leading-relaxed text-gray-700 whitespace-pre-wrap">{{ selectedItem.description }}</p>

                    <div class="flex justify-end pt-4 mt-auto border-t border-black/10">
                        <button @click="showDetailsModal = false"
                            class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showDeleteModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="relative w-full max-w-md overflow-hidden bg-white rounded-lg shadow-2xl">
                <div class="absolute inset-0 grid-lines opacity-10"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-black/10">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-8 h-8 mr-3 text-red-500 border-2 border-red-500 rounded-full">
                                <span class="text-lg font-bold">!</span>
                            </div>
                            <h3 class="text-xl font-bold tracking-wider text-gray-800 uppercase">Confirm Delete</h3>
                        </div>
                        <button @click="showDeleteModal = false" class="text-2xl leading-none transition-colors text-black/50 hover:text-black">
                            &times;
                        </button>
                    </div>
                    <p class="mb-6 text-gray-700">Are you sure you want to delete the event titled "<strong>{{ events[itemToDeleteIndex] ? events[itemToDeleteIndex].title : 'this event' }}</strong>"?<br> This action cannot be undone.</p>
                    <div class="flex justify-end gap-3 pt-4 border-t border-black/10">
                        <button @click="showDeleteModal = false"
                            class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black/10">
                            Cancel
                        </button>
                        <button @click="confirmDelete"
                            class="flex items-center px-4 py-2 text-sm font-medium text-white uppercase transition-all duration-300 bg-red-600 border border-red-600 rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-red-500">
                            Delete Event
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </AdminLayout>
</template>

<style scoped>
.grid-lines {
    background-image:
        linear-gradient(to right, rgba(0, 0, 0, 0.03) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(0, 0, 0, 0.03) 1px, transparent 1px);
    background-size: 20px 20px; 
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
.whitespace-pre-wrap {
    white-space: pre-wrap; 
}

.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}
.overflow-y-auto::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.05);
    border-radius: 10px;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.3);
    border-radius: 10px;
}
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.5);
}
</style>