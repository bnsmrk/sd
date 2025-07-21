<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { Check, Pencil, Plus, Save, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);
const props = defineProps<{
    sections: {
        data: Array<{
            id: number;
            name: string;
            year_level: { id: number; name: string };
        }>;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    yearLevels: Array<{ id: number; name: string }>;
    filters: {
        search?: string;
    };
}>();

const search = ref(props.filters.search || '');
watch(
    search,
    debounce((value) => {
        router.get('/sections', { search: value }, { preserveState: true, replace: true });
    }, 300),
);

const showAddModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);

const createForm = useForm({
    name: '',
    year_level_id: '',
});

const editForm = useForm({
    id: null as number | null,
    name: '',
    year_level_id: '',
});

const deleteId = ref<number | null>(null);

const openAddModal = () => {
    createForm.reset();
    showAddModal.value = true;
};

const openEditModal = (section: any) => {
    editForm.id = section.id;
    editForm.name = section.name;
    editForm.year_level_id = section.year_level.id;
    showEditModal.value = true;
};

const openDeleteModal = (id: number) => {
    deleteId.value = id;
    showDeleteModal.value = true;
};

const cancelDelete = () => {
    deleteId.value = null;
    showDeleteModal.value = false;
};

const submitCreate = () => {
    isCreating.value = true;
    createForm.post('/sections', {
        onSuccess: () => {
            showAddModal.value = false;
            createForm.reset();
        },
        onFinish: () => {
            setTimeout(() => {
                isCreating.value = false;
            }, 2000);
        },
    });
};

const submitEdit = () => {
    if (editForm.id) {
        isUpdating.value = true;
        editForm.put(`/sections/${editForm.id}`, {
            onSuccess: () => {
                showEditModal.value = false;
            },
            onFinish: () => {
                setTimeout(() => {
                    isUpdating.value = false;
                }, 2000);
            },
        });
    }
};

