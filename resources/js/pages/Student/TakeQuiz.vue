<script setup lang="ts">
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';
// import AppLayout from '@/layouts/AppLayout.vue';

import { router } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, Check, Eye } from 'lucide-vue-next';
import { computed, nextTick, reactive, ref, watch, watchEffect } from 'vue';

const props = defineProps<{
    quiz: {
        id: number;
        title: string;
        type: string;
        scheduled_at: string;
        questions: Array<{
            id: number;
            question: string;
            type: string;
            options?: string;
            answer_key?: string;
        }>;
    };
}>();

const answers = reactive<Record<number, string | string[]>>({});
const previewMode = ref(false);
const currentPage = ref(1);
const questionsPerPage = 5;
const selectedQuestionId = ref<number | null>(null);

watchEffect(() => {
    props.quiz.questions.forEach((q) => {
        if (q.type === 'checkboxes' && !Array.isArray(answers[q.id])) {
            answers[q.id] = [];
        }
    });
});

function previewAnswers() {
    previewMode.value = true;
}

function submitAnswers() {
    router.post('/quizzes/submit', {
        quiz_id: props.quiz.id,
        answers,
    });
}

function formatAnswer(value: any): string {
    if (Array.isArray(value)) return value.join(', ');
    return value || 'No answer';
}

function isAnswered(id: number): boolean {
    const a = answers[id];
    return Array.isArray(a) ? a.length > 0 : !!a;
}

function scrollToQuestion(id: number) {
    nextTick(() => {
        const el = document.getElementById(`question-${id}`);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            console.error(`Element with id 'question-${id}' not found`);
        }
    });
}

function getPageForQuestion(id: number): number {
    return Math.ceil((props.quiz.questions.findIndex((q) => q.id === id) + 1) / questionsPerPage);
}

const totalPages = computed(() => Math.ceil(props.quiz.questions.length / questionsPerPage));
const paginatedQuestions = computed(() => {
    const start = (currentPage.value - 1) * questionsPerPage;
    return props.quiz.questions.slice(start, start + questionsPerPage);
});

const groupedButtons = computed(() => {
    const groups: number[][] = [];
    const total = props.quiz.questions.length;
    for (let i = 0; i < total; i += questionsPerPage) {
        const group = props.quiz.questions.slice(i, i + questionsPerPage).map((q) => q.id);
        groups.push(group);
    }
    return groups;
});

watch(selectedQuestionId, (id) => {
    if (id !== null) {
        scrollToQuestion(id);
        const page = getPageForQuestion(id);
        if (page !== currentPage.value) {
            currentPage.value = page;
        }
    }
});
</script>

