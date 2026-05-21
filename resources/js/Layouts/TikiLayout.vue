<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const auth = computed(() => page.props.auth as { user: { id: number; name: string; is_admin?: boolean } | null });
const tiki = computed(() => page.props.tiki as { phone: string; address: string; email: string });
const flash = computed(() => page.props.flash as { success?: string; error?: string });

function logout() {
    router.post('/logout');
}
</script>

<template>
    <div class="flex min-h-screen flex-col text-tiki-night">
        <header class="border-b-2 border-tiki-bamboo/40 bg-tiki-sand/90 backdrop-blur sticky top-0 z-30">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
                <Link href="/" class="flex items-center gap-2 text-2xl font-semibold tracking-tight text-tiki-sunset-dark hover:text-tiki-sunset">
                    <span>Tiki Bar</span>
                    <span class="ml-1 hidden text-xs font-medium uppercase tracking-widest text-tiki-leaf sm:inline">Málaga</span>
                </Link>

                <nav class="flex items-center gap-2 sm:gap-4">
                    <Link
                        href="/carta"
                        class="rounded-full px-3 py-1.5 text-sm font-medium text-tiki-night hover:bg-tiki-bamboo/20"
                    >
                        Carta
                    </Link>

                    <a
                        :href="`tel:${tiki.phone.replace(/\s+/g, '')}`"
                        class="hidden items-center gap-1 rounded-full bg-tiki-leaf px-3 py-1.5 text-sm font-medium text-white shadow hover:bg-tiki-leaf-dark sm:inline-flex"
                    >
                        {{ tiki.phone }}
                    </a>

                    <template v-if="auth.user">
                        <Link
                            v-if="auth.user.is_admin"
                            href="/admin"
                            class="rounded-full bg-tiki-night px-3 py-1.5 text-sm font-medium text-tiki-sand shadow hover:bg-tiki-night/80"
                        >
                            Admin
                        </Link>
                        <Link
                            href="/reservas"
                            class="rounded-full px-3 py-1.5 text-sm font-medium text-tiki-night hover:bg-tiki-bamboo/20"
                        >
                            Mis reservas
                        </Link>
                        <span class="hidden text-sm text-tiki-bamboo sm:inline">Hola, {{ auth.user.name.split(' ')[0] }}</span>
                        <button
                            type="button"
                            class="rounded-full bg-tiki-night px-3 py-1.5 text-sm font-medium text-tiki-sand hover:bg-tiki-night/80"
                            @click="logout"
                        >
                            Salir
                        </button>
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            class="rounded-full px-3 py-1.5 text-sm font-medium text-tiki-night hover:bg-tiki-bamboo/20"
                        >
                            Entrar
                        </Link>
                        <Link
                            href="/registro"
                            class="rounded-full bg-tiki-sunset px-3 py-1.5 text-sm font-medium text-white shadow hover:bg-tiki-sunset-dark"
                        >
                            Registrarse
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <div v-if="flash.success" class="mx-auto mt-4 w-full max-w-6xl px-4 sm:px-6">
            <div class="rounded-lg border border-tiki-leaf/30 bg-tiki-leaf/10 px-4 py-3 text-sm font-medium text-tiki-leaf-dark">
                {{ flash.success }}
            </div>
        </div>
        <div v-if="flash.error" class="mx-auto mt-4 w-full max-w-6xl px-4 sm:px-6">
            <div class="rounded-lg border border-tiki-sunset/40 bg-tiki-sunset/10 px-4 py-3 text-sm font-medium text-tiki-sunset-dark">
                {{ flash.error }}
            </div>
        </div>

        <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:px-6">
            <slot />
        </main>

        <footer class="border-t-2 border-tiki-bamboo/40 bg-tiki-night text-tiki-sand">
            <div class="mx-auto grid max-w-6xl gap-6 px-4 py-8 sm:grid-cols-3 sm:px-6">
                <div>
                    <p class="text-lg font-semibold">Tiki Bar Málaga</p>
                    <p class="mt-2 text-sm opacity-80">{{ tiki.address }}</p>
                </div>
                <div>
                    <p class="font-semibold">Reservas</p>
                    <a :href="`tel:${tiki.phone.replace(/\s+/g, '')}`" class="mt-1 block text-sm hover:underline">
                        {{ tiki.phone }}
                    </a>
                    <a :href="`mailto:${tiki.email}`" class="mt-1 block text-sm hover:underline">
                        {{ tiki.email }}
                    </a>
                </div>
                <div>
                    <p class="font-semibold">Horario</p>
                    <p class="mt-1 text-sm opacity-80">Lunes a domingo: 12:00 – 01:00</p>
                    <p class="text-sm opacity-80">Cocina hasta las 23:30</p>
                </div>
            </div>
            <div class="border-t border-tiki-sand/10 py-3 text-center text-xs opacity-70">
                © {{ new Date().getFullYear() }} Tiki Bar Málaga — Hecho por Daniel Jimenez.
            </div>
        </footer>
    </div>
</template>
