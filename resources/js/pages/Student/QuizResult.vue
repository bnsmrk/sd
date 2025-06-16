<script setup lang="ts">
defineProps<{
    result: {
        score: number;
        total_points: number;
        created_at: string;
    };
}>();
</script>

<template>
    <div class="mx-auto mt-10 max-w-xl space-y-4 rounded-xl border bg-white p-6 text-center shadow">
        <h2 class="text-2xl font-bold text-green-600">Quiz Result</h2>
        <p class="text-lg">
            Score: <span class="font-semibold">{{ result.score }}</span> / {{ result.total_points }}
        </p>
        <p class="text-sm text-gray-500">Taken at: {{ new Date(result.created_at).toLocaleString() }}</p>
        <router-link href="/my-subjects" class="mt-4 inline-block rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
            Back to Subjects
        </router-link>
    </div>
</template>
