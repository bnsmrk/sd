<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

const images = ['/images/1.jpg', '/images/3.jpg', '/images/4.jpg', '/images/2.jpg'];

const current = ref(0);
const currentImage = ref(images[0]);

let interval: ReturnType<typeof setInterval>;

onMounted(() => {
    interval = setInterval(() => {
        current.value = (current.value + 1) % images.length;
        currentImage.value = images[current.value];
    }, 3000);
});

onUnmounted(() => clearInterval(interval));
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div
        class="relative flex min-h-svh flex-col bg-cover bg-center transition-all duration-1000"
        :style="`background-image: url('${currentImage}');`"
    >
        <nav class="absolute top-6 right-6 z-10 flex gap-4 text-sm text-white">
            <template v-if="$page.props.auth.user">
                <Link :href="route('dashboard')" class="rounded-sm border border-white/40 px-5 py-1.5 backdrop-blur-sm hover:border-white/70">
                    Dashboard
                </Link>
            </template>
            <template v-else>
                <Link :href="route('login')" class="rounded-sm border border-white/40 px-5 py-1.5 backdrop-blur-sm hover:border-white/70">
                    Log in
                </Link>
                <Link :href="route('register')" class="rounded-sm border border-white/40 px-5 py-1.5 backdrop-blur-sm hover:border-white/70">
                    Register
                </Link>
            </template>
        </nav>

        <div class="flex flex-1 flex-col items-center justify-center gap-6 p-6 md:p-10">
            <!-- <h1 class="text-2xl font-bold text-white drop-shadow">Welcome to the LMS</h1> -->
        </div>

        <footer class="py-4 text-center text-xs text-white/80 backdrop-blur-sm">
            © {{ new Date().getFullYear() }} Your LMS. All rights reserved.
        </footer>
    </div>
</template>
