<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps<{
  subject: any,
  yearLevels: Array<{ id: number; name: string }>,
  sections: Array<{ id: number; name: string }>
}>();

const form = useForm({
    name: props.subject.name,
    year_level_id: props.subject.year_level_id,
    section_id: props.subject.section_id,
});

const sections = ref(props.sections);

watch(() => form.year_level_id, async (val) => {
    if (val && val !== props.subject.year_level_id) {
        const res = await fetch(`/api/sections-by-year-level/${val}`);
        sections.value = await res.json();
        form.section_id = '';
    }
});
</script>

<template>
    <Head title="Edit Subject" />
    <AppLayout>
        <div class="p-4">
            <h2 class="text-xl font-bold mb-4">Edit Subject</h2>

            <form @submit.prevent="form.put(`/subjects/${props.subject.id}`)">
                <div class="mb-4">
                    <label class="block mb-1">Subject Name</label>
                    <input v-model="form.name" type="text" class="w-full border px-4 py-2 rounded" />
                </div>

                <div class="mb-4">
                    <label class="block mb-1">Year Level</label>
                    <select v-model="form.year_level_id" class="w-full border px-4 py-2 rounded">
                        <option value="">Select Year Level</option>
                        <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block mb-1">Section</label>
                    <select v-model="form.section_id" class="w-full border px-4 py-2 rounded">
                        <option value="">Select Section</option>
                        <option v-for="s in sections" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>

                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Update</button>
            </form>
        </div>
    </AppLayout>
</template>
