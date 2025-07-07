<script setup lang="ts">
import { ArrowLeftCircle, CheckCircle2 } from 'lucide-vue-next';
defineProps<{
    score?: number;
    total?: number;
}>();
</script>

<template>
    <div class="flex min-h-[70vh] items-center justify-center bg-gradient-to-br from-pink-50 via-white to-blue-50 px-4 py-8">
        <div class="w-full max-w-md rounded-xl border border-indigo-200 bg-white p-8 text-center shadow-md">
            <div class="mb-4 flex flex-col items-center">
                <CheckCircle2 class="h-12 w-12 text-green-600" />
                <h1 class="mt-2 text-3xl font-extrabold text-indigo-900">Test Completed!</h1>
            </div>

            <p class="text-lg text-gray-700">Here’s how you did on the proficiency test:</p>

            <div class="my-6 flex flex-col items-center justify-center">
                <div class="rounded-full bg-indigo-100 px-6 py-6 shadow-inner">
                    <span class="text-4xl font-bold text-indigo-800">{{ score }}</span>
                    <span class="text-xl text-gray-600"> / {{ total }}</span>
                </div>
                <p class="mt-2 text-sm text-gray-500 italic">Keep practicing to improve even more!</p>
            </div>

            <a
                href="/student-dashboard"
                class="mt-6 inline-flex items-center gap-2 rounded-lg bg-indigo-700 px-6 py-3 font-semibold text-white shadow transition hover:bg-yellow-500 hover:text-black"
            >
                <ArrowLeftCircle class="h-5 w-5" /> Back to Dashboard
            </a>
        </div>
    </div>
</template>
