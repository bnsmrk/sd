<script setup lang="ts">
defineProps<{ message: string }>();
defineEmits(['confirm', 'cancel']);
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="max-w-md w-full rounded bg-white p-6 shadow-lg">
            <h2 class="text-lg font-semibold mb-4">Confirm Deletion</h2>
            <p class="mb-6 text-gray-600">{{ message }}</p>
            <div class="flex justify-end space-x-3">
                <button @click="$emit('cancel')" class="text-gray-600 hover:underline">Cancel</button>
                <button @click="$emit('confirm')" class="text-red-600 hover:underline">Delete</button>
            </div>
        </div>
    </div>
</template>
