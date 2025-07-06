<script setup lang="ts">
//import AppLayout from '@/layouts/AppLayout.vue';
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';
import { router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { BookOpen, FileText, Package, CheckCircle, Clock } from 'lucide-vue-next';

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
onMounted(() => {
    const hash = window.location.hash;
    if (hash.startsWith('#activity-')) {
        const el = document.querySelector(hash);
        if (el) {
            setTimeout(() => el.scrollIntoView({ behavior: 'smooth' }), 200); // allow Inertia to fully load content first
        }
    }
});
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
        <div class="space-y-8 px-6 py-8 bg-gray-50 min-h-screen">
            <h1 class="text-3xl font-bold text-indigo-800 flex items-center gap-2">
                <BookOpen class="h-6 w-6 text-sky-600" />
                <span>Subject: {{ subject.name }}</span>
            </h1>

            <div
                v-for="(module, index) in subject.modules"
                :key="module.id"
                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow hover:shadow-md"
            >
                <!-- Module Header -->
                <div
                    class="cursor-pointer bg-gradient-to-r from-sky-100 to-indigo-100 px-6 py-4"
                    @click="toggleModule(module.id)"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-gray-800 font-semibold">
                            <Package class="h-5 w-5 text-indigo-600" />
                            <span>Module {{ index + 1 }}: {{ module.title }}</span>
                        </div>
                        <span class="text-sm text-gray-600 bg-white border px-2 py-0.5 rounded">
                            {{ module.progress }}%
                        </span>
                    </div>
                    <div class="mt-3 h-2 w-full rounded bg-gray-200">
                        <div
                            class="h-full rounded bg-indigo-400 transition-all duration-300"
                            :style="{ width: module.progress + '%' }"
                        ></div>
                    </div>
                </div>

                <!-- Module Content -->
                <transition name="fade">
                    <div v-if="openModuleId === module.id" class="space-y-6 px-6 py-5 bg-white">
                        <!-- Materials -->
                        <div v-if="module.materials.length" class="border-b border-gray-200 pb-4">
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                <FileText class="h-5 w-5 text-sky-500" />
                                Materials
                            </h3>
                            <ul class="mt-4 space-y-3">
                                <li
                                    v-for="material in module.materials"
                                    :key="material.id"
                                    class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm"
                                >
                                    <div class="text-sm font-medium text-indigo-700">
                                        <a
                                            :href="`/storage/${material.file_path}`"
                                            target="_blank"
                                            class="hover:underline"
                                        >
                                            {{ material.title }}
                                        </a>
                                    </div>
                                    <div v-if="material.description" class="mt-1 text-xs text-gray-600">
                                        {{ material.description }}
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        Uploaded by: {{ material.uploaded_by }}
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Activities -->
                        <div
                            v-for="activity in module.activities"
                            :key="activity.id"
                            :id="`activity-${activity.id}`"
                            class="rounded-lg border border-gray-200 p-4 bg-white transition hover:bg-gray-50"
                            :class="{
                                'cursor-pointer': !activity.score && !activity.submitted,
                                'cursor-not-allowed opacity-60': activity.score || activity.submitted,
                            }"
                            @click="!activity.score && !activity.submitted ? goToActivity(activity) : null"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-gray-800">{{ activity.title }}</div>
                                    <div class="text-sm text-gray-500 flex items-center gap-1">
                                        <Clock class="h-4 w-4 text-sky-500" />
                                        <span>{{ activity.type }} • {{ activity.scheduled_at }}</span>
                                    </div>

                                    <!-- Score Display -->
                                    <div
                                        v-if="activity.type !== 'essay' && activity.score !== null"
                                        class="mt-1 text-sm"
                                    >
                                        <span
                                            class="inline-block rounded-full bg-green-100 px-2 py-0.5 text-xs font-bold text-green-700"
                                        >
                                            Score: {{ activity.score }} / {{ activity.total_points }}
                                        </span>
                                    </div>

                                    <!-- Essay Status -->
                                    <div
                                        v-else-if="activity.type === 'essay' && activity.submitted && activity.essay_score === null"
                                        class="mt-1 text-sm text-yellow-600"
                                    >
                                        🕓 Awaiting Grade
                                    </div>
                                    <div
                                        v-else-if="activity.type === 'essay' && activity.submitted && activity.essay_score !== null"
                                        class="mt-1 text-sm text-green-600"
                                    >
                                        📝 Graded: {{ activity.essay_score }}
                                    </div>

                                    <!-- Not Taken -->
                                    <div
                                        v-else
                                        class="mt-1 text-sm italic text-gray-400"
                                    >
                                        Not taken yet
                                    </div>
                                </div>

                                <div v-if="activity.score !== null || activity.submitted">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700"
                                    >
                                        <CheckCircle class="h-4 w-4" />
                                        Completed
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div v-if="module.activities.length === 0" class="text-sm italic text-gray-500">
                            No activities in this module.
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </AppLayoutStudent>
</template>



<!-- <style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-5px);
}
</style> -->
