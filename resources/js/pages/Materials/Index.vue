<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    materials: Array<{
        id: number;
        title: string;
        file_path: string;
        year_level: { name: string };
        section: { name: string };
        subject: { name: string };
        user: { name: string };
    }>;
}>();
</script>

<template>
    <Head title="Learning Materials" />
    <AppLayout>
        <!-- <div v-if="$page.props.flash.success" class="mb-4 text-green-600">
            {{ $page.props.flash.success }}
        </div> -->
        <div class="p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-2xl font-bold">Learning Materials</h2>
                <Link href="/materials/create" class="rounded bg-blue-600 px-4 py-2 text-white">Upload Material</Link>
            </div>

            <!-- <table class="mt-4 w-full border">
                    <thead class="bg-gray-100 text-left">
                        <tr>
                            <th class="p-2">Title</th>
                            <th class="p-2">Year Level</th>
                            <th class="p-2">Section</th>
                            <th class="p-2">Subject</th>
                            <th class="p-2">Uploaded By</th>
                            <th class="p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="m in materials" :key="m.id" class="border-t">
                            <td class="p-2">{{ m.title }}</td>
                            <td class="p-2">{{ m.year_level.name }}</td>
                            <td class="p-2">{{ m.section.name }}</td>
                            <td class="p-2">{{ m.subject.name }}</td>
                            <td class="p-2">{{ m.user.name }}</td>
                            <td class="space-x-2 p-2">
                                <a :href="`/storage/${m.file_path}`" target="_blank" class="text-blue-600">View</a>
                                <Link :href="`/materials/${m.id}/edit`" class="text-green-600">Edit</Link>
                                <Link
                                    :href="`/materials/${m.id}`"
                                    method="delete"
                                    as="button"
                                    class="text-red-600"
                                    onclick="return confirm('Are you sure you want to delete this material?')"
                                >
                                    Delete
                                </Link>
                            </td>
                        </tr>
                    </tbody>
            </table> -->

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-left text-sm text-gray-500 rtl:text-right dark:text-gray-400">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Title</th>
                            <th scope="col" class="px-6 py-3">Year Level</th>
                            <th scope="col" class="px-6 py-3">Section</th>
                            <th scope="col" class="px-6 py-3">Subject</th>
                            <th scope="col" class="px-6 py-3">Uploaded By</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="m in materials"
                            :key="m.id"
                            class="border-b border-gray-200 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600"
                        >
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ m.title }}</td>
                            <td class="px-6 py-4">{{ m.year_level.name }}</td>
                            <td class="px-6 py-4">{{ m.section.name }}</td>
                            <td class="px-6 py-4">{{ m.subject.name }}</td>
                            <td class="px-6 py-4">{{ m.user.name }}</td>
                            <td class="flex items-center space-x-2 px-6 py-4">
                                <a
                                    :href="`/storage/${m.file_path}`"
                                    target="_blank"
                                    class="font-medium text-blue-600 hover:underline dark:text-blue-500"
                                    >View</a
                                >
                                <Link :href="`/materials/${m.id}/edit`" class="font-medium text-green-600 hover:underline dark:text-green-500"
                                    >Edit</Link
                                >
                                <Link
                                    :href="`/materials/${m.id}`"
                                    method="delete"
                                    as="button"
                                    class="font-medium text-red-600 hover:underline dark:text-red-500"
                                    onclick="return confirm('Are you sure you want to delete this material?')"
                                >
                                    Delete
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
