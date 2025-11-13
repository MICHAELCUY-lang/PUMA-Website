// composables/useDivisions.ts
import { ref } from "vue";
import axios from "axios";

const API_BASE_URL = "http://localhost:8000/api";
const STORAGE_BASE_URL = "http://localhost:8000";

// Helper function to get the correct image URL
const getImageUrl = (imagePath: string | undefined): string => {
  if (!imagePath) {
    return `${STORAGE_BASE_URL}/storage/divisions/default.jpg`; // Fallback image from backend
  }

  // If it's already a full URL, return it
  if (imagePath.startsWith("http://") || imagePath.startsWith("https://")) {
    return imagePath;
  }

  // If it's a relative path from storage, construct full URL (backend)
  if (imagePath.startsWith("storage/") || imagePath.startsWith("/storage/")) {
    return `${STORAGE_BASE_URL}/${imagePath.replace(/^\//, "")}`;
  }

  // Remove leading slash and /division/ prefix if present
  let cleanPath = imagePath.replace(/^\//, "").replace(/^division\//, "");

  // Serve all images from backend storage/divisions
  return `${STORAGE_BASE_URL}/storage/divisions/${cleanPath}`;
};

interface Division {
  id: number;
  code: string;
  name: string;
  title: string;
  description: string;
  image?: string;
  members_count?: number;
  members?: any[];
}

interface ApiResponse {
  success: boolean;
  data: Division | Division[];
  message?: string;
}

export const useDivisions = () => {
  const divisions = ref<Division[]>([]);
  const division = ref<Division | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Fetch all divisions
  const fetchDivisions = async () => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get<ApiResponse>(
        `${API_BASE_URL}/divisions`
      );

      if (response.data.success) {
        const divisionsData = response.data.data as Division[];
        // Transform image URLs
        divisions.value = divisionsData.map((division) => ({
          ...division,
          image: getImageUrl(division.image),
        }));
        console.log("Divisions loaded:", divisions.value); // Debug log
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to fetch divisions";
      console.error("Error fetching divisions:", err);
    } finally {
      loading.value = false;
    }
  };

  // Fetch single division by ID
  const fetchDivisionById = async (id: number) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get<ApiResponse>(
        `${API_BASE_URL}/divisions/${id}`
      );

      if (response.data.success) {
        const divisionData = response.data.data as Division;
        division.value = {
          ...divisionData,
          image: getImageUrl(divisionData.image),
        };
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to fetch division";
      console.error("Error fetching division:", err);
    } finally {
      loading.value = false;
    }
  };

  // Fetch division by code (e.g., BOD, RNT, HRD)
  const fetchDivisionByCode = async (code: string) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get<ApiResponse>(
        `${API_BASE_URL}/divisions/code/${code}`
      );

      if (response.data.success) {
        const divisionData = response.data.data as Division;
        const transformedDivision = {
          ...divisionData,
          image: getImageUrl(divisionData.image),
        };
        division.value = transformedDivision;
        return transformedDivision;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to fetch division";
      console.error("Error fetching division by code:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Create new division
  const createDivision = async (divisionData: Partial<Division>) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.post<ApiResponse>(
        `${API_BASE_URL}/divisions`,
        divisionData
      );

      if (response.data.success) {
        return response.data.data as Division;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to create division";
      console.error("Error creating division:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Update existing division
  const updateDivision = async (
    id: number,
    divisionData: Partial<Division>
  ) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.put<ApiResponse>(
        `${API_BASE_URL}/divisions/${id}`,
        divisionData
      );

      if (response.data.success) {
        return response.data.data as Division;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to update division";
      console.error("Error updating division:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Delete division
  const deleteDivision = async (id: number) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.delete<ApiResponse>(
        `${API_BASE_URL}/divisions/${id}`
      );

      if (response.data.success) {
        // Remove from local divisions array if present
        divisions.value = divisions.value.filter((d) => d.id !== id);
        return true;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to delete division";
      console.error("Error deleting division:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    divisions,
    division,
    loading,
    error,
    fetchDivisions,
    fetchDivisionById,
    fetchDivisionByCode,
    createDivision,
    updateDivision,
    deleteDivision,
  };
};
