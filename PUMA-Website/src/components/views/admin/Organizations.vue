<template>
    <AdminLayout>
        <div class="container relative p-4 mx-auto overflow-hidden font-mono">
            <div class="absolute inset-0 z-0 grid-lines opacity-10"></div>

            <!-- Loading Indicator -->
            <div v-if="isLoading" class="relative z-10 flex items-center justify-center min-h-[400px]">
                <div class="text-center">
                    <div class="inline-block w-8 h-8 border-2 border-black border-solid rounded-full animate-spin border-t-transparent"></div>
                    <p class="mt-2 text-sm text-black/60">Loading organization data...</p>
                </div>
            </div>

            <div v-else class="relative z-10">
                <div class="relative mb-6 overflow-hidden bg-white border rounded-lg shadow-lg border-black/10">
                    <div class="absolute inset-0 grid-lines opacity-5"></div>

                    <div class="relative p-6">
                        <div class="flex flex-col justify-between mb-6 md:flex-row md:items-center">
                            <h2 class="mb-4 text-xl font-bold tracking-wider uppercase md:mb-0">Divisions</h2>

                            <div class="flex flex-col gap-4 md:flex-row">
                                <div class="relative">
                                    <input v-model="searchQuery" placeholder="Search divisions..."
                                        class="w-full p-2 pl-3 font-mono text-sm border rounded-lg md:w-64 border-black/20 bg-white/80 backdrop-blur-sm">
                                </div>
                                <div class="relative">
                                    <select v-model="selectedCabinetFilter"
                                        class="w-full p-2 pl-3 font-mono text-sm border rounded-lg md:w-48 border-black/20 bg-white/80 backdrop-blur-sm">
                                        <option value="">All Cabinets</option>
                                        <option v-for="cabinet in cabinets" :key="cabinet.id" :value="cabinet.id">
                                            {{ cabinet.name }}
                                        </option>
                                    </select>
                                </div>
                                <button @click="openCreateModal()"
                                    class="flex items-center px-3 py-2 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                    <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-black/10">ADD</span>
                                    New Division
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr>
                                        <th
                                            class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            ID</th>
                                        <th
                                            class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Image</th>
                                        <th
                                            class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Name</th>
                                        <th
                                            class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Title</th>
                                        <th
                                            class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Cabinet</th>
                                        <th
                                            class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="division in paginatedDivisions" :key="division.id"
                                        class="transition-colors border-b border-black/5 hover:bg-black/5">
                                        <td class="p-3">
                                            <span class="inline-block px-2 py-1 text-xs text-white bg-black rounded">
                                                {{ division.id.toString().padStart(2, '0') }}
                                            </span>
                                        </td>
                                        <td class="p-3">
                                            <div class="w-10 h-10 overflow-hidden rounded-full">
                                                <img :src="division.image" alt="Division Image"
                                                    class="object-cover w-full h-full">
                                            </div>
                                        </td>
                                        <td class="p-3 font-medium">{{ division.name }}</td>
                                        <td class="p-3 text-black/70">{{ division.title }}</td>
                                        <td class="p-3 text-black/70">{{ getDivisionCabinets(division) }}</td>
                                        <td class="p-3">
                                            <div class="flex gap-2">
                                                <button @click="viewDetails(division)"
                                                    class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                    <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                                                    View
                                                </button>
                                                <button @click="editDivision(division)"
                                                    class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                    <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">02</span>
                                                    Edit
                                                </button>
                                                <button @click="deleteDivision(division.id)"
                                                    class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                    <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">03</span>
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="paginatedDivisions.length === 0">
                                        <td colspan="6" class="p-4 text-center text-black/50">No divisions found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-between pt-4 mt-4 border-t border-black/10">
                            <div class="text-sm text-black/60">
                                Showing {{ startIndex + 1 }}-{{ endIndex }} of {{ filteredDivisions.length }} divisions
                            </div>
                            <div class="flex gap-2">
                                <button @click="pageNum--" :disabled="pageNum === 1"
                                    class="flex items-center px-3 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-black hover:text-white">
                                    Prev
                                </button>
                                <span class="flex items-center px-3 py-1 text-xs font-medium">
                                    {{ pageNum }} / {{ totalPages }}
                                </span>
                                <button @click="pageNum++" :disabled="pageNum === totalPages"
                                    class="flex items-center px-3 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-black hover:text-white">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-hidden bg-white border rounded-lg shadow-lg border-black/10">
                    <div class="absolute inset-0 grid-lines opacity-5"></div>

                    <div class="relative p-6">
                        <div class="flex flex-col justify-between mb-6 md:flex-row md:items-center">
                            <h2 class="mb-4 text-xl font-bold tracking-wider uppercase md:mb-0">Cabinets</h2>

                            <div class="flex flex-col gap-4 md:flex-row">
                                <div class="relative">
                                    <input v-model="cabinetSearchQuery" placeholder="Search cabinets..."
                                        class="w-full p-2 pl-3 font-mono text-sm border rounded-lg md:w-64 border-black/20 bg-white/80 backdrop-blur-sm">
                                </div>
                                <button @click="openCreateCabinetModal()"
                                    class="flex items-center px-3 py-2 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                    <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-black/10">ADD</span>
                                    New Cabinet
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr>
                                        <th
                                            class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            ID</th>
                                        <th
                                            class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Name</th>
                                        <th
                                            class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Year</th>
                                        <th
                                            class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Logo</th>
                                        <th
                                            class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Associated Divisions</th>
                                        <th
                                            class="p-3 text-xs tracking-wider text-left uppercase border-b border-black/10">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="cabinet in paginatedCabinets" :key="cabinet.id"
                                        class="transition-colors border-b border-black/5 hover:bg-black/5">
                                        <td class="p-3">
                                            <span class="inline-block px-2 py-1 text-xs text-white bg-black rounded">
                                                {{ cabinet.id.toString().padStart(2, '0') }}
                                            </span>
                                        </td>
                                        <td class="p-3 font-medium">{{ cabinet.name }}</td>
                                        <td class="p-3 text-black/70">{{ cabinet.year || '—' }}</td>
                                        <td class="p-3">
                                            <div class="flex items-center gap-2">
                                                <img v-if="cabinet.logo" :src="getImageUrl(cabinet.logo)" alt="Logo" class="object-cover w-8 h-8 rounded-full border border-black/10" />
                                                <span v-else class="px-2 py-1 text-xs text-black/50 border border-dashed rounded border-black/20">No logo</span>
                                            </div>
                                        </td>
                                        <td class="p-3 text-black/70">{{ getAssociatedDivision(cabinet.id) }}</td>
                                        <td class="p-3">
                                            <div class="flex gap-2">
                                                <button @click="editCabinet(cabinet)"
                                                    class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                    <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                                                    Edit
                                                </button>
                                                <button @click="deleteCabinet(cabinet.id)"
                                                    class="flex items-center px-2 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                                    <span class="mr-1 text-[9px] px-1 rounded-sm bg-black/10">02</span>
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="paginatedCabinets.length === 0">
                                        <td colspan="4" class="p-4 text-center text-black/50">No cabinets found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-between pt-4 mt-4 border-t border-black/10">
                            <div class="text-sm text-black/60">
                                Showing {{ cabinetStartIndex + 1 }}-{{ cabinetEndIndex }} of {{ filteredCabinets.length
                                }} cabinets
                            </div>
                            <div class="flex gap-2">
                                <button @click="cabinetPageNum--" :disabled="cabinetPageNum === 1"
                                    class="flex items-center px-3 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-black hover:text-white">
                                    Prev
                                </button>
                                <span class="flex items-center px-3 py-1 text-xs font-medium">
                                    {{ cabinetPageNum }} / {{ cabinetTotalPages }}
                                </span>
                                <button @click="cabinetPageNum++" :disabled="cabinetPageNum === cabinetTotalPages"
                                    class="flex items-center px-3 py-1 text-xs font-medium uppercase transition-all duration-300 border rounded border-black/20 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-black hover:text-white">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="showDetailsModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                <div class="relative w-full max-w-2xl overflow-hidden bg-white rounded-lg shadow-2xl">
                    <div class="absolute inset-0 grid-lines opacity-10"></div>

                    <div class="relative p-6">
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-black/10">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 mr-3 bg-black rounded-full">
                                    <span class="text-xs font-bold text-white">{{
                                        selectedDivision.id?.toString().padStart(2,
                                            '0') || '00' }}</span>
                                </div>
                                <h3 class="text-xl font-bold tracking-wider uppercase">{{ selectedDivision.name }}</h3>
                            </div>
                            <button @click="showDetailsModal = false"
                                class="transition-colors text-black/50 hover:text-black">
                                &times;
                            </button>
                        </div>

                        <div class="flex flex-col gap-6 md:flex-row">
                            <div class="flex-shrink-0">
                                <div class="w-32 h-32 mx-auto overflow-hidden rounded-full">
                                    <img :src="selectedDivision.image" alt="Division Image"
                                        class="object-cover w-full h-full">
                                </div>
                            </div>

                            <div class="flex-1">
                                <div class="grid grid-cols-1 gap-4 mb-4">
                                    <div>
                                        <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Title</h4>
                                        <p class="font-medium">{{ selectedDivision.title }}</p>
                                    </div>

                                    <div>
                                        <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Cabinet
                                        </h4>
                                        <p class="font-medium">{{ getCabinetName(selectedDivision.cabinetId) }}</p>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-xs font-bold tracking-wider uppercase text-black/70">Description
                                    </h4>
                                    <p class="mt-1 text-black/80">{{ selectedDivision.description }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 mt-6 border-t border-black/10">
                            <button @click="showDetailsModal = false"
                                class="flex items-center px-4 py-2 text-sm font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black hover:text-white">
                                <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <div v-if="showFormModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                <div class="relative w-full max-w-2xl p-0 overflow-hidden bg-white rounded-lg shadow-2xl">
                    <div class="absolute inset-0 grid-lines opacity-10"></div>

                    <div class="relative p-6">
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-black/10">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 mr-3 bg-black rounded-full">
                                    <span class="text-xs font-bold text-white">{{ isEditMode ? 'ED' : 'NEW' }}</span>
                                </div>
                                <h3 class="text-xl font-bold tracking-wider uppercase">
                                    {{ isEditMode ? 'Edit Division' : 'Add New Division' }}
                                </h3>
                            </div>
                            <button @click="showFormModal = false"
                                class="transition-colors text-black/50 hover:text-black">
                                &times;
                            </button>
                        </div>

                        <form @submit.prevent="saveDivision">
                            <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
                                <div class="mb-2 md:col-span-2">
                                    <label class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">
                                        Division Image
                                    </label>
                                    <div class="flex items-center gap-4">
                                        <div v-if="formDivision.imagePreview || formDivision.image" class="w-16 h-16 overflow-hidden rounded-full">
                                            <img :src="formDivision.imagePreview || formDivision.image" alt="Division Image"
                                                class="object-cover w-full h-full">
                                        </div>
                                        <input type="file" accept="image/*" @change="handleImageUpload"
                                            class="flex-1 p-3 font-mono border rounded border-black/20 bg-white/80" />
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label for="name"
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">
                                        Division Name
                                    </label>
                                    <input type="text" id="name" v-model="formDivision.name" required
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" />
                                </div>

                                <div class="mb-2">
                                    <label for="title"
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">
                                        Division Title
                                    </label>
                                    <input type="text" id="title" v-model="formDivision.title" required
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" />
                                </div>

                                <div class="mb-2 md:col-span-2">
                                    <label for="description"
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">
                                        Description
                                    </label>
                                    <textarea id="description" v-model="formDivision.description" rows="4"
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80"></textarea>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-black/10">
                                <button type="button" @click="showFormModal = false"
                                    class="flex items-center px-4 py-2 text-sm font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black/10">
                                    <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                                    Cancel
                                </button>
                                <button type="submit" :disabled="isSaving"
                                    class="flex items-center px-4 py-2 text-sm font-medium text-white uppercase transition-all duration-300 bg-black border border-black rounded hover:bg-black/80 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-white/20">02</span>
                                    {{ isSaving ? 'Saving...' : (isEditMode ? 'Update' : 'Add') }} Division
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div v-if="showCabinetFormModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                <div class="relative w-full max-w-2xl p-0 overflow-hidden bg-white rounded-lg shadow-2xl max-h-[85vh] flex flex-col">
                    <div class="absolute inset-0 grid-lines opacity-10"></div>

                    <div class="relative p-4 md:p-6 overflow-y-auto max-h-[75vh]">
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-black/10">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 mr-3 bg-black rounded-full">
                                    <span class="text-xs font-bold text-white">{{ isEditCabinetMode ? 'ED' : 'NEW'
                                        }}</span>
                                </div>
                                <h3 class="text-xl font-bold tracking-wider uppercase">
                                    {{ isEditCabinetMode ? 'Edit Cabinet' : 'Add New Cabinet' }}
                                </h3>
                            </div>
                            <button @click="showCabinetFormModal = false"
                                class="transition-colors text-black/50 hover:text-black">
                                &times;
                            </button>
                        </div>

                        <form @submit.prevent="saveCabinet">
                            <div class="grid grid-cols-1 gap-3 mb-6">
                                <div class="mb-2">
                                    <label for="cabinetName"
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">
                                        Cabinet Name
                                    </label>
                                    <input type="text" id="cabinetName" v-model="formCabinet.name" required
                                        class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" />
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div class="mb-2">
                                        <label for="cabinetYear"
                                            class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">
                                            Year
                                        </label>
                                        <input type="number" id="cabinetYear" v-model.number="formCabinet.year" min="2000" max="2100"
                                            class="w-full p-3 font-mono border rounded border-black/20 bg-white/80" placeholder="e.g., 2025" />
                                    </div>
                                    <div class="mb-2">
                                        <label for="cabinetLogo"
                                            class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">
                                            Logo
                                        </label>
                                        <div class="flex items-center gap-4">
                                            <div v-if="formCabinet.logoPreview || formCabinet.logo" class="w-12 h-12 overflow-hidden rounded-full border border-black/10">
                                                <img :src="formCabinet.logoPreview || getImageUrl(formCabinet.logo)" alt="Cabinet Logo" class="object-cover w-full h-full" />
                                            </div>
                                            <input type="file" accept="image/*" @change="handleCabinetLogoUpload"
                                                class="flex-1 p-3 font-mono border rounded border-black/20 bg-white/80" />
                                        </div>
                                        <p class="mt-1 text-xs text-black/50">You can also paste a URL if you prefer.</p>
                                        <input type="text" id="cabinetLogo" v-model="formCabinet.logo"
                                            class="w-full p-3 mt-2 font-mono border rounded border-black/20 bg-white/80" placeholder="https://... or storage path" />
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label for="cabinetDivisions"
                                        class="block mb-1 text-xs font-bold tracking-wider uppercase text-black/70">
                                        Associated Divisions
                                    </label>
                                    <div class="p-3 space-y-2 border rounded border-black/20 bg-white/80 max-h-48 overflow-y-auto">
                                        <label v-for="division in divisions" :key="division.id" 
                                            class="flex items-center p-2 transition-colors rounded cursor-pointer hover:bg-black/5">
                                            <input 
                                                type="checkbox" 
                                                :value="division.id" 
                                                v-model="formCabinet.divisionIds"
                                                class="w-4 h-4 mr-3 border-gray-300 rounded text-black focus:ring-black focus:ring-2"
                                            />
                                            <div class="flex-1">
                                                <span class="font-mono text-sm font-medium text-black">{{ division.name }}</span>
                                                <span class="ml-2 font-mono text-xs text-black/60">{{ division.title }}</span>
                                            </div>
                                        </label>
                                        <p v-if="divisions.length === 0" class="p-2 text-sm text-center text-black/50">
                                            No divisions available
                                        </p>
                                    </div>
                                    <p class="mt-1 text-xs text-black/60">
                                        Selected: {{ formCabinet.divisionIds.length }} division(s)
                                    </p>
                                </div>
                            </div>

                            <div class="sticky bottom-0 left-0 right-0 flex justify-end gap-3 pt-4 mt-2 bg-white border-t border-black/10">
                                <button type="button" @click="showCabinetFormModal = false"
                                    class="flex items-center px-4 py-2 text-sm font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black/10">
                                    <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                                    Cancel
                                </button>
                                <button type="submit" :disabled="isSaving"
                                    class="flex items-center px-4 py-2 text-sm font-medium text-white uppercase transition-all duration-300 bg-black border border-black rounded hover:bg-black/80 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-white/20">02</span>
                                    {{ isSaving ? 'Saving...' : (isEditCabinetMode ? 'Update' : 'Add') }} Cabinet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div v-if="showDeleteModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                <div class="relative w-full max-w-md overflow-hidden bg-white rounded-lg shadow-2xl">
                    <div class="absolute inset-0 grid-lines opacity-10"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-black/10">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 mr-3 bg-black rounded-full">
                                    <span class="text-xs font-bold text-white">DEL</span>
                                </div>
                                <h3 class="text-xl font-bold tracking-wider uppercase">
                                    Delete {{ deleteType.charAt(0).toUpperCase() + deleteType.slice(1) }}
                                </h3>
                            </div>
                            <button @click="showDeleteModal = false"
                                class="transition-colors text-black/50 hover:text-black">
                                &times;
                            </button>
                        </div>

                        <div class="mb-6">
                            <p class="mb-4">
                                Are you sure you want to delete this {{ deleteType }}? This action cannot be undone.
                                <span v-if="deleteType === 'cabinet'">
                                    The associated divisions will be unassigned from this cabinet.
                                </span>
                            </p>

                            <div v-if="deleteType === 'cabinet' && divisions.filter(d => d.cabinetId === deleteItemId).length > 0"
                                class="p-3 mt-3 mb-3 border rounded-lg border-yellow-200 bg-yellow-50">
                                <p class="text-sm font-medium text-yellow-800">Affected Divisions:</p>
                                <ul class="mt-1 text-sm text-yellow-700">
                                    <li v-for="division in divisions.filter(d => d.cabinetId === deleteItemId)" 
                                        :key="division.id" class="ml-2">
                                        • {{ division.name }} - {{ division.title }}
                                    </li>
                                </ul>
                            </div>

                            <div v-if="deleteType === 'division'"
                                class="p-4 mt-4 border rounded-lg border-black/10 bg-black/5">
                                <p class="font-medium">
                                    {{divisions.find(d => d.id === deleteItemId)?.name || 'Unknown Division'}}
                                </p>
                                <p class="mt-1 text-sm text-black/60">
                                    {{divisions.find(d => d.id === deleteItemId)?.title || ''}}
                                </p>
                            </div>

                            <div v-if="deleteType === 'cabinet'"
                                class="p-4 mt-4 border rounded-lg border-black/10 bg-black/5">
                                <p class="font-medium">
                                    {{cabinets.find(c => c.id === deleteItemId)?.name || 'Unknown Cabinet'}}
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-black/10">
                            <button @click="showDeleteModal = false"
                                class="flex items-center px-4 py-2 text-sm font-medium uppercase transition-all duration-300 border rounded border-black/20 hover:bg-black/10">
                                <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-black/10">01</span>
                                Cancel
                            </button>
                            <button @click="confirmDelete"
                                class="flex items-center px-4 py-2 text-sm font-medium text-white uppercase transition-all duration-300 bg-black border border-black rounded hover:bg-black/80">
                                <span class="mr-1.5 text-[9px] px-1 rounded-sm bg-white/20">02</span>
                                Delete {{ deleteType }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch, h } from 'vue';
import AdminLayout from './Layout.vue';
import { toast } from '@/components/ui/toast';
import { ToastAction } from '@/components/ui/toast';

// API base URL - adjust according to your backend setup
const API_BASE_URL = 'http://localhost:8000/api'; // Adjust this to match your Laravel backend URL
const STORAGE_BASE_URL = 'http://localhost:8000'; // Base URL for storage files

// Helper function to get the correct image URL
const getImageUrl = (imagePath) => {
    if (!imagePath) {
        return `${STORAGE_BASE_URL}/storage/divisions/default.jpg`; // Fallback image from backend
    }
    
    // If it's already a full URL, return it
    if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
        return imagePath;
    }
    
    // If it's a relative path from storage, construct full URL
    if (imagePath.startsWith('storage/') || imagePath.startsWith('/storage/')) {
        return `${STORAGE_BASE_URL}/${imagePath.replace(/^\//, '')}`;
    }
    
    // Remove leading slash and /division/ prefix if present
    const cleanPath = imagePath.replace(/^\//, '').replace(/^division\//, '');
    
    // Serve all images from backend storage/divisions
    return `${STORAGE_BASE_URL}/storage/divisions/${cleanPath}`;
}

// API integration functions
async function fetchDivisionsFromAPI() {
    try {
        const response = await fetch(`${API_BASE_URL}/divisions`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const json = await response.json();
        const data = json?.data || [];
        // Transform API data to match component structure
        return data.map(division => ({
            id: division.id,
            name: division.name,
            title: division.title,
            description: division.description || '',
            image: getImageUrl(division.image),
            code: division.code,
            cabinetId: division.cabinet_id || null, // First cabinet for compatibility
            cabinets: division.cabinets || [] // Store all cabinets (many-to-many)
        }));
    } catch (error) {
        console.error('Error fetching divisions:', error);
        return [];
    }
}

async function fetchCabinetsFromAPI() {
    try {
        const response = await fetch(`${API_BASE_URL}/cabinets`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const json = await response.json();
        const data = json?.data || [];
        // Transform API data to match component structure
        return data.map(cabinet => ({
            id: cabinet.id,
            name: cabinet.name,
            description: cabinet.description || '',
            logo: cabinet.logo || '',
            theme_color: cabinet.theme_color || '',
            year: cabinet.year || '',
            status: cabinet.status || 'active'
        }));
    } catch (error) {
        console.error('Error fetching cabinets:', error);
        return [];
    }
}

// API functions for CRUD operations
async function createDivisionAPI(divisionData) {
    try {
        const form = new FormData();
        form.append('code', (divisionData.name || '').toUpperCase());
        form.append('name', divisionData.name || '');
        form.append('title', divisionData.title || '');
        if (divisionData.description) form.append('description', divisionData.description);
        if (divisionData.cabinetId) form.append('cabinet_id', divisionData.cabinetId);
        if (divisionData.imageFile) form.append('image', divisionData.imageFile);

        const response = await fetch(`${API_BASE_URL}/divisions`, {
            method: 'POST',
            body: form
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const json = await response.json();
        return json?.data;
    } catch (error) {
        console.error('Error creating division:', error);
        throw error;
    }
}

async function updateDivisionAPI(divisionId, divisionData) {
    try {
        const form = new FormData();
        form.append('_method', 'PUT');
        form.append('code', (divisionData.name || '').toUpperCase());
        form.append('name', divisionData.name || '');
        form.append('title', divisionData.title || '');
        if (divisionData.description) form.append('description', divisionData.description);
        if (divisionData.cabinetId) form.append('cabinet_id', divisionData.cabinetId);
        if (divisionData.imageFile) form.append('image', divisionData.imageFile);

        const response = await fetch(`${API_BASE_URL}/divisions/${divisionId}`, {
            method: 'POST',
            body: form
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const json = await response.json();
        return json?.data;
    } catch (error) {
        console.error('Error updating division:', error);
        throw error;
    }
}

async function deleteDivisionAPI(divisionId) {
    try {
        const response = await fetch(`${API_BASE_URL}/divisions/${divisionId}`, {
            method: 'DELETE'
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return true;
    } catch (error) {
        console.error('Error deleting division:', error);
        throw error;
    }
}

async function createCabinetAPI(cabinetData) {
    try {
        const form = new FormData();
        form.append('name', cabinetData.name || '');
        if (cabinetData.description) form.append('description', cabinetData.description);
        form.append('status', 'active');
        form.append('year', (cabinetData.year || new Date().getFullYear()).toString());
        if (cabinetData.logoFile) {
            form.append('logo', cabinetData.logoFile);
        } else if (cabinetData.logo) {
            // Optional: allow passing a URL string for backend to store as-is if you adapt backend
        }
        (cabinetData.divisionIds || []).forEach(id => form.append('division_ids[]', id));

        const response = await fetch(`${API_BASE_URL}/cabinets`, {
            method: 'POST',
            body: form
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const json = await response.json();
        return json?.data;
    } catch (error) {
        console.error('Error creating cabinet:', error);
        throw error;
    }
}

async function updateCabinetAPI(cabinetId, cabinetData) {
    try {
        const form = new FormData();
        form.append('_method', 'PUT');
        form.append('name', cabinetData.name || '');
        if (cabinetData.description) form.append('description', cabinetData.description);
        form.append('year', (cabinetData.year || new Date().getFullYear()).toString());
        if (cabinetData.logoFile) form.append('logo', cabinetData.logoFile);
        (cabinetData.divisionIds || []).forEach(id => form.append('division_ids[]', id));

        const response = await fetch(`${API_BASE_URL}/cabinets/${cabinetId}`, {
            method: 'POST',
            body: form
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const json = await response.json();
        return json?.data;
    } catch (error) {
        console.error('Error updating cabinet:', error);
        throw error;
    }
}

async function deleteCabinetAPI(cabinetId) {
    try {
        const response = await fetch(`${API_BASE_URL}/cabinets/${cabinetId}`, {
            method: 'DELETE'
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return true;
    } catch (error) {
        console.error('Error deleting cabinet:', error);
        throw error;
    }
}

const divisions = ref([]);
const cabinets = ref([]);
const nextDivisionId = ref(1);
const nextCabinetId = ref(1);

// Loading states
const isLoading = ref(true);
const isSaving = ref(false);

const showDetailsModal = ref(false);
const showFormModal = ref(false);
const showCabinetFormModal = ref(false);
const showDeleteModal = ref(false);
const isEditMode = ref(false);
const isEditCabinetMode = ref(false);
const selectedDivision = ref({});
const formDivision = ref({
    name: '',
    title: '',
    description: '',
    image: '',
    imageFile: null,
    imagePreview: '',
    cabinetId: ''
});
const formCabinet = ref({
    name: '',
    logo: '',
    logoFile: null,
    logoPreview: '',
    year: new Date().getFullYear(),
    divisionIds: []
});
const deleteType = ref('');
const deleteItemId = ref(null);

const pageNum = ref(1);
const itemsPerPage = 5;
const searchQuery = ref('');
const selectedCabinetFilter = ref(''); 

watch([searchQuery, selectedCabinetFilter], () => {
    pageNum.value = 1;
});

const filteredDivisions = computed(() => {
    let result = divisions.value;

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(division =>
            division.name.toLowerCase().includes(query) ||
            division.title.toLowerCase().includes(query)
        );
    }

    if (selectedCabinetFilter.value) {
        result = result.filter(division =>
            division.cabinetId === Number(selectedCabinetFilter.value)
        );
    }

    return result;
});

const totalPages = computed(() => Math.ceil(filteredDivisions.value.length / itemsPerPage) || 1);
const startIndex = computed(() => (pageNum.value - 1) * itemsPerPage);
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, filteredDivisions.value.length));
const paginatedDivisions = computed(() => filteredDivisions.value.slice(startIndex.value, endIndex.value));

const cabinetPageNum = ref(1);
const cabinetItemsPerPage = 5;
const cabinetSearchQuery = ref('');

watch(cabinetSearchQuery, () => {
    cabinetPageNum.value = 1;
});

const filteredCabinets = computed(() => {
    if (!cabinetSearchQuery.value) return cabinets.value;
    const query = cabinetSearchQuery.value.toLowerCase();
    return cabinets.value.filter(cabinet =>
        cabinet.name.toLowerCase().includes(query)
    );
});

const cabinetTotalPages = computed(() => Math.ceil(filteredCabinets.value.length / cabinetItemsPerPage) || 1);
const cabinetStartIndex = computed(() => (cabinetPageNum.value - 1) * cabinetItemsPerPage);
const cabinetEndIndex = computed(() => Math.min(cabinetStartIndex.value + cabinetItemsPerPage, filteredCabinets.value.length));
const paginatedCabinets = computed(() => filteredCabinets.value.slice(cabinetStartIndex.value, cabinetEndIndex.value));

const availableCabinets = computed(() => {
    // All cabinets are available since multiple divisions can belong to the same cabinet
    return cabinets.value;
});

function getCabinetName(cabinetId) {
    if (!cabinetId) return 'Unassigned';
    const cabinet = cabinets.value.find(c => c.id === cabinetId);
    return cabinet ? cabinet.name : 'Unknown Cabinet';
}

function getDivisionCabinets(division) {
    if (!division.cabinets || division.cabinets.length === 0) {
        return 'Unassigned';
    }
    const cabinetNames = division.cabinets.map(c => c.name).join(', ');
    return cabinetNames;
}

function getAssociatedDivision(cabinetId) {
    // Check if cabinet exists in division's cabinets array (many-to-many)
    const cabinetDivisions = divisions.value.filter(d => 
        d.cabinets && d.cabinets.some(c => c.id === cabinetId)
    );
    if (cabinetDivisions.length === 0) {
        return 'No Divisions';
    } else if (cabinetDivisions.length === 1) {
        return cabinetDivisions[0].name;
    } else {
        return `${cabinetDivisions.length} divisions (${cabinetDivisions.map(d => d.name).join(', ')})`;
    }
}

function viewDetails(division) {
    selectedDivision.value = division;
    showDetailsModal.value = true;
}

function openCreateModal() {
    isEditMode.value = false;
    formDivision.value = {
        name: '',
        title: '',
        description: '',
        image: '',
        cabinetId: ''
    };
    showFormModal.value = true;
}

function editDivision(division) {
    isEditMode.value = true;
    formDivision.value = { ...division, imageFile: null, imagePreview: '' };
    showFormModal.value = true;
}

function handleImageUpload(event) {
    const file = event.target.files[0];
    if (file) {
        formDivision.value.imageFile = file;
        formDivision.value.imagePreview = URL.createObjectURL(file);
    }
}

async function saveDivision() {
    isSaving.value = true;
    try {
        if (isEditMode.value) {
            // Update existing division
            const updated = await updateDivisionAPI(formDivision.value.id, formDivision.value);
            const index = divisions.value.findIndex(d => d.id === formDivision.value.id);
            if (index !== -1) {
                divisions.value[index] = {
                    ...divisions.value[index],
                    name: updated.name,
                    title: updated.title,
                    description: updated.description || '',
                    image: getImageUrl(updated.image),
                    cabinetId: updated.cabinet_id,
                    code: updated.code
                };
            }
            toast({
                title: 'Division Updated',
                description: `Division "${formDivision.value.name}" has been successfully updated.`,
            });
        } else {
            // Create new division
            const newDivision = await createDivisionAPI(formDivision.value);
            divisions.value.push({
                id: newDivision.id,
                name: newDivision.name,
                title: newDivision.title,
                description: newDivision.description || '',
                image: getImageUrl(newDivision.image),
                cabinetId: newDivision.cabinet_id,
                code: newDivision.code,
                cabinets: newDivision.cabinets || []
            });
            nextDivisionId.value = Math.max(nextDivisionId.value, newDivision.id + 1);
            toast({
                title: 'Division Created',
                description: `Division "${newDivision.name}" has been successfully created.`,
            });
        }
        showFormModal.value = false;
        // Ensure UI reflects latest server state
        divisions.value = await fetchDivisionsFromAPI();
    } catch (error) {
        console.error('Error saving division:', error);
        toast({
            title: 'Error',
            description: 'Failed to save division. Please try again.',
            variant: 'destructive',
        });
    } finally {
        isSaving.value = false;
    }
}

function openCreateCabinetModal() {
    isEditCabinetMode.value = false;
    formCabinet.value = {
        name: '',
        logo: '',
        logoFile: null,
        logoPreview: '',
        year: new Date().getFullYear(),
        divisionIds: []
    };
    showCabinetFormModal.value = true;
}

function editCabinet(cabinet) {
    isEditCabinetMode.value = true;
    
    // Get all divisions that belong to this cabinet
    const cabinetDivisions = divisions.value
        .filter(d => d.cabinets && d.cabinets.some(c => c.id === cabinet.id))
        .map(d => d.id);
    
    formCabinet.value = { 
        ...cabinet,
        logo: cabinet.logo || '',
        logoFile: null,
        logoPreview: '',
        year: cabinet.year || new Date().getFullYear(),
        divisionIds: cabinetDivisions
    };
    showCabinetFormModal.value = true;
}

function handleCabinetLogoUpload(event) {
    const file = event.target.files[0];
    if (file) {
        formCabinet.value.logoFile = file;
        formCabinet.value.logoPreview = URL.createObjectURL(file);
    }
}

async function saveCabinet() {
    isSaving.value = true;
    try {
        if (isEditCabinetMode.value) {
            // Update existing cabinet
            const updated = await updateCabinetAPI(formCabinet.value.id, formCabinet.value);
            const index = cabinets.value.findIndex(c => c.id === formCabinet.value.id);
            if (index !== -1) {
                cabinets.value[index] = {
                    ...cabinets.value[index],
                    name: updated.name,
                    description: updated.description || '',
                    logo: updated.logo || '',
                    theme_color: updated.theme_color || '',
                    year: updated.year || '',
                    status: updated.status || 'active'
                };
            }
            toast({
                title: 'Cabinet Updated',
                description: `Cabinet "${formCabinet.value.name}" has been successfully updated.`,
            });
        } else {
            // Create new cabinet
            const newCabinet = await createCabinetAPI(formCabinet.value);
            cabinets.value.push({
                id: newCabinet.id,
                name: newCabinet.name,
                description: newCabinet.description || '',
                logo: newCabinet.logo || '',
                theme_color: newCabinet.theme_color || '',
                year: newCabinet.year || '',
                status: newCabinet.status || 'active'
            });
            nextCabinetId.value = Math.max(nextCabinetId.value, newCabinet.id + 1);
            toast({
                title: 'Cabinet Created',
                description: `Cabinet "${newCabinet.name}" has been successfully created.`,
            });
        }
        showCabinetFormModal.value = false;
        // Refresh both lists to reflect latest server state and relationships
        cabinets.value = await fetchCabinetsFromAPI();
        divisions.value = await fetchDivisionsFromAPI();
    } catch (error) {
        console.error('Error saving cabinet:', error);
        toast({
            title: 'Error',
            description: 'Failed to save cabinet. Please try again.',
            variant: 'destructive',
        });
    } finally {
        isSaving.value = false;
    }
}

function deleteDivision(id) {
    deleteType.value = 'division';
    deleteItemId.value = id;
    showDeleteModal.value = true;
}

function deleteCabinet(id) {
    deleteType.value = 'cabinet';
    deleteItemId.value = id;
    showDeleteModal.value = true;
}

async function confirmDelete() {
    let deletedItem = null;
    const itemType = deleteType.value;
    
    try {
        if (deleteType.value === 'division') {
            // Store the deleted division for potential restore
            deletedItem = divisions.value.find(d => d.id === deleteItemId.value);
            // Delete division via API
            await deleteDivisionAPI(deleteItemId.value);
            divisions.value = divisions.value.filter(d => d.id !== deleteItemId.value);
            
            toast({
                title: 'Division Deleted',
                description: `Division "${deletedItem?.name || 'Unknown'}" has been successfully deleted.`,
                action: h(ToastAction, {
                    altText: 'Undo',
                    onClick: async () => {
                        if (deletedItem) {
                            try {
                                // Restore the division
                                const restored = await createDivisionAPI(deletedItem);
                                
                                // Restore cabinet associations if the division had any
                                if (deletedItem.cabinets && deletedItem.cabinets.length > 0) {
                                    // For each cabinet that had this division, add it back
                                    for (const cabinet of deletedItem.cabinets) {
                                        try {
                                            // Get the cabinet's current divisions
                                            const cabinetData = cabinets.value.find(c => c.id === cabinet.id);
                                            if (cabinetData) {
                                                // Get current division IDs for this cabinet
                                                const currentDivisionIds = divisions.value
                                                    .filter(d => d.cabinets && d.cabinets.some(c => c.id === cabinet.id))
                                                    .map(d => d.id);
                                                
                                                // Add the restored division ID
                                                const updatedDivisionIds = [...new Set([...currentDivisionIds, restored.id])];
                                                
                                                // Update the cabinet with the restored division
                                                await updateCabinetAPI(cabinet.id, {
                                                    ...cabinetData,
                                                    divisionIds: updatedDivisionIds
                                                });
                                            }
                                        } catch (err) {
                                            console.error('Error restoring cabinet association:', err);
                                        }
                                    }
                                }
                                
                                await fetchDivisionsFromAPI();
                                toast({
                                    title: 'Division Restored',
                                    description: 'The division has been restored successfully.',
                                });
                            } catch (err) {
                                console.error('Error restoring division:', err);
                                toast({
                                    title: 'Error',
                                    description: 'Failed to restore division.',
                                    variant: 'destructive',
                                });
                            }
                        }
                    },
                }, 'Undo'),
            });
        } else if (deleteType.value === 'cabinet') {
            // Store the deleted cabinet for potential restore
            deletedItem = cabinets.value.find(c => c.id === deleteItemId.value);
            // Delete cabinet via API
            await deleteCabinetAPI(deleteItemId.value);
            
            // Update local data: remove cabinet association from divisions
            const cabinetDivisions = divisions.value.filter(d => d.cabinetId === deleteItemId.value);
            cabinetDivisions.forEach(division => {
                division.cabinetId = null;
            });
            
            // Remove the cabinet from local data
            cabinets.value = cabinets.value.filter(c => c.id !== deleteItemId.value);
            await fetchDivisionsFromAPI(); // Refresh divisions to update cabinet associations
            
            toast({
                title: 'Cabinet Deleted',
                description: `Cabinet "${deletedItem?.name || 'Unknown'}" has been successfully deleted.`,
                action: h(ToastAction, {
                    altText: 'Undo',
                    onClick: async () => {
                        if (deletedItem) {
                            try {
                                await createCabinetAPI(deletedItem);
                                await fetchCabinetsFromAPI();
                                await fetchDivisionsFromAPI();
                                toast({
                                    title: 'Cabinet Restored',
                                    description: 'The cabinet has been restored successfully.',
                                });
                            } catch (err) {
                                console.error('Error restoring cabinet:', err);
                                toast({
                                    title: 'Error',
                                    description: 'Failed to restore cabinet.',
                                    variant: 'destructive',
                                });
                            }
                        }
                    },
                }, 'Undo'),
            });
        }
        showDeleteModal.value = false;
    } catch (error) {
        console.error('Error deleting:', error);
        toast({
            title: 'Error',
            description: `Failed to delete ${itemType}. Please try again.`,
            variant: 'destructive',
        });
        showDeleteModal.value = false;
    }
}

onMounted(async () => {
    isLoading.value = true;
    try {
        // Load data from API
        const [apiDivisions, apiCabinets] = await Promise.all([
            fetchDivisionsFromAPI(),
            fetchCabinetsFromAPI()
        ]);

        divisions.value = apiDivisions;
        cabinets.value = apiCabinets;

        // Set next IDs based on existing data
        nextDivisionId.value = divisions.value.length > 0 ? Math.max(...divisions.value.map(d => d.id)) + 1 : 1;
        nextCabinetId.value = cabinets.value.length > 0 ? Math.max(...cabinets.value.map(c => c.id)) + 1 : 1;

    } catch (error) {
        console.error('Error loading data:', error);
        
        // Fallback to sample data if API fails
        divisions.value = [
            {
                id: 1,
                name: 'BOD',
                title: 'Board of Directors',
                image: 'https://i.pinimg.com/736x/f2/96/65/f296659f98543ad0ee11738a62e7652f.jpg',
                description: 'Responsible for strategic planning and organizational leadership',
                cabinetId: 1
            },
            {
                id: 2,
                name: 'HRD',
                title: 'Human Resources Development',
                image: 'https://i.pinimg.com/736x/f2/96/65/f296659f98543ad0ee11738a62e7652f.jpg',
                description: 'Manages human capital development and organizational culture',
                cabinetId: 1
            },
            {
                id: 3,
                name: 'ICM',
                title: 'Information and Creative Media',
                image: 'https://i.pinimg.com/736x/f2/96/65/f296659f98543ad0ee11738a62e7652f.jpg',
                description: 'Handles information systems and creative content management',
                cabinetId: 1
            }
        ];

        cabinets.value = [
            {
                id: 1,
                name: 'Kaustav Cabinet'
            }
        ];

        nextDivisionId.value = Math.max(...divisions.value.map(d => d.id)) + 1;
        nextCabinetId.value = Math.max(...cabinets.value.map(c => c.id)) + 1;    } finally {
        isLoading.value = false;
    }
});
</script>

<style scoped>
.grid-lines {
    background-image: linear-gradient(to right, rgba(0, 0, 0, 0.05) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
    background-size: 20px 20px;
}
</style>