<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import TikiLayout from '@/Layouts/TikiLayout.vue';

type RealZone = 'terraza' | 'interior' | 'chiringuito';

interface TurnSlot {
    total: number;
    zones: Record<RealZone, number>;
}

interface Availability {
    date: string;
    guests: number;
    turns: Record<string, TurnSlot>;
}

const props = defineProps<{
    user: { name: string; phone: string | null };
    zones: string[];
    turns: string[];
    availability: Availability;
}>();

const today = new Date().toISOString().slice(0, 10);
const defaultTurn = props.turns.includes('21:30') ? '21:30' : (props.turns[0] ?? '');

const form = useForm({
    contact_name: props.user.name,
    contact_phone: props.user.phone ?? '',
    reservation_date: today,
    reservation_time: defaultTurn,
    adults: 2,
    children: 0,
    ages: [] as number[],
    zone_preference: 'cualquiera',
    notes: '',
});

const totalGuests = computed(() => Number(form.adults) + Number(form.children));
const availabilityLoading = ref(false);

watch(
    () => form.children,
    (newCount) => {
        const count = Math.max(0, Number(newCount));
        const current = [...form.ages];

        if (current.length < count) {
            while (current.length < count) {
                current.push(5);
            }
        } else if (current.length > count) {
            current.length = count;
        }

        form.ages = current;
    },
    { immediate: true },
);

function refreshAvailability(): void {
    if (!form.reservation_date) {
        return;
    }

    router.reload({
        only: ['availability'],
        data: { date: form.reservation_date, guests: totalGuests.value },
        replace: true,
        onStart: () => {
            availabilityLoading.value = true;
        },
        onFinish: () => {
            availabilityLoading.value = false;
        },
    });
}

// Re-check availability whenever the date or party size changes.
watchDebounced([() => form.reservation_date, totalGuests], refreshAvailability, { debounce: 400 });

function capitalize(value: string): string {
    return value.charAt(0).toUpperCase() + value.slice(1);
}

function formatDate(value: string): string {
    const [y, m, d] = value.slice(0, 10).split('-');
    return `${d}/${m}/${y}`;
}

function turnFree(turn: string): number {
    return props.availability.turns[turn]?.total ?? 0;
}

function turnOptionLabel(turn: string): string {
    const free = turnFree(turn);
    if (free <= 0) {
        return `${turn} — completo`;
    }
    return `${turn} — ${free} mesa${free === 1 ? '' : 's'} libre${free === 1 ? '' : 's'}`;
}

function zoneFree(zone: string): number {
    const slot = props.availability.turns[form.reservation_time];
    if (!slot) {
        return 0;
    }
    if (zone === 'cualquiera') {
        return slot.total;
    }
    return slot.zones[zone as RealZone] ?? 0;
}

function zoneOptionLabel(zone: string): string {
    const free = zoneFree(zone);
    if (free <= 0) {
        return `${capitalize(zone)} — completo`;
    }
    return `${capitalize(zone)} — ${free} mesa${free === 1 ? '' : 's'} libre${free === 1 ? '' : 's'}`;
}

const realZones = computed(() => props.zones.filter((z) => z !== 'cualquiera'));
const selectedZoneFree = computed(() => zoneFree(form.zone_preference));
const canSubmit = computed(() => selectedZoneFree.value > 0);

const submitLabel = computed(() => {
    if (form.processing) {
        return 'Reservando…';
    }
    if (!canSubmit.value) {
        return 'Sin mesas en este turno';
    }
    return 'Reservar mesa';
});

function submit(): void {
    if (!canSubmit.value) {
        return;
    }
    form.post('/reservas');
}
</script>

