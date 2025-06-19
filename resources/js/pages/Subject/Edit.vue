<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps<{
    subject: { id: number; name: string; year_level_id: number };
    yearLevels: { id: number; name: string }[];
}>();

const form = useForm({
    name: props.subject.name,
    year_level_id: props.subject.year_level_id,
});

const selectedYearName = ref(props.yearLevels.find((y) => y.id === props.subject.year_level_id)?.name || '');

watch(
    () => form.year_level_id,
    (newId) => {
        const level = props.yearLevels.find((y) => y.id === Number(newId));
        selectedYearName.value = level?.name || '';
    },
    { immediate: true },
);
</script>

<template>
    <Head title="Edit Subject" />
    <AppLayout>
        <div class="mx-auto max-w-xl p-4">
            <h2 class="mb-4 text-xl font-bold">Edit Subject</h2>

            <form @submit.prevent="form.put(`/subjects/${props.subject.id}`)">
                <!-- Year Level -->
                <div class="mb-4">
                    <label class="block font-semibold">Year Level</label>
                    <select v-model="form.year_level_id" class="w-full rounded border px-3 py-2">
                        <option value="">Select Year Level</option>
                        <option v-for="level in props.yearLevels" :key="level.id" :value="level.id">
                            {{ level.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.year_level_id" class="text-sm text-red-500">{{ form.errors.year_level_id }}</div>
                </div>

                <!-- Subject Name -->
                <div class="mb-4">
                    <label class="block font-semibold">Subject Name</label>
                    <input v-model="form.name" type="text" class="w-full rounded border px-3 py-2" :class="{ 'border-red-500': form.errors.name }" />
                    <div v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</div>
                </div>

                <div class="flex justify-end gap-4">
                    <Link href="/subjects" class="text-gray-600 hover:underline">Cancel</Link>
                    <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
