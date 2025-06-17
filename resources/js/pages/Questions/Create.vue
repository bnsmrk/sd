<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

type QuestionForm = {
    id?: number;
    question: string;
    type: 'multiple_choice' | 'checkboxes' | 'true_false' | 'essay' | 'fill_in_blank';
    options: string[];
    answer_key: string | string[]; // string[] for checkboxes
};

const props = defineProps<{
    activity: {
        id: number;
        title: string;
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
    router.post(`/activities/${props.activity.id}/questions`, {
        questions: questions.value,
    });
}
</script>

<template>
    <Head title="Add Questions" />
    <AppLayout>
        <div class="mx-auto max-w-4xl p-6">
            <h2 class="mb-4 text-xl font-bold">Questions for "{{ props.activity.title }}"</h2>

            <div v-for="(q, idx) in questions" :key="q.id ?? idx" class="mb-6 space-y-3 rounded border p-4">
                <div>
                    <label>Question</label>
                    <input v-model="q.question" type="text" class="w-full rounded border px-2 py-1" />
                </div>

                <div>
                    <label>Type</label>
                    <select v-model="q.type" @change="onTypeChange(idx)" class="w-full rounded border px-2 py-1">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="checkboxes">Checkboxes</option>
                        <option value="true_false">True/False</option>
                        <option value="essay">Essay</option>
                        <option value="fill_in_blank">Fill in the Blank</option>
                    </select>
                </div>

                <!-- MULTIPLE CHOICE -->
                <div v-if="q.type === 'multiple_choice'">
                    <label class="font-semibold">Options (select one correct answer)</label>
                    <div v-for="(opt, i) in q.options" :key="i" class="mb-2 flex items-center gap-2">
                        <input type="radio" :name="`answer-${idx}`" :value="opt" :checked="q.answer_key === opt" @change="q.answer_key = opt" />
                        <input v-model="q.options[i]" type="text" class="flex-1 rounded border px-2 py-1" />
                        <button v-if="q.options.length > 2" @click="q.options.splice(i, 1)" type="button" class="text-red-500">✖</button>
                    </div>
                    <button @click="q.options.push('')" type="button" class="text-blue-600">+ Add Option</button>
                </div>

                <!-- CHECKBOXES -->
                <div v-if="q.type === 'checkboxes'">
                    <label class="font-semibold">Options (select multiple correct answers)</label>
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
                        <input v-model="q.options[i]" type="text" class="flex-1 rounded border px-2 py-1" />
                        <button v-if="q.options.length > 2" @click="q.options.splice(i, 1)" type="button" class="text-red-500">✖</button>
                    </div>
                    <button @click="q.options.push('')" type="button" class="text-blue-600">+ Add Option</button>
                </div>

                <!-- TRUE/FALSE -->
                <div v-if="q.type === 'true_false'">
                    <label>Answer Key</label>
                    <select v-model="q.answer_key" class="w-full rounded border px-2 py-1">
                        <option value="">Select</option>
                        <option value="true">True</option>
                        <option value="false">False</option>
                    </select>
                </div>

                <!-- ESSAY -->
                <div v-if="q.type === 'essay'">
                    <label>Answer Key (optional)</label>
                    <input v-model="q.answer_key" type="text" class="w-full rounded border px-2 py-1" />
                </div>

                <!-- FILL IN THE BLANK -->
                <div v-if="q.type === 'fill_in_blank'">
                    <label>Correct Answer</label>
                    <input v-model="q.answer_key" type="text" class="w-full rounded border px-2 py-1" />
                </div>

                <button @click="removeQuestion(idx)" type="button" class="text-red-600">Remove</button>
            </div>

            <div class="flex gap-4">
                <button @click="addQuestion" type="button" class="rounded bg-gray-500 px-4 py-2 text-white">Add Question</button>
                <button @click="submit" type="button" class="rounded bg-green-600 px-4 py-2 text-white">Submit All</button>
            </div>
        </div>
    </AppLayout>
</template>
