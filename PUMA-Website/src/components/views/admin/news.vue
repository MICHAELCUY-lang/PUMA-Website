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
                    ADD NEW ITEM
                </button>
            </div>

            <div class="relative overflow-hidden bg-white border rounded-lg shadow-lg border-black/10">
                <div class="absolute inset-0 grid-lines opacity-5"></div>

                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold tracking-wider uppercase">News Items</h2>

                        <div class="relative">
                            <input v-model="searchQuery" placeholder="Search items..."
                                class="w-full p-2 pl-3 font-mono text-sm border rounded-lg md:w-64 border-black/20 bg-white/80 backdrop-blur-sm">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr>
                                    <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                        ID</th>
                                    <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                        Category</th>
                                    <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                        Date</th>
                                    <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                        Title</th>
                                    <th class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in filteredNews" :key="item.id"
                                    class="transition-colors border-b border-black/5 hover:bg-black/5">
                                    <td class="p-3">
                                        <span class="inline-block px-2 py-1 text-xs text-white bg-black rounded">{{
                                            item.id.toString().padStart(2, '0') }}</span>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 text-xs bg-white border rounded-full border-black/20">{{
                                            item.category }}</span>
                                    </td>
                                    <td class="p-3 text-black/70">{{ safeFormatDate(item.published_at || item.date) }}</td>
                                    <td class="p-3 font-medium">{{ item.title }}</td>
                                    <td class="p-3">
                                        <div class="flex gap-2">
                                            <button @click="viewDetails(item)"
                                                class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                                                View
                                            </button>
                                            <button @click="editItem(item)"
                                                class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">02</span>
                                                Edit
                                            </button>
                                            <button @click="deleteItem(item.id)"
                                                class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">03</span>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredNews.length === 0">
                                    <td colspan="5" class="p-4 text-center text-black/50">No news items found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showEditModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-lg w-full max-w-2xl p-0 shadow-2xl relative overflow-hidden max-h-[90vh] flex flex-col">
                <div class="absolute inset-0 grid-lines opacity-10 pointer-events-none"></div>

                <div class="relative z-10 flex items-center justify-between p-6 pb-4 border-b border-black/10">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 mr-3 bg-black rounded-full">
                            <span class="text-xs font-bold text-white">{{ isEditing ? 'ED' : 'NEW' }}</span>
                        </div>
                        <h3 class="text-xl font-bold tracking-wider uppercase">{{ isEditing ? 'Edit News Item' :
                            'Add New News Item' }}</h3>
                    </div>
                    <button type="button" @click.stop="closeEditModal" class="transition-colors text-black/50 hover:text-black">
                        &times;
                    </button>
                </div>

                <div class="relative z-0 flex-1 p-6 overflow-y-auto">
                    <div v-if="isSaving" class="absolute inset-0 z-20 flex items-center justify-center bg-white/60 backdrop-blur-sm">
                        <div class="flex items-center gap-3 px-4 py-2 bg-white border rounded shadow border-black/10">
                            <span class="inline-block w-5 h-5 border-2 border-black rounded-full animate-spin border-t-transparent"></span>
                            <span class="text-sm font-medium text-black/80">{{ isEditing ? 'Updating...' : 'Creating...' }}</span>
                        </div>
                    </div>

                    <form @submit.prevent="saveNewsItem">
                        <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
                            <div class="mb-2">
                                <label
                                    class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Category</label>
                                <input v-model="currentItem.category" type="text"
                                class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" required>
                            </div>

                            <div class="mb-2">
                                <label
                                    class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Date</label>
                                <input v-model="currentItem.date" type="date"
                                    class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" required>
                            </div>

                            <div class="mb-2 md:col-span-2">
                                <label
                                class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Title</label>
                                <input v-model="currentItem.title" type="text"
                                    class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" required>
                            </div>

                            <div class="mb-2 md:col-span-2">
                                <label
                                    class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Description</label>
                                <textarea v-model="currentItem.description" rows="2"
                                    class="w-full p-3 font-mono border rounded border-black/20 bg-white/80"
                                    required></textarea>
                            </div>

                            <div class="mb-2 md:col-span-2">
                                <label
                                    class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">Content
                                    (Optional)</label>
                                <textarea v-model="currentItem.content" rows="4"
                                    class="w-full p-3 font-mono border rounded border-black/20 bg-white/80"></textarea>
                            </div>
                        </div>

                        <div class="h-6"></div>
                    </form>
                </div>

                <div class="relative z-10 flex items-center justify-end gap-3 p-4 border-t border-black/10 bg-white/80">
                    <button type="button" @click.stop="closeEditModal"
                        class="flex items-center px-4 py-2 text-sm font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black/10">
                        <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                        Cancel
                    </button>
                    <button type="button" @click.stop="saveNewsItem" :disabled="isSaving"
                        class="flex items-center px-4 py-2 text-sm font-medium text-white uppercase transition-all duration-300 bg-black border border-black rounded hover:bg-black/80 disabled:opacity-60 disabled:cursor-not-allowed">
                        <span v-if="isSaving" class="inline-block w-4 h-4 mr-2 border-2 border-white rounded-full animate-spin border-t-transparent"></span>
                        <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-white/20">02</span>
                        {{ isEditing ? 'Update' : 'Add' }} Item
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showDetailsModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-lg w-full max-w-2xl p-0 shadow-2xl relative overflow-hidden max-h-[90vh] flex flex-col">
                <div class="absolute inset-0 grid-lines opacity-10 pointer-events-none"></div>

                <div class="relative z-10 flex items-center justify-between p-6 pb-4 border-b border-black/10">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 mr-3 bg-black rounded-full">
                            <span class="text-xs font-bold text-white">{{ selectedItem.id?.toString().padStart(2,
                                '0') || '00' }}</span>
                        </div>
                        <h3 class="text-xl font-bold tracking-wider uppercase">{{ selectedItem.title }}</h3>
                    </div>
                    <button type="button" @click.stop="closeDetailsModal"
                        class="transition-colors text-black/50 hover:text-black">
                        &times;
                    </button>
                </div>

                <div class="relative z-0 flex-1 p-6 overflow-y-auto">
                    <div class="mb-4">
                        <span
                            class="inline-block px-2 py-1 mr-2 text-xs bg-white border rounded-full border-black/20">{{
                            selectedItem.category }}</span>
                        <span class="text-black/70">{{ safeFormatDate((selectedItem as any).published_at || (selectedItem as any).date) }}</span>
                    </div>

                    <div class="p-4 mb-4 font-medium rounded-lg bg-black/5">
                        {{ selectedItem.description }}
                    </div>

                    <div v-if="selectedItem.content" class="pt-4 mt-4 border-t border-black/10">
                        <h4 class="mb-2 text-xs font-bold tracking-wider uppercase text-black/70">Full Content</h4>
                        <p class="leading-relaxed text-black/80">{{ selectedItem.content }}</p>
                    </div>
                </div>

                <div class="relative z-10 flex items-center justify-end gap-3 p-4 border-t border-black/10 bg-white/80">
                    <button type="button" @click.stop="closeDetailsModal"
                        class="flex items-center px-4 py-2 text-sm font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                        <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                        Close
                    </button>
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

                    <p class="mb-6">Are you sure you want to delete this news item? This action cannot be undone.</p>
                    
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

