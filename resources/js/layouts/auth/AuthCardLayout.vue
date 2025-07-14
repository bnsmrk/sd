<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-gradient-to-r from-pink-400 via-blue-300 to-yellow-100 p-6 md:p-10">
        <div class="flex w-full max-w-md flex-col gap-6">
            <Link :href="route('home')" class="flex items-center gap-2 self-center font-medium"> </Link>

            <div class="flex flex-col gap-6">
                <Card class="rounded-xl">
                    <CardHeader class="px-10 pt-6 pb-0 text-center">
                        <div class="mb-2 flex justify-center">
                            <img src="/images/lmslogoo.png" alt="Login Banner" class="h-36 w-auto object-contain" />
                        </div>
                        <CardTitle class="text-xl">{{ title }}</CardTitle>
                        <CardDescription>
                            {{ description }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="px-10 py-8">
                        <slot />
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
