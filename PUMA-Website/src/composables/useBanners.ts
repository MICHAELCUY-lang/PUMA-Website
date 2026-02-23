import { ref } from "vue";
import axios from "axios";

const API_BASE_URL = "http://localhost:8000/api";
const STORAGE_BASE_URL = "http://localhost:8000";

export interface Banner {
    id: number;
    title: string;
    image_path: string;
    link?: string;
    description?: string;
    is_active: boolean;
    display_order: number;
    start_date?: string;
    end_date?: string;
}

interface ApiResponse {
    success: boolean;
    data: Banner | Banner[];
    message?: string;
}

export const useBanners = () => {
    const banners = ref<Banner[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const getImageUrl = (path: string) => {
        if (!path) return "";
        if (path.startsWith("http")) return path;
        if (path.startsWith("/storage")) return `${STORAGE_BASE_URL}${path}`;
        if (path.startsWith("storage")) return `${STORAGE_BASE_URL}/${path}`;
        return `${STORAGE_BASE_URL}/storage/${path}`;
    };

    const fetchActiveBanners = async () => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.get<ApiResponse>(`${API_BASE_URL}/banners?active_only=true`);

            if (response.data.success) {
                const rawBanners = response.data.data as Banner[];
                banners.value = rawBanners.map(b => ({
                    ...b,
                    image_path: getImageUrl(b.image_path)
                }));
            }
        } catch (err: any) {
            error.value = err.response?.data?.message || "Failed to fetch banners";
            console.error("Error fetching banners:", err);
        } finally {
            loading.value = false;
        }
    };

    const fetchBanners = async () => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.get<ApiResponse>(`${API_BASE_URL}/banners`);

            if (response.data.success) {
                const rawBanners = response.data.data as Banner[];
                banners.value = rawBanners.map(b => ({
                    ...b,
                    image_path: getImageUrl(b.image_path)
                }));
            }
        } catch (err: any) {
            error.value = err.response?.data?.message || "Failed to fetch banners";
            console.error("Error fetching banners:", err);
        } finally {
            loading.value = false;
        }
    };

    const createBanner = async (data: Partial<Banner>) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.post<ApiResponse>(`${API_BASE_URL}/banners`, data);
            if (response.data.success) {
                return response.data.data;
            }
        } catch (err: any) {
            error.value = err.response?.data?.message || "Failed to create banner";
            console.error("Error creating banner:", err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const updateBanner = async (id: number, data: Partial<Banner>) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.put<ApiResponse>(`${API_BASE_URL}/banners/${id}`, data);
            if (response.data.success) {
                return response.data.data;
            }
        } catch (err: any) {
            error.value = err.response?.data?.message || "Failed to update banner";
            console.error("Error updating banner:", err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const deleteBanner = async (id: number) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.delete<ApiResponse>(`${API_BASE_URL}/banners/${id}`);
            if (response.data.success) {
                banners.value = banners.value.filter(b => b.id !== id);
                return true;
            }
        } catch (err: any) {
            error.value = err.response?.data?.message || "Failed to delete banner";
            console.error("Error deleting banner:", err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const uploadBannerImage = async (file: File) => {
        loading.value = true;
        error.value = null;

        const formData = new FormData();
        formData.append('image', file);

        try {
            const response = await axios.post(`${API_BASE_URL}/banners/upload-image`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            if (response.data.success) {
                // Backend returns { success: true, data: { image_path: '...' } }
                const imagePath = response.data.data.image_path;
                return {
                    image_path: getImageUrl(imagePath)
                };
            }
            throw new Error(response.data.message || 'Upload failed');
        } catch (err: any) {
            error.value = err.response?.data?.message || "Failed to upload image";
            console.error("Error uploading image:", err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return {
        banners,
        loading,
        error,
        fetchActiveBanners,
        fetchBanners,
        createBanner,
        updateBanner,
        deleteBanner,
        uploadBannerImage
    };
};
