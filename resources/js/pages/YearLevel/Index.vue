<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { Check, Pencil, Plus, Save, Trash2, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';

// Props
const props = defineProps<{
    yearLevels: {
        data: Array<{ id: number; name: string }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: { search?: string };
}>();

// Breadcrumbs
const breadcrumbs = [{ title: 'Year Levels', href: '/year-levels' }];

// Search
const search = ref(props.filters.search || '');

watch(
    search,
    debounce((value) => {
        router.get('/year-levels', { search: value }, { preserveState: true, replace: true });
    }, 300),
);

// Modal state
const showAddModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);

// Form state
const createForm = useForm({ name: '' });
const editForm = useForm({ id: null as number | null, name: '' });
const deleteId = ref<number | null>(null);

// Open modals
const openAddModal = () => {
    createForm.reset();
    showAddModal.value = true;
};

const openEditModal = (level: { id: number; name: string }) => {
    editForm.id = level.id;
    editForm.name = level.name;
    showEditModal.value = true;
};

const openDeleteModal = (id: number) => {
    deleteId.value = id;
    showDeleteModal.value = true;
};

// Submit handlers
const createYearLevel = () => {
    createForm.post('/year-levels', {
        onSuccess: () => (showAddModal.value = false),
    });
};

const updateYearLevel = () => {
    if (editForm.id) {
        editForm.put(`/year-levels/${editForm.id}`, {
            onSuccess: () => (showEditModal.value = false),
        });
    }
};

const deleteYearLevel = () => {
    if (deleteId.value !== null) {
        router.delete(`/year-levels/${deleteId.value}`, {
            onSuccess: () => {
                deleteId.value = null;
                showDeleteModal.value = false;
            },
        });
    }
};
</script>

<template>
    <Head title="Year Levels" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-xl font-bold">🎓 Year Levels</h2>
                <div class="flex items-center gap-2">
                    <input v-model="search" type="text" placeholder="Search year level..." class="rounded border px-3 py-2 text-sm shadow-sm" />
                    <button @click="openAddModal" class="inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        <Plus class="h-4 w-4" /> Add Year Level
                    </button>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-center">ID</th>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white text-sm">
                        <tr v-for="level in props.yearLevels.data" :key="level.id">
                            <td class="px-6 py-4 text-center font-medium text-gray-800">{{ level.id }}</td>
                            <td class="px-6 py-4 text-left text-gray-700">{{ level.name }}</td>
                            <td class="space-x-2 px-6 py-4 text-center">
                                <button @click="openEditModal(level)" class="inline-flex items-center gap-1 text-blue-600 hover:underline">
                                    <Pencil class="h-4 w-4" /> Edit
                                </button>
                                <button @click="openDeleteModal(level.id)" class="inline-flex items-center gap-1 text-red-600 hover:underline">
                                    <Trash2 class="h-4 w-4" /> Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="mt-6 flex justify-center gap-2">
                <template v-for="(link, i) in props.yearLevels.links" :key="i">
                    <span v-if="!link.url" class="px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    <Link
                        v-else
                        :href="link.url"
                        class="inline-flex items-center justify-center rounded-md border px-3 py-1 text-sm transition"
                        :class="{
                            'border-blue-600 bg-blue-600 text-white': link.active,
                            'border-gray-300 text-gray-700 hover:bg-gray-100': !link.active,
                        }"
                    >
                        <span v-html="link.label" />
                    </Link>
                </template>
            </div>
        </div>

        <!-- Add Modal -->
        <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-bold">➕ Add Year Level</h3>
                <form @submit.prevent="createYearLevel" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Name</label>
                        <input v-model="createForm.name" type="text" class="w-full rounded border p-2" placeholder="e.g. Grade 7" />
                        <div v-if="createForm.errors.name" class="text-sm text-red-600">
                            {{ createForm.errors.name }}
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="showAddModal = false" class="inline-flex items-center gap-1 rounded bg-gray-300 px-4 py-2">
                            <X class="h-4 w-4" /> Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                            <Save class="h-4 w-4" /> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-bold">✏️ Edit Year Level</h3>
                <form @submit.prevent="updateYearLevel" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Name</label>
                        <input v-model="editForm.name" type="text" class="w-full rounded border p-2" />
                        <div v-if="editForm.errors.name" class="text-sm text-red-600">
                            {{ editForm.errors.name }}
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="showEditModal = false" class="inline-flex items-center gap-1 rounded bg-gray-300 px-4 py-2">
                            <X class="h-4 w-4" /> Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1 rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700">
                            <Check class="h-4 w-4" /> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-bold text-red-600">⚠️ Confirm Deletion</h3>
                <p class="mb-6 text-gray-600">Are you sure you want to delete this year level?</p>
                <div class="flex justify-end space-x-2">
                    <button @click="showDeleteModal = false" class="inline-flex items-center gap-1 rounded bg-gray-300 px-4 py-2">
                        <X class="h-4 w-4" /> Cancel
                    </button>
                    <button @click="deleteYearLevel" class="inline-flex items-center gap-1 rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                        <Trash2 class="h-4 w-4" /> Delete
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
