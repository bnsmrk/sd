<script setup lang="ts">
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';
import { FileDown, PencilLine, UploadCloud, Send, FileText } from 'lucide-vue-next';import { Head, useForm } from '@inertiajs/vue3';

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
        <div class="mx-auto max-w-6xl px-8 py-12">
            <!-- Header -->
            <div class="mb-10">
                <h1 class="text-4xl font-bold text-[#01006c] flex items-center gap-3 mb-2">
                    <FileText class="h-8 w-8" /> Submit Essay
                </h1>
                <p class="text-base text-gray-600">
                    Activity: <span class="font-semibold">{{ props.activity.title }}</span>
                </p>
            </div>

            <!-- Description -->
            <div
                v-if="props.activity.description"
                class="mb-6 rounded-lg border-l-4 border-yellow-400 bg-yellow-50 p-6 text-gray-800 shadow-sm"
            >
                <strong>Description:</strong> {{ props.activity.description }}
            </div>

            <!-- File Download -->
            <div v-if="props.activity.file_path" class="mb-6 text-sm">
                <a
                    :href="`/storage/${props.activity.file_path}`"
                    target="_blank"
                    class="inline-flex items-center gap-2 text-blue-600 underline hover:text-blue-800 transition"
                >
                    <FileDown class="h-4 w-4" /> Download Attached File
                </a>
            </div>

            <!-- Form -->
            <form
                @submit.prevent="form.post(`/activities/${props.activity.id}/essay`, { forceFormData: true })"
                class="rounded-xl bg-white p-10 shadow-lg border border-gray-200"
            >
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <!-- Left: Essay Input -->
                    <div>
                        <label class="block text-lg font-semibold text-gray-700 mb-2 flex items-center gap-2">
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

                    <!-- Right: Upload + Submit -->
                    <div class="flex flex-col justify-between space-y-6">
                        <!-- Upload -->
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <UploadCloud class="h-5 w-5" /> Upload File (Optional)
                            </label>
                            <input
                                type="file"
                                @change="(e) => (form.file = (e.target as HTMLInputElement)?.files?.[0] ?? null)"
                                class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-[#01006c] file:px-6 file:py-2 file:text-white file:hover:bg-yellow-500 file:hover:text-black transition"
                            />
                        </div>

                        <!-- Submit -->
                        <div class="text-right">
                            <button
                                type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-[#01006c] px-8 py-3 text-white text-base font-semibold shadow hover:bg-yellow-500 hover:text-black transition"
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

