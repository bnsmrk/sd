<script setup lang="ts">
defineProps<{ assignment: any }>();
defineEmits(['close']);
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="max-w-md w-full rounded bg-white p-6 shadow-lg">
            <h2 class="text-xl font-semibold mb-4">Assignment Details</h2>
            <div class="space-y-3">
                <p><strong>Teacher:</strong> {{ assignment.teacher.name }}</p>
                <p><strong>Year Level:</strong> {{ assignment.year_level.name }}</p>
                <p><strong>Section:</strong> {{ assignment.section?.name ?? 'Shared Subject' }}</p>
                <p><strong>Subject:</strong> {{ assignment.subject.name }}</p>
            </div>
            <div class="mt-6 flex justify-end">
                <button @click="$emit('close')" class="text-sm text-gray-500 hover:underline">Close</button>
            </div>
        </div>
    </div>
</template>
