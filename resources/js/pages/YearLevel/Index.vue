<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
// import { ref, watch } from 'vue';
// const page = usePage();
// const success = ref<string | null>(null);
// const danger = ref<string | null>(null);
// const warning = ref<string | null>(null);

// watch(
//     () => page.props.flash as Record<string, string>,
//     (flash) => {
//         success.value = flash.success || null;
//         danger.value = flash.danger || null;
//         warning.value = flash.warning || null;
//     },
//     // { immediate: true }
// );
defineProps<{ yearLevels: Array<{ id: number; name: string }> }>();

const breadcrumbs = [{ title: 'Year Levels', href: '/yearlevel' }];

const destroyItem = (id: number) => {
    if (confirm('Are you sure you want to delete this?')) {
        router.delete(`/year-levels/${id}`);
    }
};
</script>

<template>
    <Head title="Year Levels" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">Year Levels</h2>
                <Link href="/year-levels/create" class="rounded bg-blue-600 px-4 py-2 text-white">Add Year Level</Link>
            </div>

            <!-- <table class="w-full table-auto border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Name</th>
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="level in yearLevels" :key="level.id">
                        <td class="border px-4 py-2">{{ level.id }}</td>
                        <td class="border px-4 py-2">{{ level.name }}</td>
                        <td class="border px-4 py-2">
                            <Link :href="`/year-levels/${level.id}/edit`" class="mr-4 text-blue-600 hover:underline">Edit</Link>
                            <button @click="destroyItem(level.id)" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table> -->
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full text-left text-sm text-gray-500 rtl:text-right dark:text-gray-400">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th scope="col" class="px-6 py-3">Name</th>
                            <th scope="col" class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="level in yearLevels"
                            :key="level.id"
                            class="border-b border-gray-200 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600"
                        >
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ level.id }}</td>
                            <td class="px-6 py-4">{{ level.name }}</td>
                            <td class="flex items-center justify-center space-x-3 px-6 py-4">
                                <Link :href="`/year-levels/${level.id}/edit`" class="font-medium text-blue-600 hover:underline dark:text-blue-500"
                                    >Edit</Link
                                >
                                <button @click="destroyItem(level.id)" class="font-medium text-red-600 hover:underline dark:text-red-500">
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
