<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AdminLayout from './Layout.vue';
import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

interface Stats {
    totalMembers: number;
    activeMembers: number;
    totalDivisions: number;
    totalCabinets: number;
    totalUIContent: number;
    activeBanners: number;
    totalEvents: number;
    totalNews: number;
}

const stats = ref<Stats>({
    totalMembers: 0,
    activeMembers: 0,
    totalDivisions: 0,
    totalCabinets: 0,
    totalUIContent: 0,
    activeBanners: 0,
    totalEvents: 0,
    totalNews: 0,
});

const loading = ref(true);

const fetchDashboardStats = async () => {
    loading.value = true;
    try {
        // Fetch all data in parallel
        const [membersRes, divisionsRes, cabinetsRes, uiContentRes, bannersRes] = await Promise.all([
            axios.get(`${API_URL}/members`),
            axios.get(`${API_URL}/divisions`),
            axios.get(`${API_URL}/cabinets`),
            axios.get(`${API_URL}/ui-content`),
            axios.get(`${API_URL}/banners`),
        ]);

        const members = membersRes.data.data || membersRes.data;
        const divisions = divisionsRes.data.data || divisionsRes.data;
        const cabinets = cabinetsRes.data.data || cabinetsRes.data;
        const uiContent = uiContentRes.data.data || uiContentRes.data;
        const banners = bannersRes.data.data || bannersRes.data;

        stats.value = {
            totalMembers: members.length,
            activeMembers: members.filter((m: any) => m.is_visible).length,
            totalDivisions: divisions.length,
            totalCabinets: cabinets.length,
            totalUIContent: uiContent.length,
            activeBanners: banners.filter((b: any) => b.is_active).length,
            totalEvents: 0, // Will be implemented when events API is ready
            totalNews: 0, // Will be implemented when news API is ready
        };
    } catch (error) {
        console.error('Error fetching dashboard stats:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchDashboardStats();
});

defineOptions({
    name: 'Dashboard',
});
</script>

<template>
    <AdminLayout>
        <div class="min-h-screen text-gray-800 bg-white">
            <div class="flex">
                <main class="flex-1 p-6">
                    <!-- Header -->
                    <div class="mb-8">
                        <h1 class="mb-2 text-2xl font-bold">Dashboard Overview</h1>
                        <p class="text-gray-600">Welcome to PUMA Admin Control System</p>
                    </div>

                    <!-- Loading State -->
                    <div v-if="loading" class="flex items-center justify-center py-20">
                        <div class="relative">
                            <div class="w-16 h-16 border-4 border-gray-200 rounded-full"></div>
                            <div class="absolute top-0 left-0 w-16 h-16 border-4 border-black rounded-full animate-spin border-t-transparent"></div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div v-else class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
                        <!-- Total Members -->
                        <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-2xl hover:shadow-md">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Total Members</h3>
                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="flex items-baseline">
                                <span class="text-3xl font-bold">{{ stats.totalMembers }}</span>
                                <span class="ml-2 text-sm text-gray-500">members</span>
                            </div>
                        </div>

                        <!-- Active Members -->
                        <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-2xl hover:shadow-md">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Active Members</h3>
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex items-baseline">
                                <span class="text-3xl font-bold">{{ stats.activeMembers }}</span>
                                <span class="ml-2 text-sm text-gray-500">visible</span>
                            </div>
                        </div>

                        <!-- Divisions -->
                        <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-2xl hover:shadow-md">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Divisions</h3>
                                <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div class="flex items-baseline">
                                <span class="text-3xl font-bold">{{ stats.totalDivisions }}</span>
                                <span class="ml-2 text-sm text-gray-500">divisions</span>
                            </div>
                        </div>

                        <!-- Cabinets -->
                        <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-2xl hover:shadow-md">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Cabinets</h3>
                                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex items-baseline">
                                <span class="text-3xl font-bold">{{ stats.totalCabinets }}</span>
                                <span class="ml-2 text-sm text-gray-500">cabinets</span>
                            </div>
                        </div>

                        <!-- UI Content -->
                        <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-2xl hover:shadow-md">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-500">UI Content</h3>
                                <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                                </svg>
                            </div>
                            <div class="flex items-baseline">
                                <span class="text-3xl font-bold">{{ stats.totalUIContent }}</span>
                                <span class="ml-2 text-sm text-gray-500">items</span>
                            </div>
                        </div>

                        <!-- Active Banners -->
                        <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-2xl hover:shadow-md">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Active Banners</h3>
                                <svg class="w-8 h-8 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex items-baseline">
                                <span class="text-3xl font-bold">{{ stats.activeBanners }}</span>
                                <span class="ml-2 text-sm text-gray-500">active</span>
                            </div>
                        </div>

                        <!-- Events (Placeholder) -->
                        <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-2xl hover:shadow-md opacity-60">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-500">Events</h3>
                                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex items-baseline">
                                <span class="text-3xl font-bold">{{ stats.totalEvents }}</span>
                                <span class="ml-2 text-sm text-gray-500">coming soon</span>
                            </div>
                        </div>

                        <!-- News (Placeholder) -->
                        <div class="p-6 transition-shadow bg-white border border-gray-200 shadow-sm rounded-2xl hover:shadow-md opacity-60">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-500">News Articles</h3>
                                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                            <div class="flex items-baseline">
                                <span class="text-3xl font-bold">{{ stats.totalNews }}</span>
                                <span class="ml-2 text-sm text-gray-500">coming soon</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="p-6 mb-8 bg-white border border-gray-200 shadow-sm rounded-2xl">
                        <h3 class="mb-4 font-medium">Quick Actions</h3>
                        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                            <router-link to="/admin/member" class="flex items-center justify-center px-4 py-3 text-sm font-medium text-white transition-colors bg-black rounded-lg hover:bg-gray-800">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Member
                            </router-link>
                            <router-link to="/admin/ui-content" class="flex items-center justify-center px-4 py-3 text-sm font-medium transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Manage Content
                            </router-link>
                            <router-link to="/admin/banners" class="flex items-center justify-center px-4 py-3 text-sm font-medium transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Upload Banner
                            </router-link>
                            <button @click="fetchDashboardStats" class="flex items-center justify-center px-4 py-3 text-sm font-medium transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Refresh Data
                            </button>
                        </div>
                    </div>

                    <!-- System Info -->
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                            <h3 class="mb-4 font-medium">System Status</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Database</span>
                                    <span class="flex items-center text-sm font-medium text-green-600">
                                        <span class="w-2 h-2 mr-2 bg-green-500 rounded-full"></span>
                                        Connected
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">API Server</span>
                                    <span class="flex items-center text-sm font-medium text-green-600">
                                        <span class="w-2 h-2 mr-2 bg-green-500 rounded-full"></span>
                                        Running
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Storage</span>
                                    <span class="flex items-center text-sm font-medium text-green-600">
                                        <span class="w-2 h-2 mr-2 bg-green-500 rounded-full"></span>
                                        Available
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                            <h3 class="mb-4 font-medium">Recent Activity</h3>
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 mr-3 bg-blue-100 rounded-full">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium">{{ stats.totalMembers }} members loaded</p>
                                        <p class="text-xs text-gray-500">Database synchronized</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 mr-3 bg-purple-100 rounded-full">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium">{{ stats.totalDivisions }} divisions configured</p>
                                        <p class="text-xs text-gray-500">Across {{ stats.totalCabinets }} cabinets</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 mr-3 bg-indigo-100 rounded-full">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium">{{ stats.totalUIContent }} UI content items</p>
                                        <p class="text-xs text-gray-500">Ready for management</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </AdminLayout>
</template>