<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    enrollments: Array<{
        id: number;
        user: { name: string };
        year_level: { name: string };
        section: { name: string };
        subject: { name: string };
    }>;
}>();

const breadcrumbs = [{ title: 'Enrollments', href: '/enroll' }];

const destroyItem = (id: number) => {
    if (confirm('Are you sure you want to delete this enrollment?')) {
        router.delete(`/enroll/${id}`);
    }
};
</script>

<template>
    <Head title="Enrollments" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">Enrollments</h2>
                <Link href="/enroll/create" class="rounded bg-blue-600 px-4 py-2 text-white">Add Enrollment</Link>
            </div>

            <table class="w-full table-auto border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">Student</th>
                        <th class="border px-4 py-2">Year Level</th>
                        <th class="border px-4 py-2">Section</th>
                        <th class="border px-4 py-2">Subject</th>
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="enroll in enrollments" :key="enroll.id">
                        <td class="border px-4 py-2">{{ enroll.user.name }}</td>
                        <td class="border px-4 py-2">{{ enroll.year_level.name }}</td>
                        <td class="border px-4 py-2">{{ enroll.section.name }}</td>
                        <td class="border px-4 py-2">{{ enroll.subject.name }}</td>
                        <td class="border px-4 py-2">
                            <Link :href="`/enroll/${enroll.id}/edit`" class="mr-4 text-blue-600 hover:underline">Edit</Link>

                            <button @click="destroyItem(enroll.id)" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
