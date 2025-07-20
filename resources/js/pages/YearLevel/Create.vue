<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
});

const submit = () => {
    form.post('/year-levels');
};
</script>

<template>
    <Head title="Add Year Level" />
    <AppLayout>
        <div class="mx-auto max-w-md p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">Add Year Level</h2>
                <Link href="/year-levels" class="text-blue-600 hover:underline">Back</Link>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="mb-1 block font-semibold">Name</label>
                    <input v-model="form.name" type="text" class="w-full rounded border p-2" placeholder="e.g. Grade 7" />
                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
                </div>

                <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white">Save</button>
            </form>
        </div>
    </AppLayout>
</template>
