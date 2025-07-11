<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    activity: { id: number; title: string };
    answers: Array<{
        id: number;
        question: string;
        student: string;
        type: string;
        answer: string;
        score: number;
    }>;
}>();

const scores = ref<{ [id: number]: number }>({});

props.answers.forEach((answer) => {
    scores.value[answer.id] = answer.score ?? 0;
});

const essayAnswers = computed(() => props.answers.filter((a) => a.type === 'essay'));

function submitScores() {
    router.post(`/activities/${props.activity.id}/essay-scores`, {
        scores: scores.value,
    });
}
</script>

<template>
    <AppLayout>
        <div class="mx-auto w-full max-w-screen-xl space-y-6 px-6 py-8">
            <div class="flex items-center justify-between">
                <h1 class="mb-6 text-2xl font-bold text-[#01006c]">📝 Score Essay Answers – {{ activity.title }}</h1>
                <Link
                    :href="route('activities.essay.answers', activity.id)"
                    class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
                >
                    <ArrowLeft class="h-4 w-4 text-[#ff69b4]" /> Back
                </Link>
            </div>
            <div v-if="essayAnswers.length === 0" class="text-center text-gray-500">No essay submissions yet.</div>

            <div v-else class="space-y-6">
                <div
                    v-for="(answer, index) in essayAnswers"
                    :key="answer.id"
                    class="rounded-lg border border-gray-300 bg-white p-6 shadow-md"
                    :class="index % 2 === 0 ? 'bg-gray-50' : 'bg-white'"
                >
                    <div class="mb-2 text-sm text-gray-600">#{{ index + 1 }}</div>
                    <div class="mb-2">
                        <p><span class="font-semibold text-gray-700">👨‍🎓 Student:</span> {{ answer.student }}</p>
                        <p><span class="font-semibold text-gray-700">📘 Question:</span> {{ answer.question }}</p>
                        <p><span class="font-semibold text-gray-700">🧾 Type:</span> {{ answer.type }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="mb-1 font-semibold text-gray-700">✍️ Answer:</p>
                        <div class="rounded bg-gray-100 p-3 text-sm whitespace-pre-wrap text-gray-800">
                            {{ answer.answer }}
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="font-semibold text-gray-700" for="score">Score (0–10):</label>
                        <input
                            v-model.number="scores[answer.id]"
                            type="number"
                            min="0"
                            max="10"
                            class="w-24 rounded border border-gray-300 px-3 py-1 shadow-sm focus:border-[#ffc60b] focus:ring-[#ffc60b]"
                        />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        @click="submitScores"
                        class="rounded-lg bg-[#01006c] px-6 py-2 font-medium text-white shadow-md transition hover:bg-[#000055]"
                    >
                        💾 Save All Scores
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
