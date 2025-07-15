<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

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
        scheduled_at: string;
        description: string | null;
        file_path: string | null;
    };
}>();

const form = useForm({
    content: '',
    file: null as File | null,
});
function submitEssay() {
    if (!form.content) {
        alert('Essay content is required.');
        return;
    }

    isCreating.value = true;

    form.post(`/activities/${props.activity.id}/essay`, {
        forceFormData: true,
        onSuccess: () => {},
        onFinish: () => {
            setTimeout(() => {
                isCreating.value = false;
            }, 1000);
        },
    });
}
</script>

<template>
    <Head :title="`Submit Essay - ${props.activity.title}`" />
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

        <div class="mx-auto max-w-2xl space-y-6 p-6">
            <h1 class="text-xl font-bold">Submit Essay for: {{ props.activity.title }}</h1>

            <div v-if="props.activity.description" class="rounded bg-gray-100 p-3">
                <strong>Description:</strong> {{ props.activity.description }}
            </div>

            <div v-if="props.activity.file_path" class="text-sm text-blue-600">
                <a :href="`/storage/${props.activity.file_path}`" target="_blank" class="underline">Download Attached File</a>
            </div>

            <form @submit.prevent="submitEssay" class="space-y-4">
                <div>
                    <label class="mb-1 block font-medium">Your Essay</label>
                    <textarea v-model="form.content" rows="5" class="w-full rounded border px-3 py-2"></textarea>
                </div>

                <div>
                    <label class="mb-1 block font-medium">Upload File (optional)</label>
                    <input type="file" @change="(e) => (form.file = (e.target as HTMLInputElement)?.files?.[0] ?? null)" />
                </div>

                <button class="mt-2 rounded bg-blue-600 px-4 py-2 text-white">Submit</button>
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
