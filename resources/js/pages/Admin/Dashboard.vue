<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

interface Stats {
    reservations_today: number;
    guests_today: number;
    pending: number;
    unassigned: number;
    reservations_total: number;
    tables_active: number;
    tables_total: number;
    menu_unavailable: number;
}

interface Reservation {
    id: number;
    contact_name: string;
    contact_phone: string;
    reservation_date: string;
    reservation_time: string;
    adults: number;
    children: number;
    status: 'pendiente' | 'confirmada' | 'cancelada';
    zone_preference: string;
    notes: string | null;
    table: { id: number; code: string; zone: string; capacity?: number } | null;
    user: { id: number; name: string } | null;
}

defineProps<{
    stats: Stats;
    todayReservations: Reservation[];
    attention: Reservation[];
}>();

const cards: { key: keyof Stats; label: string; accent: string }[] = [
    { key: 'reservations_today', label: 'Reservas hoy', accent: 'text-tiki-sunset-dark' },
    { key: 'guests_today', label: 'Comensales hoy', accent: 'text-tiki-leaf-dark' },
    { key: 'pending', label: 'Pendientes', accent: 'text-yellow-700' },
    { key: 'unassigned', label: 'Sin mesa asignada', accent: 'text-tiki-night' },
];

const statusStyles: Record<string, string> = {
    pendiente: 'bg-yellow-100 text-yellow-800',
    confirmada: 'bg-tiki-leaf/15 text-tiki-leaf-dark',
    cancelada: 'bg-gray-200 text-gray-600 line-through',
};

function fmtDate(value: string): string {
    const [y, m, d] = value.slice(0, 10).split('-');
    return `${d}/${m}/${y}`;
}

function fmtTime(value: string): string {
    return value.slice(0, 5);
}
</script>

<template>
    <Head title="Panel · Resumen" />
    <AdminLayout>
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <article
                v-for="card in cards"
                :key="card.key"
                class="rounded-2xl border border-tiki-bamboo/30 bg-white/90 p-5 shadow-sm"
            >
                <p class="text-sm font-medium text-tiki-bamboo">{{ card.label }}</p>
                <p class="mt-2 text-4xl font-bold" :class="card.accent">{{ stats[card.key] }}</p>
            </article>
        </section>

        <section class="mt-4 grid gap-3 sm:grid-cols-3">
            <div class="rounded-xl border border-tiki-bamboo/30 bg-tiki-sand/60 px-4 py-3 text-sm">
                <span class="text-tiki-bamboo">Reservas totales</span>
                <span class="ml-2 font-bold text-tiki-night">{{ stats.reservations_total }}</span>
            </div>
            <div class="rounded-xl border border-tiki-bamboo/30 bg-tiki-sand/60 px-4 py-3 text-sm">
                <span class="text-tiki-bamboo">Mesas activas</span>
                <span class="ml-2 font-bold text-tiki-night">{{ stats.tables_active }} / {{ stats.tables_total }}</span>
            </div>
            <div class="rounded-xl border border-tiki-bamboo/30 bg-tiki-sand/60 px-4 py-3 text-sm">
                <span class="text-tiki-bamboo">Platos no disponibles</span>
                <span class="ml-2 font-bold text-tiki-night">{{ stats.menu_unavailable }}</span>
            </div>
        </section>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <section class="rounded-2xl border border-tiki-bamboo/30 bg-white/90 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-tiki-night">Reservas de hoy</h2>
                    <Link href="/admin/reservas" class="text-sm font-semibold text-tiki-sunset-dark hover:underline">
                        Ver todas
                    </Link>
                </div>

                <p v-if="todayReservations.length === 0" class="mt-4 text-sm text-tiki-bamboo">
                    No hay reservas para hoy.
                </p>

                <ul v-else class="mt-3 divide-y divide-tiki-bamboo/20">
                    <li v-for="r in todayReservations" :key="r.id" class="flex items-center justify-between gap-3 py-2.5">
                        <div>
                            <p class="font-semibold text-tiki-night">
                                {{ fmtTime(r.reservation_time) }} · {{ r.contact_name }}
                            </p>
                            <p class="text-xs text-tiki-bamboo">
                                {{ r.adults + r.children }} comensales ·
                                {{ r.table ? `Mesa ${r.table.code}` : 'Sin mesa' }}
                            </p>
                        </div>
                        <span
                            class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-widest"
                            :class="statusStyles[r.status]"
                        >
                            {{ r.status }}
                        </span>
                    </li>
                </ul>
            </section>

            <section class="rounded-2xl border border-tiki-bamboo/30 bg-white/90 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-tiki-night">Requieren atención</h2>
                    <Link href="/admin/reservas?status=pendiente" class="text-sm font-semibold text-tiki-sunset-dark hover:underline">
                        Gestionar
                    </Link>
                </div>

                <p v-if="attention.length === 0" class="mt-4 text-sm text-tiki-bamboo">
                    Todo al día, no hay reservas pendientes.
                </p>

                <ul v-else class="mt-3 divide-y divide-tiki-bamboo/20">
                    <li v-for="r in attention" :key="r.id" class="flex items-center justify-between gap-3 py-2.5">
                        <div>
                            <p class="font-semibold text-tiki-night">{{ r.contact_name }}</p>
                            <p class="text-xs text-tiki-bamboo">
                                {{ fmtDate(r.reservation_date) }} · {{ fmtTime(r.reservation_time) }} ·
                                {{ r.adults + r.children }} pax
                            </p>
                        </div>
                        <span
                            class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-widest"
                            :class="statusStyles[r.status]"
                        >
                            {{ r.status }}
                        </span>
                    </li>
                </ul>
            </section>
        </div>
    </AdminLayout>
</template>
