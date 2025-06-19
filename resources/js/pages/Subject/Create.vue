<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface YearLevel {
    id: number;
    name: string;
}

const { yearLevels } = defineProps<{ yearLevels: YearLevel[] }>();

const form = useForm({
    year_level_id: '' as string | number,
    general_subject: '',
    shared_subjects: [''] as string[],
    strand_subjects: {
        ICT: [''],
        NailCare: [''],
    },
});

const selectedYearName = ref('');

watch(
    () => form.year_level_id,
    (id) => {
        const level = yearLevels.find((y) => y.id === Number(id));
        selectedYearName.value = level?.name || '';
        form.general_subject = '';
        form.shared_subjects = [''];
        form.strand_subjects = { ICT: [''], NailCare: [''] };
    },
    { immediate: true },
);

const addTo = (arr: string[]) => arr.push('');
const removeFrom = (arr: string[], index: number) => arr.splice(index, 1);
</script>

<template>
    <Head title="Create Subject" />
    <AppLayout>
        <div class="mx-auto max-w-2xl p-4">
            <h1 class="mb-6 text-2xl font-bold">Add Subjects</h1>

            <form @submit.prevent="form.post('/subjects')">
                <!-- Year Level -->
                <div class="mb-4">
                    <label class="block font-semibold">Select Year Level</label>
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
                    <div v-if="form.errors.year_level_id" class="text-sm text-red-500">{{ form.errors.year_level_id }}</div>
                </div>

                <!-- Grade 7–10 -->
                <div v-if="/Grade (7|8|9|10)/.test(selectedYearName)" class="mb-6">
                    <label class="block font-semibold">Subject Name</label>
                    <input
                        v-model="form.general_subject"
                        type="text"
                        placeholder="e.g., Science"
                        class="w-full rounded border px-3 py-2"
                        :class="{ 'border-red-500': form.errors.general_subject }"
                    />
                    <div v-if="form.errors.general_subject" class="text-sm text-red-500">{{ form.errors.general_subject }}</div>
                </div>

                <!-- Grade 11–12 -->
                <div v-if="/Grade (11|12)/.test(selectedYearName)">
                    <!-- Shared Subjects -->
                    <div class="mb-6">
                        <label class="mb-2 block font-semibold">Shared Subjects (e.g., Math, English)</label>
                        <div v-for="(subject, index) in form.shared_subjects" :key="'shared-' + index" class="mb-2 flex gap-2">
                            <input v-model="form.shared_subjects[index]" type="text" class="flex-1 rounded border px-3 py-2" />
                            <button
                                v-if="form.shared_subjects.length > 1"
                                @click.prevent="removeFrom(form.shared_subjects, index)"
                                class="text-red-500"
                            >
                                ✕
                            </button>
                        </div>
                        <button @click.prevent="addTo(form.shared_subjects)" class="text-sm text-blue-600">+ Add Shared Subject</button>
                    </div>

                    <!-- Strand: ICT -->
                    <div class="mb-6">
                        <label class="mb-2 block font-semibold">ICT Strand Subjects</label>
                        <div v-for="(subject, index) in form.strand_subjects.ICT" :key="'ict-' + index" class="mb-2 flex gap-2">
                            <input v-model="form.strand_subjects.ICT[index]" type="text" class="flex-1 rounded border px-3 py-2" />
                            <button
                                v-if="form.strand_subjects.ICT.length > 1"
                                @click.prevent="removeFrom(form.strand_subjects.ICT, index)"
                                class="text-red-500"
                            >
                                ✕
                            </button>
                        </div>
                        <button @click.prevent="addTo(form.strand_subjects.ICT)" class="text-sm text-blue-600">+ Add ICT Subject</button>
                    </div>

                    <!-- Strand: Nail Care -->
                    <div class="mb-6">
                        <label class="mb-2 block font-semibold">Nail Care Strand Subjects</label>
                        <div v-for="(subject, index) in form.strand_subjects.NailCare" :key="'nail-' + index" class="mb-2 flex gap-2">
                            <input v-model="form.strand_subjects.NailCare[index]" type="text" class="flex-1 rounded border px-3 py-2" />
                            <button
                                v-if="form.strand_subjects.NailCare.length > 1"
                                @click.prevent="removeFrom(form.strand_subjects.NailCare, index)"
                                class="text-red-500"
                            >
                                ✕
                            </button>
                        </div>
                        <button @click.prevent="addTo(form.strand_subjects.NailCare)" class="text-sm text-blue-600">+ Add Nail Care Subject</button>
                    </div>
                </div>

                <!-- Buttons -->
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
