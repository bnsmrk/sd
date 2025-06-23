<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { ref, watch } from 'vue';

const props = defineProps<{
    users: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            role: string;
        }>;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    filters: {
        search?: string;
    };
}>();

const search = ref(props.filters.search || '');

// Debounced search
watch(
    search,
    debounce((value) => {
        router.get('/users', { search: value }, { preserveState: true, replace: true });
    }, 300),
);

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
            <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-xl font-bold">Users</h2>
                <div class="flex flex-wrap items-center gap-3">
                    <input
                        type="text"
                        v-model="search"
                        class="rounded border border-gray-300 px-3 py-2 text-sm shadow-sm"
                        placeholder="Search by name or email"
                    />
                    <Link href="/users/create" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Add User</Link>
                </div>
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm text-gray-700 dark:text-gray-300">
                    <thead class="bg-gray-50 text-xs uppercase dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id" class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ user.name }}</td>
                            <td class="px-6 py-4">{{ user.email }}</td>
                            <td class="px-6 py-4 capitalize">{{ user.role }}</td>
                            <td class="flex items-center justify-center space-x-3 px-6 py-4">
                                <Link :href="`/users/${user.id}/edit`" class="text-blue-600 hover:underline dark:text-blue-400">Edit</Link>
                                <button @click="destroyItem(user.id)" class="text-red-600 hover:underline dark:text-red-400">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-400 dark:text-gray-500">No users found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <!-- Pagination -->
            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <template v-for="(link, index) in users.links" :key="index">
                    <!-- ✅ Use <span> for disabled links -->
                    <span v-if="!link.url" class="cursor-not-allowed rounded px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    <!-- ✅ Use <Link> with text only, NOT v-html -->
                    <Link
                        v-else
                        :href="link.url"
                        class="rounded px-3 py-1 text-sm"
                        :class="{
                            'bg-blue-600 text-white': link.active,
                            'text-gray-600 hover:underline': !link.active,
                        }"
                    >
                        <span v-html="link.label" />
                    </Link>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
