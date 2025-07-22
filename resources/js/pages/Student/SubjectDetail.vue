<script setup lang="ts">
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';
import { router } from '@inertiajs/vue3';
import { BookOpen, CheckCircle, ChevronDown, Clock, FileText, ListVideo, Package, User2 } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

const isAllEmpty = computed(() => {
    return subject.modules.every((module) => module.materials.length === 0 && module.activities.length === 0);
});
const { subject } = defineProps<{
    subject: {
        id: number;
        name: string;
        teacher: string; // ✅ Add teacher here at top-level
        modules: Array<{
            id: number;
            title: string;
            progress: number;
            materials: Array<{
                id: number;
                title: string;
                description: string | null;
                file_path: string | null;
                video_path: string | null;
                video_link: string | null;
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

const openMaterials = ref<Set<number>>(new Set());
const openActivities = ref<Set<number>>(new Set());

function toggleMaterials(id: number) {
    openMaterials.value.has(id) ? openMaterials.value.delete(id) : openMaterials.value.add(id);
}

function toggleActivities(id: number) {
    openActivities.value.has(id) ? openActivities.value.delete(id) : openActivities.value.add(id);
}

function goToActivity(activity: { id: number; type: string }) {
    if (activity.type === 'essay') {
        router.get(`/activities/${activity.id}/essay`);
    } else {
        router.get(`/student/quiz/${activity.id}`);
    }
}

onMounted(() => {
    const hash = window.location.hash;
    if (hash.startsWith('#activity-')) {
        const el = document.querySelector(hash);
        if (el) {
            setTimeout(() => el.scrollIntoView({ behavior: 'smooth' }), 200);
        }
    }
});
</script>

<template>
    <AppLayoutStudent>
        <div class="min-h-screen space-y-8 bg-gray-50 px-6 py-8">
            <h1 class="flex flex-col gap-4 rounded-xl bg-white px-6 py-5 shadow sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3 text-2xl font-semibold text-indigo-800 sm:text-3xl">
                    <BookOpen class="h-8 w-8 text-sky-600" />
                    <span>Subject: {{ subject.name }}</span>
                </div>

                <div class="flex items-center gap-2 text-sm text-indigo-700">
                    <span class="inline-flex items-center gap-2 rounded-full bg-indigo-100 px-4 py-1.5 font-medium shadow-sm">
                        <User2 class="h-4 w-4 text-indigo-600" />
                        Teacher: {{ subject.teacher }}
                    </span>
                </div>
            </h1>

            <div v-if="isAllEmpty" class="text-center text-sm text-gray-500 italic">
                Please wait a moment, your teacher hasn't uploaded any materials or activities yet.
            </div>

            <div
                v-for="(module, index) in subject.modules"
                :key="module.id"
                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow hover:shadow-md"
            >
                <div class="bg-gradient-to-r from-sky-100 to-indigo-100 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 font-semibold text-gray-800">
                            <Package class="h-5 w-5 text-indigo-600" />
                            <span>Module {{ index + 1 }}: {{ module.title }}</span>
                        </div>
                        <span class="rounded border bg-white px-2 py-0.5 text-sm text-gray-600"> {{ module.progress }}% </span>
                    </div>
                    <div class="mt-3 h-2 w-full rounded bg-gray-200">
                        <div class="h-full rounded bg-indigo-400 transition-all duration-300" :style="{ width: module.progress + '%' }"></div>
                    </div>
                </div>

                <div class="cursor-pointer bg-white px-6 py-3 hover:bg-gray-50" @click="toggleMaterials(module.id)">
                    <h3 class="flex items-center justify-between text-base font-bold text-sky-700">
                        <span class="flex items-center gap-2">
                            <FileText class="h-5 w-5" />
                            Materials
                        </span>
                        <ChevronDown
                            :class="[
                                'h-5 w-5 text-sky-700 transition-transform duration-300',
                                openMaterials.has(module.id) ? 'rotate-180' : 'rotate-0',
                            ]"
                        />
                    </h3>
                </div>

                <transition name="fade">
                    <div v-if="openMaterials.has(module.id)" class="space-y-3 bg-white px-6 pb-4">
                        <ul class="space-y-3">
                            <li
                                v-for="material in module.materials"
                                :key="material.id"
                                class="rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm"
                            >
                                <div class="text-sm font-medium text-indigo-700">
                                    <a v-if="material.file_path" :href="`/storage/${material.file_path}`" target="_blank" class="hover:underline">
                                        {{ material.title }}
                                    </a>
                                    <span v-else>{{ material.title }}</span>
                                </div>

                                <div v-if="material.description" class="mt-1 text-xs text-gray-600">
                                    {{ material.description }}
                                </div>

                                <div v-if="material.video_path" class="mt-3">
                                    <div class="flex justify-center">
                                        <video class="w-full rounded-lg border" controls :src="`/storage/${material.video_path}`" />
                                    </div>
                                </div>

                                <div v-else-if="material.video_link" class="mt-3">
                                    <div class="flex justify-center">
                                        <a :href="material.video_link" target="_blank" class="text-sm text-blue-600 underline hover:text-blue-800">
                                            ▶ Watch Video
                                        </a>
                                    </div>
                                </div>

                                <div class="mt-1 text-xs text-gray-500">Uploaded by: {{ material.uploaded_by }}</div>
                            </li>
                        </ul>
                        <div v-if="module.materials.length === 0" class="mt-2 text-sm text-gray-500 italic">No materials available.</div>
                    </div>
                </transition>

                <div class="cursor-pointer bg-white px-6 py-3 hover:bg-gray-50" @click="toggleActivities(module.id)">
                    <h3 class="flex items-center justify-between text-base font-bold text-violet-700">
                        <span class="flex items-center gap-2">
                            <ListVideo class="h-5 w-5" />
                            Activities
                        </span>
                        <ChevronDown
                            :class="[
                                'h-5 w-5 text-violet-700 transition-transform duration-300',
                                openActivities.has(module.id) ? 'rotate-180' : 'rotate-0',
                            ]"
                        />
                    </h3>
                </div>

                <transition name="fade">
                    <div v-if="openActivities.has(module.id)" class="space-y-3 bg-white px-6 pb-6">
                        <div
                            v-for="activity in module.activities"
                            :key="activity.id"
                            :id="`activity-${activity.id}`"
                            class="rounded-lg border border-gray-200 bg-white p-4 transition hover:bg-gray-50"
                            :class="{
                                'cursor-pointer': !activity.score && !activity.submitted,
                                'cursor-not-allowed opacity-60': activity.score || activity.submitted,
                            }"
                            @click="!activity.score && !activity.submitted ? goToActivity(activity) : null"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-gray-800">
                                        {{ activity.title }}
                                    </div>
                                    <div class="flex items-center gap-1 text-sm text-gray-500">
                                        <Clock class="h-4 w-4 text-sky-500" />
                                        <span>{{ activity.type }} • {{ activity.scheduled_at }}</span>
                                    </div>

                                    <div v-if="activity.type !== 'essay' && activity.score !== null" class="mt-1 text-sm">
                                        <span class="inline-block rounded-full bg-green-100 px-2 py-0.5 text-xs font-bold text-green-700">
                                            Score: {{ activity.score }} / {{ activity.total_points }}
                                        </span>
                                    </div>

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

                                    <div v-else class="mt-1 text-sm text-gray-400 italic">Not taken yet</div>
                                </div>

                                <div v-if="activity.score !== null || activity.submitted">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                                        <CheckCircle class="h-4 w-4" />
                                        Completed
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div v-if="module.activities.length === 0" class="text-sm text-gray-500 italic">No activities in this module.</div>
                    </div>
                </transition>
            </div>
        </div>
    </AppLayoutStudent>
</template>
