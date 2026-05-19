<script setup lang="ts">
import TikiLayout from '@/Layouts/TikiLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps<{
    user: { name: string; phone: string | null };
    zones: string[];
}>();

const today = new Date().toISOString().slice(0, 10);

const form = useForm({
    contact_name: props.user.name,
    contact_phone: props.user.phone ?? '',
    reservation_date: today,
    reservation_time: '21:00',
    adults: 2,
    children: 0,
    ages: [] as number[],
    zone_preference: 'cualquiera',
    notes: '',
});

const totalGuests = computed(() => Number(form.adults) + Number(form.children));

watch(
    () => form.children,
    (newCount) => {
        const count = Math.max(0, Number(newCount));
        const current = [...form.ages];
        if (current.length < count) {
            while (current.length < count) current.push(5);
        } else if (current.length > count) {
            current.length = count;
        }
        form.ages = current;
    },
    { immediate: true },
);

function submit() {
    form.post('/reservas');
}
</script>

<template>
    <Head title="Reservar mesa" />
    <TikiLayout>
        <div class="mx-auto max-w-2xl">
            <header class="mb-6">
                <p class="text-sm font-semibold uppercase tracking-widest text-tiki-leaf">Reserva</p>
                <h1 class="mt-1 text-3xl font-bold text-tiki-night">Reserva tu mesa 🌴</h1>
                <p class="mt-1 text-sm text-tiki-bamboo">
                    Cuéntanos cuántos sois, las edades de los peques y elige tu zona favorita.
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
                        <label for="reservation_time" class="text-sm font-medium">Hora</label>
                        <input
                            id="reservation_time"
                            v-model="form.reservation_time"
                            required
                            type="time"
                            step="900"
                            class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                        />
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

                <div>
                    <label for="zone_preference" class="text-sm font-medium">Zona preferida</label>
                    <select
                        id="zone_preference"
                        v-model="form.zone_preference"
                        class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none focus:ring-2 focus:ring-tiki-sunset/30"
                    >
                        <option v-for="z in zones" :key="z" :value="z">
                            {{ z.charAt(0).toUpperCase() + z.slice(1) }}
                        </option>
                    </select>
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
                    :disabled="form.processing"
                    class="w-full rounded-full bg-tiki-sunset px-4 py-3 font-semibold text-white shadow hover:bg-tiki-sunset-dark disabled:opacity-60"
                >
                    {{ form.processing ? 'Reservando…' : 'Reservar mesa 🍽️' }}
                </button>
            </form>
        </div>
    </TikiLayout>
</template>
