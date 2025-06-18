<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { reactive, watchEffect } from 'vue';

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

// Ensure checkbox questions are initialized as arrays
watchEffect(() => {
    props.quiz.questions.forEach((q) => {
        if (q.type === 'checkbox' && !Array.isArray(answers[q.id])) {
            answers[q.id] = [];
        }
    });
});

function submitAnswers() {
    router.post('/quizzes/submit', {
        quiz_id: props.quiz.id,
        answers,
    });
}
</script>

<template>
    <AppLayout>
        <div class="container mx-auto max-w-4xl px-6 py-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="mb-2 text-3xl font-bold text-primary">{{ quiz.title }}</h1>
                <p class="text-sm text-gray-600">
                    Type: <span class="font-semibold">{{ quiz.type }}</span> | Scheduled: {{ quiz.scheduled_at }}
                </p>
            </div>

            <!-- Quiz Form -->
            <form @submit.prevent="submitAnswers">
                <div v-for="q in quiz.questions" :key="q.id" class="mt-2 space-y-6 rounded-lg border bg-white p-5 shadow-md">
                    <!-- Question Text -->
                    <p class="text-xl font-semibold text-gray-800">{{ q.question }}</p>

                    <!-- Multiple Choice -->
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

                    <!-- Checkbox (Multiple Correct) -->
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
                                        if (!Array.isArray(answers[q.id])) {
                                            answers[q.id] = [];
                                        }
                                        const current = answers[q.id] as string[];
                                        if (isChecked) {
                                            if (!current.includes(option)) current.push(option);
                                        } else {
                                            answers[q.id] = current.filter((o) => o !== option);
                                        }
                                    }
                                "
                            />
                            <label :for="`q-${q.id}-check-${index}`" class="text-lg text-gray-700">{{ option }}</label>
                        </div>
                    </div>

                    <!-- True/False -->
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

                    <!-- Essay -->
                    <div v-else-if="q.type === 'essay'">
                        <textarea
                            rows="4"
                            class="w-full rounded-lg border border-gray-300 p-4 text-sm text-gray-700 focus:ring-2 focus:ring-blue-200"
                            v-model="answers[q.id]"
                            placeholder="Write your answer here..."
                        ></textarea>
                    </div>

                    <!-- Fill in the Blank -->
                    <div v-else-if="q.type === 'fill_in_the_blank'">
                        <input
                            type="text"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-700 focus:ring-2 focus:ring-blue-200"
                            placeholder="Type your answer..."
                            v-model="answers[q.id]"
                        />
                    </div>

                    <!-- Unknown Type Fallback -->
                    <div v-else class="text-sm text-red-600">Unknown question type: {{ q.type }}</div>
                </div>

                <!-- Submit Button -->
                <div class="mt-8 text-right">
                    <button
                        type="submit"
                        class="rounded-xl bg-blue-600 px-8 py-3 text-lg text-white shadow-md transition duration-300 ease-in-out hover:bg-blue-700"
                    >
                        Submit Quiz
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
