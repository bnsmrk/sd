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
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-[#01006c]">
                    Essay by {{ props.submission.user.name }} – {{ props.submission.activity.title }}
                </h1>

                <a
                    href="/activities"
                    class="inline-flex items-center gap-2 rounded-md border border-[#01006c] px-4 py-2 text-sm font-semibold text-[#01006c] transition hover:bg-[#ffc60b]"
                >
                    <ArrowLeft class="h-4 w-4 text-[#ff69b4]" /> Back to Activities
                </a>
            </div>

            <!-- Status -->
            <div class="flex items-center gap-2 text-sm font-medium text-gray-700">
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

            <!-- Essay Content -->
            <div v-if="submission.content" class="rounded bg-gray-50 p-4 whitespace-pre-wrap text-gray-800 shadow-sm">
                {{ submission.content }}
            </div>
            <div v-else class="text-gray-500 italic">No essay content submitted.</div>

            <!-- Attached File -->
            <div v-if="submission.file_path">
                <a
                    :href="`/storage/${submission.file_path}`"
                    target="_blank"
                    class="inline-flex items-center gap-1 text-[#01006c] transition hover:text-[#ffc60b] hover:underline"
                >
                    <Download class="h-4 w-4 text-[#ff69b4]" /> Download Attached File
                </a>
            </div>

            <!-- Score Section -->
            <div class="mt-6 w-full max-w-md rounded-lg border p-4 shadow-sm">
                <label class="mb-2 flex items-center gap-1 text-sm font-medium text-[#ff69b4]">
                    <Medal class="h-4 w-4 text-[#ff69b4]" /> Score
                </label>
                <div class="flex flex-col gap-3 md:flex-row md:items-center">
                    <input
                        v-model.number="score"
                        type="number"
                        min="0"
                        max="100"
                        placeholder="0–100"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-[#01006c] shadow-sm focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none md:w-32"
                    />

                    <button
                        @click="saveScore"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#01006c] px-5 py-2 text-sm font-semibold text-white shadow transition hover:bg-[#ffc60b] hover:text-[#01006c] md:w-auto"
                    >
                        <FileText class="h-4 w-4 text-[#ff69b4]" /> Save Score
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
