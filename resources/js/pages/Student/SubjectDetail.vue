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
            progress: number;
            materials: Array<{
                id: number;
                title: string;
                description: string | null;
                file_path: string;
                uploaded_by: string;
            }>;
            activities: Array<{
                id: number;
                title: string;
                type: string;
                scheduled_at: string;
                score: number | null;
                total_points: number | null;
                submitted: boolean;
                essay_score: number | null;
            }>;
        }>;
    };
    progress: number;
}>();

const openModuleId = ref<number | null>(null);

function toggleModule(id: number) {
    openModuleId.value = openModuleId.value === id ? null : id;
}

function goToActivity(activity: { id: number; type: string }) {
    if (activity.type === 'essay') {
        router.get(`/activities/${activity.id}/essay`);
    } else {
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
                <!-- Header -->
                <!-- Header -->
                <div class="cursor-pointer bg-gray-100 p-4 dark:bg-gray-800" @click="toggleModule(module.id)">
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-semibold">Module {{ index + 1 }}: {{ module.title }}</span>
                        <span class="text-sm font-medium text-blue-600">{{ module.progress }}%</span>
                    </div>
                    <div class="mt-1 h-2 w-full overflow-hidden rounded bg-gray-200 dark:bg-gray-700">
                        <div class="h-full bg-green-500 transition-all duration-300" :style="{ width: module.progress + '%' }"></div>
                    </div>
                </div>

                <!-- Content -->
                <transition name="fade">
                    <div v-if="openModuleId === module.id" class="space-y-4 bg-white p-4 dark:bg-gray-900">
                        <!-- Materials -->
                        <div v-if="module.materials.length" class="border-b pb-3">
                            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300">📄 Materials</h3>
                            <ul class="mt-2 space-y-2">
                                <li v-for="material in module.materials" :key="material.id" class="rounded border bg-gray-50 p-3 dark:bg-gray-800">
                                    <div class="text-sm font-semibold text-blue-600">
                                        <a :href="`/storage/${material.file_path}`" target="_blank" class="hover:underline">
                                            {{ material.title }}
                                        </a>
                                    </div>
                                    <div v-if="material.description" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ material.description }}
                                    </div>
                                    <div class="mt-1 text-xs text-gray-400">Uploaded by: {{ material.uploaded_by }}</div>
                                </li>
                            </ul>
                        </div>

                        <!-- Activities -->
                        <div
                            v-for="activity in module.activities"
                            :key="activity.id"
                            class="rounded border p-3"
                            :class="{
                                'cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800': !activity.score && !activity.submitted,
                                'cursor-not-allowed bg-gray-100 opacity-60 dark:bg-gray-800': activity.score || activity.submitted,
                            }"
                            @click="!activity.score && !activity.submitted ? goToActivity(activity) : null"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium">{{ activity.title }}</div>
                                    <div class="text-sm text-gray-500">{{ activity.type }} – {{ activity.scheduled_at }}</div>

                                    <!-- Quiz/Exam Score -->
                                    <div
                                        v-if="activity.type !== 'essay' && activity.score !== null && activity.total_points !== null"
                                        class="text-sm text-gray-700"
                                    >
                                        Score:
                                        <span class="font-semibold">{{ activity.score }}</span> /
                                        <span class="font-semibold">{{ activity.total_points }}</span>
                                    </div>

                                    <!-- Essay: Submitted but not graded -->
                                    <div
                                        v-else-if="activity.type === 'essay' && activity.submitted && activity.essay_score === null"
                                        class="text-sm font-medium text-yellow-600"
                                    >
                                        Not Graded
                                    </div>

                                    <!-- Essay: Graded -->
                                    <div
                                        v-else-if="activity.type === 'essay' && activity.submitted && activity.essay_score !== null"
                                        class="text-sm font-medium text-green-600"
                                    >
                                        Completed – Graded: {{ activity.essay_score }}
                                    </div>

                                    <!-- Not taken -->
                                    <div v-else class="text-sm text-gray-400 italic">Not taken yet</div>
                                </div>

                                <!-- Status badge -->
                                <div v-if="activity.score !== null || activity.submitted" class="text-xs font-semibold text-green-600">
                                    ✔ Completed
                                </div>
                            </div>
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
