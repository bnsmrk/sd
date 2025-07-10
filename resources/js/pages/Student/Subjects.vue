<script setup lang="ts">
// import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';
import AppLayout from '@/layouts/AppLayout.vue';

import { router } from '@inertiajs/vue3';
import { BookOpen, User2 } from 'lucide-vue-next';

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
</script>

<template>
    <AppLayout>
        <div class="min-h-screen space-y-6 bg-pink-50 px-6 py-8">
            <h1 class="flex items-center gap-2 text-3xl font-bold text-[#01006c]">
                <BookOpen class="h-6 w-6 text-[#01006c]" /> My Enrolled Subjects
            </h1>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(subject, index) in subjects"
                    :key="subject.id"
                    @click="goToSubject(subject.id)"
                    class="group relative cursor-pointer overflow-hidden rounded-xl border border-pink-200 bg-white p-5 shadow-md transition-all hover:scale-[1.02] hover:shadow-xl"
                >
                    <div
                        :class="[
                            'absolute top-0 left-0 h-1 w-full',
                            index % 3 === 0 ? 'bg-yellow-400' : index % 3 === 1 ? 'bg-pink-400' : 'bg-indigo-400',
                        ]"
                    ></div>

                    <div class="flex h-full flex-col justify-between">
                        <div class="pb-4">
                            <h2 class="text-lg font-semibold text-[#01006c] group-hover:text-indigo-700">
                                {{ subject.name }}
                            </h2>
                            <p class="mt-1 flex items-center gap-1 text-sm text-gray-600 group-hover:text-[#01006c]">
                                <User2 class="h-4 w-4 text-[#01006c]" />
                                <span class="font-medium">{{ subject.teacher }}</span>
                            </p>
                        </div>

                        <div class="pt-2">
                            <div class="mb-1 flex items-center gap-1 text-sm font-semibold text-indigo-800">
                                <span class="text-yellow-500">➡</span> View Activities
                            </div>
                            <p class="text-xs text-gray-500 group-hover:text-[#01006c]">Click to see activities and progress</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
