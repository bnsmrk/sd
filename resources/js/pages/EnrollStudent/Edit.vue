<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { computed } from 'vue';

import { ref } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);

const props = defineProps<{
    enrollment: {
        id: number;
        user: { id: number; name: string };
        year_level_id: number;
        section_id: number | null;
    };
    yearLevels: { id: number; name: string }[];
    sections: { id: number; name: string; year_level_id: number }[];
    subjects: { id: number; name: string; section_id: number | null; year_level_id: number }[];
}>();

const form = useForm({
    user_id: props.enrollment.user.id,
    year_level_id: props.enrollment.year_level_id,
    section_id: props.enrollment.section_id ?? '',
});

const filteredSections = computed(() => props.sections.filter((s) => s.year_level_id === form.year_level_id));

const filteredSubjects = computed(() => props.subjects.filter((s) => s.year_level_id === form.year_level_id));

const submitForm = () => {
    isUpdating.value = true;

    form.put(`/enroll/${props.enrollment.id}`, {
        onFinish: () => {
            setTimeout(() => {
                isUpdating.value = false;
            }, 800);
        },
    });
};
</script>

<template>
    <Head title="Edit Enrollment" />
    <AppLayout :breadcrumbs="[{ title: 'Enrollments', href: '/enroll' }]">
        <div v-if="isLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="flex flex-col items-center gap-4">
                <div class="relative h-16 w-16">
                    <div class="animate-spin-slow-cw absolute inset-0 rounded-full border-4 border-blue-600 border-t-transparent"></div>

                    <div class="animate-spin-slow-ccw absolute inset-2 rounded-full border-4 border-yellow-400 border-t-transparent"></div>

                    <div class="animate-spin-fast-cw absolute inset-4 rounded-full border-4 border-pink-500 border-t-transparent"></div>
                </div>

                <div class="text-center">
                    <span class="block animate-pulse text-base font-semibold text-[#01006c]">Processing Request...</span>
                    <span class="text-xs text-[#01006c]/70">This may take a moment</span>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-8 p-6 md:grid-cols-2">
            <div class="space-y-6">
                <div>
                    <Link
                        href="/enroll"
                        class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
                    >
                        <ArrowLeft class="h-4 w-4" /> Back
                    </Link>
                </div>

                <h2 class="text-xl font-bold text-[#01006c]">📝 Edit Student Enrollment</h2>

                <div>
                    <label class="mb-1 block text-sm font-medium text-[#ff69b4]">🎓 Year Level</label>
                    <select
                        v-model="form.year_level_id"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                    >
                        <option disabled value="">Select Year Level</option>
                        <option v-for="yl in yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                    </select>
                    <p class="text-sm text-red-500" v-if="form.errors.year_level_id">{{ form.errors.year_level_id }}</p>
                </div>

                <div v-if="filteredSections.length">
                    <label class="mb-1 block text-sm font-medium text-[#ff69b4]">🏫 Section</label>
                    <select
                        v-model="form.section_id"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                    >
                        <option disabled value="">Select Section</option>
                        <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                    <p class="text-sm text-red-500" v-if="form.errors.section_id">{{ form.errors.section_id }}</p>
                </div>

                <button
                    @click="submitForm"
                    :disabled="form.processing"
                    class="inline-flex w-full items-center justify-center gap-2 rounded bg-[#01006c] py-2 text-white transition hover:bg-[#0d1282]"
                >
                    <Save class="h-4 w-4" /> Update Enrollment
                </button>
            </div>

            <div class="rounded border border-[#01006c] bg-white p-4 shadow">
                <h3 class="mb-3 text-lg font-semibold text-[#01006c]">📚 Subjects in Selected Year Level</h3>
                <ul v-if="filteredSubjects.length" class="ml-4 list-disc space-y-1 text-sm text-gray-700">
                    <li v-for="subject in filteredSubjects" :key="subject.id">
                        {{ subject.name }}
                        <span v-if="subject.section_id" class="text-gray-500">(Section ID: {{ subject.section_id }})</span>
                    </li>
                </ul>
                <p v-else class="text-sm text-gray-500 italic">No subjects found for selected year level.</p>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@keyframes spin-cw {
    to {
        transform: rotate(360deg);
    }
}

@keyframes spin-ccw {
    to {
        transform: rotate(-360deg);
    }
}

.animate-spin-slow-cw {
    animation: spin-cw 2s linear infinite;
}

.animate-spin-slow-ccw {
    animation: spin-ccw 3s linear infinite;
}

.animate-spin-fast-cw {
    animation: spin-cw 1s linear infinite;
}
</style>
