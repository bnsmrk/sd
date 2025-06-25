<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface EnrollmentDetail {
    user: { name: string };
    year_level: { name: string };
    section: { name: string } | null;
    subject: { name: string };
}

defineProps<{
    enrollments: Array<{
        id: number;
        user: { name: string };
        year_level: { name: string };
        section: { name: string } | null;
    }>;
    enrollment?: EnrollmentDetail; // optional for modal data
}>();

const breadcrumbs = [{ title: 'Enrollments', href: '/enroll' }];

const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

const showViewModal = ref(false);
const enrollmentDetail = ref<EnrollmentDetail | null>(null);

// DELETE logic
const confirmDelete = (id: number) => {
    deleteId.value = id;
    showDeleteModal.value = true;
};

const destroyItem = () => {
    if (deleteId.value !== null) {
        router.delete(`/enroll/${deleteId.value}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
                deleteId.value = null;
            },
        });
    }
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    deleteId.value = null;
};

// VIEW logic (load modal data like delete)
const showDetails = (id: number) => {
    router.get(
        `/enroll/${id}`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
            only: ['enrollment'],
            onSuccess: (page: any) => {
                enrollmentDetail.value = page.props.enrollment as EnrollmentDetail;
                showViewModal.value = true;
            },
        },
    );
};

const closeViewModal = () => {
    showViewModal.value = false;
    enrollmentDetail.value = null;
};
</script>

<template>
    <Head title="Enrollments" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">Enrollments</h2>
                <Link href="/enroll/create" class="rounded bg-blue-600 px-4 py-2 text-white">Add Enrollment</Link>
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full text-left text-sm text-gray-700">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                        <tr>
                            <th class="px-6 py-3">Student</th>
                            <th class="px-6 py-3">Year Level</th>
                            <th class="px-6 py-3">Section</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="enroll in enrollments" :key="enroll.id" class="border-b bg-white hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ enroll.user.name }}</td>
                            <td class="px-6 py-4">{{ enroll.year_level.name }}</td>
                            <td class="px-6 py-4">{{ enroll.section?.name ?? '—' }}</td>
                            <td class="flex items-center justify-center space-x-3 px-6 py-4">
                                <button @click="showDetails(enroll.id)" class="text-green-600 hover:underline">View</button>
                                <Link :href="`/enroll/${enroll.id}/edit`" class="text-blue-600 hover:underline">Edit</Link>
                                <button @click="confirmDelete(enroll.id)" class="text-red-600 hover:underline">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- View Modal -->
        <div v-if="showViewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded bg-white p-6 shadow-lg">
                <h2 class="mb-4 text-xl font-semibold">Enrollment Details</h2>
                <div v-if="enrollmentDetail" class="space-y-2">
                    <p><strong>Student:</strong> {{ enrollmentDetail.user.name }}</p>
                    <p><strong>Year Level:</strong> {{ enrollmentDetail.year_level.name }}</p>
                    <p><strong>Section:</strong> {{ enrollmentDetail.section?.name ?? '—' }}</p>
                    <p><strong>Subject:</strong> {{ enrollmentDetail.subject.name }}</p>
                </div>
                <div class="mt-6 text-right">
                    <button @click="closeViewModal" class="rounded bg-gray-300 px-4 py-2 hover:bg-gray-400">Close</button>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded bg-white p-6 shadow-lg">
                <h2 class="mb-4 text-lg font-semibold">Confirm Deletion</h2>
                <p class="mb-6 text-gray-600">Are you sure you want to delete this enrollment?</p>
                <div class="flex justify-end space-x-4">
                    <button @click="cancelDelete" class="rounded bg-gray-300 px-4 py-2 text-sm hover:bg-gray-400">Cancel</button>
                    <button @click="destroyItem" class="rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">Confirm</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
