<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    heads: { id: number; name: string }[];
    yearLevels: { id: number; name: string }[];
}>();

const isSubmitting = ref(false);

const form = useForm({
    user_id: '',
    year_level_id: '',
});

const submitForm = () => {
    isSubmitting.value = true;
    form.post('/head-assignments/assign-to-all', {
        preserveScroll: true,
        onFinish: () => {
            setTimeout(() => {
                isSubmitting.value = false;
            }, 800);
        },
    });
};

const isLoading = computed(() => isSubmitting.value);
</script>

<template>
    <Head title="Assign Head" />

    <AppLayout>
        <div v-if="isLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="flex flex-col items-center gap-4">
                <div class="relative h-16 w-16">
                    <div class="animate-spin-slow-cw absolute inset-0 rounded-full border-4 border-blue-600 border-t-transparent"></div>
                    <div class="animate-spin-slow-ccw absolute inset-2 rounded-full border-4 border-yellow-400 border-t-transparent"></div>
                    <div class="animate-spin-fast-cw absolute inset-4 rounded-full border-4 border-pink-500 border-t-transparent"></div>
                </div>
                <div class="text-center">
                    <span class="block animate-pulse text-base font-semibold text-[#01006c]">Assigning Head...</span>
                    <span class="text-xs text-[#01006c]/70">Please wait</span>
                </div>
            </div>
        </div>

        <div class="w-full space-y-6 p-6">
            <h1 class="mb-6 text-xl font-semibold text-pink-500">Assign Head to Year Level</h1>

            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Horizontal row for both fields -->

                <!-- Head -->
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-pink-600">Head</label>
                    <select v-model="form.user_id" class="w-full rounded border border-pink-300 px-3 py-2 text-pink-600">
                        <option value="">Select Head</option>
                        <option v-for="h in props.heads" :key="h.id" :value="h.id">{{ h.name }}</option>
                    </select>
                    <span v-if="form.errors.user_id" class="text-sm text-red-500">{{ form.errors.user_id }}</span>
                </div>

                <!-- Year Level -->
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-pink-600">Year Level</label>
                    <select v-model="form.year_level_id" class="w-full rounded border border-pink-300 px-3 py-2 text-pink-600">
                        <option value="">Select Year Level</option>
                        <option v-for="yl in props.yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                    </select>
                    <span v-if="form.errors.year_level_id" class="text-sm text-red-500">{{ form.errors.year_level_id }}</span>
                </div>

                <!-- Buttons below -->
                <div class="mt-6 flex justify-end gap-2">
                    <Link href="/head-assignments" class="rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600">Cancel</Link>
                    <button type="submit" class="rounded bg-pink-500 px-4 py-2 text-white hover:bg-pink-600">Assign</button>
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
