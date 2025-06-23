<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';

const { subjects } = defineProps<{
    subjects: Array<{
        id: number;
        name: string;
    }>;
}>();

function goToSubject(id: number) {
    router.get(`/student/subjects/${id}`);
}
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
                    @click="goToSubject(subject.id)"
                >
                    <h2 class="text-center text-lg font-semibold">{{ subject.name }}</h2>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
