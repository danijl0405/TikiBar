<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

interface Category {
    id: number;
    name: string;
    type: 'food' | 'drink';
}

interface MenuItem {
    id: number;
    category_id: number;
    name: string;
    description: string | null;
    price: string;
    contains_alcohol: boolean;
    is_available: boolean;
    emoji: string | null;
    category: Category | null;
}

const props = defineProps<{
    categories: Category[];
    items: MenuItem[];
}>();

const editingId = ref<number | null>(null);

const form = useForm({
    category_id: props.categories[0]?.id ?? 0,
    name: '',
    description: '',
    price: 0,
    contains_alcohol: false,
    is_available: true,
    emoji: '',
});

const grouped = computed(() =>
    props.categories.map((category) => ({
        category,
        items: props.items.filter((item) => item.category_id === category.id),
    })),
);

function startEdit(item: MenuItem): void {
    editingId.value = item.id;
    form.category_id = item.category_id;
    form.name = item.name;
    form.description = item.description ?? '';
    form.price = Number(item.price);
    form.contains_alcohol = item.contains_alcohol;
    form.is_available = item.is_available;
    form.emoji = item.emoji ?? '';
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
        form.patch(`/admin/carta/${editingId.value}`, {
            preserveScroll: true,
            onSuccess: () => cancelEdit(),
        });
    } else {
        form.post('/admin/carta', {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    }
}

function remove(item: MenuItem): void {
    if (confirm(`¿Eliminar «${item.name}» de la carta?`)) {
        router.delete(`/admin/carta/${item.id}`, { preserveScroll: true });
    }
}

function toggleAvailable(item: MenuItem): void {
    router.patch(`/admin/carta/${item.id}`, {
        category_id: item.category_id,
        name: item.name,
        description: item.description,
        price: item.price,
        contains_alcohol: item.contains_alcohol,
        is_available: !item.is_available,
        emoji: item.emoji,
    }, { preserveScroll: true });
}
</script>

<template>
    <Head title="Panel · Carta" />
    <AdminLayout>
        <form
            class="mb-8 space-y-4 rounded-2xl border border-tiki-bamboo/30 bg-white/90 p-5 shadow-sm"
            @submit.prevent="submit"
        >
            <h2 class="text-lg font-bold text-tiki-night">
                {{ editingId === null ? 'Nuevo plato' : 'Editar plato' }}
            </h2>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-medium">Categoría</label>
                    <select
                        v-model.number="form.category_id"
                        class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none"
                    >
                        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                    <p v-if="form.errors.category_id" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.category_id }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium">Nombre</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none"
                    />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.name }}</p>
                </div>
            </div>

            <div>
                <label class="text-sm font-medium">Descripción</label>
                <textarea
                    v-model="form.description"
                    rows="2"
                    class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none"
                />
                <p v-if="form.errors.description" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.description }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="text-sm font-medium">Precio (€)</label>
                    <input
                        v-model.number="form.price"
                        type="number"
                        step="0.01"
                        min="0"
                        class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none"
                    />
                    <p v-if="form.errors.price" class="mt-1 text-xs text-tiki-sunset-dark">{{ form.errors.price }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium">Emoji (opcional)</label>
                    <input
                        v-model="form.emoji"
                        type="text"
                        maxlength="8"
                        class="mt-1 w-full rounded-lg border border-tiki-bamboo/40 bg-white px-3 py-2 text-sm focus:border-tiki-sunset focus:outline-none"
                    />
                </div>
                <div class="flex flex-col justify-end gap-2 pb-1">
                    <label class="flex items-center gap-2 text-sm font-medium">
                        <input v-model="form.is_available" type="checkbox" class="h-4 w-4 rounded" />
                        Disponible
                    </label>
                    <label class="flex items-center gap-2 text-sm font-medium">
                        <input v-model="form.contains_alcohol" type="checkbox" class="h-4 w-4 rounded" />
                        Contiene alcohol
                    </label>
                </div>
            </div>

            <div class="flex gap-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-full bg-tiki-sunset px-5 py-2 text-sm font-semibold text-white shadow hover:bg-tiki-sunset-dark disabled:opacity-60"
                >
                    {{ editingId === null ? 'Añadir plato' : 'Guardar cambios' }}
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

        <div v-for="group in grouped" :key="group.category.id" class="mb-6">
            <h3 class="mb-2 text-sm font-semibold uppercase tracking-widest text-tiki-leaf">
                {{ group.category.name }}
            </h3>

            <p v-if="group.items.length === 0" class="text-sm text-tiki-bamboo">Sin platos en esta categoría.</p>

            <div v-else class="space-y-2">
                <article
                    v-for="item in group.items"
                    :key="item.id"
                    class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-tiki-bamboo/30 bg-white/90 px-4 py-3 shadow-sm"
                    :class="{ 'opacity-60': !item.is_available }"
                >
                    <div class="min-w-0">
                        <p class="font-semibold text-tiki-night">
                            <span v-if="item.emoji">{{ item.emoji }} </span>{{ item.name }}
                            <span v-if="item.contains_alcohol" class="ml-1 text-xs text-tiki-sunset-dark">(alcohol)</span>
                        </p>
                        <p v-if="item.description" class="truncate text-xs text-tiki-bamboo">{{ item.description }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-tiki-night">{{ Number(item.price).toFixed(2) }} €</span>
                        <button
                            type="button"
                            class="rounded-full px-3 py-1 text-xs font-semibold"
                            :class="item.is_available
                                ? 'bg-tiki-leaf/15 text-tiki-leaf-dark hover:bg-tiki-leaf/25'
                                : 'bg-gray-200 text-gray-600 hover:bg-gray-300'"
                            @click="toggleAvailable(item)"
                        >
                            {{ item.is_available ? 'Disponible' : 'Oculto' }}
                        </button>
                        <button
                            type="button"
                            class="rounded-full border border-tiki-bamboo/40 px-3 py-1 text-xs font-semibold text-tiki-night hover:bg-tiki-bamboo/10"
                            @click="startEdit(item)"
                        >
                            Editar
                        </button>
                        <button
                            type="button"
                            class="rounded-full border border-tiki-sunset/50 px-3 py-1 text-xs font-semibold text-tiki-sunset-dark hover:bg-tiki-sunset/10"
                            @click="remove(item)"
                        >
                            Eliminar
                        </button>
                    </div>
                </article>
            </div>
        </div>
    </AdminLayout>
</template>
