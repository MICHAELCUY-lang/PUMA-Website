import { ref } from 'vue';
import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

export interface ActivityLog {
    id: number;
    user_id: number;
    user: {
        id: number;
        name: string;
        email: string;
    };
    action: 'create' | 'update' | 'delete';
    model: string;
    model_id: number | null;
    description: string;
    old_values: any;
    new_values: any;
    ip_address: string | null;
    user_agent: string | null;
    created_at: string;
    updated_at: string;
}

export interface ActivityLogStats {
    total_activities: number;
    by_action: Record<string, number>;
    by_model: Record<string, number>;
    by_user: Array<{
        user_id: number;
        user_name: string;
        count: number;
    }>;
    today: number;
    this_week: number;
    this_month: number;
}

export interface ActivityLogFilters {
    action?: string;
    model?: string;
    user_id?: number;
    start_date?: string;
    end_date?: string;
    search?: string;
    per_page?: number;
}

export function useActivityLogs() {
    const logs = ref<ActivityLog[]>([]);
    const stats = ref<ActivityLogStats | null>(null);
    const models = ref<string[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        per_page: 20,
        total: 0,
    });

    const fetchLogs = async (filters: ActivityLogFilters = {}) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.get(`${API_URL}/activity-logs`, { params: filters });
            logs.value = response.data.data.data || response.data.data;

            if (response.data.data.current_page) {
                pagination.value = {
                    current_page: response.data.data.current_page,
                    last_page: response.data.data.last_page,
                    per_page: response.data.data.per_page,
                    total: response.data.data.total,
                };
            }
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch activity logs';
            console.error('Error fetching activity logs:', err);
        } finally {
            loading.value = false;
        }
    };

    const fetchStats = async () => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.get(`${API_URL}/activity-logs/stats`);
            stats.value = response.data.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch stats';
            console.error('Error fetching stats:', err);
        } finally {
            loading.value = false;
        }
    };

    const fetchRecent = async (limit = 10) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.get(`${API_URL}/activity-logs/recent`, {
                params: { limit },
            });
            logs.value = response.data.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch recent logs';
            console.error('Error fetching recent logs:', err);
        } finally {
            loading.value = false;
        }
    };

    const fetchModels = async () => {
        try {
            const response = await axios.get(`${API_URL}/activity-logs/models`);
            models.value = response.data.data;
        } catch (err: any) {
            console.error('Error fetching models:', err);
        }
    };

    const fetchByUser = async (userId: number) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.get(`${API_URL}/activity-logs/user/${userId}`);
            logs.value = response.data.data.data || response.data.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch user logs';
            console.error('Error fetching user logs:', err);
        } finally {
            loading.value = false;
        }
    };

    return {
        logs,
        stats,
        models,
        loading,
        error,
        pagination,
        fetchLogs,
        fetchStats,
        fetchRecent,
        fetchModels,
        fetchByUser,
    };
}
