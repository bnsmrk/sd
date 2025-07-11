<script setup lang="ts">
// import AppLayout from '@/layouts/AppLayout.vue';
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';

import { Head, useForm } from '@inertiajs/vue3';
import { FileDown, FileText, PencilLine, Send, UploadCloud } from 'lucide-vue-next';

const props = defineProps<{
    activity: {
        id: number;
        title: string;
        type: string;
        scheduled_at: string;
        description: string | null;
        file_path: string | null;
    };
}>();

const form = useForm({
    content: '',
    file: null as File | null,
});
</script>

<template>
    <Head :title="`Submit Essay - ${props.activity.title}`" />

    <AppLayoutStudent>
        <div class="min-h-screen space-y-6 bg-pink-50 px-6 py-8">
            <div class="mb-10">
                <h1 class="mb-2 flex items-center gap-3 text-4xl font-bold text-[#01006c]"><FileText class="h-8 w-8" /> Submit Essay</h1>
                <p class="text-base text-gray-600">
                    Activity: <span class="font-semibold">{{ props.activity.title }}</span>
                </p>
            </div>

            <div v-if="props.activity.description" class="mb-6 rounded-lg border-l-4 border-yellow-400 bg-yellow-50 p-6 text-gray-800 shadow-sm">
                <strong>Description:</strong> {{ props.activity.description }}
            </div>

            <div v-if="props.activity.file_path" class="mb-6 text-sm">
                <a
                    :href="`/storage/${props.activity.file_path}`"
                    target="_blank"
                    class="inline-flex items-center gap-2 text-blue-600 underline transition hover:text-blue-800"
                >
                    <FileDown class="h-4 w-4" /> Download Attached File
                </a>
            </div>

            <form
                @submit.prevent="form.post(`/activities/${props.activity.id}/essay`, { forceFormData: true })"
                class="rounded-xl border border-gray-200 bg-white p-10 shadow-lg"
            >
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block flex items-center gap-2 text-lg font-semibold text-gray-700">
                            <PencilLine class="h-5 w-5" /> Your Essay
                        </label>
                        <textarea
                            v-model="form.content"
                            rows="14"
                            class="w-full rounded-lg border border-gray-300 px-5 py-4 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-blue-300 focus:outline-none"
                            placeholder="Write your essay here..."
                            required
                        ></textarea>
                    </div>

                    <div class="flex flex-col justify-between space-y-6">
                        <div>
                            <label class="mb-2 block flex items-center gap-2 text-lg font-semibold text-gray-700">
                                <UploadCloud class="h-5 w-5" /> Upload File (Optional)
                            </label>
                            <input
                                type="file"
                                @change="(e) => (form.file = (e.target as HTMLInputElement)?.files?.[0] ?? null)"
                                class="block w-full text-sm text-gray-600 transition file:mr-4 file:rounded-md file:border-0 file:bg-[#01006c] file:px-6 file:py-2 file:text-white file:hover:bg-yellow-500 file:hover:text-black"
                            />
                        </div>

                        <div class="text-right">
                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-[#01006c] px-8 py-3 text-base font-semibold text-white shadow transition hover:bg-yellow-500 hover:text-black"
                            >
                                <Send class="h-5 w-5" /> Submit Essay
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayoutStudent>
</template>
