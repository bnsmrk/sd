<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    subjects: Array<{
        id: number;
        name: string;
        year_level: { name: string };
        section: { name: string };
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
                <Link href="/subjects/create" class="rounded bg-blue-600 px-4 py-2 text-white">Add Subject</Link>
            </div>
            <table class="w-full table-auto border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">Name</th>
                        <th class="border px-4 py-2">Year Level</th>
                        <th class="border px-4 py-2">Section</th>
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="subject in subjects" :key="subject.id">
                        <td class="border px-4 py-2">{{ subject.name }}</td>
                        <td class="border px-4 py-2">{{ subject.year_level.name }}</td>
                        <td class="border px-4 py-2">{{ subject.section.name }}</td>
                        <td class="border px-4 py-2">
                            <Link :href="`/subjects/${subject.id}/edit`" class="mr-2 text-blue-600">Edit</Link>
                            <button @click="destroyItem(subject.id)" class="text-red-600">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
