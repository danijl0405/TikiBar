<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import TikiLayout from '@/Layouts/TikiLayout.vue';

interface MenuItem {
    id: number;
    name: string;
    description: string | null;
    price: string;
    emoji: string | null;
    contains_alcohol: boolean;
}

interface Category {
    id: number;
    name: string;
    slug: string;
    type: 'food' | 'drink';
    description: string | null;
    items: MenuItem[];
}

defineProps<{
    categories: Category[];
    phone: string;
}>();
</script>

<template>
    <Head title="Carta" />
    <TikiLayout>
        <header class="mb-8 text-center">
            <p class="text-sm font-semibold uppercase tracking-widest text-tiki-leaf">Tiki Bar</p>
            <h1 class="mt-2 text-4xl font-bold text-tiki-night">Nuestra carta</h1>
            <p class="mx-auto mt-3 max-w-xl text-tiki-bamboo">
                Producto fresco de la lonja de Málaga, brasa de leña de olivo y los mejores cócteles tropicales.
            </p>
        </header>

        <nav class="sticky top-[68px] z-10 -mx-2 mb-6 flex gap-2 overflow-x-auto rounded-2xl bg-white/70 px-2 py-2 backdrop-blur">
            <a
                v-for="cat in categories"
                :key="cat.id"
                :href="`#cat-${cat.slug}`"
                class="whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold text-tiki-night hover:bg-tiki-bamboo/20"
                :class="cat.type === 'drink' ? 'bg-tiki-ocean/10' : 'bg-tiki-sunset/10'"
            >
                {{ cat.name }}
            </a>
        </nav>

        <div class="grid gap-8 lg:grid-cols-2">
            <section
                v-for="cat in categories"
                :id="`cat-${cat.slug}`"
                :key="cat.id"
                class="scroll-mt-32 rounded-2xl border border-tiki-bamboo/30 bg-white/80 p-6 shadow-sm"
            >
                <header class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-tiki-night">{{ cat.name }}</h2>
                        <p v-if="cat.description" class="text-sm text-tiki-bamboo">{{ cat.description }}</p>
                    </div>
                    <span
                        class="rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-widest"
                        :class="cat.type === 'drink' ? 'bg-tiki-ocean/15 text-tiki-ocean-dark' : 'bg-tiki-sunset/15 text-tiki-sunset-dark'"
                    >
                        {{ cat.type === 'drink' ? 'Bebida' : 'Comida' }}
                    </span>
                </header>

                <ul class="divide-y divide-tiki-bamboo/20">
                    <li
                        v-for="item in cat.items"
                        :key="item.id"
                        class="flex items-start gap-3 py-3"
                    >
                        <div class="flex-1">
                            <div class="flex items-baseline justify-between gap-2">
                                <p class="font-medium text-tiki-night">
                                    {{ item.name }}
                                    <span
                                        v-if="item.contains_alcohol"
                                        class="ml-1 rounded bg-tiki-sunset/15 px-1 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-tiki-sunset-dark"
                                    >
                                        +18
                                    </span>
                                </p>
                                <span class="font-semibold text-tiki-leaf-dark">{{ item.price }} €</span>
                            </div>
                            <p v-if="item.description" class="text-xs text-tiki-bamboo">
                                {{ item.description }}
                            </p>
                        </div>
                    </li>
                </ul>
            </section>
        </div>

        <div class="mt-10 rounded-2xl border-2 border-dashed border-tiki-bamboo/40 p-6 text-center">
            <p class="text-tiki-night">
                ¿Quieres pedir para llevar? Llámanos al
                <a :href="`tel:${phone.replace(/\s+/g, '')}`" class="font-semibold text-tiki-sunset-dark hover:underline">
                    {{ phone }}
                </a>
            </p>
        </div>
    </TikiLayout>
</template>
