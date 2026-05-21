<script setup lang="ts">
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';

defineProps<{
    phone: string;
    highlights: Array<{
        id: number;
        name: string;
        slug: string;
        description: string | null;
        items: Array<{ id: number; name: string; description: string | null; price: string; emoji: string | null }>;
    }>;
}>();

const page = usePage();
const auth = computed(() => page.props.auth as { user: { id: number; name: string } | null });
const tiki = computed(
    () =>
        page.props.tiki as {
            phone: string;
            address: string;
            email: string;
            heroVideo: string;
            heroPoster: string;
        },
);

const videoError = ref(false);
const scrolled = ref(false);
const visible = ref<Set<number>>(new Set());

let observer: IntersectionObserver | null = null;
let onScroll: (() => void) | null = null;

onMounted(() => {
    onScroll = () => {
        scrolled.value = window.scrollY > 80;
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    observer = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting) {
                    const idx = Number((entry.target as HTMLElement).dataset.idx);

                    if (!Number.isNaN(idx)) {
                        const next = new Set(visible.value);
                        next.add(idx);
                        visible.value = next;
                    }

                    observer?.unobserve(entry.target);
                }
            }
        },
        { threshold: 0.18, rootMargin: '0px 0px -10% 0px' },
    );
    document.querySelectorAll<HTMLElement>('[data-reveal]').forEach((el) => observer?.observe(el));
});

onBeforeUnmount(() => {
    observer?.disconnect();

    if (onScroll) {
window.removeEventListener('scroll', onScroll);
}
});

function isVisible(i: number) {
    return visible.value.has(i);
}

function revealClasses(i: number) {
    return [
        'transition-all duration-1000 ease-out will-change-transform',
        isVisible(i) ? 'opacity-100 translate-y-0 blur-0' : 'opacity-0 translate-y-12 blur-[2px]',
    ];
}

function logout() {
    router.post('/logout');
}

const telHref = computed(() => `tel:${tiki.value.phone.replace(/\s+/g, '')}`);
</script>

