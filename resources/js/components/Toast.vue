<script setup lang="ts">
import { onMounted } from 'vue';
const emit = defineEmits(['close']);

onMounted(() => {
    setTimeout(() => {
        // emit close after 5 seconds
        emit('close');
    }, 5000);
});
defineProps<{
    message: string | null;
    variant: 'success' | 'danger' | 'warning';
}>();

const toastStyle = {
    success: {
        container: 'bg-white text-green-500 dark:bg-green-800 dark:text-green-200',
        icon: 'inline-flex items-center justify-center w-8 h-8 bg-green-100 rounded-lg dark:bg-green-700',
        iconClass: 'fas fa-check-circle',
    },
    danger: {
        container: 'bg-white text-red-500 dark:bg-red-800 dark:text-red-200',
        icon: 'inline-flex items-center justify-center w-8 h-8 bg-red-100 rounded-lg dark:bg-red-700',
        iconClass: 'fas fa-exclamation-circle',
    },
    warning: {
        container: 'bg-white text-orange-500 dark:bg-orange-800 dark:text-orange-200',
        icon: 'inline-flex items-center justify-center w-8 h-8 bg-orange-100 rounded-lg dark:bg-orange-700',
        iconClass: 'fas fa-exclamation-triangle',
    },
};
</script>

<template>
    <div
        v-if="message"
        :class="['fixed top-4 right-4 z-50 flex max-w-xs items-center rounded-lg p-4 text-sm font-medium shadow-sm', toastStyle[variant].container]"
        role="alert"
    >
        <div :class="toastStyle[variant].icon">
            <i :class="toastStyle[variant].iconClass"></i>
        </div>
        <span class="ms-3">{{ message }}</span>
        <button @click="$emit('close')" class="ms-auto rounded-lg p-1.5 text-gray-400 hover:text-gray-900 focus:outline-none">
            <i class="fas fa-times text-sm"></i>
        </button>
    </div>
</template>
