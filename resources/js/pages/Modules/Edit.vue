<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, BookText, FileSignature, Pencil, School, Users } from 'lucide-vue-next';
import { computed, watch } from 'vue';

import { ref } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);

const props = defineProps<{
    module: {
        id: number;
        name: string;
        year_level_id: number;
        section_id: number;
        subject_id: number;
    };
    assignments: Array<{
        id: number;
        name: string;
        sections: Array<{ id: number; name: string }>;
        subjects: Array<{ id: number; name: string; section_ids: number[] }>;
    }>;
}>();

const form = useForm({
    name: props.module.name,
    year_level_id: props.module.year_level_id,
    section_id: props.module.section_id,
    subject_id: props.module.subject_id,
});

const selectedYearLevel = computed(() => props.assignments.find((a) => a.id === form.year_level_id));

const availableSections = computed(() => (selectedYearLevel.value ? selectedYearLevel.value.sections : []));

const filteredSubjects = computed(() => {
    if (!selectedYearLevel.value || !form.section_id) return [];
    return selectedYearLevel.value.subjects.filter((subject) => subject.section_ids.includes(form.section_id));
});

watch(
    () => form.year_level_id,
    () => {
        form.section_id = 0;
        form.subject_id = 0;
    },
);

watch(
    () => form.section_id,
    () => {
        form.subject_id = 0;
    },
);

const updateModule = () => {
    isUpdating.value = true;

    form.put(`/modules/${props.module.id}`, {
        onSuccess: () => {},
        onFinish: () => {
            setTimeout(() => {
                isUpdating.value = false;
            }, 800);
        },
    });
};
</script>

<template>
    <Head title="Edit Module" />
    <AppLayout>
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

        <div class="mx-auto w-full max-w-screen-xl space-y-6 px-6 py-8">
            <div class="flex items-center justify-between">
                <h1 class="inline-flex items-center gap-2 text-2xl font-semibold text-[#01006c]">
                    <Pencil class="h-5 w-5 text-[#01006c]" />
                    Edit Module
                </h1>
                <Link
                    href="/modules"
                    class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Back to Modules
                </Link>
            </div>

            <form @submit.prevent="updateModule" class="space-y-6">
                <div>
                    <label class="flex items-center gap-1 text-sm font-medium text-[#ff69b4]">
                        <FileSignature class="h-4 w-4 text-[#ff69b4]" />
                        Module Name
                    </label>
                    <input
                        v-model="form.name"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 text-sm focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                        required
                    />
                    <div v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="flex items-center gap-1 text-sm font-medium text-[#ff69b4]">
                        <School class="h-4 w-4 text-[#ff69b4]" />
                        Year Level
                    </label>
                    <select
                        v-model.number="form.year_level_id"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 text-sm focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                        required
                    >
                        <option value="">Select Year Level</option>
                        <option v-for="yl in props.assignments" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                    </select>
                    <div v-if="form.errors.year_level_id" class="text-sm text-red-600">{{ form.errors.year_level_id }}</div>
                </div>

                <div v-if="availableSections.length > 0">
                    <label class="flex items-center gap-1 text-sm font-medium text-[#ff69b4]">
                        <Users class="h-4 w-4 text-[#ff69b4]" />
                        Section
                    </label>
                    <select
                        v-model.number="form.section_id"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 text-sm focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                        required
                    >
                        <option value="">Select Section</option>
                        <option v-for="section in availableSections" :key="section.id" :value="section.id">
                            {{ section.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.section_id" class="text-sm text-red-600">{{ form.errors.section_id }}</div>
                </div>

                <div v-if="filteredSubjects.length > 0">
                    <label class="flex items-center gap-1 text-sm font-medium text-[#ff69b4]">
                        <BookText class="h-4 w-4 text-[#ff69b4]" />
                        Subject
                    </label>
                    <select
                        v-model.number="form.subject_id"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 text-sm focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                        required
                    >
                        <option value="">Select Subject</option>
                        <option v-for="subject in filteredSubjects" :key="subject.id" :value="subject.id">
                            {{ subject.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.subject_id" class="text-sm text-red-600">{{ form.errors.subject_id }}</div>
                </div>

                <div>
                    <button class="w-full rounded bg-[#01006c] py-2 text-white transition hover:bg-[#0d1282]" :disabled="form.processing">
                        Update
                    </button>
                </div>
            </form>
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
