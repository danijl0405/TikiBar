<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import TikiLayout from '@/Layouts/TikiLayout.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Entrar" />
    <TikiLayout>
        <div class="mx-auto max-w-md">
            <div class="rounded-3xl border border-tiki-bamboo/30 bg-white/90 p-8 shadow-lg">
                <h1 class="text-3xl font-bold text-tiki-night">Bienvenido de vuelta</h1>
                <p class="mt-1 text-sm text-tiki-bamboo">
                    Entra para reservar mesa en el Tiki Bar.
                </p>

                <form class="mt-6 space-y-4" @submit.prevent="submit">
                    <div>
                        <label class="text-sm font-medium text-tiki-night" for="email">Correo</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autocomplete="email"
                            class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-tiki-night" for="password">Contraseña</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                        />
                        <p v-if="form.errors.password" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.password }}</p>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-tiki-night">
                        <input v-model="form.remember" type="checkbox" class="rounded border-tiki-bamboo/50" />
                        Recordarme
                    </label>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-full bg-tiki-sunset px-4 py-2.5 font-semibold text-white shadow hover:bg-tiki-sunset-dark disabled:opacity-60"
                    >
                        {{ form.processing ? 'Entrando…' : 'Entrar' }}
                    </button>
                </form>

                <p class="mt-4 text-center text-sm text-tiki-bamboo">
                    ¿Aún no tienes cuenta?
                    <Link href="/registro" class="font-semibold text-tiki-ocean-dark hover:underline">
                        Crea una aquí
                    </Link>
                </p>
            </div>
        </div>
    </TikiLayout>
</template>
