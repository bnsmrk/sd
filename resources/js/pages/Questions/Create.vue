<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

type QuestionForm = {
    id?: number;
    question: string;
    type: 'multiple_choice' | 'checkboxes' | 'true_false' | 'essay' | 'fill_in_blank';
    options: string[];
    answer_key: string | string[];
};

const props = defineProps<{
    activity: {
        id: number;
        title: string;
        type: string;
        scheduled_at: string;
        module: {
            name: string;
            year_level: { name: string };
            section: { name: string };
            subject: { name: string };
        };
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
        <div class="grid grid-cols-1 gap-6 p-8 md:grid-cols-3">
            <!-- LEFT: Activity Info -->

            <div class="col-span-1 space-y-4 rounded border bg-gray-50 p-6 text-sm shadow">
                <h2 class="mb-4 text-lg font-bold">Activity Details</h2>
                <div>
                    <label class="block font-semibold">Title</label>
                    <input type="text" :value="props.activity.title" disabled class="mt-1 w-full rounded border bg-gray-100 px-3 py-2" />
                </div>
                <div>
                    <label class="block font-semibold">Type</label>
                    <input type="text" :value="props.activity.type" disabled class="mt-1 w-full rounded border bg-gray-100 px-3 py-2" />
                </div>
                <div>
                    <label class="block font-semibold">Scheduled At</label>
                    <input type="text" :value="props.activity.scheduled_at" disabled class="mt-1 w-full rounded border bg-gray-100 px-3 py-2" />
                </div>
                <div>
                    <label class="block font-semibold">Module</label>
                    <input type="text" :value="props.activity.module.name" disabled class="mt-1 w-full rounded border bg-gray-100 px-3 py-2" />
                </div>
                <div>
                    <label class="block font-semibold">Year Level</label>
                    <input
                        type="text"
                        :value="props.activity.module.year_level.name"
                        disabled
                        class="mt-1 w-full rounded border bg-gray-100 px-3 py-2"
                    />
                </div>
                <div>
                    <label class="block font-semibold">Section</label>
                    <input
                        type="text"
                        :value="props.activity.module.section.name"
                        disabled
                        class="mt-1 w-full rounded border bg-gray-100 px-3 py-2"
                    />
                </div>
                <div>
                    <label class="block font-semibold">Subject</label>
                    <input
                        type="text"
                        :value="props.activity.module.subject.name"
                        disabled
                        class="mt-1 w-full rounded border bg-gray-100 px-3 py-2"
                    />
                </div>
            </div>

            <!-- RIGHT: Questions -->
            <div class="col-span-1 md:col-span-2">
                <h2 class="mb-6 text-2xl font-semibold text-gray-800">Questions for "{{ props.activity.title }}"</h2>

                <div v-for="(q, idx) in questions" :key="q.id ?? idx" class="mb-8 rounded-lg border border-gray-300 bg-white p-6 shadow-md">
                    <!-- Question Input -->
                    <div class="mb-4">
                        <label class="mb-2 block text-lg font-semibold text-gray-700">Question</label>
                        <input
                            v-model="q.question"
                            type="text"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500"
                        />
                    </div>

                    <!-- Question Type -->
                    <div class="mb-4">
                        <label class="mb-2 block text-lg font-semibold text-gray-700">Type</label>
                        <select v-model="q.type" @change="onTypeChange(idx)" class="w-full rounded-lg border border-gray-300 px-4 py-2">
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="checkboxes">Checkboxes</option>
                            <option value="true_false">True/False</option>
                            <option value="essay">Essay</option>
                            <option value="fill_in_blank">Fill in the Blank</option>
                        </select>
                    </div>

                    <!-- Dynamic Options per Type -->
                    <template v-if="q.type === 'multiple_choice'">
                        <div class="mb-4">
                            <label class="mb-2 block font-semibold">Options</label>
                            <div v-for="(opt, i) in q.options" :key="i" class="mb-2 flex items-center gap-2">
                                <input type="radio" :name="`answer-${idx}`" :checked="q.answer_key === opt" @change="q.answer_key = opt" />
                                <input v-model="q.options[i]" class="flex-1 rounded border px-3 py-2" type="text" />
                                <button v-if="q.options.length > 2" type="button" @click="q.options.splice(i, 1)">✖</button>
                            </div>
                            <button type="button" @click="q.options.push('')" class="text-sm text-blue-600">+ Add Option</button>
                        </div>
                    </template>

                    <template v-if="q.type === 'checkboxes'">
                        <div class="mb-4">
                            <label class="mb-2 block font-semibold">Options</label>
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
                                <input v-model="q.options[i]" class="flex-1 rounded border px-3 py-2" type="text" />
                                <button v-if="q.options.length > 2" type="button" @click="q.options.splice(i, 1)">✖</button>
                            </div>
                            <button type="button" @click="q.options.push('')" class="text-sm text-blue-600">+ Add Option</button>
                        </div>
                    </template>

                    <template v-if="q.type === 'true_false'">
                        <label class="mb-1 block font-semibold">Answer</label>
                        <select v-model="q.answer_key" class="w-full rounded border px-3 py-2">
                            <option value="">Select</option>
                            <option value="true">True</option>
                            <option value="false">False</option>
                        </select>
                    </template>

                    <template v-if="q.type === 'essay' || q.type === 'fill_in_blank'">
                        <label class="mb-1 block font-semibold">Answer Key</label>
                        <input v-model="q.answer_key" class="w-full rounded border px-3 py-2" type="text" />
                    </template>

                    <!-- Remove Question -->
                    <button @click="removeQuestion(idx)" type="button" class="mt-3 text-sm text-red-600">Remove Question</button>
                </div>

                <!-- Buttons -->
                <div class="mt-8 flex justify-between">
                    <button @click="addQuestion" type="button" class="rounded bg-gray-600 px-4 py-2 text-white">+ Add Question</button>
                    <button @click="submit" type="button" class="rounded bg-green-600 px-6 py-2 text-white">Submit All</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
