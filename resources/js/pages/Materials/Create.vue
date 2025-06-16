<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface YearLevel {
    id: number;
    name: string;
}
interface Section {
    id: number;
    name: string;
    year_level_id: number;
}
interface Subject {
    id: number;
    name: string;
    year_level_id: number;
    section_id: number;
}

const props = defineProps<{
    yearLevels: YearLevel[];
    sections: Section[];
    subjects: Subject[];
}>();

const selectedYearLevel = ref('');
const selectedSection = ref('');
const selectedSubject = ref('');

const form = useForm({
    title: '',
    year_level: '',
    section: '',
    subject: '',
    file: null as File | null,
});

const errors = ref({
    title: '',
    year_level: '',
    section: '',
    subject: '',
    file: '',
});

const filteredSections = computed(() => {
    const level = props.yearLevels.find((y) => y.name === selectedYearLevel.value);
    return level ? props.sections.filter((s) => s.year_level_id === level.id) : [];
});

const filteredSubjects = computed(() => {
    const level = props.yearLevels.find((y) => y.name === selectedYearLevel.value);
    const section = props.sections.find((s) => s.name === selectedSection.value);
    if (level && section) {
        return props.subjects.filter((sub) => sub.year_level_id === level.id && sub.section_id === section.id);
    }
    return [];
});

function submitForm() {
    errors.value = {
        title: '',
        year_level: '',
        section: '',
        subject: '',
        file: '',
    };

    if (!form.title) errors.value.title = 'Title is required';
    if (!selectedYearLevel.value) errors.value.year_level = 'Year level is required';
    if (!selectedSection.value) errors.value.section = 'Section is required';
    if (!selectedSubject.value) errors.value.subject = 'Subject is required';
    if (!form.file) errors.value.file = 'Please select a file';

    if (!Object.values(errors.value).some(Boolean)) {
        const data = new FormData();
        data.append('title', form.title);
        data.append('year_level', selectedYearLevel.value);
        data.append('section', selectedSection.value);
        data.append('subject', selectedSubject.value);
        if (form.file) {
            data.append('file', form.file);
        }

        router.post('/materials', data);
    }
}
</script>

<template>
    <Head title="Upload Material" />
    <AppLayout>
        <div class="mx-auto max-w-xl space-y-6 p-6">
            <h2 class="text-xl font-bold">Upload Learning Material</h2>

            <!-- Title -->
            <div>
                <label class="block font-medium">Title</label>
                <input type="text" v-model="form.title" class="w-full rounded border p-2" placeholder="Enter material title" />
                <p v-if="errors.title" class="text-sm text-red-500">{{ errors.title }}</p>
            </div>

            <!-- Year Level -->
            <div>
                <label class="block font-medium">Year Level</label>
                <select v-model="selectedYearLevel" class="w-full rounded border p-2">
                    <option value="" disabled>Select Year Level</option>
                    <option v-for="y in props.yearLevels" :key="y.id" :value="y.name">{{ y.name }}</option>
                </select>
                <p v-if="errors.year_level" class="text-sm text-red-500">{{ errors.year_level }}</p>
            </div>

            <!-- Section -->
            <div>
                <label class="block font-medium">Section</label>
                <select v-model="selectedSection" class="w-full rounded border p-2">
                    <option value="" disabled>Select Section</option>
                    <option v-for="s in filteredSections" :key="s.id" :value="s.name">{{ s.name }}</option>
                </select>
                <p v-if="errors.section" class="text-sm text-red-500">{{ errors.section }}</p>
            </div>

            <!-- Subject -->
            <div>
                <label class="block font-medium">Subject</label>
                <select v-model="selectedSubject" class="w-full rounded border p-2">
                    <option value="" disabled>Select Subject</option>
                    <option v-for="s in filteredSubjects" :key="s.id" :value="s.name">{{ s.name }}</option>
                </select>
                <p v-if="errors.subject" class="text-sm text-red-500">{{ errors.subject }}</p>
            </div>

            <!-- File Upload -->
            <div>
                <label class="block font-medium">Upload File</label>
                <input
                    type="file"
                    class="w-full"
                    accept=".pdf,.doc,.docx,.ppt,.pptx"
                    @change="
                        (e) => {
                            const target = e.target as HTMLInputElement;
                            if (target?.files?.[0]) {
                                form.file = target.files[0];
                            }
                        }
                    "
                />
                <p v-if="errors.file" class="text-sm text-red-500">{{ errors.file }}</p>
            </div>

            <!-- Submit -->
            <button @click="submitForm" class="w-full rounded bg-blue-600 px-4 py-2 text-white">Upload Material</button>
        </div>
    </AppLayout>
</template>
