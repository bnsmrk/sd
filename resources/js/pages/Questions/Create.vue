<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, BookText, CalendarClock, FileText, ListChecks, NotebookPen, PlusCircle, Send, Trash2, Users } from 'lucide-vue-next';
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
        <div class="p-8">
            <div class="mb-6">
                <Link
                    href="/activities"
                    class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
                >
                    <ArrowLeft class="h-4 w-4 text-[#01006c]" />
                    Back to Activities
                </Link>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="col-span-1 h-fit space-y-4 rounded border border-[#01006c] bg-gray-50 p-6 text-sm shadow md:sticky md:top-6">
                    <h2 class="mb-4 text-lg font-bold text-[#01006c]">Activity Details</h2>
                    <div
                        v-for="field in [
                            { label: 'Title', icon: FileText, value: props.activity.title },
                            { label: 'Type', icon: ListChecks, value: props.activity.type },
                            { label: 'Scheduled At', icon: CalendarClock, value: props.activity.scheduled_at },
                            { label: 'Module', icon: NotebookPen, value: props.activity.module.name },
                            { label: 'Year Level', icon: BookOpen, value: props.activity.module.year_level.name },
                            { label: 'Section', icon: Users, value: props.activity.module.section.name },
                            { label: 'Subject', icon: BookText, value: props.activity.module.subject.name },
                        ]"
                        :key="field.label"
                    >
                        <label class="mb-1 flex items-center gap-2 font-semibold text-[#ff69b4]">
                            <component :is="field.icon" class="h-4 w-4 text-[#ff69b4]" />
                            {{ field.label }}
                        </label>
                        <input
                            type="text"
                            :value="field.value"
                            disabled
                            class="w-full rounded border-2 border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                        />
                    </div>
                </div>

                <div class="col-span-1 md:col-span-2">
                    <h2 class="mb-6 text-2xl font-bold text-[#01006c]">Questions for "{{ props.activity.title }}"</h2>

                    <div v-for="(q, idx) in questions" :key="q.id ?? idx" class="mb-8 rounded-lg border border-[#01006c] bg-white p-6 shadow-md">
                        <div class="mb-2 text-lg font-semibold text-[#01006c]">Question #{{ idx + 1 }}</div>

                        <div class="mb-4">
                            <label class="mb-1 block font-semibold text-[#ff69b4]">Question</label>
                            <input
                                v-model="q.question"
                                type="text"
                                class="w-full rounded border-2 border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                            />
                        </div>

                        <div class="mb-4">
                            <label class="mb-1 block font-semibold text-[#ff69b4]">Type</label>
                            <select
                                v-model="q.type"
                                @change="onTypeChange(idx)"
                                class="w-full rounded border-2 border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                            >
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="checkboxes">Checkboxes</option>
                                <option value="true_false">True/False</option>
                                <option value="essay">Essay</option>
                                <option value="fill_in_blank">Fill in the Blank</option>
                            </select>
                        </div>

                        <template v-if="q.type === 'multiple_choice'">
                            <div class="mb-4">
                                <label class="mb-1 block font-semibold text-[#ff69b4]">Options</label>
                                <div v-for="(opt, i) in q.options" :key="i" class="mb-2 flex items-center gap-2">
                                    <input type="radio" :name="`answer-${idx}`" :checked="q.answer_key === opt" @change="q.answer_key = opt" />
                                    <input
                                        v-model="q.options[i]"
                                        class="w-full rounded border-2 border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                                        type="text"
                                    />
                                    <button v-if="q.options.length > 2" @click="q.options.splice(i, 1)" class="text-red-600 hover:text-red-800">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                                <button type="button" @click="q.options.push('')" class="text-sm text-blue-600">+ Add Option</button>
                            </div>
                        </template>

                        <template v-if="q.type === 'checkboxes'">
                            <div class="mb-4">
                                <label class="mb-1 block font-semibold text-[#ff69b4]">Options</label>
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
                                    <input
                                        v-model="q.options[i]"
                                        class="w-full rounded border-2 border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                                        type="text"
                                    />
                                    <button v-if="q.options.length > 2" @click="q.options.splice(i, 1)" class="text-red-600 hover:text-red-800">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                                <button type="button" @click="q.options.push('')" class="text-sm text-blue-600">+ Add Option</button>
                            </div>
                        </template>

                        <template v-if="q.type === 'true_false'">
                            <label class="mb-1 block font-semibold text-[#ff69b4]">Answer</label>
                            <select
                                v-model="q.answer_key"
                                class="w-full rounded border-2 border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                            >
                                <option value="">Select</option>
                                <option value="true">True</option>
                                <option value="false">False</option>
                            </select>
                        </template>

                        <template v-if="q.type === 'essay' || q.type === 'fill_in_blank'">
                            <label class="mb-1 block font-semibold text-[#ff69b4]">Answer Key</label>
                            <input
                                v-model="q.answer_key"
                                class="w-full rounded border-2 border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                                type="text"
                            />
                        </template>

                        <button
                            @click="removeQuestion(idx)"
                            type="button"
                            class="mt-3 inline-flex items-center gap-1 text-sm text-red-600 hover:text-red-800"
                        >
                            <Trash2 class="h-4 w-4" />
                            Remove Question
                        </button>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button
                            @click="addQuestion"
                            type="button"
                            class="inline-flex items-center gap-2 rounded bg-[#01006c] px-4 py-2 text-white hover:bg-[#0d1282]"
                        >
                            <PlusCircle class="h-5 w-5" />
                            Add Question
                        </button>
                        <button
                            @click="submit"
                            type="button"
                            class="inline-flex items-center gap-2 rounded bg-[#ff69b4] px-6 py-2 text-white hover:bg-[#e858a1]"
                        >
                            <Send class="h-5 w-5" />
                            Submit All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
