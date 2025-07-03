<script setup lang="ts">
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';

import { Head, useForm } from '@inertiajs/vue3';

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
        <div class="mx-auto max-w-2xl space-y-6 p-6">
            <h1 class="text-xl font-bold">Submit Essay for: {{ props.activity.title }}</h1>

            <div v-if="props.activity.description" class="rounded bg-gray-100 p-3">
                <strong>Description:</strong> {{ props.activity.description }}
            </div>

            <div v-if="props.activity.file_path" class="text-sm text-blue-600">
                <a :href="`/storage/${props.activity.file_path}`" target="_blank" class="underline">Download Attached File</a>
            </div>

            <form @submit.prevent="form.post(`/activities/${props.activity.id}/essay`, { forceFormData: true })" class="space-y-4">
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
    </AppLayoutStudent>
</template>
