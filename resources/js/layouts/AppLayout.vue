<script setup lang="ts">
import Toast from '@/components/Toast.vue';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';

import type { BreadcrumbItemType } from '@/types';
import '@fortawesome/fontawesome-free/css/all.min.css';
import { usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const success = ref<string | null>(null);
const danger = ref<string | null>(null);
const warning = ref<string | null>(null);

watch(
    () => page.props.flash as Record<string, string>,
    (flash) => {
        success.value = flash.success || null;
        danger.value = flash.danger || null;
        warning.value = flash.warning || null;
    },
    { immediate: true },
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" class="">
        <slot />

        <Toast v-if="success" :message="success" variant="success" @close="success = null" />
        <Toast v-if="danger" :message="danger" variant="danger" @close="danger = null" />
        <Toast v-if="warning" :message="warning" variant="warning" @close="warning = null" />
    </AppLayout>
</template>
