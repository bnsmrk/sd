<script setup lang="ts">
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';
import { router } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, BookOpen, Calendar, Check, Eye, FileText } from 'lucide-vue-next';
import { computed, nextTick, reactive, ref, watch, watchEffect } from 'vue';
const props = defineProps<{
    test: {
        id: number;
        title: string;
        type: string;
        scheduled_at: string;
        questions: {
            id: number;
            question: string;
            type: string;
            options?: string | string[] | null;
        }[];
    };
}>();

const answers = reactive<Record<number, string | string[]>>({});
const previewMode = ref(false);
const currentPage = ref(1);
const questionsPerPage = 5;

function getOptions(optionData: string | string[] | null | undefined): string[] {
    if (Array.isArray(optionData)) return optionData;
    if (typeof optionData === 'string') {
        try {
            return JSON.parse(optionData);
        } catch {
            return [];
        }
    }
    return [];
}

watchEffect(() => {
    props.test.questions.forEach((q) => {
        if (q.type === 'checkboxes' && !Array.isArray(answers[q.id])) {
            answers[q.id] = [];
        }
    });
});

function previewAnswers() {
    previewMode.value = true;
}

function submitAnswers() {
    router.post(`/student/proficiency-test/${props.test.id}/submit`, {
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
            console.log(`Scrolling to question-${id}`);
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            console.error(`Element with id 'question-${id}' not found`);
        }
    });
}

function getPageForQuestion(id: number): number {
    return Math.ceil((props.test.questions.findIndex((q) => q.id === id) + 1) / questionsPerPage);
}

const totalPages = computed(() => Math.ceil(props.test.questions.length / questionsPerPage));
const paginatedQuestions = computed(() => {
    const start = (currentPage.value - 1) * questionsPerPage;
    return props.test.questions.slice(start, start + questionsPerPage);
});
const groupedButtons = computed(() => {
    const groups: number[][] = [];
    const total = props.test.questions.length;
    for (let i = 0; i < total; i += questionsPerPage) {
        const group = props.test.questions.slice(i, i + questionsPerPage).map((q) => q.id);
        groups.push(group);
    }
    return groups;
});