<template>
    <Head title="Inicio">
        <meta name="theme-color" content="#1b1b18" />
    </Head>

    <!-- Fondo de vídeo fijo a pantalla completa -->
    <div class="fixed inset-0 -z-10 overflow-hidden bg-tiki-night">
        <video
            v-if="!videoError"
            class="absolute inset-0 h-full w-full object-cover"
            :src="tiki.heroVideo"
            :poster="tiki.heroPoster"
            autoplay
            loop
            muted
            playsinline
            preload="auto"
            @error="videoError = true"
        />
        <!-- Respaldo animado si el vídeo no carga -->
        <div
            v-else
            class="absolute inset-0 animate-[hero-fallback_18s_ease_infinite] bg-[linear-gradient(120deg,#0a7ea4,#ff6b35,#ff9d76,#1f5436)] bg-[length:300%_300%]"
        />
        <!-- Capa oscura para legibilidad -->
        <div class="absolute inset-0 bg-gradient-to-b from-tiki-night/40 via-tiki-night/55 to-tiki-night/90" />
        <!-- Vignette lateral suave -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_transparent_45%,_rgba(0,0,0,0.55)_100%)]" />
    </div>

    <!-- Cabecera transparente que se solidifica al hacer scroll -->
    <header
        class="fixed top-0 left-0 right-0 z-30 transition-all duration-500"
        :class="scrolled ? 'border-b border-tiki-bamboo/30 bg-tiki-night/85 backdrop-blur-md shadow-lg' : 'bg-transparent'"
    >
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6">
            <Link href="/" class="flex items-center gap-2 text-2xl font-semibold tracking-tight text-tiki-sand drop-shadow">
                <span>Tiki Bar</span>
                <span class="ml-1 hidden text-xs font-medium uppercase tracking-widest text-tiki-coral sm:inline">Málaga</span>
            </Link>
            <nav class="flex items-center gap-2 sm:gap-3">
                <Link href="/carta" class="rounded-full px-3 py-1.5 text-sm font-medium text-tiki-sand hover:bg-white/10">
                    Carta
                </Link>
                <a
                    :href="telHref"
                    class="hidden items-center gap-1 rounded-full bg-tiki-leaf px-3 py-1.5 text-sm font-medium text-white shadow hover:bg-tiki-leaf-dark sm:inline-flex"
                >
                    {{ tiki.phone }}
                </a>
                <template v-if="auth.user">
                    <Link href="/reservas" class="rounded-full px-3 py-1.5 text-sm font-medium text-tiki-sand hover:bg-white/10">
                        Mis reservas
                    </Link>
                    <button
                        type="button"
                        class="rounded-full bg-tiki-sand/15 px-3 py-1.5 text-sm font-medium text-tiki-sand hover:bg-tiki-sand/25"
                        @click="logout"
                    >
                        Salir
                    </button>
                </template>
                <template v-else>
                    <Link href="/login" class="rounded-full px-3 py-1.5 text-sm font-medium text-tiki-sand hover:bg-white/10">
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

    <main class="relative text-tiki-sand">
        <!-- HERO -->
        <section class="relative flex min-h-screen items-center justify-center px-4 pt-24 pb-16 text-center">
            <div
                data-reveal
                data-idx="0"
                :class="revealClasses(0)"
                class="max-w-3xl rounded-[2.5rem] border border-white/15 bg-tiki-night/55 px-6 py-12 shadow-2xl backdrop-blur-xl sm:px-14 sm:py-16"
            >
                <p class="text-xs font-semibold uppercase tracking-[0.4em] text-tiki-coral drop-shadow">
                    Chiringuito malagueño · Desde 1998
                </p>
                <h1 class="mt-6 text-6xl font-extrabold tracking-tight text-tiki-sand drop-shadow-[0_4px_18px_rgba(0,0,0,0.55)] sm:text-7xl md:text-8xl">
                    Tiki Bar
                </h1>
                <p class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-tiki-sand/90 drop-shadow sm:text-xl">
                    Donde Málaga se viste de aloha. Espetos al fuego, cócteles tropicales y la mejor
                    puesta de sol del Mediterráneo.
                </p>
                <div class="mt-10 flex flex-wrap justify-center gap-3">
                    <a
                        :href="telHref"
                        class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 font-semibold text-tiki-sunset-dark shadow-xl hover:bg-tiki-sand"
                    >
                        Llámanos
                    </a>
                    <Link
                        v-if="auth.user"
                        href="/reservas/nueva"
                        class="inline-flex items-center gap-2 rounded-full bg-tiki-sunset px-6 py-3 font-semibold text-white shadow-xl hover:bg-tiki-sunset-dark"
                    >
                        Reservar mesa
                    </Link>
                    <Link
                        v-else
                        href="/login"
                        class="inline-flex items-center gap-2 rounded-full bg-tiki-sunset px-6 py-3 font-semibold text-white shadow-xl hover:bg-tiki-sunset-dark"
                    >
                        Reservar mesa
                    </Link>
                    <Link
                        href="/carta"
                        class="inline-flex items-center gap-2 rounded-full border-2 border-white/70 px-6 py-3 font-semibold text-tiki-sand hover:bg-white/10"
                    >
                        Ver carta
                    </Link>
                </div>
            </div>

            <!-- Indicador de scroll -->
            <div
                class="pointer-events-none absolute bottom-8 left-1/2 -translate-x-1/2 text-tiki-sand/80"
                :class="scrolled ? 'opacity-0 transition-opacity duration-500' : 'opacity-100'"
            >
                <p class="text-xs uppercase tracking-[0.4em]">Desliza para conocernos</p>
                <div class="mx-auto mt-2 h-10 w-px animate-bounce bg-tiki-sand/70" />
            </div>
        </section>

        <!-- CAPÍTULO 1: ORIGEN -->
        <section class="relative px-4 py-32 sm:py-40">
            <div class="mx-auto max-w-3xl">
                <div data-reveal data-idx="1" :class="revealClasses(1)" class="rounded-3xl border border-white/10 bg-tiki-night/55 p-8 shadow-2xl backdrop-blur-xl sm:p-12">
                    <p class="text-xs font-semibold uppercase tracking-[0.4em] text-tiki-coral">Capítulo 1 — El origen</p>
                    <h2 class="mt-4 text-4xl font-bold sm:text-5xl">Una caseta de madera frente al mar</h2>
                    <p class="mt-6 text-lg leading-relaxed text-tiki-sand/90">
                        En 1998, Pedro y María plantaron cuatro cañas de bambú y una lona en la
                        Playa de la Misericordia. Empezaron con dos hornillas, una nevera vieja y
                        las sardinas que el padre de Pedro traía cada mañana de la lonja.
                    </p>
                    <p class="mt-4 text-lg leading-relaxed text-tiki-sand/80">
                        Veinticinco veranos después, el Tiki Bar sigue donde siempre, con el mismo
                        ritual: leña de olivo, sal gorda, y una sonrisa para cada vecino que pasa.
                    </p>
                </div>
            </div>
        </section>

        <!-- CAPÍTULO 2: BRASA -->
        <section class="relative px-4 py-32 sm:py-40">
            <div class="mx-auto grid max-w-5xl gap-10 sm:grid-cols-2 sm:items-center">
                <div
                    data-reveal
                    data-idx="2"
                    :class="revealClasses(2)"
                    class="rounded-3xl border border-white/10 bg-tiki-night/55 p-8 shadow-2xl backdrop-blur-xl"
                >
                    <p class="text-xs font-semibold uppercase tracking-[0.4em] text-tiki-coral">Capítulo 2 — La brasa</p>
                    <h2 class="mt-4 text-4xl font-bold sm:text-5xl">Sal, fuego y aceite de oliva</h2>
                    <p class="mt-6 text-lg leading-relaxed text-tiki-sand/90">
                        Nuestra barca de espetos enciende cada día a las once. Olivo, sal en
                        escamas y pescado que esa misma mañana nadaba en el Mediterráneo.
                    </p>
                    <p class="mt-4 text-lg leading-relaxed text-tiki-sand/80">
                        Aquí no usamos truco ni atajo: solo la receta que aprendimos de los
                        marengos malagueños.
                    </p>
                </div>
                <div
                    data-reveal
                    data-idx="3"
                    :class="revealClasses(3)"
                    class="rounded-3xl border border-white/10 bg-tiki-night/55 p-8 shadow-2xl ring-1 ring-tiki-sunset/30 backdrop-blur-xl"
                >
                    <p class="text-2xl font-semibold text-tiki-coral">+40 espetos por hora</p>
                    <p class="mt-2 text-tiki-sand/80">
                        Sardinas, boquerones, doradas, lubinas, pulpo y secreto ibérico cuando se
                        tercia. Hasta que se acabe la marea.
                    </p>
                </div>
            </div>
        </section>

        <!-- CAPÍTULO 3: COCTELES -->
        <section class="relative px-4 py-32 sm:py-40">
            <div class="mx-auto grid max-w-5xl gap-10 sm:grid-cols-2 sm:items-center">
                <div
                    data-reveal
                    data-idx="4"
                    :class="revealClasses(4)"
                    class="order-2 rounded-3xl border border-white/10 bg-tiki-night/55 p-8 shadow-2xl ring-1 ring-tiki-ocean/30 backdrop-blur-xl sm:order-1"
                >
                    <p class="text-2xl font-semibold text-tiki-coral">Mai Tai, Piña Colada, Mojito…</p>
                    <p class="mt-2 text-tiki-sand/80">
                        Y nuestro <strong>Tiki Bar Special</strong>: tres rones, frutas tropicales y
                        un secreto que solo conoce el camarero.
                    </p>
                </div>
                <div
                    data-reveal
                    data-idx="5"
                    :class="revealClasses(5)"
                    class="order-1 rounded-3xl border border-white/10 bg-tiki-night/55 p-8 shadow-2xl backdrop-blur-xl sm:order-2"
                >
                    <p class="text-xs font-semibold uppercase tracking-[0.4em] text-tiki-coral">Capítulo 3 — La barra</p>
                    <h2 class="mt-4 text-4xl font-bold sm:text-5xl">Cócteles con vista al mar</h2>
                    <p class="mt-6 text-lg leading-relaxed text-tiki-sand/90">
                        Cuando cae el sol, la coctelera empieza a sonar. Frutas frescas, hielo
                        picado y ron de la isla. Cada copa lleva una sombrilla y, si insistes,
                        también una cerilla bengala.
                    </p>
                </div>
            </div>
        </section>

        <!-- CAPÍTULO 4: FAMILIA -->
        <section class="relative px-4 py-32 sm:py-40">
            <div class="mx-auto max-w-3xl text-center">
                <div
                    data-reveal
                    data-idx="6"
                    :class="revealClasses(6)"
                    class="rounded-3xl border border-white/10 bg-tiki-night/55 p-8 shadow-2xl backdrop-blur-xl sm:p-12"
                >
                    <p class="text-xs font-semibold uppercase tracking-[0.4em] text-tiki-coral">Capítulo 4 — La gente</p>
                    <h2 class="mt-4 text-4xl font-bold sm:text-5xl">Una mesa, una familia</h2>
                    <p class="mt-6 text-lg leading-relaxed text-tiki-sand/90">
                        Aquí celebramos bautizos, jubilaciones, primeras citas y empates del Málaga.
                        Tenemos trona para los peques, sombrilla para los abuelos y siempre una
                        mesa más por si os animáis a venir.
                    </p>
                </div>
            </div>
        </section>

        <!-- DESTACADOS DE LA CARTA -->
        <section class="relative px-4 py-24">
            <div class="mx-auto max-w-6xl">
                <div
                    data-reveal
                    data-idx="7"
                    :class="revealClasses(7)"
                    class="flex flex-wrap items-end justify-between gap-3 rounded-3xl border border-white/10 bg-tiki-night/55 px-6 py-5 shadow-2xl backdrop-blur-xl"
                >
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.4em] text-tiki-coral">La carta</p>
                        <h2 class="mt-2 text-4xl font-bold sm:text-5xl">Lo más rico de la casa</h2>
                    </div>
                    <Link href="/carta" class="text-sm font-medium text-tiki-coral hover:underline">
                        Ver carta completa →
                    </Link>
                </div>

                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="(cat, i) in highlights"
                        :key="cat.id"
                        data-reveal
                        :data-idx="8 + i"
                        :class="revealClasses(8 + i)"
                        class="rounded-2xl border border-white/10 bg-tiki-night/65 p-5 shadow-xl backdrop-blur-xl"
                    >
                        <h3 class="text-xl font-semibold text-tiki-coral">{{ cat.name }}</h3>
                        <p v-if="cat.description" class="mt-1 text-sm text-tiki-sand/70">{{ cat.description }}</p>
                        <ul class="mt-4 space-y-3">
                            <li v-for="item in cat.items" :key="item.id" class="flex items-start gap-3">
                                <div class="flex-1">
                                    <div class="flex items-baseline justify-between gap-2">
                                        <p class="font-medium text-tiki-sand">{{ item.name }}</p>
                                        <span class="font-semibold text-tiki-coral">{{ item.price }} €</span>
                                    </div>
                                    <p v-if="item.description" class="text-xs text-tiki-sand/70">
                                        {{ item.description }}
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </article>
                </div>
            </div>
        </section>

        <!-- LLAMADA Y RESERVA -->
        <section class="relative px-4 py-32">
            <div class="mx-auto grid max-w-5xl gap-6 sm:grid-cols-2">
                <div
                    data-reveal
                    data-idx="20"
                    :class="revealClasses(20)"
                    class="rounded-3xl border border-tiki-leaf/40 bg-tiki-night/55 p-8 shadow-xl backdrop-blur-xl"
                >
                    <h3 class="text-2xl font-bold">Pídenos por teléfono</h3>
                    <p class="mt-2 text-tiki-sand/85">
                        Encargo para llevar, dudas con la carta o reservas a última hora.
                    </p>
                    <a
                        :href="telHref"
                        class="mt-5 inline-flex rounded-full bg-tiki-leaf px-5 py-2.5 font-semibold text-white hover:bg-tiki-leaf-dark"
                    >
                        Llamar a {{ phone }}
                    </a>
                </div>
                <div
                    data-reveal
                    data-idx="21"
                    :class="revealClasses(21)"
                    class="rounded-3xl border border-tiki-sunset/40 bg-tiki-night/55 p-8 shadow-xl backdrop-blur-xl"
                >
                    <h3 class="text-2xl font-bold">Reserva tu mesa</h3>
                    <p class="mt-2 text-tiki-sand/85">
                        Dinos cuántos sois, las edades de los peques y la zona favorita. Te
                        confirmamos al instante.
                    </p>
                    <Link
                        v-if="auth.user"
                        href="/reservas/nueva"
                        class="mt-5 inline-flex rounded-full bg-tiki-sunset px-5 py-2.5 font-semibold text-white hover:bg-tiki-sunset-dark"
                    >
                        Reservar ahora
                    </Link>
                    <Link
                        v-else
                        href="/registro"
                        class="mt-5 inline-flex rounded-full bg-tiki-sunset px-5 py-2.5 font-semibold text-white hover:bg-tiki-sunset-dark"
                    >
                        Crear cuenta y reservar
                    </Link>
                </div>
            </div>
        </section>

        <!-- CONTACTO -->
        <section class="relative bg-tiki-night/90 px-4 py-20 backdrop-blur-md">
            <div class="mx-auto grid max-w-5xl gap-6 sm:grid-cols-3">
                <div data-reveal data-idx="22" :class="revealClasses(22)">
                    <p class="text-lg font-semibold text-tiki-coral">Tiki Bar Málaga</p>
                    <p class="mt-2 text-sm text-tiki-sand/80">{{ tiki.address }}</p>
                </div>
                <div data-reveal data-idx="23" :class="revealClasses(23)">
                    <p class="font-semibold">Reservas</p>
                    <a :href="telHref" class="mt-1 block text-sm text-tiki-sand/85 hover:underline">
                        {{ tiki.phone }}
                    </a>
                    <a :href="`mailto:${tiki.email}`" class="mt-1 block text-sm text-tiki-sand/85 hover:underline">
                        {{ tiki.email }}
                    </a>
                </div>
                <div data-reveal data-idx="24" :class="revealClasses(24)">
                    <p class="font-semibold">Horario</p>
                    <p class="mt-1 text-sm text-tiki-sand/80">Lunes a domingo: 12:00 – 01:00</p>
                    <p class="text-sm text-tiki-sand/80">Cocina hasta las 23:30</p>
                </div>
            </div>
            <p class="mt-8 text-center text-xs text-tiki-sand/60">
                © {{ new Date().getFullYear() }} Tiki Bar Málaga — Hecho por Daniel Jimenez.
            </p>
        </section>
    </main>
</template>

<style>
@keyframes hero-fallback {
    0%,
    100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}
</style>
