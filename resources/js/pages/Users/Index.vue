<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { Pencil, Save, Trash2, UserPlus, XCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    users: {
        data: Array<{ id: number; name: string; email: string; role: string }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: { search?: string };
}>();

const search = ref(props.filters.search || '');
watch(
    search,
    debounce((val) => {
        router.get('/users', { search: val }, { preserveState: true, replace: true });
    }, 300),
);

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);

const createForm = useForm({
    name: '',
    email: '',
    role: 'teacher',
    password: '',
    password_confirmation: '',
});

const editForm = useForm({
    id: null as number | null,
    name: '',
    email: '',
    role: 'teacher',
});

const deleteId = ref<number | null>(null);

const openCreateModal = () => {
    createForm.reset();
    showCreateModal.value = true;
};

const submitCreate = () => {
    createForm.post('/users', {
        onSuccess: () => {
            showCreateModal.value = false;
        },
    });
};

const openEditModal = (user: any) => {
    editForm.id = user.id;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.role = user.role;
    showEditModal.value = true;
};

const submitEdit = () => {
    if (!editForm.id) return;
    editForm.put(`/users/${editForm.id}`, {
        onSuccess: () => {
            showEditModal.value = false;
        },
    });
};

const confirmDelete = (id: number) => {
    deleteId.value = id;
    showDeleteModal.value = true;
};

