<script setup lang="ts">
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Target, FileText, CalendarDays, CheckCircle, GraduationCap } from 'lucide-vue-next';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/student-dashboard',
    },
];

const props = defineProps<{
    proficiencyTests: {
        id: number;
        title: string;
        type: string;
        scheduled_at: string;
        description: string | null;
    }[];
    takenTestIds: number[];
}>();

function isTaken(testId: number): boolean {
    return props.takenTestIds.includes(testId);
}
</script>
<template>
    <Head title="Dashboard" />

    <AppLayoutStudent :breadcrumbs="breadcrumbs">
        <div class="space-y-6 px-6 py-8 min-h-screen bg-pink-50">
            <h1 class="text-3xl font-bold text-[#01006c] flex items-center gap-2">
                <Target class="w-6 h-6 text-[#01006c]" /> Student Dashboard
            </h1>

            <!-- Proficiency Tests -->
            <div class="rounded-xl bg-white p-6 shadow-md border border-pink-200">
                <h2 class="mb-4 text-xl font-semibold text-indigo-800 flex items-center gap-2">
                    <FileText class="w-5 h-5 text-indigo-800" /> Proficiency Tests
                </h2>

                <div v-if="proficiencyTests.length === 0" class="text-sm text-gray-500 italic">
                    No proficiency tests scheduled.
                </div>

                <!-- Responsive Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="(test, index) in proficiencyTests"
                        :key="test.id"
                        class="relative overflow-hidden rounded-xl border p-5 shadow-sm bg-white transition-transform hover:scale-[1.01] hover:shadow-lg"
                    >
                        <!-- Colored top bar -->
                        <div
                            :class="[
                                'absolute top-0 left-0 h-1 w-full',
                                index % 3 === 0 ? 'bg-yellow-400' :
                                index % 3 === 1 ? 'bg-pink-400' : 'bg-indigo-400'
                            ]"
                        ></div>

                        <div class="flex flex-col justify-between gap-2 mt-1">
                            <div>
                                <h3 class="text-lg font-bold text-[#01006c]">{{ test.title }}</h3>
                                <p class="text-sm text-gray-600 flex items-center gap-1">
    <CalendarDays class="w-4 h-4 text-blue-500" /> {{ test.scheduled_at }}
</p>

                                <p class="mt-1 text-sm text-gray-500 italic" v-if="test.description">{{ test.description }}</p>
                            </div>

                            <div class="mt-2">
                                <a
    v-if="!isTaken(test.id)"
    :href="`/student/proficiency-test/${test.id}`"
    class="inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm text-white shadow hover:bg-blue-700 transition"
>
    <GraduationCap class="w-4 h-4" /> Take Test
</a>

<span
    v-else
    class="inline-flex items-center gap-2 cursor-not-allowed rounded bg-gray-400 px-4 py-2 text-sm text-white"
>
    <CheckCircle class="w-4 h-4" /> Already Taken
</span>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayoutStudent>
</template>
