<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const { section, yearLevels } = defineProps<{
    section: { id: number; name: string; year_level_id: number };
    yearLevels: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    name: section.name,
    year_level_id: section.year_level_id,
});

const submit = () => {
    form.put(`/sections/${section.id}`);
};
</script>

<template>
    <Head title="Edit Section" />
    <AppLayout>
        <div class="mx-auto max-w-md p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">Edit Section</h2>
                <Link href="/sections" class="text-blue-600 hover:underline">Back</Link>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="mb-1 block font-semibold">Section Name</label>
                    <input v-model="form.name" type="text" class="w-full rounded border p-2" />
                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="mb-1 block font-semibold">Year Level</label>
                    <select v-model="form.year_level_id" class="w-full rounded border p-2">
                        <option value="">Select Year Level</option>
                        <option v-for="level in yearLevels" :key="level.id" :value="level.id">
                            {{ level.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.year_level_id" class="mt-1 text-sm text-red-600">{{ form.errors.year_level_id }}</div>
                </div>

                <button type="submit" class="rounded bg-green-600 px-4 py-2 text-white">Update</button>
            </form>
        </div>
    </AppLayout>
</template>
