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
  display_order?: number;
  is_visible?: boolean;
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
  const createMember = async (memberData: Partial<Member>, avatarFile?: File | null) => {
    loading.value = true;
    error.value = null;

    console.log('Creating member with data:', memberData);
    console.log('Avatar file:', avatarFile);

    try {
      // Build FormData for multipart/form-data submission
      const formData = new FormData();

      // Append all member fields
      if (memberData.name) formData.append('name', memberData.name);
      if (memberData.email) formData.append('email', memberData.email);
      if (memberData.batch) formData.append('batch', memberData.batch);
      if (memberData.position) formData.append('position', memberData.position);
      if (memberData.user_id) formData.append('user_id', memberData.user_id.toString());
      if (memberData.cabinet_id) formData.append('cabinet_id', memberData.cabinet_id.toString());
      if (memberData.division_id) formData.append('division_id', memberData.division_id.toString());

      // Append avatar file if provided
      if (avatarFile) {
        formData.append('avatar', avatarFile);
        console.log('Appended avatar file to FormData');
      }

      const response = await axios.post<ApiResponse>(
        `${API_BASE_URL}/members`,
        formData,
        {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        }
      );

      if (response.data.success) {
        console.log('Member created successfully:', response.data.data);
        return response.data.data as Member;
      }
    } catch (err: any) {
      console.error('Error creating member:', err);
      console.error('Error response:', err.response?.data);
      console.error('Error status:', err.response?.status);
      error.value = err.response?.data?.message || "Failed to create member";
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

  // Reorder members
  const reorderMembers = async (memberOrders: { id: number; order: number }[]) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.post<ApiResponse>(
        `${API_BASE_URL}/members/reorder`,
        { members: memberOrders }
      );

      if (response.data.success) {
        return true;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to reorder members";
      console.error("Error reordering members:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Toggle member visibility
  const toggleMemberVisibility = async (id: number) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.patch<ApiResponse>(
        `${API_BASE_URL}/members/${id}/visibility`
      );

      if (response.data.success) {
        // Update local member if present
        const memberIndex = members.value.findIndex((m) => m.id === id);
        if (memberIndex !== -1) {
          members.value[memberIndex].is_visible = !members.value[memberIndex].is_visible;
        }
        return true;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to toggle visibility";
      console.error("Error toggling visibility:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Upload member photo
  const uploadMemberPhoto = async (id: number, file: File) => {
    loading.value = true;
    error.value = null;

    try {
      const formData = new FormData();
      formData.append("photo", file);

      const response = await axios.post<ApiResponse>(
        `${API_BASE_URL}/members/${id}/photo`,
        formData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );

      if (response.data.success) {
        return response.data.data;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to upload photo";
      console.error("Error uploading photo:", err);
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
    reorderMembers,
    toggleMemberVisibility,
    uploadMemberPhoto,
  };
}
