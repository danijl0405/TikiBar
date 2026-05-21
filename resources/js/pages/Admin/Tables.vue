<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

interface RestaurantTable {
    id: number;
    code: string;
    capacity: number;
    zone: 'terraza' | 'interior' | 'chiringuito';
    is_active: boolean;
    upcoming_count: number;
}

defineProps<{
    tables: RestaurantTable[];
}>();

const zones = ['terraza', 'interior', 'chiringuito'];
const editingId = ref<number | null>(null);

const form = useForm({
    code: '',
    capacity: 2,
    zone: 'chiringuito',
    is_active: true,
});

const zoneStyles: Record<string, string> = {
    terraza: 'bg-tiki-leaf/15 text-tiki-leaf-dark',
    interior: 'bg-tiki-bamboo/20 text-tiki-night',
    chiringuito: 'bg-tiki-sunset/15 text-tiki-sunset-dark',
};

function startEdit(table: RestaurantTable): void {
    editingId.value = table.id;
    form.code = table.code;
    form.capacity = table.capacity;
    form.zone = table.zone;
    form.is_active = table.is_active;
    form.clearErrors();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelEdit(): void {
    editingId.value = null;
    form.reset();
    form.clearErrors();
}

function submit(): void {
    if (editingId.value !== null) {
        form.patch(`/admin/mesas/${editingId.value}`, {
            preserveScroll: true,
            onSuccess: () => cancelEdit(),
        });
    } else {
        form.post('/admin/mesas', {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    }
}

function remove(table: RestaurantTable): void {
    if (confirm(`¿Eliminar la mesa ${table.code}? Las reservas asociadas quedarán sin mesa.`)) {
        router.delete(`/admin/mesas/${table.id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Panel · Mesas" />
    <AdminLayout>
        <form
            class="mb-8 space-y-4 rounded-2xl border border-tiki-bamboo/30 bg-white/90 p-5 shadow-sm"
            @submit.prevent="submit"
        >
            <h2 class="text-lg font-bold text-tiki-night">
                {{ editingId === null ? 'Nueva mesa' : 'Editar mesa' }}
            </h2>

            <div class="grid gap-4 sm:grid-cols-4">
                <div>
                    <label class="text-sm font-medium">Código</label>
                    <input
                        v-model="form.code"
                        type="text"
                        maxlength="10"
                        placeholder="T-01"
                        class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none"
                    />
                    <p v-if="form.errors.code" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.code }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium">Capacidad</label>
                    <input
                        v-model.number="form.capacity"
                        type="number"
                        min="1"
                        max="30"
                        class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none"
                    />
                    <p v-if="form.errors.capacity" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.capacity }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium">Zona</label>
                    <select
                        v-model="form.zone"
                        class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none"
                    >
                        <option v-for="z in zones" :key="z" :value="z">
                            {{ z.charAt(0).toUpperCase() + z.slice(1) }}
                        </option>
                    </select>
                    <p v-if="form.errors.zone" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.zone }}</p>
                </div>
                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-2 text-sm font-medium">
                        <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded" />
                        Activa
                    </label>
                </div>
            </div>

            <div class="flex gap-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-full bg-tiki-sunset px-5 py-2 text-sm font-semibold text-white shadow hover:bg-tiki-sunset-dark disabled:opacity-60"
                >
                    {{ editingId === null ? 'Crear mesa' : 'Guardar cambios' }}
                </button>
                <button
                    v-if="editingId !== null"
                    type="button"
                    class="rounded-full border border-tiki-bamboo/40 px-5 py-2 text-sm font-semibold text-tiki-night hover:bg-tiki-bamboo/10"
                    @click="cancelEdit"
                >
                    Cancelar
                </button>
            </div>
        </form>

        <p class="mb-3 text-sm text-tiki-bamboo">{{ tables.length }} mesa(s)</p>

        <div v-if="tables.length === 0" class="rounded-2xl border-2 border-dashed border-tiki-bamboo/40 bg-white/60 p-10 text-center">
            <p class="font-semibold text-tiki-night">Todavía no hay mesas registradas.</p>
        </div>

        <div v-else class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <article
                v-for="table in tables"
                :key="table.id"
                class="rounded-2xl border border-tiki-bamboo/30 bg-white/90 p-4 shadow-sm"
                :class="{ 'opacity-60': !table.is_active }"
            >
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="text-lg font-bold text-tiki-night">{{ table.code }}</p>
                        <p class="text-sm text-tiki-bamboo">{{ table.capacity }} personas</p>
                    </div>
                    <span
                        class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-widest"
                        :class="zoneStyles[table.zone]"
                    >
                        {{ table.zone }}
                    </span>
                </div>

                <p class="mt-2 text-xs text-tiki-bamboo">
                    {{ table.upcoming_count }} reserva(s) próxima(s) ·
                    <span :class="table.is_active ? 'text-tiki-leaf-dark' : 'text-tiki-sunset-dark'">
                        {{ table.is_active ? 'activa' : 'inactiva' }}
                    </span>
                </p>

                <div class="mt-3 flex gap-2">
                    <button
                        type="button"
                        class="rounded-full border border-tiki-bamboo/40 px-3 py-1 text-xs font-semibold text-tiki-night hover:bg-tiki-bamboo/10"
                        @click="startEdit(table)"
                    >
                        Editar
                    </button>
                    <button
                        type="button"
                        class="rounded-full border border-tiki-sunset/50 px-3 py-1 text-xs font-semibold text-tiki-sunset-dark hover:bg-tiki-sunset/10"
                        @click="remove(table)"
                    >
                        Eliminar
                    </button>
                </div>
            </article>
        </div>
    </AdminLayout>
</template>
