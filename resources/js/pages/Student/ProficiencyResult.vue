<script setup lang="ts">
import { CheckCircle2, ArrowLeftCircle } from 'lucide-vue-next';
defineProps<{
    score?: number;
    total?: number;
}>();
</script>

<template>
    <div class="flex min-h-[70vh] items-center justify-center bg-gradient-to-br from-pink-50 via-white to-blue-50 px-4 py-8">
        <div class="w-full max-w-md rounded-xl border border-indigo-200 bg-white p-8 text-center shadow-md">
            <!-- Header with Icon -->
            <div class="flex flex-col items-center mb-4">
                <CheckCircle2 class="h-12 w-12 text-green-600" />
                <h1 class="text-3xl font-extrabold text-indigo-900 mt-2">Test Completed!</h1>
            </div>

            <p class="text-gray-700 text-lg">Here’s how you did on the proficiency test:</p>

            <!-- Score Section -->
            <div class="my-6 flex flex-col items-center justify-center">
                <div class="rounded-full bg-indigo-100 px-6 py-6 shadow-inner">
                    <span class="text-4xl font-bold text-indigo-800">{{ score }}</span>
                    <span class="text-xl text-gray-600"> / {{ total }}</span>
                </div>
                <p class="mt-2 text-sm text-gray-500 italic">Keep practicing to improve even more!</p>
            </div>

            <!-- Button -->
            <a
                href="/student-dashboard"
                class="inline-flex items-center gap-2 mt-6 rounded-lg bg-indigo-700 px-6 py-3 text-white font-semibold shadow hover:bg-yellow-500 hover:text-black transition"
            >
                <ArrowLeftCircle class="h-5 w-5" /> Back to Dashboard
            </a>
        </div>
    </div>
</template>