<script setup lang="ts">
import { ref, computed, onMounted, h } from 'vue';
import AdminLayout from './Layout.vue';
import { useNews } from '@/composables/useNews';
import { toast } from '@/components/ui/toast';
import { ToastAction } from '@/components/ui/toast';

const { articles, fetchNews, createArticle, updateArticle, deleteArticle, fetchArticleById, article } = useNews();

const searchQuery = ref('');
const showDetailsModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const isSaving = ref(false);
const selectedItem = ref<any>({});
const itemToDeleteId = ref<number|null>(null);
const deletedArticle = ref<any>(null);
const previousArticleState = ref<any>(null);
const createdArticleId = ref<number|null>(null);

type CurrentItem = {
    id: number | null;
    category: string;
    date: string; // YYYY-MM-DD
    title: string;
    description: string;
    content: string;
};

const currentItem = ref<CurrentItem>({
    id: null,
    category: '',
    date: '',
    title: '',
    description: '',
    content: '',
});

onMounted(async () => {
    await fetchNews({ all: true });
});

const filteredNews = computed(() => {
    const list = articles.value || [];
    if (!searchQuery.value) return list;
    const q = searchQuery.value.toLowerCase();
    return list.filter((i: any) =>
        (i.title || '').toLowerCase().includes(q) || (i.category || '').toLowerCase().includes(q)
    );
});

function safeFormatDate(input?: string) {
    if (!input) return '';
    // If already human readable, return as is
    if (/[A-Za-z]{3,}/.test(input)) return input;
    const d = new Date(input);
    if (isNaN(d.getTime())) return input;
    return new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'short', day: 'numeric' }).format(d);
}

function openAddModal() {
    resetForm();
    isEditing.value = false;
    showEditModal.value = true;
}

