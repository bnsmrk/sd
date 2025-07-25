<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Layers, Save, User } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);

const props = defineProps<{
    assignment: {
        id: number;
        user_id: number;
        year_level_id: number;
    };
    teachers: Array<{ id: number; name: string }>;
    yearLevels: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    user_id: props.assignment.user_id,
    year_level_id: props.assignment.year_level_id,
});

function submitForm() {
    isUpdating.value = true;

    form.put(`/teacher-assignments/${props.assignment.id}`, {
        onSuccess: () => {},
        onFinish: () => {
            setTimeout(() => {
                isUpdating.value = false;
            }, 1000);
        },
    });
}
</script>

<template>
    <Head title="Edit Teacher Assignment" />
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

        <div class="mx-auto w-full max-w-4xl space-y-6 p-6">
            <div class="mb-6">
                <Link
                    href="/teacher-assignments"
                    class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] hover:bg-[#ffc60b]"
                >
                    <ArrowLeft class="h-4 w-4" /> Back
                </Link>
            </div>

            <h1 class="mb-6 text-2xl font-bold text-[#01006c]">✏️ Edit Teacher Assignment</h1>

            <form @submit.prevent="submitForm" class="space-y-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="mb-2 flex items-center gap-1 text-sm font-semibold text-[#ff69b4]"> <User class="h-4 w-4" /> Teacher </label>
                        <select
                            v-model="form.user_id"
                            required
                            class="w-full rounded border-2 border-[#01006c] bg-white px-3 py-2 text-[#01006c] focus:border-[#ffc60b] focus:outline-none"
                        >
                            <option value="">Select a Teacher</option>
                            <option v-for="t in props.teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
                        </select>
                        <p v-if="form.errors.user_id" class="mt-1 text-sm text-red-600">{{ form.errors.user_id }}</p>
                    </div>

                    <div>
                        <label class="mb-2 flex items-center gap-1 text-sm font-semibold text-[#ff69b4]">
                            <Layers class="h-4 w-4" /> Year Level
                        </label>
                        <select
                            v-model="form.year_level_id"
                            required
                            class="w-full rounded border-2 border-[#01006c] bg-white px-3 py-2 text-[#01006c] focus:border-[#ffc60b] focus:outline-none"
                        >
                            <option value="">Select a Year Level</option>
                            <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                        </select>
                        <p v-if="form.errors.year_level_id" class="mt-1 text-sm text-red-600">{{ form.errors.year_level_id }}</p>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-md bg-[#01006c] px-6 py-2 text-sm font-semibold text-white hover:bg-[#e858a1]"
                    >
                        <Save class="h-4 w-4" /> Update Assignment
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
