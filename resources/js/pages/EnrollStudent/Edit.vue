<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ArrowLeft, Save } from 'lucide-vue-next';
const props = defineProps<{
    enrollment: {
        id: number;
        user: { id: number; name: string };
        year_level_id: number;
        section_id: number | null;
    };
    yearLevels: { id: number; name: string }[];
    sections: { id: number; name: string; year_level_id: number }[];
    subjects: { id: number; name: string; section_id: number | null; year_level_id: number }[];
}>();

const form = useForm({
    user_id: props.enrollment.user.id,
    year_level_id: props.enrollment.year_level_id,
    section_id: props.enrollment.section_id ?? '',
});

const filteredSections = computed(() => props.sections.filter((s) => s.year_level_id === form.year_level_id));

const filteredSubjects = computed(() => props.subjects.filter((s) => s.year_level_id === form.year_level_id));

const submitForm = () => {
    form.put(`/enroll/${props.enrollment.id}`);
};
</script>

<template>
    <Head title="Edit Enrollment" />
    <AppLayout :breadcrumbs="[{ title: 'Enrollments', href: '/enroll' }]">
        <div class="mx-auto max-w-5xl p-6">
            <!-- Back Button -->
            <div class="mb-6">
                <Link
                    href="/enroll"
                    class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
                >
                    <ArrowLeft class="w-4 h-4" /> Back
                </Link>
            </div>

            <h2 class="mb-4 text-xl font-bold text-[#01006c]">📝 Edit Student Enrollment</h2>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <!-- LEFT COLUMN -->
                <div class="space-y-6">
                    <!-- Year Level -->
                    <div>
                        <label class="mb-1 block font-medium text-[#ff69b4]">🎓 Year Level</label>
                        <select v-model="form.year_level_id" class="w-full rounded border-2 border-[#01006c] p-2 focus:border-[#ffc60b]">
                            <option disabled value="">Select Year Level</option>
                            <option v-for="yl in yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                        </select>
                        <p class="text-sm text-red-500" v-if="form.errors.year_level_id">{{ form.errors.year_level_id }}</p>
                    </div>

                    <!-- Section -->
                    <div v-if="filteredSections.length">
                        <label class="mb-1 block font-medium text-[#ff69b4]">🏫 Section</label>
                        <select v-model="form.section_id" class="w-full rounded border-2 border-[#01006c] p-2 focus:border-[#ffc60b]">
                            <option disabled value="">Select Section</option>
                            <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                        <p class="text-sm text-red-500" v-if="form.errors.section_id">{{ form.errors.section_id }}</p>
                    </div>

                    <!-- Submit Button -->
                    <button
                        @click="submitForm"
                        class="inline-flex w-full items-center justify-center gap-2 rounded bg-[#01006c] py-2 text-white hover:bg-[#0d1282]"
                        :disabled="form.processing"
                    >
                        <Save class="w-4 h-4" /> Update Enrollment
                    </button>
                </div>

                <!-- RIGHT COLUMN: Subjects -->
                <div class="rounded border border-[#01006c] bg-gray-50 p-4 shadow">
                    <h3 class="mb-3 text-lg font-bold text-[#01006c]">📚 Subjects in Selected Year Level</h3>
                    <ul v-if="filteredSubjects.length" class="list-disc space-y-1 pl-5 text-sm text-gray-700">
                        <li v-for="subject in filteredSubjects" :key="subject.id">
                            {{ subject.name }}
                            <span v-if="subject.section_id" class="text-gray-500">(Section ID: {{ subject.section_id }})</span>
                        </li>
                    </ul>
                    <p v-else class="text-sm text-gray-500 italic">No subjects found for selected year level.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

