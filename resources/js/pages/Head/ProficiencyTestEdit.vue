<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    test: {
        id: number;
        title: string;
        type: string;
        year_level_id: number;
        scheduled_at: string;
        description: string | null;
    };
    yearLevels: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    title: props.test.title,
    type: props.test.type,
    year_level_id: props.test.year_level_id,
    scheduled_at: props.test.scheduled_at,
    description: props.test.description ?? '',
});

function deleteTest() {
    if (confirm('Are you sure you want to delete this test?')) {
        router.delete(`/proficiency-test/${props.test.id}`);
    }
}
</script>

<template>
    <Head title="Edit Proficiency Test" />
    <AppLayout>
        <div class="mx-auto max-w-2xl space-y-6 p-6">
            <h1 class="text-xl font-bold text-[#01006c]">Edit Proficiency Test</h1>

            <form @submit.prevent="form.put(`/proficiency-test/${props.test.id}`)">
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
                    <label class="block font-semibold text-[#ff69b4]">Description</label>
                    <textarea v-model="form.description" class="floating-input" rows="3"></textarea>
                </div>

                <div class="mt-4 flex justify-between">
                    <button type="submit" class="rounded bg-[#01006c] px-4 py-2 text-white hover:bg-[#0d1282]">Update</button>
                    <button @click.prevent="deleteTest" type="button" class="rounded bg-red-600 px-4 py-2 text-white hover:bg-red-800">Delete</button>
                </div>
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
