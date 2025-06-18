<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Users', href: '/users' },
    { title: 'Create', href: '/users/create' },
];

const form = useForm({
    name: '',
    email: '',
    role: 'teacher',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/users');
};
</script>

<template>
    <Head title="Create User" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl space-y-4 p-4">
            <h1 class="text-2xl font-semibold">Create New User</h1>
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label>Name</label>
                    <input v-model="form.name" class="w-full rounded border p-2" type="text" />
                </div>
                <div>
                    <label>Email</label>
                    <input v-model="form.email" class="w-full rounded border p-2" type="email" />
                </div>
                <div>
                    <label>Role</label>
                    <select v-model="form.role" class="w-full rounded border p-2">
                        <option value="teacher">Teacher</option>
                        <option value="ict">ICT</option>
                        <option value="head">Head</option>
                    </select>
                </div>
                <div>
                    <label>Password</label>
                    <input v-model="form.password" class="w-full rounded border p-2" type="password" />
                </div>
                <div>
                    <label>Confirm Password</label>
                    <input v-model="form.password_confirmation" class="w-full rounded border p-2" type="password" />
                </div>
                <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Save</button>
            </form>
        </div>
    </AppLayout>
</template>
