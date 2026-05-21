<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import TikiLayout from '@/Layouts/TikiLayout.vue';

const page = usePage();

const tabs = [
    { label: 'Resumen', href: '/admin' },
    { label: 'Reservas', href: '/admin/reservas' },
    { label: 'Carta', href: '/admin/carta' },
    { label: 'Mesas', href: '/admin/mesas' },
];

const path = computed(() => page.url.split('?')[0]);

function isActive(href: string): boolean {
    return href === '/admin' ? path.value === '/admin' : path.value.startsWith(href);
}
</script>

<template>
    <TikiLayout>
        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-tiki-leaf">Panel de gestión</p>
                <h1 class="mt-1 text-3xl font-bold text-tiki-night">Administración</h1>
            </div>
            <nav class="flex flex-wrap gap-2">
                <Link
                    v-for="tab in tabs"
                    :key="tab.href"
                    :href="tab.href"
                    class="rounded-full px-4 py-1.5 text-sm font-semibold transition"
                    :class="isActive(tab.href)
                        ? 'bg-tiki-sunset text-white shadow'
                        : 'bg-white/70 text-tiki-night hover:bg-tiki-bamboo/20'"
                >
                    {{ tab.label }}
                </Link>
            </nav>
        </div>

        <slot />
    </TikiLayout>
</template>
