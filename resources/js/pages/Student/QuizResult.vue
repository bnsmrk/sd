<script setup lang="ts">
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';

import { router } from '@inertiajs/vue3';

defineProps<{
    score: number;
    total: number;
    quizId: number;
}>();
</script>

<template>
    <AppLayoutStudent>
        <div class="space-y-6 p-8 text-center">
            <h1 class="text-3xl font-bold text-green-600">Quiz Submitted!</h1>
            <p class="text-xl">
                Your Score: <strong>{{ score }} / {{ total }}</strong>
            </p>

            <button class="mt-4 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" @click="router.get('/my-subjects')">
                Back to Subjects
            </button>
        </div>
    </AppLayoutStudent>
</template>