const confirmDelete = () => {
    if (deleteId.value !== null) {
        isDeleting.value = true;
        router.delete(`/sections/${deleteId.value}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
                deleteId.value = null;
            },
            onFinish: () => {
                setTimeout(() => {
                    isDeleting.value = false;
                }, 2000);
            },
        });
    }
};
type SortableKey = 'id' | 'name' | 'year_level';
const sortKey = ref<SortableKey>('id');
const sortAsc = ref(true);

function toggleSort(key: SortableKey) {
    if (sortKey.value === key) {
        sortAsc.value = !sortAsc.value;
    } else {
        sortKey.value = key;
        sortAsc.value = true;
    }
}
const sortedSections = computed(() => {
    return [...props.sections.data].sort((a, b) => {
        let aValue: string | number = '';
        let bValue: string | number = '';

        if (sortKey.value === 'year_level') {
            aValue = a.year_level.name.toLowerCase();
            bValue = b.year_level.name.toLowerCase();
        } else {
            aValue = (a as any)[sortKey.value];
            bValue = (b as any)[sortKey.value];
        }

        if (aValue < bValue) return sortAsc.value ? -1 : 1;
        if (aValue > bValue) return sortAsc.value ? 1 : -1;
        return 0;
    });
});
</script>

<template>
    <Head title="Sections" />
    <AppLayout>
        <div v-if="isLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="flex flex-col items-center gap-4">
                <div class="relative h-16 w-16">
                    <div class="animate-spin-slow-cw absolute inset-0 rounded-full border-4 border-blue-600 border-t-transparent"></div>
                    <div class="animate-spin-slow-ccw absolute inset-2 rounded-full border-4 border-yellow-400 border-t-transparent"></div>
                    <div class="animate-spin-fast-cw absolute inset-4 rounded-full border-4 border-pink-500 border-t-transparent"></div>
                </div>
                <div class="text-center">
                    <span class="block animate-pulse text-base font-semibold text-[#01006c]">Processing Request...</span>
                    <span class="text-xs text-[#01006c]/70">This may take a moment</span>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-bold text-pink-400">Sections</h2>
                <div class="flex items-center gap-2">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search sections..."
                        class="rounded border border-pink-300 px-3 py-2 text-sm text-pink-400 shadow-sm focus:border-yellow-400 focus:outline-none"
                    />
                    <button
                        @click="openAddModal"
                        class="inline-flex items-center gap-2 rounded bg-pink-400 px-4 py-2 text-white transition hover:bg-pink-500"
                    >
                        <Plus class="h-4 w-4" /> Add Section
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-pink-200 shadow">
                <table class="min-w-full table-auto text-left text-sm text-pink-400">
                    <thead class="bg-pink-100 text-xs font-semibold text-pink-400 uppercase">
                        <tr>
                            <th @click="toggleSort('id')" class="cursor-pointer px-6 py-3">
                                ID <span v-if="sortKey === 'id'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('name')" class="cursor-pointer px-6 py-3">
                                Section Name <span v-if="sortKey === 'name'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('year_level')" class="cursor-pointer px-6 py-3">
                                Year Level <span v-if="sortKey === 'year_level'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pink-100 bg-white">
                        <tr v-for="section in sortedSections" :key="section.id" class="hover:bg-pink-50">
                            <td class="px-6 py-4 text-[#01006c]">{{ section.id }}</td>
                            <td class="px-6 py-4 text-[#01006c]">{{ section.name }}</td>
                            <td class="px-6 py-4 text-[#01006c]">{{ section.year_level.name }}</td>
                            <td class="space-x-2 px-6 py-4 text-center">
                                <button @click="openEditModal(section)" class="text-blue-500 hover:text-blue-800">
                                    <Pencil class="h-4 w-4" />
                                </button>
                                <button @click="openDeleteModal(section.id)" class="text-red-600 hover:text-red-800">
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-center gap-2">
                <template v-for="(link, i) in sections.links" :key="i">
                    <span v-if="!link.url" class="px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    <Link
                        v-else
                        :href="link.url"
                        preserve-scroll
                        preserve-state
                        class="inline-flex items-center justify-center rounded-md border px-3 py-1 text-sm transition"
                        :class="{
                            'border-pink-500 bg-pink-500 text-white': link.active,
                            'border-pink-200 text-pink-800 hover:bg-pink-100': !link.active,
                        }"
                    >
                        <span v-html="link.label" />
                    </Link>
                </template>
            </div>
        </div>

        <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl border-2 border-pink-300 bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-xl font-bold text-pink-600">Add Section</h3>
                <form @submit.prevent="submitCreate" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-pink-700">Section Name</label>
                        <input
                            v-model="createForm.name"
                            type="text"
                            class="w-full rounded-lg border-2 border-pink-200 px-3 py-2 text-pink-800 focus:border-yellow-400 focus:outline-none"
                        />
                        <div v-if="createForm.errors.name" class="text-sm text-red-600">{{ createForm.errors.name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-pink-700">Year Level</label>
                        <select
                            v-model="createForm.year_level_id"
                            class="w-full rounded-lg border-2 border-pink-200 bg-white px-3 py-2 text-pink-800 focus:border-yellow-400 focus:outline-none"
                        >
                            <option value="">Select Year Level</option>
                            <option v-for="level in yearLevels" :key="level.id" :value="level.id">{{ level.name }}</option>
                        </select>
                        <div v-if="createForm.errors.year_level_id" class="text-sm text-red-600">{{ createForm.errors.year_level_id }}</div>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button
                            type="button"
                            @click="showAddModal = false"
                            class="rounded-md bg-yellow-400 px-4 py-2 text-sm font-semibold text-[#01006c] hover:brightness-110"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-1 rounded-md bg-pink-500 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-600"
                        >
                            <Save class="h-4 w-4" /> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl border-2 border-pink-300 bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-xl font-bold text-pink-600">Edit Section</h3>
                <form @submit.prevent="submitEdit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-pink-700">Section Name</label>
                        <input
                            v-model="editForm.name"
                            type="text"
                            class="w-full rounded-lg border-2 border-pink-200 px-3 py-2 text-pink-800 focus:border-yellow-400 focus:outline-none"
                        />
                        <div v-if="editForm.errors.name" class="text-sm text-red-600">{{ editForm.errors.name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-pink-700">Year Level</label>
                        <select
                            v-model="editForm.year_level_id"
                            class="w-full rounded-lg border-2 border-pink-200 bg-white px-3 py-2 text-pink-800 focus:border-yellow-400 focus:outline-none"
                        >
                            <option value="">Select Year Level</option>
                            <option v-for="level in yearLevels" :key="level.id" :value="level.id">{{ level.name }}</option>
                        </select>
                        <div v-if="editForm.errors.year_level_id" class="text-sm text-red-600">{{ editForm.errors.year_level_id }}</div>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button
                            type="button"
                            @click="showEditModal = false"
                            class="rounded-md bg-yellow-400 px-4 py-2 text-sm font-semibold text-[#01006c] hover:brightness-110"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-1 rounded-md bg-pink-500 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-600"
                        >
                            <Check class="h-4 w-4" /> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl border-2 border-pink-300 bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-xl font-bold text-pink-600">Confirm Deletion</h3>
                <p class="mb-6 text-gray-700">Are you sure you want to delete this section?</p>
                <div class="flex justify-end space-x-2 pt-2">
                    <button
                        type="button"
                        @click="cancelDelete"
                        class="rounded-md bg-yellow-400 px-4 py-2 text-sm font-semibold text-[#01006c] hover:brightness-110"
                    >
                        Cancel
                    </button>
                    <button
                        @click="confirmDelete"
                        class="inline-flex items-center gap-1 rounded-md bg-pink-500 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-600"
                    >
                        <Trash2 class="h-4 w-4" /> Delete
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@keyframes spin-cw {
    to {
        transform: rotate(360deg);
    }
}

@keyframes spin-ccw {
    to {
        transform: rotate(-360deg);
    }
}

.animate-spin-slow-cw {
    animation: spin-cw 2s linear infinite;
}

.animate-spin-slow-ccw {
    animation: spin-ccw 3s linear infinite;
}

.animate-spin-fast-cw {
    animation: spin-cw 1s linear infinite;
}
</style>
