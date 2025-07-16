<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, CalendarClock, CalendarDays, FileText, ListChecks, Send } from 'lucide-vue-next';

import { computed, ref } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);

const props = defineProps<{
    test: {
        id: number;
        title: string;
        type: string;
        year_level_id: number;
        scheduled_at: string;
        due_date: string;
        description: string | null;
    };
    yearLevels: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    title: props.test.title,
    type: props.test.type,
    year_level_id: props.test.year_level_id,
    scheduled_at: props.test.scheduled_at,
    due_date: props.test.due_date,
    description: props.test.description ?? '',
});

const submitUpdateForm = () => {
    isUpdating.value = true;
    form.put(`/proficiency-test/${props.test.id}`, {
        onFinish: () => {
            setTimeout(() => {
                isUpdating.value = false;
            }, 800);
        },
    });
};

function cancelEdit() {
    router.get('/proficiency-test');
}
</script>

<template>
    <Head title="Edit Proficiency Test" />
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

        <div class="mx-auto w-full max-w-7xl space-y-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-[#01006c]">Edit Proficiency Test</h1>
                <button
                    @click.prevent="cancelEdit"
                    class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
                >
                    <ArrowLeft class="h-4 w-4 text-gray-600" />
                    Back
                </button>
            </div>

            <form @submit.prevent="submitUpdateForm" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="col-span-1">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-[#ff69b4]">
                        <FileText class="h-4 w-4 text-[#ff69b4]" />
                        Title
                    </label>
                    <input
                        v-model="form.title"
                        type="text"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                        required
                    />
                </div>

                <div class="col-span-1">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-[#ff69b4]">
                        <ListChecks class="h-4 w-4 text-[#ff69b4]" />
                        Type
                    </label>
                    <select
                        v-model="form.type"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                        required
                    >
                        <option value="reading">Reading</option>
                        <option value="numerical">Numerical</option>
                    </select>
                </div>

                <div class="col-span-1">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-[#ff69b4]">
                        <BookOpen class="h-4 w-4 text-[#ff69b4]" />
                        Year Level
                    </label>
                    <select
                        v-model="form.year_level_id"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                        required
                    >
                        <option disabled value="">-- Choose Year Level --</option>
                        <option v-for="yl in props.yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                    </select>
                </div>

                <div class="col-span-1">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-[#ff69b4]">
                        <CalendarClock class="h-4 w-4 text-[#ff69b4]" />
                        Scheduled At
                    </label>
                    <input
                        v-model="form.scheduled_at"
                        type="datetime-local"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                        required
                    />
                </div>

                <div class="col-span-1">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-[#ff69b4]">
                        <CalendarDays class="h-4 w-4 text-[#ff69b4]" />
                        Due Date
                    </label>
                    <input
                        v-model="form.due_date"
                        type="datetime-local"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                    />
                </div>

                <div class="col-span-full">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-[#ff69b4]">
                        <FileText class="h-4 w-4 text-[#ff69b4]" />
                        Description
                    </label>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                    ></textarea>
                </div>

                <div class="col-span-full flex justify-end gap-4 pt-4">
                    <button
                        type="button"
                        @click.prevent="cancelEdit"
                        class="inline-flex items-center gap-2 rounded bg-gray-500 px-4 py-2 text-sm text-white hover:bg-gray-700"
                    >
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex items-center gap-2 rounded bg-[#01006c] px-4 py-2 text-sm text-white hover:bg-[#0d1282]">
                        <Send class="h-4 w-4" />
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
