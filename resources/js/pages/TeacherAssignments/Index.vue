<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    assignments: Array<{
        id: number;
        teacher: { id: number; name: string };
        year_level: { name: string };
        subject: { name: string };
    }>;
}>();

function deleteAssignment(id: number) {
    if (confirm('Are you sure you want to delete this assignment?')) {
        router.delete(`/teacher-assignments/${id}`);
    }
}
</script>

<template>
    <Head title="Teacher Assignments" />
    <AppLayout>
        <div class="p-6">
            <h1 class="mb-4 text-xl font-bold">Teacher Assignments</h1>

            <Link href="/teacher-assignments/create" class="inline-block rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                Assign Teacher
            </Link>

            <table class="mt-6 w-full table-auto border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-4 py-2 text-left">Teacher</th>
                        <th class="border px-4 py-2 text-left">Year Level</th>
                        <th class="border px-4 py-2 text-left">Subject</th>
                        <th class="border px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="a in assignments" :key="a.id" class="hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ a.teacher.name }}</td>
                        <td class="border px-4 py-2">{{ a.year_level.name }}</td>
                        <td class="border px-4 py-2">{{ a.subject.name }}</td>
                        <td class="space-x-2 border px-4 py-2">
                            <Link :href="`/teacher-assignments/${a.id}`" class="text-green-600 hover:underline"> View </Link>
                            <Link :href="`/teacher-assignments/${a.id}/edit`" class="text-blue-600 hover:underline"> Edit </Link>
                            <button @click="deleteAssignment(a.id)" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
