<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Book, Users, LayoutGrid, User, ClipboardList, GraduationCap, MonitorSmartphone } from 'lucide-vue-next';

const props = defineProps<{
    counts: {
        yearLevels: number;
        sections: number;
        subjects: number;
        teachers: number;
        students: number;
        heads: number;
        ict: number;
    };
}>();

const iconMap = {
    yearLevels: Book,
    sections: LayoutGrid,
    subjects: ClipboardList,
    teachers: User,
    students: GraduationCap,
    heads: Users,
    ict: MonitorSmartphone,
};

const labels = {
    yearLevels: 'Year Levels',
    sections: 'Sections',
    subjects: 'Subjects',
    teachers: 'Teachers',
    students: 'Students',
    heads: 'Heads',
    ict: 'ICT Personnel',
};

const statsKeys = Object.keys(props.counts) as (keyof typeof props.counts)[];
</script>

<template>
    <Head title="Principal Dashboard" />
    <AppLayout :breadcrumbs="[{ title: 'Principal Dashboard', href: '/principal-dashboard' }]">
        <div class="p-6 space-y-8">
            <h1 class="text-3xl font-bold text-[#01006c]">📊 Principal Dashboard</h1>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="key in statsKeys"
                    :key="key"
                    class="rounded-xl border border-indigo-200 bg-white p-6 shadow hover:shadow-md transition"
                >
                    <component :is="iconMap[key]" class="h-8 w-8 text-indigo-600 mb-3" />
                    <h2 class="text-2xl font-bold text-gray-800">{{ props.counts[key] }}</h2>
                    <p class="text-sm text-gray-500">{{ labels[key] }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
