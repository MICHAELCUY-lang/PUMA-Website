import { ref } from "vue";
import axios from "axios";

const API_BASE_URL = "http://localhost:8000/api";

interface Division {
  id: number;
  name: string;
}

interface Cabinet {
  id: number;
  name: string;
}

interface Member {
  position: string;
  batch: string;
  joined_date: string;
  status: string;
  cabinet: Cabinet | null;
  division: Division | null;
}

export interface User {
  id: number;
  name: string;
  email: string;
  role: "guest" | "student" | "member" | "admin" | "instructor";
  status: "active" | "inactive" | "alumni";
  avatar?: string | null;
  personal_description?: string | null;
  batch?: string | null;
  linkedin?: string | null;
  instagram?: string | null;
  created_at: string;
  updated_at: string;
  member?: Member | null;
}

interface PaginationMeta {
  current_page: number;
  from: number;
  last_page: number;
  per_page: number;
  to: number;
  total: number;
}

interface UsersResponse {
  success: boolean;
  data: {
    data: User[];
    meta?: PaginationMeta;
    links?: any;
    current_page?: number;
    last_page?: number;
    per_page?: number;
    total?: number;
  };
}

interface UserResponse {
  success: boolean;
  data: User;
  message?: string;
}

export function useUsers() {
  const users = ref<User[]>([]);
  const user = ref<User | null>(null);
  const loading = ref<boolean>(false);
  const error = ref<string | null>(null);
  const pagination = ref<PaginationMeta | null>(null);

  const fetchUsers = async (params?: {
    role?: string;
    status?: string;
    search?: string;
    page?: number;
    per_page?: number;
  }) => {
    loading.value = true;
    error.value = null;

    try {
      const token = localStorage.getItem("auth_token");
      const response = await axios.get<UsersResponse>(`${API_BASE_URL}/users`, {
        params,
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      if (response.data.success) {
        users.value = response.data.data.data || response.data.data;

        // Handle pagination metadata
        if (response.data.data.current_page) {
          pagination.value = {
            current_page: response.data.data.current_page,
            from: (response.data.data as any).from || 0,
            last_page: response.data.data.last_page || 1,
            per_page: response.data.data.per_page || 10,
            to: (response.data.data as any).to || 0,
            total: response.data.data.total || 0,
          };
        }
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to fetch users";
      console.error("Error fetching users:", err);
    } finally {
      loading.value = false;
    }
  };

  const fetchUser = async (userId: number) => {
    loading.value = true;
    error.value = null;

    try {
      const token = localStorage.getItem("auth_token");
      const response = await axios.get<UserResponse>(
        `${API_BASE_URL}/users/${userId}`,
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (response.data.success) {
        user.value = response.data.data;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to fetch user";
      console.error("Error fetching user:", err);
    } finally {
      loading.value = false;
    }
  };

  const updateUser = async (userId: number, data: Partial<User>) => {
    loading.value = true;
    error.value = null;

    try {
      const token = localStorage.getItem("auth_token");
      const response = await axios.put<UserResponse>(
        `${API_BASE_URL}/users/${userId}`,
        data,
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (response.data.success) {
        user.value = response.data.data;
        // Update in the list if it exists
        const index = users.value.findIndex((u) => u.id === userId);
        if (index !== -1) {
          users.value[index] = response.data.data;
        }
      }

      return {
        success: true,
        message: response.data.message || "User updated successfully",
      };
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to update user";
      console.error("Error updating user:", err);
      return { success: false, message: error.value };
    } finally {
      loading.value = false;
    }
  };

  const deleteUser = async (userId: number) => {
    loading.value = true;
    error.value = null;

    try {
      const token = localStorage.getItem("auth_token");
      const response = await axios.delete<{
        success: boolean;
        message: string;
      }>(`${API_BASE_URL}/users/${userId}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      if (response.data.success) {
        // Remove from the list
        users.value = users.value.filter((u) => u.id !== userId);
      }

      return {
        success: true,
        message: response.data.message || "User deleted successfully",
      };
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to delete user";
      console.error("Error deleting user:", err);
      return { success: false, message: error.value };
    } finally {
      loading.value = false;
    }
  };

  return {
    users,
    user,
    loading,
    error,
    pagination,
    fetchUsers,
    fetchUser,
    updateUser,
    deleteUser,
  };
}
