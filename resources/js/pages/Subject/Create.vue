<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface YearLevel {
    id: number;
    name: string;
}

interface Section {
    id: number;
    name: string;
    year_level_id: number;
}

const { yearLevels, sections } = defineProps<{
    yearLevels: YearLevel[];
    sections: Section[];
}>();

const form = useForm({
    name: '',
    year_level_id: '' as string | number,
    section_id: '' as string | number,
});

const filteredSections = ref<Section[]>([]);

watch(
    () => form.year_level_id,
    (newYearLevelId) => {
        if (newYearLevelId) {
            filteredSections.value = sections.filter((section) => section.year_level_id === Number(newYearLevelId));
        } else {
            filteredSections.value = [];
        }
        form.section_id = ''; // Reset section selection
    },
    { immediate: true }, // Run immediately to handle initial state
);
</script>

<template>
    <Head title="Add Subject" />
    <AppLayout>
        <div class="mx-auto max-w-xl p-4">
            <h2 class="mb-4 text-xl font-bold">Add Subject</h2>

            <form @submit.prevent="form.post('/subjects')">
                <!-- Subject Name -->
                <div class="mb-4">
                    <label class="mb-1 block font-medium">Subject Name</label>
                    <input v-model="form.name" type="text" class="w-full rounded border px-3 py-2" :class="{ 'border-red-500': form.errors.name }" />
                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">
                        {{ form.errors.name }}
                    </div>
                </div>

                <!-- Year Level Dropdown -->
                <div class="mb-4">
                    <label class="mb-1 block font-medium">Year Level</label>
                    <select
                        v-model="form.year_level_id"
                        class="w-full rounded border px-3 py-2"
                        :class="{ 'border-red-500': form.errors.year_level_id }"
                    >
                        <option value="">Select Year Level</option>
                        <option v-for="level in yearLevels" :key="level.id" :value="level.id">
                            {{ level.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.year_level_id" class="mt-1 text-sm text-red-500">
                        {{ form.errors.year_level_id }}
                    </div>
                </div>

                <!-- Section Dropdown -->
                <div class="mb-4">
                    <label class="mb-1 block font-medium">Section</label>
                    <select
                        v-model="form.section_id"
                        class="w-full rounded border px-3 py-2"
                        :disabled="!form.year_level_id"
                        :class="{ 'border-red-500': form.errors.section_id }"
                    >
                        <option value="">Select Section</option>
                        <option v-for="section in filteredSections" :key="section.id" :value="section.id">
                            {{ section.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.section_id" class="mt-1 text-sm text-red-500">
                        {{ form.errors.section_id }}
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end">
                    <Link href="/subjects" class="mr-4 text-gray-600 hover:underline"> Cancel </Link>
                    <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
