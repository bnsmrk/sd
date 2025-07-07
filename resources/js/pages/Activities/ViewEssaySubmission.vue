<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, Download, FileText, Medal, XCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    submission: {
        id: number;
        content: string | null;
        file_path: string | null;
        score: number | null;
        graded: string;
        user: { id: number; name: string };
        activity: { id: number; title: string };
    };
}>();

const score = ref<number | null>(props.submission.score ?? null);

function saveScore() {
    if (score.value !== null) {
        router.post(`/submissions/${props.submission.id}/score`, { score: score.value }, { preserveScroll: true });
    }
}
</script>

<template>
    <AppLayout>
        <Head :title="`View Essay - ${props.submission.user.name}`" />

        <div class="mx-auto w-full max-w-screen-2xl space-y-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-800">
                    Essay by {{ props.submission.user.name }} – {{ props.submission.activity.title }}
                </h1>

                <a href="/activities" class="inline-flex items-center gap-2 text-sm text-gray-600 transition hover:text-blue-600 hover:underline">
                    <ArrowLeft class="h-4 w-4" /> Back to Activities
                </a>
            </div>

            <div class="flex items-center gap-2 text-sm text-gray-600">
                Status:
                <span
                    :class="
                        submission.graded === 'Graded'
                            ? 'inline-flex items-center gap-1 text-green-600'
                            : 'inline-flex items-center gap-1 text-red-600'
                    "
                >
                    <component :is="submission.graded === 'Graded' ? CheckCircle2 : XCircle" class="h-4 w-4" />
                    {{ submission.graded }}
                </span>
            </div>

            <div v-if="submission.content" class="rounded bg-gray-50 p-4 whitespace-pre-wrap text-gray-800 shadow-sm">
                {{ submission.content }}
            </div>
            <div v-else class="text-gray-500 italic">No essay content submitted.</div>

            <div v-if="submission.file_path">
                <a :href="`/storage/${submission.file_path}`" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:underline">
                    <Download class="h-4 w-4" /> Download Attached File
                </a>
            </div>

            <div class="mt-6">
                <label class="mb-1 flex items-center gap-1 text-sm font-medium text-gray-700"> <Medal class="h-4 w-4" /> Score </label>
                <input v-model.number="score" type="number" min="0" max="100" class="w-32 rounded border px-3 py-2 text-sm" placeholder="0–100" />

                <button
                    @click="saveScore"
                    class="ml-3 inline-flex items-center gap-1 rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
                >
                    <FileText class="h-4 w-4" /> Save Score
                </button>
            </div>
        </div>
    </AppLayout>
</template>
