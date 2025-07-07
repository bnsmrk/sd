<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { Check, Pencil, Plus, Save, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';
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
    createForm.post('/sections', {
        onSuccess: () => {
            showAddModal.value = false;
        },
    });
};

const submitEdit = () => {
    if (editForm.id) {
        editForm.put(`/sections/${editForm.id}`, {
            onSuccess: () => {
                showEditModal.value = false;
            },
        });
    }
};

const confirmDelete = () => {
    if (deleteId.value !== null) {
        router.delete(`/sections/${deleteId.value}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
                deleteId.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Sections" />
    <AppLayout>
        <div class="p-4">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-xl font-bold text-gray-800">🏫 Sections</h2>
                <div class="flex items-center gap-2">
                    <input v-model="search" type="text" placeholder="Search sections..." class="rounded border px-3 py-2 text-sm shadow-sm" />
                    <button @click="openAddModal" class="inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        <Plus class="h-4 w-4" /> Add Section
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full table-auto text-left text-sm text-gray-700">
                    <thead class="bg-gray-100 text-xs font-semibold text-gray-600 uppercase">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Section Name</th>
                            <th class="px-6 py-3">Year Level</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="section in sections.data" :key="section.id">
                            <td class="px-6 py-4">{{ section.id }}</td>
                            <td class="px-6 py-4">{{ section.name }}</td>
                            <td class="px-6 py-4">{{ section.year_level.name }}</td>
                            <td class="space-x-4 px-6 py-4 text-center">
                                <button @click="openEditModal(section)" class="inline-flex items-center gap-1 text-blue-600 hover:underline">
                                    <Pencil class="h-4 w-4" /> Edit
                                </button>
                                <button @click="openDeleteModal(section.id)" class="inline-flex items-center gap-1 text-red-600 hover:underline">
                                    <Trash2 class="h-4 w-4" /> Delete
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
                        class="inline-flex items-center justify-center rounded-md border px-3 py-1 text-sm transition"
                        :class="{
                            'border-blue-600 bg-blue-600 text-white': link.active,
                            'border-gray-300 text-gray-700 hover:bg-gray-100': !link.active,
                        }"
                        preserve-scroll
                        preserve-state
                    >
                        <span v-html="link.label" />
                    </Link>
                </template>
            </div>
        </div>

        <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-bold">➕ Add Section</h3>
                <form @submit.prevent="submitCreate" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Section Name</label>
                        <input v-model="createForm.name" type="text" class="w-full rounded border p-2" />
                        <div v-if="createForm.errors.name" class="text-sm text-red-600">{{ createForm.errors.name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Year Level</label>
                        <select v-model="createForm.year_level_id" class="w-full rounded border p-2">
                            <option value="">Select Year Level</option>
                            <option v-for="level in yearLevels" :key="level.id" :value="level.id">{{ level.name }}</option>
                        </select>
                        <div v-if="createForm.errors.year_level_id" class="text-sm text-red-600">{{ createForm.errors.year_level_id }}</div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button @click="showAddModal = false" type="button" class="rounded bg-gray-300 px-4 py-2">Cancel</button>
                        <button type="submit" class="inline-flex items-center gap-1 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                            <Save class="h-4 w-4" /> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-bold">✏️ Edit Section</h3>
                <form @submit.prevent="submitEdit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Section Name</label>
                        <input v-model="editForm.name" type="text" class="w-full rounded border p-2" />
                        <div v-if="editForm.errors.name" class="text-sm text-red-600">{{ editForm.errors.name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Year Level</label>
                        <select v-model="editForm.year_level_id" class="w-full rounded border p-2">
                            <option value="">Select Year Level</option>
                            <option v-for="level in yearLevels" :key="level.id" :value="level.id">{{ level.name }}</option>
                        </select>
                        <div v-if="editForm.errors.year_level_id" class="text-sm text-red-600">{{ editForm.errors.year_level_id }}</div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button @click="showEditModal = false" type="button" class="rounded bg-gray-300 px-4 py-2">Cancel</button>
                        <button type="submit" class="inline-flex items-center gap-1 rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700">
                            <Check class="h-4 w-4" /> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-bold text-red-600">🗑️ Confirm Deletion</h3>
                <p class="mb-6 text-gray-700">Are you sure you want to delete this section?</p>
                <div class="flex justify-end space-x-2">
                    <button @click="cancelDelete" type="button" class="rounded bg-gray-300 px-4 py-2">Cancel</button>
                    <button @click="confirmDelete" class="inline-flex items-center gap-1 rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                        <Trash2 class="h-4 w-4" /> Delete
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
