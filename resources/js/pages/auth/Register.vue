<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { CheckCircle, Circle, LoaderCircle } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const passwordFocused = ref(false);
const confirmFocused = ref(false);
const hasTypedPassword = ref(false);
const hasTypedConfirm = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

watch(
    () => form.password,
    (val) => {
        if (val.length > 0) hasTypedPassword.value = true;
    },
);

watch(
    () => form.password_confirmation,
    (val) => {
        if (val.length > 0) hasTypedConfirm.value = true;
    },
);

const hasUppercase = computed(() => /[A-Z]/.test(form.password));
const hasLowercase = computed(() => /[a-z]/.test(form.password));
const hasNumber = computed(() => /\d/.test(form.password));
const hasSpecialChar = computed(() => /[\W_]/.test(form.password));
const hasMinLength = computed(() => form.password.length >= 8);

const allValid = computed(() => hasUppercase.value && hasLowercase.value && hasNumber.value && hasSpecialChar.value && hasMinLength.value);

const passwordsMatch = computed(() => form.password === form.password_confirmation);

const showPasswordChecklist = computed(() => hasTypedPassword.value);
const showConfirmStatus = computed(() => hasTypedConfirm.value);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
            hasTypedPassword.value = false;
            hasTypedConfirm.value = false;
        },
    });
};
</script>
<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Full name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Password"
                        @focus="passwordFocused = true"
                        @blur="passwordFocused = false"
                    />

                    <div v-if="showPasswordChecklist" class="mt-2 space-y-1 text-sm">
                        <div class="flex items-center gap-2" :class="hasUppercase ? 'text-green-600' : 'text-gray-400'">
                            <component :is="hasUppercase ? CheckCircle : Circle" class="h-4 w-4" />
                            At least one uppercase letter
                        </div>
                        <div class="flex items-center gap-2" :class="hasLowercase ? 'text-green-600' : 'text-gray-400'">
                            <component :is="hasLowercase ? CheckCircle : Circle" class="h-4 w-4" />
                            At least one lowercase letter
                        </div>
                        <div class="flex items-center gap-2" :class="hasNumber ? 'text-green-600' : 'text-gray-400'">
                            <component :is="hasNumber ? CheckCircle : Circle" class="h-4 w-4" />
                            At least one number
                        </div>
                        <div class="flex items-center gap-2" :class="hasSpecialChar ? 'text-green-600' : 'text-gray-400'">
                            <component :is="hasSpecialChar ? CheckCircle : Circle" class="h-4 w-4" />
                            At least one special character
                        </div>
                        <div class="flex items-center gap-2" :class="hasMinLength ? 'text-green-600' : 'text-gray-400'">
                            <component :is="hasMinLength ? CheckCircle : Circle" class="h-4 w-4" />
                            Minimum 8 characters
                        </div>
                    </div>

                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirm password"
                    />

                    <div v-if="showConfirmStatus" class="mt-1 text-sm">
                        <div v-if="passwordsMatch" class="flex items-center gap-1 text-green-600">
                            <CheckCircle class="h-4 w-4" /> Passwords match
                        </div>
                        <div v-else class="flex items-center gap-1 text-red-600"><Circle class="h-4 w-4" /> Passwords do not match</div>
                    </div>

                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Log in</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
