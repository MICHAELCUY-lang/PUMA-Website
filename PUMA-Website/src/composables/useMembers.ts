import { ref } from "vue";
import axios from "axios";

const API_BASE_URL = "http://localhost:8000/api";

export interface Member {
  id: number;
  name: string;
  email: string;
  avatar: string;
  position: string;
  batch: string;
  status: string;
  division: string;
  division_id: number;
  cabinet: string;
  cabinet_id: number;
  user_id: number;
  instagram?: string;
  linkedin?: string;
  personal_description?: string;
}

interface ApiResponse {
  success: boolean;
  data: Member | Member[];
  message?: string;
}

export function useMembers() {
  const members = ref<Member[]>([]);
  const member = ref<Member | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Fetch all members
  const fetchMembers = async () => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get<ApiResponse>(`${API_BASE_URL}/members`);

      if (response.data.success) {
        members.value = response.data.data as Member[];
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to fetch members";
      console.error("Error fetching members:", err);
    } finally {
      loading.value = false;
    }
  };

  // Fetch single member by ID
  const fetchMemberById = async (id: number) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get<ApiResponse>(
        `${API_BASE_URL}/members/${id}`
      );

      if (response.data.success) {
        member.value = response.data.data as Member;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to fetch member";
      console.error("Error fetching member:", err);
    } finally {
      loading.value = false;
    }
  };

  // Create new member
  const createMember = async (memberData: Partial<Member>) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.post<ApiResponse>(
        `${API_BASE_URL}/members`,
        memberData
      );

      if (response.data.success) {
        return response.data.data as Member;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to create member";
      console.error("Error creating member:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Update existing member
  const updateMember = async (id: number, memberData: Partial<Member>) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.put<ApiResponse>(
        `${API_BASE_URL}/members/${id}`,
        memberData
      );

      if (response.data.success) {
        return response.data.data as Member;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to update member";
      console.error("Error updating member:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Delete member
  const deleteMember = async (id: number) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.delete<ApiResponse>(
        `${API_BASE_URL}/members/${id}`
      );

      if (response.data.success) {
        // Remove from local members array if present
        members.value = members.value.filter((m) => m.id !== id);
        return true;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to delete member";
      console.error("Error deleting member:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    members,
    member,
    loading,
    error,
    fetchMembers,
    fetchMemberById,
    createMember,
    updateMember,
    deleteMember,
  };
}
