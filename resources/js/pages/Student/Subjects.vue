<script setup lang="ts">
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';
import { BookOpen, ArrowRight, User2 } from 'lucide-vue-next';
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
        <div class="space-y-6 px-6 py-8 min-h-screen bg-pink-50">
            <h1 class="text-3xl font-bold text-[#01006c] flex items-center gap-2">
                <BookOpen class="w-6 h-6 text-[#01006c]" /> My Enrolled Subjects
            </h1>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(subject, index) in subjects"
                    :key="subject.id"
                    @click="goToSubject(subject.id)"
                    class="group relative cursor-pointer overflow-hidden rounded-xl border border-pink-200 bg-white p-5 shadow-md transition-all hover:scale-[1.02] hover:shadow-xl"
                >
                    <!-- Gradient bar for visual accent -->
                    <div
                        :class="[
                            'absolute top-0 left-0 h-1 w-full',
                            index % 3 === 0 ? 'bg-yellow-400' :
                            index % 3 === 1 ? 'bg-pink-400' : 'bg-indigo-400'
                        ]"
                    ></div>

                    <div class="flex flex-col justify-between h-full">
                        <div class="pb-4">
                            <h2 class="text-lg font-semibold text-[#01006c] group-hover:text-indigo-700">
                                {{ subject.name }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 group-hover:text-[#01006c]">
                                <User2 class="w-4 h-4 text-[#01006c]" /> <span class="font-medium">{{ subject.teacher }}</span>
                            </p>
                        </div>

                        <div class="pt-2">
                            <div class="text-indigo-800 font-semibold text-sm mb-1 flex items-center gap-1">
                                <span class="text-yellow-500">➡</span> View Activities
                            </div>
                            <p class="text-xs text-gray-500 group-hover:text-[#01006c]">
                                Click to see activities and progress
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayoutStudent>
</template>

