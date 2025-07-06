<script setup lang="ts">
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';
import { router } from '@inertiajs/vue3';
import { computed, nextTick, reactive, ref, watch, watchEffect } from 'vue';
import { ArrowLeft, ArrowRight, Check, Eye, Send, BookOpen, FileText, Calendar, ListOrdered } from 'lucide-vue-next';
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

// Update scroll function
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

// Calculate the page number for a specific question ID
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
        // Scroll to the selected question
        scrollToQuestion(id);
        // Dynamically set the page based on the question ID
        const page = getPageForQuestion(id);
        if (page !== currentPage.value) {
            currentPage.value = page;
        }
    }
});
</script>

<template>
    <AppLayoutStudent>
        <div class="container mx-auto max-w-6xl px-6 py-8 bg-gradient-to-b from-pink-50 via-white to-blue-50 min-h-screen">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-indigo-900 flex items-center gap-2 mb-1">
                    <BookOpen class="w-6 h-6" /> {{ test.title }}
                </h1>
                <p class="text-sm text-gray-600 flex items-center gap-4">
    <span class="flex items-center gap-1">
        <FileText class="w-4 h-4 text-indigo-600" /> <span class="capitalize font-semibold">{{ test.type }}</span>
    </span>
    <span class="flex items-center gap-1">
        <Calendar class="w-4 h-4 text-indigo-600" /> {{ test.scheduled_at }}
    </span>
</p>

            </div>

            <div class="flex flex-col md:flex-row gap-8">
                <!-- Sidebar Navigator -->
                <aside v-if="!previewMode" class="w-full md:max-w-[220px] sticky top-20">
                    <div class="rounded-xl border bg-white shadow-md p-4">
                        <h3 class="text-center text-sm font-bold text-gray-700 mb-4">🔢 Question Navigator</h3>
                        <div class="space-y-3">
                            <div v-for="(group, gIndex) in groupedButtons" :key="gIndex" class="grid grid-cols-5 gap-2 justify-items-center">
                                <label
                                    v-for="(qid, index) in group"
                                    :key="qid"
                                    class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-semibold transition-all duration-300 cursor-pointer"
                                    :class="{
                                        'bg-green-500 text-white hover:bg-green-600': isAnswered(qid),
                                        'bg-red-400 text-white hover:bg-red-500': !isAnswered(qid)
                                    }"
                                >
                                    <input type="radio" v-model="selectedQuestionId" :value="qid" class="hidden" />
                                    {{ gIndex * questionsPerPage + index + 1 }}
                                </label>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main Test Content -->
                <section class="flex-1">
                    <!-- Form Mode -->
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

                            <!-- Multiple Choice -->
                            <div v-if="q.type === 'multiple_choice'" class="space-y-3">
                                <div
                                    v-for="(option, idx) in getOptions(q.options)"
                                    :key="idx"
                                    class="flex items-center space-x-3"
                                >
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

                            <!-- Checkboxes -->
                            <div v-else-if="q.type === 'checkboxes'" class="space-y-3">
                                <div
                                    v-for="(option, idx) in getOptions(q.options)"
                                    :key="idx"
                                    class="flex items-center space-x-3"
                                >
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

                            <!-- True/False -->
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

                            <!-- Essay -->
                            <div v-else-if="q.type === 'essay'">
                                <textarea
                                    rows="4"
                                    class="w-full rounded-lg border border-gray-300 p-4 text-sm text-gray-700 focus:ring-2 focus:ring-pink-300"
                                    v-model="answers[q.id]"
                                    placeholder="Write your answer here..."
                                ></textarea>
                            </div>

                            <!-- Fill in the Blank -->
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

                        <!-- Pagination -->
                        <div class="mt-4 flex justify-between items-center">
                            <button
                                v-if="currentPage > 1"
                                class="inline-flex items-center gap-2 rounded bg-gray-200 px-5 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300"
                                @click.prevent="currentPage--"
                            >
                                <ArrowLeft class="w-4 h-4" /> Previous
                            </button>
                            <button
                                v-if="currentPage < totalPages"
                                class="inline-flex items-center gap-2 rounded bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                                @click.prevent="currentPage++"
                            >
                                Next <ArrowRight class="w-4 h-4" />
                            </button>
                        </div>

                        <!-- Done Button -->
                        <div class="mt-8 text-right">
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 rounded-lg bg-yellow-500 hover:bg-yellow-600 px-6 py-3 text-white text-lg font-medium shadow-md"
                            >
                                <Eye class="w-5 h-5" /> Preview Answers
                            </button>
                        </div>
                    </form>

                    <!-- Preview -->
                    <div v-else class="space-y-6 pt-6">
                        <h2 class="text-2xl font-bold text-gray-700 mb-4 flex items-center gap-2">
                            <Eye class="w-6 h-6" /> Review Your Answers
                        </h2>
                        <div
                            v-for="q in props.test.questions"
                            :key="q.id"
                            class="rounded-lg border bg-white p-5 shadow-sm"
                        >
                            <p class="mb-1 text-base font-medium text-indigo-900">Q: {{ q.question }}</p>
                            <p class="text-gray-600">
                                Answer: <span class="font-semibold">{{ formatAnswer(answers[q.id]) }}</span>
                            </p>
                        </div>
                        <div class="flex justify-end gap-4 mt-6">
                            <button
                                class="inline-flex items-center gap-2 rounded bg-gray-500 px-6 py-2 text-white hover:bg-gray-600"
                                @click="previewMode = false"
                            >
                                <ArrowLeft class="w-4 h-4" /> Back
                            </button>
                            <button
                                class="inline-flex items-center gap-2 rounded bg-green-600 px-6 py-2 text-white hover:bg-green-700"
                                @click="submitAnswers"
                            >
                                <Check class="w-5 h-5" /> Confirm & Submit
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AppLayoutStudent>
</template>

