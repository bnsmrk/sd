<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

type QuestionForm = {
    id?: number;
    question: string;
    type: 'multiple_choice' | 'checkboxes' | 'true_false' | 'essay' | 'fill_in_blank';
    options: string[];
    answer_key: string | string[];
};

const props = defineProps<{
    proficiencyTest: {
        id: number;
        title: string;
        type: string;
        scheduled_at: string;
        year_level: { name: string };
    };
    existingQuestions: Array<{
        id: number;
        question: string;
        type: string;
        options: string | null;
        answer_key: string | null;
    }>;
}>();

const questions = ref<QuestionForm[]>(
    props.existingQuestions.length
        ? props.existingQuestions.map((q) => ({
              id: q.id,
              question: q.question,
              type: q.type as QuestionForm['type'],
              options: q.options ? JSON.parse(q.options) : [],
              answer_key: q.type === 'checkboxes' && q.answer_key ? JSON.parse(q.answer_key) : q.answer_key || '',
          }))
        : [
              {
                  question: '',
                  type: 'multiple_choice',
                  options: ['', '', '', ''],
                  answer_key: '',
              },
          ],
);

function addQuestion() {
    questions.value.push({
        question: '',
        type: 'multiple_choice',
        options: ['', '', ''],
        answer_key: '',
    });
}

function removeQuestion(idx: number) {
    questions.value.splice(idx, 1);
}

function onTypeChange(idx: number) {
    const q = questions.value[idx];
    if (['multiple_choice', 'checkboxes'].includes(q.type)) {
        if (q.options.length < 2) q.options = ['', '', ''];
    } else {
        q.options = [];
    }
    q.answer_key = q.type === 'checkboxes' ? [] : '';
}

function submit() {
    router.post(`/proficiency-test/${props.proficiencyTest.id}/questions`, {
        questions: questions.value,
    });
}
</script>

<template>
    <Head title="Add Questions" />

    <AppLayout>
        <div class="p-8">
            <!-- 🔙 Back Button -->
            <div class="mb-6">
                <Link
                    href="/proficiency-test"
                    class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
                >
                    ← Back to Tests
                </Link>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <!-- LEFT: Test Info -->
                <div class="col-span-1 space-y-4 rounded border border-[#01006c] bg-gray-50 p-6 text-sm shadow md:sticky md:top-6 h-fit">
                    <h2 class="mb-4 text-lg font-bold text-[#01006c]">Proficiency Test Details</h2>
                    <div
                        v-for="field in [
                            { label: 'Title', value: props.proficiencyTest.title },
                            { label: 'Type', value: props.proficiencyTest.type },
                            { label: 'Scheduled At', value: props.proficiencyTest.scheduled_at },
                            { label: 'Year Level', value: props.proficiencyTest.year_level.name },
                        ]"
                        :key="field.label"
                    >
                        <label class="block font-semibold text-[#ff69b4]">{{ field.label }}</label>
                        <input type="text" :value="field.value" disabled class="floating-input" />
                    </div>
                </div>

                <!-- RIGHT: Questions -->
                <div class="col-span-1 md:col-span-2">
                    <h2 class="mb-6 text-2xl font-bold text-[#01006c]">Questions for "{{ props.proficiencyTest.title }}"</h2>

                    <div v-for="(q, idx) in questions" :key="q.id ?? idx" class="mb-8 rounded-lg border border-[#01006c] bg-white p-6 shadow-md">
                        
                         <div class="mb-2 text-lg font-semibold text-[#01006c]">Question #{{ idx + 1 }}</div>
                        <!-- QUESTION TEXT -->
                        <div class="mb-4">
                            <label class="floating-label">Question</label>
                            <input v-model="q.question" type="text" class="floating-input" />
                        </div>

                        <!-- TYPE DROPDOWN -->
                        <div class="mb-4">
                            <label class="floating-label">Type</label>
                            <select v-model="q.type" @change="onTypeChange(idx)" class="floating-input">
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="checkboxes">Checkboxes</option>
                                <option value="true_false">True/False</option>
                                <option value="essay">Essay</option>
                                <option value="fill_in_blank">Fill in the Blank</option>
                            </select>
                        </div>

                        <!-- MULTIPLE CHOICE -->
                        <template v-if="q.type === 'multiple_choice'">
                            <div class="mb-4">
                                <label class="floating-label">Options</label>
                                <div v-for="(opt, i) in q.options" :key="i" class="mb-2 flex items-center gap-2">
                                    <input type="radio" :name="`answer-${idx}`" :checked="q.answer_key === opt" @change="q.answer_key = opt" />
                                    <input v-model="q.options[i]" class="floating-input flex-1" type="text" />
                                    <button v-if="q.options.length > 2" type="button" @click="q.options.splice(i, 1)">✖</button>
                                </div>
                                <button type="button" @click="q.options.push('')" class="text-sm text-blue-600">+ Add Option</button>
                            </div>
                        </template>

                        <!-- CHECKBOXES -->
                        <template v-if="q.type === 'checkboxes'">
                            <div class="mb-4">
                                <label class="floating-label">Options</label>
                                <div v-for="(opt, i) in q.options" :key="i" class="mb-2 flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        :checked="(q.answer_key as string[]).includes(opt)"
                                        @change="
                                            (e: Event) => {
                                                const checked = (e.target as HTMLInputElement).checked;
                                                const value = q.options[i];
                                                const answers = q.answer_key as string[];
                                                if (checked) answers.push(value);
                                                else q.answer_key = answers.filter((a) => a !== value);
                                            }
                                        "
                                    />
                                    <input v-model="q.options[i]" class="floating-input flex-1" type="text" />
                                    <button v-if="q.options.length > 2" type="button" @click="q.options.splice(i, 1)">✖</button>
                                </div>
                                <button type="button" @click="q.options.push('')" class="text-sm text-blue-600">+ Add Option</button>
                            </div>
                        </template>

                        <!-- TRUE/FALSE -->
                        <template v-if="q.type === 'true_false'">
                            <label class="floating-label">Answer</label>
                            <select v-model="q.answer_key" class="floating-input">
                                <option value="">Select</option>
                                <option value="true">True</option>
                                <option value="false">False</option>
                            </select>
                        </template>

                        <!-- ESSAY / FILL IN BLANK -->
                        <template v-if="q.type === 'essay' || q.type === 'fill_in_blank'">
                            <label class="floating-label">Answer Key</label>
                            <input v-model="q.answer_key" class="floating-input" type="text" />
                        </template>

                        <button @click="removeQuestion(idx)" type="button" class="mt-3 text-sm text-red-600">Remove Question</button>
                    </div>

                    <!-- BUTTONS -->
                    <div class="mt-8 flex justify-between">
                        <button @click="addQuestion" type="button" class="rounded bg-[#01006c] px-4 py-2 text-white hover:bg-[#0d1282]">
                            + Add Question
                        </button>
                        <button @click="submit" type="button" class="rounded bg-[#ff69b4] px-6 py-2 text-white hover:bg-[#e858a1]">Submit All</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.floating-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #ff69b4;
}
.floating-input {
    width: 100%;
    border: 2px solid #01006c;
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    transition:
        border-color 0.2s,
        background-color 0.2s;
}
.floating-input:focus {
    outline: none;
    border-color: #ffc60b;
    background-color: #ffebf5;
}
</style>
