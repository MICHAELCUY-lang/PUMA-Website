import { ref } from 'vue';
import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

export interface UIContent {
    id: number;
    key: string;
    title: string;
    content: string | null;
    type: 'text' | 'html' | 'image' | 'video' | 'section';
    is_active: boolean;
    display_order: number;
    metadata: Record<string, any> | null;
    created_at?: string;
    updated_at?: string;
}

export interface CreateUIContentData {
    key: string;
    title: string;
    content?: string;
    type: 'text' | 'html' | 'image' | 'video' | 'section';
    is_active?: boolean;
    display_order?: number;
    metadata?: Record<string, any>;
}

export function useUIContent() {
    const contents = ref<UIContent[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const fetchUIContents = async (activeOnly = false) => {
        loading.value = true;
        error.value = null;
        try {
            const params = activeOnly ? { active_only: 'true' } : {};
            const response = await axios.get(`${API_URL}/ui-content`, { params });
            contents.value = response.data.data || response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch UI contents';
            console.error('Error fetching UI contents:', err);
        } finally {
            loading.value = false;
        }
    };

    const getUIContent = async (id: number) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.get(`${API_URL}/ui-content/${id}`);
            return response.data.data || response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch UI content';
            console.error('Error fetching UI content:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const createUIContent = async (data: CreateUIContentData) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.post(`${API_URL}/ui-content`, data);
            await fetchUIContents();
            return response.data.data || response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to create UI content';
            console.error('Error creating UI content:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const updateUIContent = async (id: number, data: Partial<CreateUIContentData>) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.put(`${API_URL}/ui-content/${id}`, data);
            await fetchUIContents();
            return response.data.data || response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to update UI content';
            console.error('Error updating UI content:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const deleteUIContent = async (id: number) => {
        loading.value = true;
        error.value = null;
        try {
            await axios.delete(`${API_URL}/ui-content/${id}`);
            await fetchUIContents();
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to delete UI content';
            console.error('Error deleting UI content:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return {
        contents,
        loading,
        error,
        fetchUIContents,
        getUIContent,
        createUIContent,
        updateUIContent,
        deleteUIContent,
    };
}
