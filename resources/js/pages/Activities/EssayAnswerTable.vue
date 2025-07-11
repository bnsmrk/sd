<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    activity: { id: number; title: string };
    submissions: {
        data: Array<{
            id: number;
            answer: string;
            score: number | null;
            user: { id: number; name: string };
            question: { question: string };
        }>;
        links: any[];
    };
    filters: { search?: string };
}>();

const search = ref(props.filters.search || '');
let debounceTimer: ReturnType<typeof setTimeout> | null = null;

watch(search, (val) => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(`/activities/${props.activity.id}/essay-answers`, { search: val }, { preserveState: true, replace: true });
    }, 300);
});
</script>

<template>
    <AppLayout>
        <Head :title="`Essay Answers - ${props.activity.title}`" />

        <div class="mx-auto w-full max-w-screen-2xl space-y-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-[#01006c]">📝 Essay Answers – {{ props.activity.title }}</h1>
                <div class="flex gap-2">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search student..."
                        class="rounded border border-[#01006c] px-3 py-2 text-sm focus:ring-[#ffc60b]"
                    />
                    <Link
                        href="/activities"
                        class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm text-[#01006c] hover:bg-[#ffc60b]"
                    >
                        <ArrowLeft class="h-4 w-4 text-[#ff69b4]" /> Back
                    </Link>
                </div>
            </div>

            <div v-if="props.submissions.data.length === 0" class="rounded bg-yellow-100 px-4 py-3 text-yellow-800">
                No essay answers submitted yet.
            </div>

            <div class="overflow-x-auto rounded border border-[#01006c] bg-white">
                <table class="min-w-full table-auto text-sm">
                    <thead class="bg-[#01006c] text-white">
                        <tr>
                            <th class="px-6 py-3 text-left">Student</th>
                            <th class="px-6 py-3 text-left">Question</th>
                            <th class="px-6 py-3 text-left">Answer</th>
                            <th class="px-6 py-3 text-left">Score</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="submission in props.submissions.data" :key="submission.id">
                            <td class="px-6 py-3 font-medium">{{ submission.user.name }}</td>
                            <td class="px-6 py-3">{{ submission.question.question }}</td>
                            <td class="px-6 py-3 whitespace-pre-wrap text-gray-700">{{ submission.answer }}</td>
                            <td class="px-6 py-3">
                                <template v-if="submission.score !== null">
                                    <span class="font-semibold text-green-700">{{ submission.score }}</span>
                                    <Link :href="route('activities.essay.scoring.form', [props.activity.id, submission.user.id])">
                                        Add Essay Score
                                    </Link>
                                </template>

                                <template v-else>
                                    <Link
                                        :href="route('submissions.show', submission.id)"
                                        class="text-sm text-red-600 hover:text-[#ffc60b] hover:underline"
                                    >
                                        Add Score
                                    </Link>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-center gap-2">
                <template v-for="(link, i) in props.submissions.links" :key="i">
                    <span v-if="!link.url" v-html="link.label" class="px-3 py-1 text-gray-400" />
                    <Link
                        v-else
                        :href="link.url"
                        class="rounded px-3 py-1 text-sm"
                        :class="{
                            'bg-[#01006c] text-white': link.active,
                            'text-gray-700 hover:text-[#ffc60b]': !link.active,
                        }"
                    >
                        <span v-html="link.label" />
                    </Link>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
