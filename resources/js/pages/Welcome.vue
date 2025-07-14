<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

const images = [
    'https://picsum.photos/id/1/800/600',
    'https://picsum.photos/id/10/800/600',
    'https://picsum.photos/id/20/800/600',
    'https://picsum.photos/id/30/800/600',
    'https://picsum.photos/id/40/800/600',
];

const current = ref(0);

let interval: ReturnType<typeof setInterval>;

onMounted(() => {
    interval = setInterval(() => {
        current.value = (current.value + 1) % images.length;
    }, 3000);
});

onUnmounted(() => clearInterval(interval));
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-gradient-to-r from-pink-400 via-blue-300 to-yellow-100 p-6 md:p-10">
        <header class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="route('register')"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <div class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
            <main class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row">
                <div
                    class="flex-1 rounded-br-lg rounded-bl-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:rounded-tl-lg lg:rounded-br-none lg:p-20 dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <h1 class="mb-1 text-xl font-semibold">Welcome to the LMS</h1>
                    <p class="mt-2 text-sm">Access your learning materials, track your progress, and stay connected with your instructors.</p>
                </div>

                <div
                    class="relative -mb-px aspect-335/376 w-full shrink-0 overflow-hidden rounded-t-lg bg-[#fff2f2] lg:mb-0 lg:-ml-px lg:aspect-auto lg:w-[438px] lg:rounded-t-none lg:rounded-r-lg dark:bg-[#1D0002]"
                >
                    <transition-group name="fade" tag="div" class="relative h-full w-full">
                        <img
                            v-for="(img, i) in images"
                            :key="i"
                            v-show="current === i"
                            :src="img"
                            class="absolute inset-0 h-full w-full object-cover transition-opacity duration-1000 ease-in-out"
                        />
                    </transition-group>

                    <div
                        class="absolute inset-0 rounded-t-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:overflow-hidden lg:rounded-t-none lg:rounded-r-lg dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                    />
                </div>
            </main>
        </div>

        <footer class="mt-10 text-center text-xs text-gray-700 dark:text-gray-300">
            © {{ new Date().getFullYear() }} Your LMS. All rights reserved.
        </footer>
    </div>
</template>
