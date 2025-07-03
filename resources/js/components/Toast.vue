<script setup lang="ts">
import { onMounted } from 'vue';
const emit = defineEmits(['close']);

onMounted(() => {
    setTimeout(() => {
        emit('close');
    }, 5000);
});
defineProps<{
    message: string | null;
    variant: 'success' | 'danger' | 'warning';
}>();

const toastStyle = {
    success: {
        container: 'bg-gradient-to-r from-teal-100 via-teal-200 to-teal-300 border-l-4 border-teal-700 p-4 shadow-xl',
        icon: 'inline-flex items-center justify-center w-8 h-8 bg-teal-200 text-teal-700 rounded-full',
        iconClass: 'fas fa-check-circle text-xl',
    },
    danger: {
        container: 'bg-gradient-to-r from-red-100 via-red-200 to-red-300 border-l-4 border-red-700 p-4 shadow-xl',
        icon: 'inline-flex items-center justify-center w-8 h-8 bg-red-200 text-red-700 rounded-full',
        iconClass: 'fas fa-times-circle text-xl',
    },
    warning: {
        container: 'bg-gradient-to-r from-yellow-100 via-yellow-200 to-yellow-300 border-l-4 border-yellow-700 p-4 shadow-xl',
        icon: 'inline-flex items-center justify-center w-8 h-8 bg-yellow-200 text-yellow-700 rounded-full',
        iconClass: 'fas fa-exclamation-triangle text-xl',
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
