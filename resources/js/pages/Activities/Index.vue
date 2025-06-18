<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    activities: {
        id: number;
        title: string;
        type: string;
        scheduled_at: string;
        year_level: string;
        section: string;
        subject: string;
    }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Activities', href: '/activities' }];

function deleteActivity(id: number) {
    if (confirm('Are you sure you want to delete this activity?')) {
        router.delete(`/activities/${id}`);
    }
}
</script>

<template>
    <Head title="Activities" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">Activity List</h1>
                <Link href="/activities/create" class="rounded bg-blue-600 px-4 py-2 text-white">Create Activity</Link>
            </div>

            <!-- <div class="overflow-auto rounded border">
                <table class="min-w-full divide-y">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Title</th>
                            <th class="p-2 text-left">Type</th>
                            <th class="p-2 text-left">Date</th>
                            <th class="p-2 text-left">Year</th>
                            <th class="p-2 text-left">Section</th>
                            <th class="p-2 text-left">Subject</th>
                            <th class="p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="a in props.activities" :key="a.id" class="border-t">
                            <td class="p-2">{{ a.title }}</td>
                            <td class="p-2">{{ a.type }}</td>
                            <td class="p-2">{{ a.scheduled_at }}</td>
                            <td class="p-2">{{ a.year_level }}</td>
                            <td class="p-2">{{ a.section }}</td>
                            <td class="p-2">{{ a.subject }}</td>
                            <td class="space-x-2 p-2 text-center">
                                <Link :href="`/activities/${a.id}/edit`" class="text-blue-600 hover:underline">Edit</Link>
                                <button @click="deleteActivity(a.id)" class="text-red-600 hover:underline">Delete</button>
                                <Link :href="`/activities/${a.id}/questions/create`" class="text-indigo-600 hover:underline">Add Question</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div> -->

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full text-left text-sm text-gray-500 rtl:text-right dark:text-gray-400">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Title</th>
                            <th scope="col" class="px-6 py-3">Type</th>
                            <th scope="col" class="px-6 py-3">Date</th>
                            <!-- <th scope="col" class="px-6 py-3">Year</th>
                            <th scope="col" class="px-6 py-3">Section</th>
                            <th scope="col" class="px-6 py-3">Subject</th> -->
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="a in props.activities"
                            :key="a.id"
                            class="border-b border-gray-200 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600"
                        >
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ a.title }}</td>
                            <td class="px-6 py-4">{{ a.type }}</td>
                            <td class="px-6 py-4">{{ a.scheduled_at }}</td>
                            <!-- <td class="px-6 py-4">{{ a.year_level }}</td>
                            <td class="px-6 py-4">{{ a.section }}</td>
                            <td class="px-6 py-4">{{ a.subject }}</td> -->
                            <td class="flex items-center space-x-3 px-6 py-4">
                                <Link :href="`/activities/${a.id}/edit`" class="font-medium text-blue-600 hover:underline dark:text-blue-500"
                                    >Edit</Link
                                >
                                <button @click="deleteActivity(a.id)" class="font-medium text-red-600 hover:underline dark:text-red-500">
                                    Delete
                                </button>
                                <Link
                                    :href="`/activities/${a.id}/questions/create`"
                                    class="font-medium text-indigo-600 hover:underline dark:text-indigo-500"
                                    >Add Question</Link
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
