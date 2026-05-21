<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import TikiLayout from '@/Layouts/TikiLayout.vue';

interface Reservation {
    id: number;
    contact_name: string;
    contact_phone: string;
    reservation_date: string;
    reservation_time: string;
    adults: number;
    children: number;
    ages: number[] | null;
    zone_preference: string;
    status: 'pendiente' | 'confirmada' | 'cancelada';
    notes: string | null;
    table: { id: number; code: string; capacity: number; zone: string } | null;
}

defineProps<{
    reservations: Reservation[];
}>();

function cancel(id: number) {
    if (confirm('¿Seguro que quieres cancelar esta reserva?')) {
        router.delete(`/reservas/${id}`);
    }
}

const statusStyles: Record<string, string> = {
    pendiente: 'bg-yellow-100 text-yellow-800',
    confirmada: 'bg-tiki-leaf/15 text-tiki-leaf-dark',
    cancelada: 'bg-gray-200 text-gray-600 line-through',
};
</script>

<template>
    <Head title="Mis reservas" />
    <TikiLayout>
        <header class="mb-6 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-tiki-leaf">Tus reservas</p>
                <h1 class="mt-1 text-3xl font-bold text-tiki-night">Mis mesas en el Tiki</h1>
            </div>
            <Link
                href="/reservas/nueva"
                class="rounded-full bg-tiki-sunset px-4 py-2 font-semibold text-white shadow hover:bg-tiki-sunset-dark"
            >
                + Nueva reserva
            </Link>
        </header>

        <div v-if="reservations.length === 0" class="rounded-2xl border-2 border-dashed border-tiki-bamboo/40 bg-white/60 p-10 text-center">
            <p class="font-semibold text-tiki-night">Todavía no tienes reservas</p>
            <p class="text-sm text-tiki-bamboo">Reserva tu primera mesa y vente a disfrutar de Málaga.</p>
            <Link
                href="/reservas/nueva"
                class="mt-4 inline-flex rounded-full bg-tiki-sunset px-4 py-2 font-semibold text-white shadow hover:bg-tiki-sunset-dark"
            >
                Reservar mesa
            </Link>
        </div>

        <div v-else class="grid gap-4 sm:grid-cols-2">
            <article
                v-for="r in reservations"
                :key="r.id"
                class="rounded-2xl border border-tiki-bamboo/30 bg-white/90 p-5 shadow-sm"
            >
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="text-sm text-tiki-bamboo">{{ r.reservation_date }} · {{ r.reservation_time }}</p>
                        <p class="text-lg font-bold text-tiki-night">
                            {{ r.adults }} adultos
                            <span v-if="r.children > 0" class="text-base font-medium text-tiki-bamboo">
                                + {{ r.children }} niños
                            </span>
                        </p>
                    </div>
                    <span
                        class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-widest"
                        :class="statusStyles[r.status]"
                    >
                        {{ r.status }}
                    </span>
                </div>

                <dl class="mt-3 space-y-1 text-sm">
                    <div class="flex justify-between gap-2">
                        <dt class="text-tiki-bamboo">Zona</dt>
                        <dd class="font-medium text-tiki-night">{{ r.zone_preference }}</dd>
                    </div>
                    <div v-if="r.table" class="flex justify-between gap-2">
                        <dt class="text-tiki-bamboo">Mesa asignada</dt>
                        <dd class="font-medium text-tiki-leaf-dark">
                            {{ r.table.code }} ({{ r.table.zone }}, {{ r.table.capacity }}p)
                        </dd>
                    </div>
                    <div v-if="r.ages && r.ages.length" class="flex justify-between gap-2">
                        <dt class="text-tiki-bamboo">Edades peques</dt>
                        <dd class="font-medium text-tiki-night">{{ r.ages.join(', ') }} años</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-tiki-bamboo">Contacto</dt>
                        <dd class="font-medium text-tiki-night">{{ r.contact_name }} · {{ r.contact_phone }}</dd>
                    </div>
                </dl>

                <p v-if="r.notes" class="mt-3 rounded bg-tiki-sand px-3 py-2 text-xs text-tiki-night/80">
                    {{ r.notes }}
                </p>

                <div class="mt-4 flex justify-end">
                    <button
                        v-if="r.status !== 'cancelada'"
                        type="button"
                        class="rounded-full border border-tiki-sunset/50 px-3 py-1 text-xs font-semibold text-tiki-sunset-dark hover:bg-tiki-sunset/10"
                        @click="cancel(r.id)"
                    >
                        Cancelar reserva
                    </button>
                </div>
            </article>
        </div>
    </TikiLayout>
</template>
