// composables/useEvents.ts
// Place this in your PUMA-Website/src/composables/ directory

import { ref } from "vue";
import axios from "axios";

const API_BASE_URL = "http://localhost:8000/api";

interface Event {
  id: number;
  title: string;
  date: string;
  description: string;
  images: string[];
  status: "completed" | "upcoming";
  location?: string;
  category?: string;
  content?: string;
  cabinet?: any;
}

interface ApiResponse {
  success: boolean;
  data: Event | Event[];
  message?: string;
}

export const useEvents = () => {
  const events = ref<Event[]>([]);
  const event = ref<Event | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Fetch all events (with optional status filter)
  const fetchEvents = async (status?: "completed" | "upcoming") => {
    loading.value = true;
    error.value = null;

    try {
      const params = status ? { status } : {};
      const response = await axios.get<ApiResponse>(`${API_BASE_URL}/events`, {
        params,
      });

      if (response.data.success) {
        events.value = response.data.data as Event[];
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to fetch events";
      console.error("Error fetching events:", err);
    } finally {
      loading.value = false;
    }
  };

  // Fetch completed events only
  const fetchCompletedEvents = async () => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get<ApiResponse>(
        `${API_BASE_URL}/events/completed`
      );

      if (response.data.success) {
        events.value = response.data.data as Event[];
      }
    } catch (err: any) {
      error.value =
        err.response?.data?.message || "Failed to fetch completed events";
      console.error("Error fetching completed events:", err);
    } finally {
      loading.value = false;
    }
  };

  // Fetch upcoming events only
  const fetchUpcomingEvents = async () => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get<ApiResponse>(
        `${API_BASE_URL}/events/upcoming`
      );

      if (response.data.success) {
        events.value = response.data.data as Event[];
      }
    } catch (err: any) {
      error.value =
        err.response?.data?.message || "Failed to fetch upcoming events";
      console.error("Error fetching upcoming events:", err);
    } finally {
      loading.value = false;
    }
  };

  // Fetch single event by ID
  const fetchEventById = async (id: number) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get<ApiResponse>(
        `${API_BASE_URL}/events/${id}`
      );

      if (response.data.success) {
        event.value = response.data.data as Event;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to fetch event";
      console.error("Error fetching event:", err);
    } finally {
      loading.value = false;
    }
  };

  // Create new event
  const createEvent = async (eventData: FormData | Partial<Event>) => {
    loading.value = true;
    error.value = null;

    try {
      const headers =
        eventData instanceof FormData
          ? { "Content-Type": "multipart/form-data" }
          : {};

      const response = await axios.post<ApiResponse>(
        `${API_BASE_URL}/events`,
        eventData,
        { headers }
      );

      if (response.data.success) {
        return response.data.data as Event;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to create event";
      console.error("Error creating event:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Update existing event
  const updateEvent = async (
    id: number,
    eventData: FormData | Partial<Event>
  ) => {
    loading.value = true;
    error.value = null;

    try {
      // If using FormData, we need to use POST with _method override since FormData doesn't support PUT
      const isFormData = eventData instanceof FormData;

      if (isFormData) {
        eventData.append("_method", "PUT");
      }

      const headers = isFormData
        ? { "Content-Type": "multipart/form-data" }
        : {};

      const response = await axios.post<ApiResponse>(
        `${API_BASE_URL}/events/${id}`,
        eventData,
        { headers }
      );

      if (response.data.success) {
        return response.data.data as Event;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to update event";
      console.error("Error updating event:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Delete event
  const deleteEvent = async (id: number) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.delete<ApiResponse>(
        `${API_BASE_URL}/events/${id}`
      );

      if (response.data.success) {
        // Remove from local events array if present
        events.value = events.value.filter((e) => e.id !== id);
        return true;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to delete event";
      console.error("Error deleting event:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    events,
    event,
    loading,
    error,
    fetchEvents,
    fetchCompletedEvents,
    fetchUpcomingEvents,
    fetchEventById,
    createEvent,
    updateEvent,
    deleteEvent,
  };
};
