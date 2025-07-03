<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';

defineProps<{ yearLevels: Array<{ id: number; name: string }> }>();

const breadcrumbs = [{ title: 'Year Levels', href: '/year-levels' }];

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
        <div class="p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">Year Levels</h2>
                <button @click="openAddModal" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Add Year Level</button>
            </div>

            <div class="overflow-hidden rounded-lg border shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-600">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white text-sm">
                        <tr v-for="level in yearLevels" :key="level.id">
                            <td class="px-6 py-4">{{ level.id }}</td>
                            <td class="px-6 py-4">{{ level.name }}</td>
                            <td class="px-6 py-4 text-center space-x-3">
                                <button @click="openEditModal(level)" class="text-blue-600 hover:underline">Edit</button>
                                <button @click="openDeleteModal(level.id)" class="text-red-600 hover:underline">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Modal -->
        <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-bold">Add Year Level</h3>
                <form @submit.prevent="createYearLevel" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Name</label>
                        <input v-model="createForm.name" type="text" class="w-full rounded border p-2" placeholder="e.g. Grade 7" />
                        <div v-if="createForm.errors.name" class="text-sm text-red-600">{{ createForm.errors.name }}</div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-bold">Edit Year Level</h3>
                <form @submit.prevent="updateYearLevel" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Name</label>
                        <input v-model="editForm.name" type="text" class="w-full rounded border p-2" />
                        <div v-if="editForm.errors.name" class="text-sm text-red-600">{{ editForm.errors.name }}</div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-bold text-red-600">Confirm Deletion</h3>
                <p class="mb-6 text-gray-600">Are you sure you want to delete this year level?</p>
                <div class="flex justify-end space-x-2">
                    <button @click="showDeleteModal = false" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                    <button @click="deleteYearLevel" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
