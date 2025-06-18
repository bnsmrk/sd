<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const { subject } = defineProps<{
    subject: {
        id: number;
        name: string;
        modules: Array<{
            id: number;
            title: string;
            activities: Array<{
                id: number;
                title: string;
                type: string;
                scheduled_at: string;
            }>;
        }>;
    };
}>();

// Track which module is open
const openModuleId = ref<number | null>(null);

function toggleModule(id: number) {
    openModuleId.value = openModuleId.value === id ? null : id;
}

function goToActivity(activity: { id: number; type: string }) {
    if (activity.type === 'quiz') {
        router.get(`/student/quiz/${activity.id}`);
    }
}
</script>

<template>
    <AppLayout>
        <div class="space-y-6 p-6">
            <h1 class="text-2xl font-bold">Subject: {{ subject.name }}</h1>

            <div
                v-for="(module, index) in subject.modules"
                :key="module.id"
                class="overflow-hidden rounded border border-gray-300 shadow-sm dark:border-gray-700"
            >
                <!-- Collapsible Header -->
                <div class="flex cursor-pointer items-center justify-between bg-gray-100 p-4 dark:bg-gray-800" @click="toggleModule(module.id)">
                    <span class="text-lg font-semibold">Module {{ index + 1 }}: {{ module.title }}</span>
                    <svg
                        class="h-5 w-5 transform transition-transform duration-200"
                        :class="{ 'rotate-180': openModuleId === module.id }"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>

                <!-- Collapsible Body -->
                <transition name="fade">
                    <div v-if="openModuleId === module.id" class="space-y-2 bg-white p-4 dark:bg-gray-900">
                        <div
                            v-for="activity in module.activities"
                            :key="activity.id"
                            class="cursor-pointer rounded border p-3 hover:bg-gray-100 dark:hover:bg-gray-800"
                            @click="goToActivity(activity)"
                        >
                            <div class="font-medium">{{ activity.title }}</div>
                            <div class="text-sm text-gray-500">{{ activity.type }} – {{ activity.scheduled_at }}</div>
                        </div>
                        <div v-if="module.activities.length === 0" class="text-sm text-gray-400 italic">No activities in this module.</div>
                    </div>
                </transition>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-5px);
}
</style>