<template>
    <AppLayoutStudent>
        <div class="min-h-screen space-y-6 bg-pink-50 px-6 py-8">
            <div class="mb-6">
                <h1 class="mb-2 text-3xl font-bold text-primary">{{ quiz.title }}</h1>
                <p class="text-sm text-gray-600">
                    Type: <span class="font-semibold">{{ quiz.type }}</span> | Scheduled: {{ quiz.scheduled_at }}
                </p>
            </div>

            <div class="flex flex-col gap-8 md:flex-row">
                <aside v-if="!previewMode" class="w-full md:sticky md:top-20 md:ml-6 md:max-w-[200px]">
                    <div class="rounded-lg border bg-white p-3 shadow-sm">
                        <h3 class="mb-4 text-center text-sm font-semibold text-gray-700">Question Navigator</h3>

                        <div class="overflow-x-auto md:overflow-visible">
                            <div class="space-y-3">
                                <div v-for="(group, gIndex) in groupedButtons" :key="gIndex" class="grid grid-cols-5 justify-items-center gap-2">
                                    <label
                                        v-for="(qid, index) in group"
                                        :key="qid"
                                        class="flex h-7 w-7 cursor-pointer items-center justify-center rounded-full text-xs font-semibold text-white transition-colors"
                                        :class="{
                                            'bg-green-600 hover:bg-green-700': isAnswered(qid),
                                            'bg-red-500 hover:bg-red-600': !isAnswered(qid),
                                        }"
                                    >
                                        <input type="radio" v-model="selectedQuestionId" :value="qid" class="hidden" />
                                        {{ gIndex * questionsPerPage + index + 1 }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <section class="flex-1">
                    <form v-if="!previewMode" @submit.prevent="previewAnswers">
                        <div
                            v-for="(q, index) in paginatedQuestions"
                            :key="q.id"
                            :id="`question-${q.id}`"
                            class="mb-6 space-y-6 rounded-lg border bg-white p-5 shadow-md"
                        >
                            <p class="text-xl font-semibold text-gray-800">
                                {{ (currentPage - 1) * questionsPerPage + index + 1 }}. {{ q.question }}
                            </p>

                            <div v-if="q.type === 'multiple_choice'" class="space-y-3">
                                <div v-for="(option, index) in JSON.parse(q.options || '[]')" :key="index" class="flex items-center space-x-4">
                                    <input
                                        type="radio"
                                        :name="'q-' + q.id"
                                        :id="`q-${q.id}-option-${index}`"
                                        :value="option"
                                        class="accent-blue-600"
                                        v-model="answers[q.id]"
                                    />
                                    <label :for="`q-${q.id}-option-${index}`" class="text-lg text-gray-700">{{ option }}</label>
                                </div>
                            </div>

                            <div v-else-if="q.type === 'checkboxes'" class="space-y-3">
                                <div v-for="(option, index) in JSON.parse(q.options || '[]')" :key="index" class="flex items-center space-x-4">
                                    <input
                                        type="checkbox"
                                        :id="`q-${q.id}-check-${index}`"
                                        :value="option"
                                        class="accent-blue-600"
                                        :checked="Array.isArray(answers[q.id]) && answers[q.id].includes(option)"
                                        @change="
                                            (e) => {
                                                const isChecked = (e.target as HTMLInputElement).checked;
                                                const current = answers[q.id] as string[];
                                                if (isChecked) {
                                                    current.push(option);
                                                } else {
                                                    answers[q.id] = current.filter((o) => o !== option);
                                                }
                                            }
                                        "
                                    />
                                    <label :for="`q-${q.id}-check-${index}`" class="text-lg text-gray-700">{{ option }}</label>
                                </div>
                            </div>

                            <div v-else-if="q.type === 'true_false'" class="flex space-x-8">
                                <label class="flex items-center space-x-2">
                                    <input type="radio" :name="'q-' + q.id" value="true" class="accent-green-600" v-model="answers[q.id]" />
                                    <span class="text-lg text-gray-700">True</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="radio" :name="'q-' + q.id" value="false" class="accent-red-600" v-model="answers[q.id]" />
                                    <span class="text-lg text-gray-700">False</span>
                                </label>
                            </div>

                            <div v-else-if="q.type === 'essay'">
                                <textarea
                                    rows="4"
                                    class="w-full rounded-lg border border-gray-300 p-4 text-sm text-gray-700 focus:ring-2 focus:ring-blue-200"
                                    v-model="answers[q.id]"
                                    placeholder="Write your answer here..."
                                ></textarea>
                            </div>

                            <div v-else-if="q.type === 'fill_in_blank'">
                                <input
                                    type="text"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-700 focus:ring-2 focus:ring-blue-200"
                                    placeholder="Type your answer..."
                                    v-model="answers[q.id]"
                                />
                            </div>

                            <div v-else class="text-sm text-red-600">Unknown question type: {{ q.type }}</div>
                        </div>

                        <div class="mt-4 flex justify-between">
                            <button
                                v-if="currentPage > 1"
                                class="inline-flex items-center gap-2 rounded bg-gray-300 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-400"
                                @click.prevent="currentPage--"
                            >
                                <ArrowLeft class="h-4 w-4" /> Previous
                            </button>
                            <button
                                v-if="currentPage < totalPages"
                                class="inline-flex items-center gap-2 rounded bg-blue-500 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-600"
                                @click.prevent="currentPage++"
                            >
                                Next <ArrowRight class="h-4 w-4" />
                            </button>
                        </div>

                        <div class="mt-8 text-right">
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-500 px-8 py-3 text-lg text-white shadow-md hover:bg-yellow-600"
                            >
                                <Eye class="h-5 w-5" /> Preview Answers
                            </button>
                        </div>
                    </form>

                    <div v-else class="space-y-6 border-t pt-6">
                        <h2 class="text-2xl font-semibold text-gray-700">Review Your Answers</h2>

                        <div v-for="q in quiz.questions" :key="q.id" class="rounded border bg-gray-50 p-4 shadow-sm">
                            <p class="mb-1 font-medium text-gray-700">Q: {{ q.question }}</p>
                            <p class="text-gray-600">
                                Answer: <span class="font-semibold">{{ formatAnswer(answers[q.id]) }}</span>
                            </p>
                        </div>

                        <div class="mt-6 flex justify-end space-x-4">
                            <button
                                class="inline-flex items-center gap-2 rounded bg-gray-500 px-6 py-2 text-white hover:bg-gray-600"
                                @click="previewMode = false"
                            >
                                <ArrowLeft class="h-4 w-4" /> Back
                            </button>
                            <button
                                class="inline-flex items-center gap-2 rounded bg-green-600 px-6 py-2 text-white hover:bg-green-700"
                                @click="submitAnswers"
                            >
                                <Check class="h-4 w-4" /> Confirm & Submit
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AppLayoutStudent>
</template>