async function saveNewsItem() {
    isSaving.value = true;
    try {
        // Map form to API fields
        const payload: any = {
            title: currentItem.value.title,
            description: currentItem.value.description,
            content: currentItem.value.content || currentItem.value.description, // content required by backend
            category: currentItem.value.category,
            published_at: currentItem.value.date || null,
        };

        if (isEditing.value && currentItem.value.id) {
            // Store previous state before updating
            const articleBeforeUpdate = articles.value.find(a => a.id === currentItem.value.id);
            previousArticleState.value = articleBeforeUpdate ? { ...articleBeforeUpdate } : null;
            const updatedId = currentItem.value.id;
            
            await updateArticle(currentItem.value.id, payload);
            await fetchNews({ all: true });
            showEditModal.value = false;
            resetForm();
            
            toast({
                title: 'News Article Updated',
                description: 'The news article has been successfully updated.',
                action: h(ToastAction, {
                    altText: 'Undo',
                    onClick: async () => {
                        if (previousArticleState.value) {
                            try {
                                const restorePayload: any = {
                                    title: previousArticleState.value.title,
                                    description: previousArticleState.value.description,
                                    content: previousArticleState.value.content,
                                    category: previousArticleState.value.category,
                                    published_at: previousArticleState.value.published_at || null,
                                };
                                
                                await updateArticle(updatedId, restorePayload);
                                await fetchNews({ all: true });
                                
                                toast({
                                    title: 'Update Undone',
                                    description: 'The article has been restored to its previous state.',
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
            const result = await createArticle(payload);
            createdArticleId.value = result?.id || null;
            await fetchNews({ all: true });
            showEditModal.value = false;
            resetForm();
            
            toast({
                title: 'News Article Created',
                description: 'The news article has been successfully created.',
                action: h(ToastAction, {
                    altText: 'Undo',
                    onClick: async () => {
                        if (createdArticleId.value) {
                            try {
                                await deleteArticle(createdArticleId.value);
                                await fetchNews({ all: true });
                                
                                toast({
                                    title: 'Creation Undone',
                                    description: 'The newly created article has been deleted.',
                                });
                                createdArticleId.value = null;
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
    } catch (e) {
        console.error('Failed to save news item', e);
        toast({
            title: 'Error',
            description: 'Failed to save news article. Please try again.',
            variant: 'destructive',
        });
    } finally {
        isSaving.value = false;
    }
}

function editItem(item: any) {
    isEditing.value = true;
    currentItem.value = {
        id: item.id,
        category: item.category || '',
        date: item.published_at ? new Date(item.published_at).toISOString().split('T')[0] : '',
        title: item.title || '',
        description: item.description || '',
        content: item.content || '',
    };
    showEditModal.value = true;
}

function deleteItem(id: number) {
    itemToDeleteId.value = id;
    showDeleteModal.value = true;
}

async function confirmDelete() {
    if (!itemToDeleteId.value) return;
    try {
        // Store the article data before deleting
        const articleToDelete = articles.value.find(a => a.id === itemToDeleteId.value);
        deletedArticle.value = articleToDelete ? { ...articleToDelete } : null;
        const deletedId = itemToDeleteId.value;
        
        await deleteArticle(itemToDeleteId.value);
        await fetchNews({ all: true });
        showDeleteModal.value = false;
        itemToDeleteId.value = null;
        
        toast({
            title: 'News Article Deleted',
            description: 'The news article has been successfully deleted.',
            action: h(ToastAction, {
                altText: 'Undo',
                onClick: async () => {
                    if (deletedArticle.value) {
                        try {
                            // Recreate the article
                            const restorePayload: any = {
                                title: deletedArticle.value.title,
                                description: deletedArticle.value.description,
                                content: deletedArticle.value.content,
                                category: deletedArticle.value.category,
                                published_at: deletedArticle.value.published_at || null,
                            };
                            
                            await createArticle(restorePayload);
                            await fetchNews({ all: true });
                            
                            toast({
                                title: 'Article Restored',
                                description: 'The article has been restored successfully.',
                            });
                        } catch (err) {
                            console.error('Error restoring article:', err);
                            toast({
                                title: 'Error',
                                description: 'Failed to restore article.',
                                variant: 'destructive',
                            });
                        }
                    }
                }
            }, () => 'Undo'),
        });
    } catch (e) {
        console.error('Failed to delete article', e);
        toast({
            title: 'Error',
            description: 'Failed to delete article. Please try again.',
            variant: 'destructive',
        });
    } finally {
        showDeleteModal.value = false;
        itemToDeleteId.value = null;
    }
}

async function viewDetails(item: any) {
    try {
        await fetchArticleById(item.id);
        selectedItem.value = article.value || item;
        showDetailsModal.value = true;
    } catch (e) {
        selectedItem.value = item;
        showDetailsModal.value = true;
    }
}

function resetForm() {
    currentItem.value = {
        id: null,
        category: '',
        date: '',
        title: '',
        description: '',
        content: '',
    };
    isEditing.value = false;
}

function closeEditModal() {
    showEditModal.value = false;
}

function closeDetailsModal() {
    showDetailsModal.value = false;
}
</script>

<style scoped>
.grid-lines {
    background-image:
        linear-gradient(to right, rgba(0, 0, 0, 0.05) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
    background-size: 20px 20px;
}
</style>