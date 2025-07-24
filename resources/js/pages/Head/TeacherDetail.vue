<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    teacher: { id: number; name: string; email: string };
    assignments: { subject: string; section: string; year_level: string }[];
}>();
</script>

<template>
    <Head title="Teacher Details" />
    <AppLayout :breadcrumbs="[{ title: 'Teacher Assignments', href: '/head-teacher-assignments' }]">
        <div class="min-h-screen space-y-6 bg-pink-50 p-6">
            <Link href="/head-teacher-assignments" class="inline-block rounded bg-pink-600 px-4 py-2 text-white hover:bg-pink-700">
                ← Back to Teacher Assignments
            </Link>

            <h1 class="text-2xl font-bold text-indigo-800">{{ teacher.name }}'s Assignments</h1>
            <p class="text-gray-600">{{ teacher.email }}</p>

            <div class="overflow-x-auto rounded bg-white p-4 shadow">
                <table class="min-w-full table-auto border border-gray-300">
                    <thead class="bg-indigo-100 text-indigo-800">
                        <tr>
                            <th class="px-4 py-2 text-left">Year Level</th>
                            <th class="px-4 py-2 text-left">Section</th>
                            <th class="px-4 py-2 text-left">Subject</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, i) in assignments" :key="i" class="border-t">
                            <td class="px-4 py-2">{{ item.year_level }}</td>
                            <td class="px-4 py-2">{{ item.section }}</td>
                            <td class="px-4 py-2">{{ item.subject }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
