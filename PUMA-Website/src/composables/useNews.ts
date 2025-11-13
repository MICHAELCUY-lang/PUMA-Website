// composables/useNews.ts
import { ref } from "vue";
import axios from "axios";

const API_BASE_URL = "http://localhost:8000/api";
const STORAGE_BASE_URL = "http://localhost:8000";

// Normalize image URL from storage or absolute/relative
const getImageUrl = (imagePath?: string): string | undefined => {
  if (!imagePath) return undefined;

  if (imagePath.startsWith("http://") || imagePath.startsWith("https://")) {
    return imagePath;
  }

  if (imagePath.startsWith("storage/") || imagePath.startsWith("/storage/")) {
    return `${STORAGE_BASE_URL}/${imagePath.replace(/^\//, "")}`;
  }

  // Treat any other relative as storage path
  const clean = imagePath.replace(/^\//, "");
  return `${STORAGE_BASE_URL}/storage/${clean}`;
};

interface NewsArticle {
  id: number;
  title: string;
  description: string;
  content: string;
  category: string;
  author?: string;
  featured_image?: string;
  is_featured: boolean;
  views: number;
  date: string;
  published_at?: string;
}

interface ApiResponse {
  success: boolean;
  data: NewsArticle | NewsArticle[];
  message?: string;
}

export const useNews = () => {
  const articles = ref<NewsArticle[]>([]);
  const article = ref<NewsArticle | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Fetch news articles. Pass a category string or params object (e.g., { all: true }).
  const fetchNews = async (paramsOrCategory?: string | Record<string, any>) => {
    loading.value = true;
    error.value = null;

    try {
      let params: Record<string, any> = {};
      if (typeof paramsOrCategory === "string") {
        params = { category: paramsOrCategory };
      } else if (paramsOrCategory && typeof paramsOrCategory === "object") {
        params = paramsOrCategory;
      }
      const response = await axios.get<ApiResponse>(`${API_BASE_URL}/news`, {
        params,
      });

      if (response.data.success) {
        const data = response.data.data as NewsArticle[];
        articles.value = data.map((a) => ({
          ...a,
          featured_image: getImageUrl(a.featured_image) || a.featured_image,
        }));
      }
    } catch (err: any) {
      error.value =
        err.response?.data?.message || "Failed to fetch news articles";
      console.error("Error fetching news:", err);
    } finally {
      loading.value = false;
    }
  };

  // Fetch featured news articles only
  const fetchFeaturedNews = async () => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get<ApiResponse>(
        `${API_BASE_URL}/news/featured`
      );

      if (response.data.success) {
        const data = response.data.data as NewsArticle[];
        articles.value = data.map((a) => ({
          ...a,
          featured_image: getImageUrl(a.featured_image) || a.featured_image,
        }));
      }
    } catch (err: any) {
      error.value =
        err.response?.data?.message || "Failed to fetch featured news";
      console.error("Error fetching featured news:", err);
    } finally {
      loading.value = false;
    }
  };

  // Fetch single news article by ID
  const fetchArticleById = async (id: number) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.get<ApiResponse>(
        `${API_BASE_URL}/news/${id}`
      );

      if (response.data.success) {
        const data = response.data.data as NewsArticle;
        article.value = {
          ...data,
          featured_image:
            getImageUrl(data.featured_image) || data.featured_image,
        } as NewsArticle;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to fetch article";
      console.error("Error fetching article:", err);
    } finally {
      loading.value = false;
    }
  };

  // Create new news article
  const createArticle = async (articleData: Partial<NewsArticle>) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.post<ApiResponse>(
        `${API_BASE_URL}/news`,
        articleData
      );

      if (response.data.success) {
        return response.data.data as NewsArticle;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to create article";
      console.error("Error creating article:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Update existing news article
  const updateArticle = async (
    id: number,
    articleData: Partial<NewsArticle>
  ) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.put<ApiResponse>(
        `${API_BASE_URL}/news/${id}`,
        articleData
      );

      if (response.data.success) {
        return response.data.data as NewsArticle;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to update article";
      console.error("Error updating article:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Delete news article
  const deleteArticle = async (id: number) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await axios.delete<ApiResponse>(
        `${API_BASE_URL}/news/${id}`
      );

      if (response.data.success) {
        // Remove from local articles array if present
        articles.value = articles.value.filter((a) => a.id !== id);
        return true;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || "Failed to delete article";
      console.error("Error deleting article:", err);
      throw err;
    } finally {
      loading.value = false;
    }
  };

  return {
    articles,
    article,
    loading,
    error,
    fetchNews,
    fetchFeaturedNews,
    fetchArticleById,
    createArticle,
    updateArticle,
    deleteArticle,
  };
};
