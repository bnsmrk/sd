<script setup lang="ts">
//import AppLayout from '@/layouts/AppLayout.vue';
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';
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
    <AppLayoutStudent>
        <div class="space-y-6 p-6">
            <h1 class="text-3xl font-bold text-indigo-700">📘 Subject: {{ subject.name }}</h1>

            <div
                v-for="(module, index) in subject.modules"
                :key="module.id"
                class="overflow-hidden rounded-2xl border border-gray-200 shadow-md transition-all hover:shadow-xl dark:border-gray-700"
            >
                <!-- Header -->
                <div class="cursor-pointer bg-gradient-to-r from-indigo-500 to-blue-600 p-5 text-white" @click="toggleModule(module.id)">
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-semibold"> 📦 Module {{ index + 1 }}: {{ module.title }} </span>
                        <span class="text-sm font-medium">{{ module.progress }}%</span>
                    </div>
                    <div class="bg-opacity-50 mt-2 h-2 w-full overflow-hidden rounded bg-blue-200">
                        <div class="h-full bg-green-400 transition-all duration-300" :style="{ width: module.progress + '%' }"></div>
                    </div>
                </div>

                <!-- Content -->
                <transition name="fade">
                    <div v-if="openModuleId === module.id" class="space-y-4 bg-white p-5 dark:bg-gray-900">
                        <!-- Materials -->
                        <div v-if="module.materials.length" class="border-b pb-4">
                            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300">📄 Materials</h3>
                            <ul class="mt-2 space-y-3">
                                <li
                                    v-for="material in module.materials"
                                    :key="material.id"
                                    class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                                >
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
                            class="rounded-xl border border-gray-200 p-4 shadow-sm transition hover:shadow-lg dark:border-gray-700"
                            :class="{
                                'cursor-pointer bg-white hover:bg-indigo-50 dark:hover:bg-gray-800': !activity.score && !activity.submitted,
                                'cursor-not-allowed bg-gray-100 opacity-60 dark:bg-gray-800': activity.score || activity.submitted,
                            }"
                            @click="!activity.score && !activity.submitted ? goToActivity(activity) : null"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-gray-700 dark:text-white">{{ activity.title }}</div>
                                    <div class="text-sm text-gray-500">{{ activity.type }} • {{ activity.scheduled_at }}</div>

                                    <!-- Scores -->
                                    <div v-if="activity.type !== 'essay' && activity.score !== null" class="mt-1 text-sm">
                                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-700">
                                            Score: {{ activity.score }} / {{ activity.total_points }}
                                        </span>
                                    </div>

                                    <!-- Essay Status -->
                                    <div
                                        v-else-if="activity.type === 'essay' && activity.submitted && activity.essay_score === null"
                                        class="mt-1 text-sm text-yellow-600"
                                    >
                                        🕓 Not Graded
                                    </div>
                                    <div
                                        v-else-if="activity.type === 'essay' && activity.submitted && activity.essay_score !== null"
                                        class="mt-1 text-sm text-green-600"
                                    >
                                        📝 Graded: {{ activity.essay_score }}
                                    </div>

                                    <!-- Not taken -->
                                    <div v-else class="mt-1 text-sm text-gray-400 italic">Not taken yet</div>
                                </div>

                                <div v-if="activity.score !== null || activity.submitted">
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700"> ✔ Completed </span>
                                </div>
                            </div>
                        </div>

                        <div v-if="module.activities.length === 0" class="text-sm text-gray-400 italic">No activities in this module.</div>
                    </div>
                </transition>
            </div>
        </div>
    </AppLayoutStudent>
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
