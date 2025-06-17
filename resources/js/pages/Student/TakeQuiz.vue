<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { reactive } from 'vue';

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
            options?: string; // JSON array for multiple_choice and checkbox
            answer_key?: string;
        }>;
    };
}>();

// answers format: { [questionId]: string | string[] }
const answers = reactive<Record<number, string | string[]>>({});

function submitAnswers() {
    router.post('/quizzes/submit', {
        quiz_id: props.quiz.id,
        answers,
    });
}
</script>

<template>
    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 p-6">
            <h1 class="text-3xl font-bold text-primary">{{ quiz.title }}</h1>
            <p class="text-sm text-gray-500">Type: {{ quiz.type }} | Scheduled: {{ quiz.scheduled_at }}</p>

            <form @submit.prevent="submitAnswers">
                <div v-for="q in quiz.questions" :key="q.id" class="space-y-3 rounded-xl border bg-white p-4 shadow-sm">
                    <p class="text-lg font-semibold">{{ q.question }}</p>

                    <!-- Multiple Choice -->
                    <div v-if="q.type === 'multiple_choice'" class="space-y-2">
                        <div v-for="(option, index) in JSON.parse(q.options || '[]')" :key="index" class="flex items-center space-x-2">
                            <input
                                type="radio"
                                :name="'q-' + q.id"
                                :id="`q-${q.id}-option-${index}`"
                                :value="option"
                                class="accent-blue-600"
                                v-model="answers[q.id]"
                            />
                            <label :for="`q-${q.id}-option-${index}`" class="text-sm">{{ option }}</label>
                        </div>
                    </div>

                    <!-- Checkbox (Multiple Answers) -->
                    <div v-else-if="q.type === 'checkbox'" class="space-y-2">
                        <div v-for="(option, index) in JSON.parse(q.options || '[]')" :key="index" class="flex items-center space-x-2">
                            <input
                                type="checkbox"
                                :id="`q-${q.id}-check-${index}`"
                                :value="option"
                                class="accent-blue-600"
                                v-model="answers[q.id]"
                                @change="
                                    () => {
                                        if (!Array.isArray(answers[q.id])) {
                                            answers[q.id] = [];
                                        }
                                    }
                                "
                            />
                            <label :for="`q-${q.id}-check-${index}`" class="text-sm">{{ option }}</label>
                        </div>
                    </div>

                    <!-- True / False -->
                    <div v-else-if="q.type === 'true_false'" class="flex space-x-6">
                        <label class="flex items-center space-x-2">
                            <input type="radio" :name="'q-' + q.id" value="true" class="accent-green-600" v-model="answers[q.id]" />
                            <span>True</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" :name="'q-' + q.id" value="false" class="accent-red-600" v-model="answers[q.id]" />
                            <span>False</span>
                        </label>
                    </div>

                    <!-- Essay -->
                    <div v-else-if="q.type === 'essay'">
                        <textarea
                            rows="4"
                            class="w-full rounded-lg border p-3 text-sm focus:ring focus:ring-blue-200"
                            v-model="answers[q.id]"
                            placeholder="Write your answer here..."
                        ></textarea>
                    </div>

                    <!-- Fill in the Blank -->
                    <div v-else-if="q.type === 'fill_in_the_blank'">
                        <input type="text" class="w-full rounded border px-3 py-2" placeholder="Type your answer..." v-model="answers[q.id]" />
                    </div>
                </div>

                <div class="mt-6 text-right">
                    <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2 text-white transition hover:bg-blue-700">Submit Quiz</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
