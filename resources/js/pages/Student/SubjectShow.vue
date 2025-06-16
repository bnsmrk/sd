<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const { subjects } = defineProps<{
    subjects: Array<{
        id: number;
        name: string;
        activities: Array<{
            id: number;
            title: string;
            type: string;
            date: string;
        }>;
    }>;
}>();

const selectedSubjectId = ref<number | null>(null);
</script>

<template>
    <AppLayout>
        <div class="space-y-4 p-6">
            <h1 class="text-2xl font-bold">My Enrolled Subjects</h1>

            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    v-for="subject in subjects"
                    :key="subject.id"
                    class="relative aspect-video cursor-pointer overflow-hidden rounded-xl border border-sidebar-border/70 p-4 hover:bg-gray-100 dark:hover:bg-gray-800"
                    @click="selectedSubjectId = subject.id"
                >
                    <h2 class="text-center text-lg font-semibold">{{ subject.name }}</h2>
                </div>
            </div>

            <div v-if="selectedSubjectId" class="mt-8 rounded-xl border border-sidebar-border/70 p-4">
                <h2 class="mb-4 text-xl font-bold">
                    Activities for
                    {{ subjects.find((s) => s.id === selectedSubjectId)?.name }}
                </h2>

                <ul class="space-y-2">
                    <li
                        v-for="act in subjects.find((s) => s.id === selectedSubjectId)?.activities"
                        :key="act.id"
                        class="rounded-lg border p-3 hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        <Link :href="`/student/quiz/${act.id}`" class="block">
                            <div class="font-medium">{{ act.title }}</div>
                            <div class="text-sm text-gray-500">{{ act.type }} – {{ act.date }}</div>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
