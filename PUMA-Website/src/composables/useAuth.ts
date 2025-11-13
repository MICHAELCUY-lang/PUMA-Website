import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

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

interface User {
  id: number;
  name: string;
  email: string;
  role: "guest" | "student" | "member" | "admin" | "instructor";
  avatar?: string | null;
  personal_description?: string | null;
  batch?: string | null;
  linkedin?: string | null;
  instagram?: string | null;
  member?: Member | null;
}

interface AuthResponse {
  success: boolean;
  message: string;
  data?: {
    user: User;
    token: string;
  };
}

export function useAuth() {
  const user = ref<User | null>(null);
  const token = ref<string | null>(localStorage.getItem("auth_token"));
  const loading = ref<boolean>(false);
  const error = ref<string | null>(null);
  const router = useRouter();

  // Set axios default header if token exists
  if (token.value) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token.value}`;
  }

  const login = async (
    email: string,
    password: string
  ): Promise<{ success: boolean; message: string }> => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.post<AuthResponse>(`${API_BASE_URL}/login`, {
        email,
        password,
      });

      if (response.data.success && response.data.data) {
        user.value = response.data.data.user;
        token.value = response.data.data.token;

        // Store token in localStorage
        localStorage.setItem("auth_token", response.data.data.token);
        localStorage.setItem("user", JSON.stringify(response.data.data.user));

        // Set default axios header
        axios.defaults.headers.common[
          "Authorization"
        ] = `Bearer ${response.data.data.token}`;

        return { success: true, message: response.data.message };
      }
      return { success: false, message: "Login failed" };
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || "Login failed";
      error.value = errorMessage;
      return { success: false, message: errorMessage };
    } finally {
      loading.value = false;
    }
  };

  const register = async (
    name: string,
    email: string,
    password: string
  ): Promise<{ success: boolean; message: string }> => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.post<AuthResponse>(
        `${API_BASE_URL}/register`,
        {
          name,
          email,
          password,
        }
      );

      if (response.data.success && response.data.data) {
        user.value = response.data.data.user;
        token.value = response.data.data.token;

        // Store token in localStorage
        localStorage.setItem("auth_token", response.data.data.token);
        localStorage.setItem("user", JSON.stringify(response.data.data.user));

        // Set default axios header
        axios.defaults.headers.common[
          "Authorization"
        ] = `Bearer ${response.data.data.token}`;

        return { success: true, message: response.data.message };
      }
      return { success: false, message: "Registration failed" };
    } catch (err: any) {
      const errorMessage = err.response?.data?.message || "Registration failed";
      error.value = errorMessage;
      return { success: false, message: errorMessage };
    } finally {
      loading.value = false;
    }
  };

  const logout = async () => {
    loading.value = true;
    error.value = null;

    try {
      await axios.post(`${API_BASE_URL}/logout`);
    } catch (err: any) {
      console.error("Logout error:", err);
    } finally {
      // Clear local storage and state
      user.value = null;
      token.value = null;
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");
      delete axios.defaults.headers.common["Authorization"];

      loading.value = false;
      router.push("/login");
    }
  };

  const checkAuth = () => {
    const storedToken = localStorage.getItem("auth_token");
    const storedUser = localStorage.getItem("user");

    if (storedToken && storedUser) {
      token.value = storedToken;
      user.value = JSON.parse(storedUser);
      axios.defaults.headers.common["Authorization"] = `Bearer ${storedToken}`;
      return true;
    }
    return false;
  };

  const isAdmin = () => {
    return user.value?.role === "admin";
  };

  const hasRole = (role: string) => {
    return user.value?.role === role;
  };

  return {
    user,
    token,
    loading,
    error,
    login,
    register,
    logout,
    checkAuth,
    isAdmin,
    hasRole,
  };
}
