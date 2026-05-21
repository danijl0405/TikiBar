<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import TikiLayout from '@/Layouts/TikiLayout.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/registro', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Crear cuenta" />
    <TikiLayout>
        <div class="mx-auto max-w-md">
            <div class="rounded-3xl border border-tiki-bamboo/30 bg-white/90 p-8 shadow-lg">
                <h1 class="text-3xl font-bold text-tiki-night">Únete al Tiki Bar</h1>
                <p class="mt-1 text-sm text-tiki-bamboo">
                    Regístrate para reservar mesa y recibir las novedades del chiringuito.
                </p>

                <form class="mt-6 space-y-4" @submit.prevent="submit">
                    <div>
                        <label class="text-sm font-medium text-tiki-night" for="name">Nombre y apellidos</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            autocomplete="name"
                            class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.name }}</p>
                    </div>

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
                        <label class="text-sm font-medium text-tiki-night" for="phone">Teléfono (opcional)</label>
                        <input
                            id="phone"
                            v-model="form.phone"
                            type="tel"
                            autocomplete="tel"
                            placeholder="+34 600 11 22 33"
                            class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                        />
                        <p v-if="form.errors.phone" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.phone }}</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-tiki-night" for="password">Contraseña</label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                autocomplete="new-password"
                                class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                            />
                            <p v-if="form.errors.password" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.password }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-tiki-night" for="password_confirmation">Repite</label>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
                                class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-full bg-tiki-sunset px-4 py-2.5 font-semibold text-white shadow hover:bg-tiki-sunset-dark disabled:opacity-60"
                    >
                        {{ form.processing ? 'Creando cuenta…' : 'Crear cuenta' }}
                    </button>
                </form>

                <p class="mt-4 text-center text-sm text-tiki-bamboo">
                    ¿Ya eres del Tiki?
                    <Link href="/login" class="font-semibold text-tiki-ocean-dark hover:underline">
                        Entra aquí
                    </Link>
                </p>
            </div>
        </div>
    </TikiLayout>
</template>
