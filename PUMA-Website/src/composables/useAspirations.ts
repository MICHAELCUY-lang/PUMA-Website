import { ref } from "vue";
import axios from "axios";

const API_BASE_URL = "http://localhost:8000/api";

interface Aspiration {
  id: number;
  name: string;
  content: string;
  type: string;
  status: string;
  response: string | null;
  date: string;
  created_at: string;
}

export function useAspirations() {
  const aspirations = ref<Aspiration[]>([]);
  const loading = ref<boolean>(false);
  const error = ref<string | null>(null);

  const fetchAspirations = async () => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get(`${API_BASE_URL}/aspirations`);

      if (response.data.success) {
        aspirations.value = response.data.data;
      }
    } catch (err: any) {
      error.value = err.message || "Failed to fetch aspirations";
      console.error("Error fetching aspirations:", err);
    } finally {
      loading.value = false;
    }
  };

  const submitAspiration = async (data: {
    name: string;
    content: string;
    type: string;
  }) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.post(`${API_BASE_URL}/aspirations`, data);

      if (response.data.success) {
        // Refresh the list after submission
        await fetchAspirations();
        return response.data;
      }
    } catch (err: any) {
      error.value = err.message || "Failed to submit aspiration";
      console.error("Error submitting aspiration:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    aspirations,
    loading,
    error,
    fetchAspirations,
    submitAspiration,
  };
}
