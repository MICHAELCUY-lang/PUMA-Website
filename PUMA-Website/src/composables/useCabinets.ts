// composables/useCabinets.ts
import { ref } from "vue";
import axios from "axios";

const API_BASE_URL = "http://localhost:8000/api";
const STORAGE_BASE_URL = "http://localhost:8000";

// Helper function to get the correct image URL
const getImageUrl = (imagePath: string | undefined): string => {
  if (!imagePath) {
    return `${STORAGE_BASE_URL}/storage/divisions/default.jpg`;
  }

  if (imagePath.startsWith("http://") || imagePath.startsWith("https://")) {
    return imagePath;
  }

  if (imagePath.startsWith("storage/") || imagePath.startsWith("/storage/")) {
    return `${STORAGE_BASE_URL}/${imagePath.replace(/^\//, "")}`;
  }

  let cleanPath = imagePath.replace(/^\//, "").replace(/^division\//, "");
  return `${STORAGE_BASE_URL}/storage/divisions/${cleanPath}`;
};

interface Division {
  id: number;
  code: string;
  name: string;
  title: string;
  description: string;
  image?: string;
}

interface Cabinet {
  id: number;
  name: string;
  description?: string;
  logo?: string;
  theme_color?: string;
  year?: string;
  status?: string;
  divisions?: Division[];
  divisions_count?: number;
}

interface ApiResponse {
  success: boolean;
  data: Cabinet | Cabinet[];
  message?: string;
}

export const useCabinets = () => {
  const cabinets = ref<Cabinet[]>([]);
  const cabinet = ref<Cabinet | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Fetch all cabinets
  const fetchCabinets = async () => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get<ApiResponse>(`${API_BASE_URL}/cabinets`);

      if (response.data.success) {
        cabinets.value = response.data.data as Cabinet[];
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to fetch cabinets";
      console.error("Error fetching cabinets:", err);
    } finally {
      loading.value = false;
    }
  };

  // Fetch single cabinet by ID with its divisions
  const fetchCabinetById = async (id: number) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get<ApiResponse>(
        `${API_BASE_URL}/cabinets/${id}`
      );

      if (response.data.success) {
        const cabinetData = response.data.data as Cabinet;

        // Transform division image URLs
        if (cabinetData.divisions) {
          cabinetData.divisions = cabinetData.divisions.map((division) => ({
            ...division,
            image: getImageUrl(division.image),
          }));
        }

        cabinet.value = cabinetData;
        return cabinetData;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to fetch cabinet";
      console.error("Error fetching cabinet:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Fetch cabinet by name with its divisions
  const fetchCabinetByName = async (name: string) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get<ApiResponse>(`${API_BASE_URL}/cabinets`);

      if (response.data.success) {
        const allCabinets = response.data.data as Cabinet[];
        const foundCabinet = allCabinets.find(
          (c) => c.name.toLowerCase() === name.toLowerCase()
        );

        if (foundCabinet) {
          // Fetch full details with divisions
          return await fetchCabinetById(foundCabinet.id);
        } else {
          throw new Error(`Cabinet "${name}" not found`);
        }
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to fetch cabinet";
      console.error("Error fetching cabinet by name:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Update cabinet
  const updateCabinet = async (id: number, data: Partial<Cabinet>) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.put<ApiResponse>(
        `${API_BASE_URL}/cabinets/${id}`,
        data
      );

      if (response.data.success) {
        return response.data.data;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to update cabinet";
      console.error("Error updating cabinet:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Delete cabinet
  const deleteCabinet = async (id: number) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.delete<ApiResponse>(
        `${API_BASE_URL}/cabinets/${id}`
      );

      if (response.data.success) {
        return true;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to delete cabinet";
      console.error("Error deleting cabinet:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    cabinets,
    cabinet,
    loading,
    error,
    fetchCabinets,
    fetchCabinetById,
    fetchCabinetByName,
    updateCabinet,
    deleteCabinet,
  };
};
