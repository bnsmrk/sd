<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    submission: {
        id: number;
        content: string | null;
        file_path: string | null;
        score: number | null;
        graded: string;
        user: { id: number; name: string };
        activity: { id: number; title: string };
    };
}>();

const score = ref<number | null>(props.submission.score ?? null);

function saveScore() {
    if (score.value !== null) {
        router.post(
            `/submissions/${props.submission.id}/score`,
            { score: score.value },
            {
                preserveScroll: true,
            },
        );
    }
}
</script>

<template>
    <AppLayout>
        <Head :title="`View Essay - ${props.submission.user.name}`" />
        <div class="mx-auto max-w-4xl space-y-6 p-6">
            <h1 class="text-xl font-bold">Essay by {{ props.submission.user.name }} - {{ props.submission.activity.title }}</h1>

            <div class="text-sm text-gray-600">
                Status:
                <span :class="submission.graded === 'Graded' ? 'text-green-600' : 'text-red-600'">
                    {{ submission.graded }}
                </span>
            </div>

            <div v-if="submission.content" class="rounded bg-gray-100 p-4 whitespace-pre-wrap">
                {{ submission.content }}
            </div>

            <div v-else class="text-gray-600 italic">No essay content submitted.</div>

            <div v-if="submission.file_path">
                <a :href="`/storage/${submission.file_path}`" target="_blank" class="text-blue-600 underline"> Download Attached File </a>
            </div>

            <div class="mt-6 space-y-2">
                <label class="block text-sm font-medium">Score </label>
                <input v-model.number="score" type="phone" min="0" max="100" class="block w-24 rounded border px-2 py-1" />

                <button @click="saveScore" class="mt-2 rounded bg-blue-600 px-4 py-2 text-white">Save Score</button>
            </div>
        </div>
    </AppLayout>
</template>
