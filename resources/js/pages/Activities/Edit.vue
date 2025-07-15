<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CalendarClock, ClipboardList, FileText, FileUp, Layers, Save, Tag } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);
// const showFlash = ref(false);

const props = defineProps<{
    activity: {
        id: number;
        title: string;
        type: string;
        module_id: number;
        scheduled_at: string | null;
        due_date: string | null;
        description: string | null;
        file_path: string | null;
    };
    modules: Array<{
        id: number;
        name: string;
        year_level: { name: string };
        section: { name: string };
        subject: { name: string };
    }>;
}>();

const form = useForm({
    _method: 'put',
    title: props.activity.title,
    type: props.activity.type,
    module_id: props.activity.module_id,
    scheduled_at: props.activity.scheduled_at?.substring(0, 16) ?? '',
    due_date: props.activity.due_date?.substring(0, 16) ?? '',
    description: props.activity.description ?? '',
    file: null as File | null,
});
const editActivity = () => {
    isUpdating.value = true;

    form.post(`/activities/${props.activity.id}`, {
        forceFormData: true,
        onSuccess: () => {},
        onFinish: () => {
            setTimeout(() => {
                isUpdating.value = false;
            }, 1500);
        },
    });
};

const selectedModule = computed(() => props.modules.find((m) => m.id === Number(form.module_id)));
</script>
<template>
    <Head title="Edit Activity" />
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
                <h1 class="text-2xl font-bold text-[#01006c]">Edit Activity</h1>
                <Link
                    href="/activities"
                    class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
                >
                    <ArrowLeft class="h-4 w-4 text-[#ff69b4]" />
                    Back to Activities
                </Link>
            </div>

            <form @submit.prevent="editActivity" class="space-y-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-[#ff69b4]">
                            <Tag class="h-4 w-4 text-[#ff69b4]" /> Title
                        </label>
                        <input
                            v-model="form.title"
                            class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                            required
                        />
                    </div>

                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-[#ff69b4]">
                            <ClipboardList class="h-4 w-4 text-[#ff69b4]" /> Type
                        </label>
                        <select
                            v-model="form.type"
                            class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                            required
                        >
                            <option value="quiz">Quiz</option>
                            <option value="exam">Exam</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>

                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-[#ff69b4]">
                            <Layers class="h-4 w-4 text-[#ff69b4]" /> Module
                        </label>
                        <select
                            v-model="form.module_id"
                            class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                            required
                        >
                            <option value="">-- Choose Module --</option>
                            <option v-for="m in props.modules" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3" v-if="selectedModule">
                    <div>
                        <label class="text-sm font-medium text-[#ff69b4]">Year Level</label>
                        <select disabled class="w-full rounded border border-[#01006c] bg-white px-3 py-2 text-gray-600">
                            <option>{{ selectedModule.year_level.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-[#ff69b4]">Section</label>
                        <select disabled class="w-full rounded border border-[#01006c] bg-gray-100 bg-white px-3 py-2 text-gray-600">
                            <option>{{ selectedModule.section.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-[#ff69b4]">Subject</label>
                        <select disabled class="w-full rounded border border-[#01006c] bg-gray-100 bg-white px-3 py-2 text-gray-600">
                            <option>{{ selectedModule.subject.name }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-[#ff69b4]">
                            <CalendarClock class="h-4 w-4 text-[#ff69b4]" /> Scheduled At
                        </label>
                        <input
                            type="datetime-local"
                            v-model="form.scheduled_at"
                            class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                            required
                        />
                    </div>
                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-[#ff69b4]">
                            <CalendarClock class="h-4 w-4 text-[#ff69b4]" /> Due Date
                        </label>
                        <input
                            type="datetime-local"
                            v-model="form.due_date"
                            class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                        />
                    </div>
                </div>

                <div v-if="form.type === 'essay'" class="space-y-4">
                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-[#ff69b4]">
                            <FileText class="h-4 w-4 text-[#ff69b4]" /> Description
                        </label>
                        <textarea
                            v-model="form.description"
                            class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                            rows="4"
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
                        <div v-if="props.activity.file_path" class="mt-3 text-sm text-gray-600">
                            Current file:
                            <a
                                :href="`/storage/${props.activity.file_path}`"
                                target="_blank"
                                class="font-medium text-blue-600 underline hover:text-[#ff69b4]"
                            >
                                View
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        class="mt-4 inline-flex items-center gap-2 rounded bg-[#01006c] px-6 py-2 text-white transition hover:bg-[#ffc60b] hover:text-[#01006c]"
                        :disabled="form.processing"
                    >
                        <Save class="h-4 w-4" />
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
