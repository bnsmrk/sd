<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    assignment: {
        id: number;
        teacher: { id: number; name: string };
        year_level: { id: number; name: string };
        section: { id: number; name: string } | null;
        subject: { id: number; name: string };
    };
}>();
</script>

<template>
    <Head title="View Assignment" />
    <AppLayout :breadcrumbs="[{ title: 'Assignments', href: '/teacher-assignments' }]">
        <div class="mx-auto w-full max-w-7xl space-y-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-lg font-bold text-[#01006c]">📄 Teacher Assignment Details</h1>
                <Link
                    href="/teacher-assignments"
                    class="inline-flex items-center gap-1 rounded-md border border-[#01006c] bg-white px-3 py-1.5 text-sm font-semibold text-[#01006c] hover:bg-[#ffc60b]"
                >
                    ← Back to List
                </Link>
            </div>

            <div class="grid grid-cols-1 gap-4 rounded-lg border-2 border-[#01006c] bg-white p-6 shadow-sm md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#ff69b4]">👤 Teacher</label>
                    <p class="text-lg font-semibold text-[#01006c]">{{ props.assignment.teacher.name }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-[#ff69b4]">🎓 Year Level</label>
                    <p class="text-lg font-semibold text-[#01006c]">{{ props.assignment.year_level.name }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-[#ff69b4]">🏫 Section</label>
                    <p class="text-lg font-semibold text-[#01006c]">
                        {{ props.assignment.section?.name ?? '— (Shared Subject)' }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-[#ff69b4]">📘 Subject</label>
                    <p class="text-lg font-semibold text-[#01006c]">{{ props.assignment.subject.name }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
