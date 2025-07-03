<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    activity: { id: number; title: string };
    submissions: {
        id: number;
        content: string | null;
        file_path: string | null;
        score: number | null;
        user: { id: number; name: string };
    }[];
}>();
</script>

<template>
    <AppLayout>
        <Head :title="`Essay Submissions - ${props.activity.title}`" />

        <div class="space-y-4 p-6">
            <h1 class="text-2xl font-bold">Essay Submissions - {{ props.activity.title }}</h1>

            <div v-if="props.submissions.length === 0">No students submitted yet.</div>

            <table class="w-full table-auto border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Student</th>
                        <th class="px-4 py-2 text-left">Graded</th>
                        <th class="px-4 py-2 text-left">Score</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="submission in props.submissions" :key="submission.id" class="border-t">
                        <td class="px-4 py-2 font-semibold">{{ submission.user.name }}</td>

                        <td class="px-4 py-2">
                            <span v-if="submission.score !== null" class="inline-block rounded bg-green-100 px-2 py-1 text-green-700"> Graded </span>
                            <span v-else class="inline-block rounded bg-red-100 px-2 py-1 text-red-700"> Not Graded </span>
                        </td>

                        <td class="px-4 py-2 font-semibold">
                            <span v-if="submission.score !== null">
                                {{ submission.score }}
                            </span>
                            <span v-else>—</span>
                        </td>

                        <td class="px-4 py-2">
                            <Link :href="`/submissions/${submission.id}`" class="text-blue-600 underline"> View </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
