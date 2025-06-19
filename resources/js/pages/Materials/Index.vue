<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    materials: Array<{
        id: number;
        title: string;
        file_path: string;
        type: string;
        year_level: { name: string };
        section: { name: string } | null;
        subject: { name: string };
        user: { name: string };
    }>;
}>();
</script>

<template>
    <Head title="Learning Materials" />
    <AppLayout>
        <div class="p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-2xl font-bold">Learning Materials</h2>
                <Link href="/materials/create" class="rounded bg-blue-600 px-4 py-2 text-white">Upload</Link>
            </div>

            <div class="overflow-x-auto rounded bg-white shadow">
                <table class="min-w-full text-sm text-gray-700">
                    <thead class="bg-gray-100 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-2">Title</th>
                            <th class="px-4 py-2">Type</th>
                            <th class="px-4 py-2">Year Level</th>

                            <th class="px-4 py-2">Subject</th>
                            <th class="px-4 py-2">Uploaded By</th>
                            <th class="px-4 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="m in materials" :key="m.id" class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ m.title }}</td>
                            <td class="px-4 py-2 capitalize">{{ m.type.replace('_', ' ') }}</td>
                            <td class="px-4 py-2">{{ m.year_level.name }}</td>

                            <td class="px-4 py-2">{{ m.subject.name }}</td>
                            <td class="px-4 py-2">{{ m.user.name }}</td>
                            <td class="px-4 py-2 text-center">
                                <a :href="`/storage/${m.file_path}`" target="_blank" class="text-blue-600 hover:underline"> View </a>
                                <Link :href="`/materials/${m.id}/edit`" class="ml-2 text-green-600 hover:underline"> Edit </Link>
                                <Link
                                    :href="`/materials/${m.id}`"
                                    method="delete"
                                    as="button"
                                    class="ml-2 text-red-600 hover:underline"
                                    onclick="return confirm('Are you sure you want to delete this material?')"
                                >
                                    Delete
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="materials.length === 0">
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">No materials found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
