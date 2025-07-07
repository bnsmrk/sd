<script setup lang="ts">
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';
import { router } from '@inertiajs/vue3';
import { ArrowLeftCircle, CheckCircle2 } from 'lucide-vue-next';
defineProps<{
    score: number;
    total: number;
    quizId: number;
}>();
</script>

<template>
    <AppLayoutStudent>
        <div class="flex min-h-[70vh] items-center justify-center bg-gradient-to-br from-blue-50 to-pink-50 px-4 py-10">
            <div class="w-full max-w-md rounded-xl border border-blue-200 bg-white p-8 text-center shadow-lg">
                <div class="mb-4 flex flex-col items-center justify-center">
                    <CheckCircle2 class="h-12 w-12 text-green-600" />
                    <h1 class="mt-2 text-4xl font-extrabold text-green-600">Done!</h1>
                </div>

                <p class="mb-4 text-lg text-gray-700">You've completed the quiz.</p>
                <div class="mx-auto mb-4 w-fit rounded-full border border-green-300 bg-green-50 px-6 py-4 shadow-inner">
                    <span class="text-3xl font-bold text-green-700">{{ score }}</span>
                    <span class="text-xl text-gray-600"> / {{ total }}</span>
                </div>

                <p class="mb-6 text-sm text-gray-500 italic">Great job! You can review or try other subjects.</p>

                <button
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 font-medium text-white shadow transition hover:bg-yellow-500 hover:text-black"
                    @click="router.get('/my-subjects')"
                >
                    <ArrowLeftCircle class="h-5 w-5" /> Back to Subjects
                </button>
            </div>
        </div>
    </AppLayoutStudent>
</template>
