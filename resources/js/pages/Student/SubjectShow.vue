<script setup lang="ts">
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';

import { router } from '@inertiajs/vue3';

defineProps<{
    subject: { id: number; name: string };
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
}>();

const goTo = (activity: { id: number; type: string }) => {
    if (activity.type === 'quiz') {
        router.get(`/student/quiz/${activity.id}`);
    }
};
</script>

<template>
    <AppLayoutStudent>
        <div class="space-y-6 p-6">
            <h1 class="text-2xl font-bold">{{ subject.name }}</h1>

            <div v-for="mod in modules" :key="mod.id" class="space-y-2 rounded-xl border p-4">
                <h2 class="text-lg font-semibold text-blue-700">{{ mod.title }}</h2>
                
                <ul class="space-y-1">
                    <li v-for="act in mod.activities" :key="act.id" class="cursor-pointer hover:underline" @click="goTo(act)">
                        {{ act.type.toUpperCase() }} – {{ act.title }} ({{ act.scheduled_at }})
                    </li>
                </ul>
            </div>
        </div>
    </AppLayoutStudent>
</template>
