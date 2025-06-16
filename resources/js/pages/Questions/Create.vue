<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    activity: {
        id: number;
        title: string;
    };
}>();

const questions = ref([
    {
        question: '',
        type: 'multiple_choice',
        options: ['', '', '', ''],
        answer_key: '',
    },
]);

function addQuestion() {
    questions.value.push({
        question: '',
        type: 'multiple_choice',
        options: ['', '', '', ''],
        answer_key: '',
    });
}

function removeQuestion(index: number) {
    questions.value.splice(index, 1);
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
            <h2 class="mb-4 text-xl font-bold">Add Questions for "{{ props.activity.title }}"</h2>

            <div v-for="(q, index) in questions" :key="index" class="mb-4 space-y-2 rounded border p-4">
                <div>
                    <label class="block font-medium">Question</label>
                    <input v-model="q.question" type="text" class="w-full rounded border px-2 py-1" />
                </div>

                <div>
                    <label class="block font-medium">Type</label>
                    <select v-model="q.type" class="w-full rounded border px-2 py-1">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="essay">Essay</option>
                    </select>
                </div>

                <div v-if="q.type === 'multiple_choice'">
                    <label class="block font-medium">Options</label>
                    <div v-for="(opt, i) in q.options" :key="i" class="mb-1">
                        <input v-model="q.options[i]" type="text" class="w-full rounded border px-2 py-1" />
                    </div>
                </div>

                <div>
                    <label class="block font-medium">Answer Key</label>
                    <input v-model="q.answer_key" type="text" class="w-full rounded border px-2 py-1" />
                </div>

                <button class="mt-2 text-red-600" @click="removeQuestion(index)">Remove Question</button>
            </div>

            <div class="flex gap-2">
                <button @click="addQuestion" class="rounded bg-gray-500 px-4 py-2 text-white">Add Question</button>
                <button @click="submit" class="rounded bg-green-600 px-4 py-2 text-white">Submit All</button>
            </div>
        </div>
    </AppLayout>
</template>
