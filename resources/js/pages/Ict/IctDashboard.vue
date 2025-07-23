<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Chart, registerables } from 'chart.js';
import { Shield, User, Users } from 'lucide-vue-next';
import { onMounted } from 'vue';

Chart.register(...registerables);

const props = defineProps<{
    userSummary: {
        students: number;
        teachers: number;
        admins: number;
    };
    monthlyStats: {
        labels: string[];
        students: number[];
        teachers: number[];
    };
}>();

const hoverBorders = ['hover:border-[#ff69b4]', 'hover:border-[#01006c]', 'hover:border-[#ffc60b]'];

function getCardClasses(index: number) {
    const borderHover = hoverBorders[index % hoverBorders.length];
    return `relative overflow-hidden rounded-xl border border-pink-200 bg-white p-5 shadow-md transition-all hover:scale-[1.02] hover:shadow-xl ${borderHover}`;
}

onMounted(() => {
    const ctx = document.getElementById('chart') as HTMLCanvasElement;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: props.monthlyStats.labels,
            datasets: [
                {
                    label: 'Students',
                    data: props.monthlyStats.students,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                },
                {
                    label: 'Teachers',
                    data: props.monthlyStats.teachers,
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        },
    });
});
</script>

<template>
    <Head title="ICT Dashboard" />

    <AppLayout :breadcrumbs="[{ title: 'ICT Dashboard', href: '/ict-dashboard' }]">
        <div class="min-h-screen space-y-6 bg-pink-50 px-6 py-8">
            <h1 class="text-3xl font-bold text-[#01006c]">ICT Dashboard</h1>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div :class="getCardClasses(0)">
                    <div class="absolute top-0 left-0 h-1 w-full bg-yellow-400"></div>
                    <div class="flex h-full flex-col justify-between">
                        <div class="pb-4">
                            <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]"><Users class="h-5 w-5" /> Students</h2>
                            <p class="mt-2 text-4xl font-bold text-blue-800">{{ props.userSummary.students }}</p>
                        </div>
                    </div>
                </div>

                <div :class="getCardClasses(1)">
                    <div class="absolute top-0 left-0 h-1 w-full bg-pink-400"></div>
                    <div class="flex h-full flex-col justify-between">
                        <div class="pb-4">
                            <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]"><User class="h-5 w-5" /> Teachers</h2>
                            <p class="mt-2 text-4xl font-bold text-green-800">{{ props.userSummary.teachers }}</p>
                        </div>
                    </div>
                </div>

                <div :class="getCardClasses(2)">
                    <div class="absolute top-0 left-0 h-1 w-full bg-indigo-400"></div>
                    <div class="flex h-full flex-col justify-between">
                        <div class="pb-4">
                            <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]"><Shield class="h-5 w-5" /> Admins</h2>
                            <p class="mt-2 text-4xl font-bold text-purple-800">{{ props.userSummary.admins }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 h-[400px] rounded-xl bg-white p-6 shadow">
                <h2 class="mb-2 text-lg font-semibold text-indigo-800">📊 Monthly User Statistics</h2>
                <canvas id="chart" class="h-full w-full"></canvas>
            </div>
        </div>
    </AppLayout>
</template>
