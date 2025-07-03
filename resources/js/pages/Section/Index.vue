<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    sections: Array<{
        id: number;
        name: string;
        year_level: { id: number; name: string };
    }>;
    yearLevels: Array<{ id: number; name: string }>;
}>();

// Modals
const showAddModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);

// Forms
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

// Actions
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

// Submit actions
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
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">Sections</h2>
                <button @click="openAddModal" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Add Section</button>
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
                        <tr v-for="section in sections" :key="section.id">
                            <td class="px-6 py-4">{{ section.id }}</td>
                            <td class="px-6 py-4">{{ section.name }}</td>
                            <td class="px-6 py-4">{{ section.year_level.name }}</td>
                            <td class="space-x-4 px-6 py-4 text-center">
                                <button @click="openEditModal(section)" class="text-blue-600 hover:underline">Edit</button>
                                <button @click="openDeleteModal(section.id)" class="text-red-600 hover:underline">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Modal -->
        <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-bold">Add Section</h3>
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
                        <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-bold">Edit Section</h3>
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
                        <button type="submit" class="rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-bold text-red-600">Confirm Deletion</h3>
                <p class="mb-6 text-gray-700">Are you sure you want to delete this section?</p>
                <div class="flex justify-end space-x-2">
                    <button @click="cancelDelete" type="button" class="rounded bg-gray-300 px-4 py-2">Cancel</button>
                    <button @click="confirmDelete" class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
