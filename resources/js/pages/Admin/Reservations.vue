<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

interface TableOption {
    id: number;
    code: string;
    zone: string;
    capacity: number;
    is_active: boolean;
}

interface Reservation {
    id: number;
    contact_name: string;
    contact_phone: string;
    reservation_date: string;
    reservation_time: string;
    adults: number;
    children: number;
    zone_preference: string;
    status: 'pendiente' | 'confirmada' | 'cancelada';
    notes: string | null;
    restaurant_table_id: number | null;
    table: { id: number; code: string; zone: string; capacity: number } | null;
    user: { id: number; name: string; email: string } | null;
}

interface Filters {
    status: string | null;
    date: string | null;
    search: string | null;
}

const props = defineProps<{
    reservations: Reservation[];
    tables: TableOption[];
    filters: Filters;
}>();

const filterForm = reactive({
    status: props.filters.status ?? '',
    date: props.filters.date ?? '',
    search: props.filters.search ?? '',
});

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

function applyFilters(): void {
    router.get('/admin/reservas', {
        status: filterForm.status || undefined,
        date: filterForm.date || undefined,
        search: filterForm.search || undefined,
    }, { preserveState: true, preserveScroll: true, replace: true });
}

function resetFilters(): void {
    filterForm.status = '';
    filterForm.date = '';
    filterForm.search = '';
    router.get('/admin/reservas', {}, { preserveState: true, preserveScroll: true, replace: true });
}

function save(r: Reservation): void {
    router.patch(`/admin/reservas/${r.id}`, {
        status: r.status,
        restaurant_table_id: r.restaurant_table_id,
    }, { preserveScroll: true });
}
</script>

<template>
    <Head title="Panel · Reservas" />
    <AdminLayout>
        <form
            class="mb-6 grid gap-3 rounded-2xl border border-tiki-bamboo/30 bg-white/90 p-4 shadow-sm sm:grid-cols-4"
            @submit.prevent="applyFilters"
        >
            <div>
                <label class="text-xs font-medium text-tiki-bamboo">Estado</label>
                <select
                    v-model="filterForm.status"
                    class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none"
                >
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="confirmada">Confirmada</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-tiki-bamboo">Fecha</label>
                <input
                    v-model="filterForm.date"
                    type="date"
                    class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none"
                />
            </div>
            <div>
                <label class="text-xs font-medium text-tiki-bamboo">Buscar (nombre o teléfono)</label>
                <input
                    v-model="filterForm.search"
                    type="search"
                    placeholder="Antonio, 600…"
                    class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none"
                />
            </div>
            <div class="flex items-end gap-2">
                <button
                    type="submit"
                    class="rounded-full bg-tiki-sunset px-4 py-2 text-sm font-semibold text-white shadow hover:bg-tiki-sunset-dark"
                >
                    Filtrar
                </button>
                <button
                    type="button"
                    class="rounded-full border border-tiki-bamboo/40 px-4 py-2 text-sm font-semibold text-tiki-night hover:bg-tiki-bamboo/10"
                    @click="resetFilters"
                >
                    Limpiar
                </button>
            </div>
        </form>

        <p class="mb-3 text-sm text-tiki-bamboo">{{ reservations.length }} reserva(s)</p>

        <div v-if="reservations.length === 0" class="rounded-2xl border-2 border-dashed border-tiki-bamboo/40 bg-white/60 p-10 text-center">
            <p class="font-semibold text-tiki-night">No hay reservas que coincidan con el filtro.</p>
        </div>

        <div v-else class="space-y-3">
            <article
                v-for="r in reservations"
                :key="r.id"
                class="rounded-2xl border border-tiki-bamboo/30 bg-white/90 p-4 shadow-sm"
            >
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="font-bold text-tiki-night">
                            {{ r.contact_name }}
                            <span class="ml-1 text-sm font-medium text-tiki-bamboo">· {{ r.contact_phone }}</span>
                        </p>
                        <p class="text-sm text-tiki-bamboo">
                            {{ fmtDate(r.reservation_date) }} a las {{ fmtTime(r.reservation_time) }} ·
                            {{ r.adults }} adultos<span v-if="r.children > 0"> + {{ r.children }} niños</span> ·
                            zona {{ r.zone_preference }}
                        </p>
                        <p v-if="r.user" class="text-xs text-tiki-bamboo">Cuenta: {{ r.user.name }} ({{ r.user.email }})</p>
                        <p v-if="r.notes" class="mt-1 rounded bg-tiki-sand px-2 py-1 text-xs text-tiki-night/80">{{ r.notes }}</p>
                    </div>
                    <span
                        class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-widest"
                        :class="statusStyles[r.status]"
                    >
                        {{ r.status }}
                    </span>
                </div>

                <div class="mt-3 flex flex-wrap items-end gap-3 border-t border-tiki-bamboo/20 pt-3">
                    <div>
                        <label class="text-xs font-medium text-tiki-bamboo">Estado</label>
                        <select
                            v-model="r.status"
                            class="mt-1 block rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-1.5 text-sm focus:border-tiki-sunset focus:outline-none"
                        >
                            <option value="pendiente">Pendiente</option>
                            <option value="confirmada">Confirmada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-tiki-bamboo">Mesa asignada</label>
                        <select
                            v-model="r.restaurant_table_id"
                            class="mt-1 block rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-1.5 text-sm focus:border-tiki-sunset focus:outline-none"
                        >
                            <option :value="null">Sin asignar</option>
                            <option v-for="t in tables" :key="t.id" :value="t.id">
                                {{ t.code }} — {{ t.zone }} ({{ t.capacity }}p){{ t.is_active ? '' : ' · inactiva' }}
                            </option>
                        </select>
                    </div>
                    <button
                        type="button"
                        class="rounded-full bg-tiki-leaf px-4 py-1.5 text-sm font-semibold text-white shadow hover:bg-tiki-leaf-dark"
                        @click="save(r)"
                    >
                        Guardar
                    </button>
                </div>
            </article>
        </div>
    </AdminLayout>
</template>
