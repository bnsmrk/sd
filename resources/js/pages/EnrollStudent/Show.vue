<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    enrollment: {
        id: number;
        user: { name: string };
        year_level: { name: string };
        section: { name: string } | null;
        subject: { name: string };
    };
}>();
</script>

<template>
    <Head title="Enrollment Details" />
    <AppLayout :breadcrumbs="[{ title: 'Enrollments', href: '/enroll' }]">
        <div class="mx-auto max-w-2xl space-y-4 p-6">
            <h2 class="text-2xl font-bold">Enrollment Details</h2>

            <div class="space-y-2 text-lg">
                <p><strong>Student:</strong> {{ enrollment.user.name }}</p>
                <p><strong>Year Level:</strong> {{ enrollment.year_level.name }}</p>
                <p><strong>Section:</strong> {{ enrollment.section?.name ?? '—' }}</p>
                <p><strong>Subject:</strong> {{ enrollment.subject.name }}</p>
            </div>

            <div class="pt-6">
                <Link href="/enroll" class="inline-block rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"> Back to List </Link>
            </div>
        </div>
    </AppLayout>
</template>
