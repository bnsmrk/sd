<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    users: Array<{
        id: number;
        name: string;
        email: string;
        role: string;
    }>;
}>();

const destroyItem = (id: number) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(`/users/${id}`);
    }
};
</script>

<template>
    <Head title="Users" />
    <AppLayout>
        <div class="p-4">
            <div class="mb-4 flex justify-between">
                <h2 class="text-xl font-bold">Users</h2>
                <Link href="/users/create" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Add User</Link>
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full text-left text-sm text-gray-500 rtl:text-right dark:text-gray-400">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Name</th>
                            <th scope="col" class="px-6 py-3">Email</th>
                            <th scope="col" class="px-6 py-3">Role</th>
                            <th scope="col" class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="user in users"
                            :key="user.id"
                            class="border-b border-gray-200 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600"
                        >
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ user.name }}</td>
                            <td class="px-6 py-4">{{ user.email }}</td>
                            <td class="px-6 py-4 capitalize">{{ user.role }}</td>
                            <td class="flex items-center justify-center space-x-3 px-6 py-4">
                                <Link :href="`/users/${user.id}/edit`" class="font-medium text-blue-600 hover:underline dark:text-blue-500">
                                    Edit
                                </Link>
                                <button @click="destroyItem(user.id)" class="font-medium text-red-600 hover:underline dark:text-red-500">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
