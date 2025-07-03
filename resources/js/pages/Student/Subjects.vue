<script setup lang="ts">
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';

import { router } from '@inertiajs/vue3';

const { subjects } = defineProps<{
    subjects: Array<{
        id: number;
        name: string;
        teacher: string;
    }>;
}>();

function goToSubject(id: number) {
    router.get(`/student/subjects/${id}`);
}

const gradients = [
    'from-pink-100 to-pink-200',
    'from-yellow-100 to-yellow-200',
    'from-blue-100 to-blue-200',
    'from-pink-200 to-yellow-100',
    'from-yellow-200 to-pink-100',
    'from-blue-200 to-yellow-100',
];

const hoverBorders = ['hover:border-[#ff69b4]', 'hover:border-[#01006c]', 'hover:border-[#ffc60b]'];

function getCardClasses(index: number) {
    const gradient = gradients[index % gradients.length];
    const borderHover = hoverBorders[index % hoverBorders.length];
    return `bg-gradient-to-br ${gradient} border border-gray-300 transition-all duration-300 hover:scale-105 hover:bg-[#ff69b4]/30 ${borderHover}`;
}
</script>

<template>
    <AppLayoutStudent>
        <div class="space-y-4 p-6">
            <h1 class="text-2xl font-bold text-[#01006c]">My Enrolled Subjects</h1>
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    v-for="(subject, index) in subjects"
                    :key="subject.id"
                    @click="goToSubject(subject.id)"
                    class="group relative transform cursor-pointer overflow-hidden rounded-xl"
                    :class="getCardClasses(index)"
                >
                    <div class="rounded-xl p-5 shadow-xl">
                        <div class="pb-3">
                            <h2 class="text-base font-semibold text-[#01006c] group-hover:text-[#01006c]">
                                {{ subject.name }}
                            </h2>
                            <p class="text-xs text-[#01006c] opacity-70 group-hover:text-[#01006c]">Teacher: {{ subject.teacher }}</p>
                        </div>
                        <div class="text-2xl font-semibold text-[#01006c] group-hover:text-[#01006c]">View</div>
                        <p class="text-xs text-gray-600 group-hover:text-[#01006c] dark:text-gray-300">Click to see activities</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayoutStudent>
</template>
