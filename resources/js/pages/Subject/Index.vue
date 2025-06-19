<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    subjects: Array<{
        id: number;
        name: string;
        year_level?: { name: string } | null;
        section?: { name: string } | null;
    }>;
}>();

const destroyItem = (id: number) => {
    if (confirm('Are you sure?')) {
        router.delete(`/subjects/${id}`);
    }
};
</script>

<template>
    <Head title="Subjects" />
    <AppLayout>
        <div class="p-4">
            <div class="mb-4 flex justify-between">
                <h2 class="text-xl font-bold">Subjects</h2>
                <Link href="/subjects/create" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Add Subject</Link>
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full text-left text-sm text-gray-700">
                    <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3">Name</th>
                            <th scope="col" class="px-6 py-3">Year Level</th>
                            <!-- <th scope="col" class="px-6 py-3">Section</th> -->
                            <th scope="col" class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="subject in subjects" :key="subject.id" class="border-b bg-white hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ subject.name }}</td>
                            <td class="px-6 py-4">{{ subject.year_level?.name ?? '—' }}</td>
                            <!-- <td class="px-6 py-4">{{ subject.section?.name ?? '—' }}</td> -->
                            <td class="space-x-3 px-6 py-4 text-center">
                                <Link :href="`/subjects/${subject.id}/edit`" class="text-blue-600 hover:underline">Edit</Link>
                                <button @click="destroyItem(subject.id)" class="text-red-600 hover:underline">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="subjects.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No subjects found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
