<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, Eye, XCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    activity: { id: number; title: string };
    submissions: {
        data: {
            id: number;
            content: string | null;
            file_path: string | null;
            score: number | null;
            user: { id: number; name: string };
        }[];
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
    };
    filters: {
        search?: string;
    };
}>();

const search = ref(props.filters.search || '');
let debounceTimer: ReturnType<typeof setTimeout> | null = null;

watch(search, (value) => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(
            `/activities/${props.activity.id}/essay-submissions`,
            {
                search: value,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 300);
});
</script>

<template>
    <AppLayout>
        <Head :title="`Essay Submissions - ${props.activity.title}`" />

        <div class="mx-auto w-full max-w-screen-2xl space-y-6 p-6">
            <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Essay Submissions – {{ props.activity.title }}</h1>

                <div class="flex gap-2">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search student..."
                        class="rounded border border-gray-300 px-3 py-2 text-sm shadow-sm"
                    />
                    <Link
                        href="/activities"
                        class="inline-flex items-center gap-2 text-sm text-gray-600 transition hover:text-blue-600 hover:underline"
                    >
                        <ArrowLeft class="h-4 w-4" /> Back to Activities
                    </Link>
                </div>
            </div>

            <div v-if="props.submissions.data.length === 0" class="rounded-lg bg-yellow-50 px-4 py-3 text-yellow-700 shadow-sm">
                No students have submitted yet.
            </div>

            <div v-else class="overflow-x-auto rounded-lg border bg-white shadow-sm">
                <table class="min-w-full divide-y text-sm text-gray-700">
                    <thead class="bg-gray-100 text-left text-sm font-semibold">
                        <tr>
                            <th class="px-6 py-3">Student</th>
                            <th class="px-6 py-3">Graded</th>
                            <th class="px-6 py-3">Score</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="submission in props.submissions.data" :key="submission.id" class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium">{{ submission.user.name }}</td>

                            <td class="px-6 py-3">
                                <span
                                    v-if="submission.score !== null"
                                    class="inline-flex items-center gap-1 rounded bg-green-100 px-2 py-1 text-green-700"
                                >
                                    <CheckCircle2 class="h-4 w-4" /> Graded
                                </span>
                                <span v-else class="inline-flex items-center gap-1 rounded bg-red-100 px-2 py-1 text-red-700">
                                    <XCircle class="h-4 w-4" /> Not Graded
                                </span>
                            </td>

                            <td class="px-6 py-3 font-semibold">
                                <span v-if="submission.score !== null">{{ submission.score }}</span>
                                <span v-else>—</span>
                            </td>

                            <td class="px-6 py-3">
                                <Link :href="`/submissions/${submission.id}`" class="inline-flex items-center gap-1 text-blue-600 hover:underline">
                                    <Eye class="h-4 w-4" /> View
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-center gap-2">
                <template v-for="(link, i) in props.submissions.links" :key="i">
                    <span v-if="!link.url" class="px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    <Link
                        v-else
                        :href="link.url"
                        class="rounded px-3 py-1 text-sm"
                        :class="{
                            'bg-blue-600 text-white': link.active,
                            'text-gray-700 hover:underline': !link.active,
                        }"
                    >
                        <span v-html="link.label" />
                    </Link>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
