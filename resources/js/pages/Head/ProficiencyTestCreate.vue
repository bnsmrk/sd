<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    yearLevels: Array<{ id: number; name: string }>;
    sections: Array<{ id: number; name: string }>;
    subjects: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    title: '',
    type: 'reading',
    year_level_id: '',
    section_id: '',
    subject_id: '',
    scheduled_at: '',
    due_date: '',
    description: '',
});
</script>

<template>
    <Head title="Create Proficiency Test" />
    <AppLayout>
        <div class="mx-auto max-w-2xl space-y-6 p-6">
            <h1 class="text-xl font-bold text-[#01006c]">Create Proficiency Test</h1>

            <form @submit.prevent="form.post('/proficiency-test')">
                <div>
                    <label class="block font-semibold text-[#ff69b4]">Test Title</label>
                    <input v-model="form.title" class="floating-input" required />
                </div>

                <div>
                    <label class="block font-semibold text-[#ff69b4]">Test Type</label>
                    <select v-model="form.type" class="floating-input" required>
                        <option value="reading">Reading Comprehension</option>
                        <option value="numerical">Numerical Test</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-[#ff69b4]">Year Level</label>
                    <select v-model="form.year_level_id" class="floating-input" required>
                        <option value="">-- Choose Year Level --</option>
                        <option v-for="yl in props.yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-[#ff69b4]">Scheduled At</label>
                    <input v-model="form.scheduled_at" type="datetime-local" class="floating-input" required />
                </div>
                <div>
                    <label class="block font-semibold text-[#ff69b4]">Due Date</label>
                    <input v-model="form.due_date" type="datetime-local" class="floating-input" />
                </div>

                <div>
                    <label class="block font-semibold text-[#ff69b4]">Description (optional)</label>
                    <textarea v-model="form.description" class="floating-input" rows="3"></textarea>
                </div>

                <button type="submit" class="mt-4 rounded bg-[#01006c] px-4 py-2 text-white hover:bg-[#0d1282]">Save Test</button>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.floating-input {
    width: 100%;
    border: 2px solid #01006c;
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    margin-top: 0.25rem;
}
</style>