const selectedQuestionId = ref<number | null>(null);

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
        <div class="container mx-auto min-h-screen max-w-6xl bg-gradient-to-b from-pink-50 via-white to-blue-50 px-6 py-8">
            <div class="mb-6">
                <h1 class="mb-1 flex items-center gap-2 text-3xl font-bold text-indigo-900"><BookOpen class="h-6 w-6" /> {{ test.title }}</h1>
                <p class="flex items-center gap-4 text-sm text-gray-600">
                    <span class="flex items-center gap-1">
                        <FileText class="h-4 w-4 text-indigo-600" /> <span class="font-semibold capitalize">{{ test.type }}</span>
                    </span>
                    <span class="flex items-center gap-1"> <Calendar class="h-4 w-4 text-indigo-600" /> {{ test.scheduled_at }} </span>
                </p>
            </div>

            <div class="flex flex-col gap-8 md:flex-row">
                <aside v-if="!previewMode" class="sticky top-20 w-full md:max-w-[220px]">
                    <div class="rounded-xl border bg-white p-4 shadow-md">
                        <h3 class="mb-4 text-center text-sm font-bold text-gray-700">🔢 Question Navigator</h3>
                        <div class="space-y-3">
                            <div v-for="(group, gIndex) in groupedButtons" :key="gIndex" class="grid grid-cols-5 justify-items-center gap-2">
                                <label
                                    v-for="(qid, index) in group"
                                    :key="qid"
                                    class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full text-xs font-semibold transition-all duration-300"
                                    :class="{
                                        'bg-green-500 text-white hover:bg-green-600': isAnswered(qid),
                                        'bg-red-400 text-white hover:bg-red-500': !isAnswered(qid),
                                    }"
                                >
                                    <input type="radio" v-model="selectedQuestionId" :value="qid" class="hidden" />
                                    {{ gIndex * questionsPerPage + index + 1 }}
                                </label>
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
                            class="mb-6 space-y-6 rounded-xl border bg-white p-6 shadow"
                        >
                            <p class="text-lg font-semibold text-indigo-900">
                                {{ (currentPage - 1) * questionsPerPage + index + 1 }}. {{ q.question }}
                            </p>

                            <div v-if="q.type === 'multiple_choice'" class="space-y-3">
                                <div v-for="(option, idx) in getOptions(q.options)" :key="idx" class="flex items-center space-x-3">
                                    <input
                                        type="radio"
                                        :id="`q-${q.id}-opt-${idx}`"
                                        :name="'q-' + q.id"
                                        class="accent-blue-600"
                                        :value="option"
                                        v-model="answers[q.id]"
                                    />
                                    <label :for="`q-${q.id}-opt-${idx}`" class="text-gray-700">{{ option }}</label>
                                </div>
                            </div>

                            <div v-else-if="q.type === 'checkboxes'" class="space-y-3">
                                <div v-for="(option, idx) in getOptions(q.options)" :key="idx" class="flex items-center space-x-3">
                                    <input
                                        type="checkbox"
                                        class="accent-pink-600"
                                        :id="`q-${q.id}-cb-${idx}`"
                                        :value="option"
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
                                    <label :for="`q-${q.id}-cb-${idx}`" class="text-gray-700">{{ option }}</label>
                                </div>
                            </div>

                            <div v-else-if="q.type === 'true_false'" class="space-x-6">
                                <label class="inline-flex items-center space-x-2">
                                    <input type="radio" :name="'q-' + q.id" value="true" class="accent-green-600" v-model="answers[q.id]" />
                                    <span>True</span>
                                </label>
                                <label class="inline-flex items-center space-x-2">
                                    <input type="radio" :name="'q-' + q.id" value="false" class="accent-red-600" v-model="answers[q.id]" />
                                    <span>False</span>
                                </label>
                            </div>

                            <div v-else-if="q.type === 'essay'">
                                <textarea
                                    rows="4"
                                    class="w-full rounded-lg border border-gray-300 p-4 text-sm text-gray-700 focus:ring-2 focus:ring-pink-300"
                                    v-model="answers[q.id]"
                                    placeholder="Write your answer here..."
                                ></textarea>
                            </div>

                            <div v-else-if="q.type === 'fill_in_blank'">
                                <input
                                    type="text"
                                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-700 focus:ring-2 focus:ring-yellow-300"
                                    v-model="answers[q.id]"
                                    placeholder="Type your answer..."
                                />
                            </div>

                            <div v-else class="text-sm text-red-600">⚠ Unknown question type: {{ q.type }}</div>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <button
                                v-if="currentPage > 1"
                                class="inline-flex items-center gap-2 rounded bg-gray-200 px-5 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300"
                                @click.prevent="currentPage--"
                            >
                                <ArrowLeft class="h-4 w-4" /> Previous
                            </button>
                            <button
                                v-if="currentPage < totalPages"
                                class="inline-flex items-center gap-2 rounded bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                                @click.prevent="currentPage++"
                            >
                                Next <ArrowRight class="h-4 w-4" />
                            </button>
                        </div>

                        <div class="mt-8 text-right">
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 rounded-lg bg-yellow-500 px-6 py-3 text-lg font-medium text-white shadow-md hover:bg-yellow-600"
                            >
                                <Eye class="h-5 w-5" /> Preview Answers
                            </button>
                        </div>
                    </form>

                    <div v-else class="space-y-6 pt-6">
                        <h2 class="mb-4 flex items-center gap-2 text-2xl font-bold text-gray-700"><Eye class="h-6 w-6" /> Review Your Answers</h2>
                        <div v-for="q in props.test.questions" :key="q.id" class="rounded-lg border bg-white p-5 shadow-sm">
                            <p class="mb-1 text-base font-medium text-indigo-900">Q: {{ q.question }}</p>
                            <p class="text-gray-600">
                                Answer: <span class="font-semibold">{{ formatAnswer(answers[q.id]) }}</span>
                            </p>
                        </div>
                        <div class="mt-6 flex justify-end gap-4">
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
                                <Check class="h-5 w-5" /> Confirm & Submit
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AppLayoutStudent>
</template>
