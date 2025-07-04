<script setup lang="ts">
defineProps<{
    score?: number;
    total?: number;
}>();
</script>

<template>
    <div class="mx-auto max-w-xl p-6 text-center">
        <h1 class="text-2xl font-bold text-[#01006c]">Proficiency Test Result</h1>
        <p class="mt-4 text-lg">You scored {{ score }} out of {{ total }}</p>
        <a href="/student-dashboard" class="mt-6 inline-block rounded bg-[#01006c] px-4 py-2 text-white">Back to Dashboard</a>
    </div>
</template>