const destroyItem = () => {
    if (deleteId.value !== null) {
        router.delete(`/users/${deleteId.value}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
            },
        });
    }
};
</script>
<template>
    <Head title="Users" />
    <AppLayout>
        <div class="p-4">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-xl font-bold text-[#01006c]">👥 Users</h1>
                <div class="flex flex-wrap items-center gap-2">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search..."
                        class="rounded border border-[#01006c] px-3 py-2 text-sm shadow-sm focus:border-[#ffc60b] focus:outline-none"
                    />
                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center gap-2 rounded bg-[#01006c] px-4 py-2 text-white hover:bg-[#0d1282]"
                    >
                        <UserPlus class="h-4 w-4" /> Add User
                    </button>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border border-gray-200 shadow">
                <table class="min-w-full divide-y divide-[#01006c] text-sm">
                    <thead class="bg-[#01006c] text-xs font-semibold text-white">
                        <tr>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-left">Role</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#01006c] bg-white">
                        <tr v-for="user in props.users.data" :key="user.id" class="transition hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-800">{{ user.name }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ user.email }}</td>
                            <td class="px-6 py-4 text-gray-700 capitalize">{{ user.role }}</td>
                            <td class="space-x-2 px-6 py-4 text-center">
                                <button @click="openEditModal(user)" class="inline-flex items-center gap-1 text-blue-600 hover:underline">
                                    <Pencil class="h-4 w-4" /> Edit
                                </button>
                                <button @click="confirmDelete(user.id)" class="inline-flex items-center gap-1 text-red-600 hover:underline">
                                    <Trash2 class="h-4 w-4" /> Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="props.users.data.length === 0">
                            <td colspan="4" class="py-4 text-center text-sm text-gray-500 italic">No users found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <template v-for="(link, i) in props.users.links" :key="i">
                    <span
                        v-if="!link.url"
                        class="inline-flex cursor-not-allowed items-center justify-center rounded bg-gray-200 px-4 py-2 text-sm text-gray-500"
                        v-html="link.label"
                    />
                    <Link
                        v-else
                        :href="link.url"
                        class="inline-flex items-center justify-center rounded px-3 py-2 text-sm font-medium transition-all"
                        :class="{
                            'bg-[#01006c] text-white hover:bg-[#0d1282]': link.active,
                            'border border-gray-300 bg-white text-gray-700 hover:bg-gray-100': !link.active,
                        }"
                    >
                        <span v-html="link.label" />
                    </Link>
                </template>
            </div>

            <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur">
                <div class="w-full max-w-md rounded bg-white p-6 shadow">
                    <h2 class="mb-4 text-lg font-bold text-[#01006c]">Create User</h2>
                    <form @submit.prevent="submitCreate" class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-[#ff69b4]">Name</label>
                            <input
                                v-model="createForm.name"
                                type="text"
                                class="w-full rounded border border-[#01006c] p-2 focus:border-[#ffc60b] focus:outline-none"
                            />
                            <div class="text-sm text-red-600">{{ createForm.errors.name }}</div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-[#ff69b4]">Email</label>
                            <input
                                v-model="createForm.email"
                                type="email"
                                class="w-full rounded border border-[#01006c] p-2 focus:border-[#ffc60b] focus:outline-none"
                            />
                            <div class="text-sm text-red-600">{{ createForm.errors.email }}</div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-[#ff69b4]">Role</label>
                            <select
                                v-model="createForm.role"
                                class="w-full rounded border border-[#01006c] bg-white p-2 focus:border-[#ffc60b] focus:outline-none"
                            >
                                <option value="teacher">Teacher</option>
                                <option value="ict">ICT</option>
                                <option value="head">Head</option>
                            </select>
                            <div class="text-sm text-red-600">{{ createForm.errors.role }}</div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-[#ff69b4]">Password</label>
                            <input
                                v-model="createForm.password"
                                type="password"
                                class="w-full rounded border border-[#01006c] p-2 focus:border-[#ffc60b] focus:outline-none"
                            />
                            <div class="text-sm text-red-600">{{ createForm.errors.password }}</div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-[#ff69b4]">Confirm Password</label>
                            <input
                                v-model="createForm.password_confirmation"
                                type="password"
                                class="w-full rounded border border-[#01006c] p-2 focus:border-[#ffc60b] focus:outline-none"
                            />
                        </div>
                        <div class="flex justify-end gap-2">
                            <button
                                type="button"
                                @click="showCreateModal = false"
                                class="inline-flex items-center gap-1 rounded bg-gray-300 px-4 py-2"
                            >
                                <XCircle class="h-4 w-4" /> Cancel
                            </button>
                            <button type="submit" class="inline-flex items-center gap-1 rounded bg-[#01006c] px-4 py-2 text-white hover:bg-[#0d1282]">
                                <Save class="h-4 w-4" /> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur">
                <div class="w-full max-w-md rounded bg-white p-6 shadow">
                    <h2 class="mb-4 text-lg font-bold text-[#01006c]">Edit User</h2>
                    <form @submit.prevent="submitEdit" class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-[#ff69b4]">Name</label>
                            <input
                                v-model="editForm.name"
                                type="text"
                                class="w-full rounded border border-[#01006c] p-2 focus:border-[#ffc60b] focus:outline-none"
                            />
                            <div class="text-sm text-red-600">{{ editForm.errors.name }}</div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-[#ff69b4]">Email</label>
                            <input
                                v-model="editForm.email"
                                type="email"
                                class="w-full rounded border border-[#01006c] p-2 focus:border-[#ffc60b] focus:outline-none"
                            />
                            <div class="text-sm text-red-600">{{ editForm.errors.email }}</div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-[#ff69b4]">Role</label>
                            <select
                                v-model="editForm.role"
                                class="w-full rounded border border-[#01006c] bg-white p-2 focus:border-[#ffc60b] focus:outline-none"
                            >
                                <option value="teacher">Teacher</option>
                                <option value="ict">ICT</option>
                                <option value="head">Head</option>
                            </select>
                            <div class="text-sm text-red-600">{{ editForm.errors.role }}</div>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showEditModal = false" class="inline-flex items-center gap-1 rounded bg-gray-300 px-4 py-2">
                                <XCircle class="h-4 w-4" /> Cancel
                            </button>
                            <button type="submit" class="inline-flex items-center gap-1 rounded bg-[#01006c] px-4 py-2 text-white hover:bg-[#0d1282]">
                                <Save class="h-4 w-4" /> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur">
                <div class="w-full max-w-sm rounded bg-white p-6 shadow">
                    <h2 class="mb-4 text-lg font-bold text-[#01006c]">Confirm Deletion</h2>
                    <p class="mb-6 text-sm text-gray-700">Are you sure you want to delete this user?</p>
                    <div class="flex justify-end gap-4">
                        <button @click="showDeleteModal = false" class="inline-flex items-center gap-1 rounded bg-gray-300 px-4 py-2">
                            <XCircle class="h-4 w-4" /> Cancel
                        </button>
                        <button @click="destroyItem" class="inline-flex items-center gap-1 rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                            <Trash2 class="h-4 w-4" /> Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