<template>
    <Head title="Reservar mesa" />
    <TikiLayout>
        <div class="mx-auto max-w-2xl">
            <header class="mb-6">
                <p class="text-sm font-semibold uppercase tracking-widest text-tiki-leaf">Reserva</p>
                <h1 class="mt-1 text-3xl font-bold text-tiki-night">Reserva tu mesa</h1>
                <p class="mt-1 text-sm text-tiki-bamboo">
                    Trabajamos por turnos de 1h30. Elige fecha, turno y comensales: te mostramos las
                    zonas con mesas libres.
                </p>
            </header>

            <form
                class="space-y-5 rounded-3xl border border-tiki-bamboo/30 bg-white/90 p-6 shadow-lg sm:p-8"
                @submit.prevent="submit"
            >
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="contact_name" class="text-sm font-medium">Nombre de contacto</label>
                        <input
                            id="contact_name"
                            v-model="form.contact_name"
                            required
                            type="text"
                            class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                        />
                        <p v-if="form.errors.contact_name" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.contact_name }}</p>
                    </div>
                    <div>
                        <label for="contact_phone" class="text-sm font-medium">Teléfono</label>
                        <input
                            id="contact_phone"
                            v-model="form.contact_phone"
                            required
                            type="tel"
                            class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                        />
                        <p v-if="form.errors.contact_phone" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.contact_phone }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="reservation_date" class="text-sm font-medium">Fecha</label>
                        <input
                            id="reservation_date"
                            v-model="form.reservation_date"
                            required
                            type="date"
                            :min="today"
                            class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                        />
                        <p v-if="form.errors.reservation_date" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.reservation_date }}</p>
                    </div>
                    <div>
                        <label for="reservation_time" class="text-sm font-medium">Turno</label>
                        <select
                            id="reservation_time"
                            v-model="form.reservation_time"
                            required
                            class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                        >
                            <option
                                v-for="turn in turns"
                                :key="turn"
                                :value="turn"
                                :disabled="turnFree(turn) === 0"
                            >
                                {{ turnOptionLabel(turn) }}
                            </option>
                        </select>
                        <p v-if="form.errors.reservation_time" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.reservation_time }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label for="adults" class="text-sm font-medium">Adultos</label>
                        <input
                            id="adults"
                            v-model.number="form.adults"
                            type="number"
                            min="1"
                            max="20"
                            required
                            class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                        />
                    </div>
                    <div>
                        <label for="children" class="text-sm font-medium">Niños</label>
                        <input
                            id="children"
                            v-model.number="form.children"
                            type="number"
                            min="0"
                            max="20"
                            class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                        />
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-medium">Total</span>
                        <p class="mt-1 inline-flex h-10 items-center rounded-lg bg-tiki-leaf/10 px-3 text-base font-semibold text-tiki-leaf-dark">
                            {{ totalGuests }} comensales
                        </p>
                    </div>
                </div>

                <div v-if="form.children > 0">
                    <p class="text-sm font-medium">Edades de los peques</p>
                    <p class="text-xs text-tiki-bamboo">Nos ayuda a tener trona o menú infantil preparado.</p>
                    <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-4">
                        <label
                            v-for="(_, idx) in form.ages"
                            :key="idx"
                            class="flex items-center gap-2 rounded-lg border border-tiki-bamboo/30 bg-tiki-sand px-2 py-1"
                        >
                            <span class="text-xs text-tiki-bamboo">Peque {{ idx + 1 }}</span>
                            <input
                                v-model.number="form.ages[idx]"
                                type="number"
                                min="0"
                                max="17"
                                class="w-full bg-transparent text-sm focus:outline-none"
                            />
                        </label>
                    </div>
                </div>

                <div class="rounded-xl border border-tiki-bamboo/30 bg-tiki-sand/60 p-3">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-medium text-tiki-night">
                            Disponibilidad · {{ formatDate(availability.date) }} · turno {{ form.reservation_time }}
                            <span class="text-tiki-bamboo">({{ availability.guests }} comensales)</span>
                        </p>
                        <span v-if="availabilityLoading" class="text-xs text-tiki-bamboo">Actualizando…</span>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span
                            v-for="z in realZones"
                            :key="z"
                            class="rounded-full px-3 py-1 text-xs font-semibold"
                            :class="zoneFree(z) > 0
                                ? 'bg-tiki-leaf/15 text-tiki-leaf-dark'
                                : 'bg-gray-200 text-gray-500'"
                        >
                            {{ capitalize(z) }}: {{ zoneFree(z) > 0 ? `${zoneFree(z)} libre(s)` : 'completo' }}
                        </span>
                    </div>
                    <p v-if="zoneFree('cualquiera') === 0" class="mt-2 text-xs font-medium text-tiki-sunset-dark">
                        No quedan mesas para {{ availability.guests }} comensales en ese turno. Prueba otro turno o fecha.
                    </p>
                </div>

                <div>
                    <label for="zone_preference" class="text-sm font-medium">Zona preferida</label>
                    <select
                        id="zone_preference"
                        v-model="form.zone_preference"
                        class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                    >
                        <option
                            v-for="z in zones"
                            :key="z"
                            :value="z"
                            :disabled="zoneFree(z) === 0"
                        >
                            {{ zoneOptionLabel(z) }}
                        </option>
                    </select>
                    <p v-if="form.errors.zone_preference" class="mt-1 text-xs text-tiki-sunset-dark">
                        {{ form.errors.zone_preference }}
                    </p>
                    <p v-else-if="!canSubmit" class="mt-1 text-xs text-tiki-sunset-dark">
                        Esa zona está completa en este turno. Elige otra zona, otro turno o cambia la fecha.
                    </p>
                </div>

                <div>
                    <label for="notes" class="text-sm font-medium">Notas (alergias, cumpleaños…)</label>
                    <textarea
                        id="notes"
                        v-model="form.notes"
                        rows="3"
                        class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                    />
                </div>

                <button
                    type="submit"
                    :disabled="form.processing || availabilityLoading || !canSubmit"
                    class="w-full rounded-full bg-tiki-sunset px-4 py-3 font-semibold text-white shadow hover:bg-tiki-sunset-dark disabled:opacity-60"
                >
                    {{ submitLabel }}
                </button>
            </form>
        </div>
    </TikiLayout>
</template>
