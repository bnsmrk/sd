<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    assignment: { user_id: number; year_level_id: number };
    heads: { id: number; name: string }[];
    yearLevels: { id: number; name: string }[];
}>();

const form = useForm({
    user_id: props.assignment.user_id,
    year_level_id: props.assignment.year_level_id,
});
</script>

<template>
    <Head title="Edit Head Assignment" />

    <AppLayout>
        <div class="w-full space-y-6 p-6">
            <h1 class="mb-4 text-xl font-semibold text-pink-500">Edit Head Assignment</h1>

            <form @submit.prevent="form.put(`/head-assignments/${props.assignment.year_level_id}`)">
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-pink-600">Head</label>
                    <select v-model="form.user_id" class="w-full rounded border border-pink-300 px-3 py-2 text-pink-600">
                        <option value="">Select Head</option>
                        <option v-for="h in props.heads" :key="h.id" :value="h.id">{{ h.name }}</option>
                    </select>
                    <span v-if="form.errors.user_id" class="text-sm text-red-500">{{ form.errors.user_id }}</span>
                </div>

                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-pink-600">Year Level</label>
                    <select v-model="form.year_level_id" class="w-full rounded border border-pink-300 px-3 py-2 text-pink-600">
                        <option value="">Select Year Level</option>
                        <option v-for="yl in props.yearLevels" :key="yl.id" :value="yl.id">
                            {{ yl.name }}
                        </option>
                    </select>
                    <span v-if="form.errors.year_level_id" class="text-sm text-red-500">{{ form.errors.year_level_id }}</span>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <Link href="/head-assignments" class="rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600">Cancel</Link>
                    <button type="submit" class="rounded bg-pink-500 px-4 py-2 text-white hover:bg-pink-600">Update</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
