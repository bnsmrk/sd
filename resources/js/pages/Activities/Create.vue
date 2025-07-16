<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

import { ArrowLeft, CalendarClock, ClipboardList, FileText, FileUp, Layers, Tag } from 'lucide-vue-next';

import { ref } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);

const props = defineProps<{
    modules: Array<{
        id: number;
        name: string;
        year_level: { name: string };
        section: { name: string };
        subject: { name: string };
    }>;
}>();

const form = useForm<{
    title: string;
    type: string;
    module_id: string;
    scheduled_at: string;
    due_date: string;
    description: string;
    file: File | null;
}>({
    title: '',
    type: 'quiz',
    module_id: '',
    scheduled_at: '',
    due_date: '',
    description: '',
    file: null,
});
const createActivity = () => {
    isCreating.value = true;

    form.post('/activities', {
        forceFormData: true,
        onFinish: () => {
            setTimeout(() => {
                isCreating.value = false;
            }, 800);
        },
    });
};

const selectedModule = computed(() => props.modules.find((m) => m.id === Number(form.module_id)));
</script>

<template>
    <Head title="Create Activity" />
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
                <h1 class="text-2xl font-bold text-[#01006c]">Create Activity</h1>
                <Link
                    href="/activities"
                    class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
                >
                    <ArrowLeft class="h-4 w-4 text-[#ff69b4]" />
                    Back to Activities
                </Link>
            </div>

            <form @submit.prevent="createActivity" class="space-y-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="ttext-[#ff69b4] flex items-center gap-1 text-sm font-medium">
                            <Tag class="h-4 w-4 text-[#ff69b4]" /> Title
                        </label>
                        <input
                            v-model="form.title"
                            class="border-[#01006c]bg-white w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                            required
                        />
                    </div>

                    <div>
                        <label class="ttext-[#ff69b4] flex items-center gap-1 text-sm font-medium">
                            <ClipboardList class="h-4 w-4 text-[#ff69b4]" /> Type
                        </label>
                        <select
                            v-model="form.type"
                            class="border-[#01006c]bg-white w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                            required
                        >
                            <option value="quiz">Quiz</option>
                            <option value="exam">Exam</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>

                    <div>
                        <label class="ttext-[#ff69b4] flex items-center gap-1 text-sm font-medium">
                            <Layers class="h-4 w-4 text-[#ff69b4]" /> Module
                        </label>
                        <select
                            v-model="form.module_id"
                            class="border-[#01006c]bg-white w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                            required
                        >
                            <option value="">-- Choose Module --</option>
                            <option v-for="m in props.modules" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3" v-if="selectedModule">
                    <div>
                        <label class="ttext-[#ff69b4] block text-sm font-medium">Year Level</label>
                        <select disabled class="border-[#01006c]bg-gray-100 w-full rounded border border-[#01006c] bg-white px-3 py-2 text-gray-600">
                            <option>{{ selectedModule.year_level.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="ttext-[#ff69b4] block text-sm font-medium">Section</label>
                        <select disabled class="border-[#01006c]bg-gray-100 w-full rounded border border-[#01006c] bg-white px-3 py-2 text-gray-600">
                            <option>{{ selectedModule.section.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="ttext-[#ff69b4] block text-sm font-medium">Subject</label>
                        <select disabled class="border-[#01006c]bg-gray-100 w-full rounded border border-[#01006c] bg-white px-3 py-2 text-gray-600">
                            <option>{{ selectedModule.subject.name }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="ttext-[#ff69b4] flex items-center gap-1 text-sm font-medium">
                            <CalendarClock class="h-4 w-4 text-[#ff69b4]" /> Schedule
                        </label>
                        <input
                            type="datetime-local"
                            v-model="form.scheduled_at"
                            class="border-[#01006c]bg-white w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                            required
                        />
                    </div>
                    <div>
                        <label class="ttext-[#ff69b4] flex items-center gap-1 text-sm font-medium">
                            <CalendarClock class="h-4 w-4 text-[#ff69b4]" /> Due Date
                        </label>
                        <input
                            type="datetime-local"
                            v-model="form.due_date"
                            class="border-[#01006c]bg-white w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                        />
                    </div>
                </div>

                <div v-if="form.type === 'essay'" class="space-y-4">
                    <div>
                        <label class="ttext-[#ff69b4] flex items-center gap-1 text-sm font-medium">
                            <FileText class="h-4 w-4 text-[#ff69b4]" /> Description
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="border-[#01006c]bg-white w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                        />
                    </div>
                    <div class="rounded border border-[#01006c] bg-white p-4">
                        <label class="mb-2 flex items-center gap-1 text-sm font-medium text-[#ff69b4]">
                            <FileUp class="h-4 w-4 text-[#ff69b4]" /> Attach File
                        </label>
                        <input
                            type="file"
                            @change="(e) => (form.file = (e.target as HTMLInputElement)?.files?.[0] ?? null)"
                            class="w-full rounded border border-gray-300 px-3 py-2 text-sm text-[#01006c] file:mr-4 file:rounded file:border-0 file:bg-[#ffc60b] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#01006c] hover:file:bg-yellow-400 focus:border-[#ffc60b] focus:ring-[#ffc60b]"
                        />
                    </div>
                </div>

                <button
                    class="mt-4 inline-flex items-center justify-center gap-2 rounded bg-[#01006c] px-6 py-2 text-white transition hover:bg-[#ffc60b] hover:text-[#01006c]"
                    :disabled="form.processing"
                >
                    <Save class="h-4 w-4" />
                    Save
                </button>
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
