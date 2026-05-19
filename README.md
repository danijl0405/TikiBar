# Tiki Bar 🗿

> Chiringuito malagueño hecho web. Espetos al fuego, cócteles tropicales y la mejor puesta de sol del Mediterráneo.

Aplicación completa para el restaurante **Tiki Bar** (Málaga): home cinematográfica con vídeo de fondo y storytelling al hacer scroll, carta navegable, registro de clientes, login y reserva de mesas con asignación automática por aforo y zona.

---

## Stack

- **Backend:** [Laravel 13](https://laravel.com/) (PHP 8.3+)
- **Frontend:** [Inertia.js](https://inertiajs.com/) + [Vue 3](https://vuejs.org/) (Composition API + `<script setup lang="ts">`)
- **Estilos:** [Tailwind CSS v4](https://tailwindcss.com/) con paleta tiki personalizada
- **Build:** [Vite](https://vite.dev/) + [Wayfinder](https://github.com/laravel/wayfinder)
- **Base de datos:** MySQL 8 (configurable por `.env`)

---

## Requisitos

- PHP **8.3** o superior
- [Composer](https://getcomposer.org/) 2.x
- [Node.js](https://nodejs.org/) 20+ y npm
- MySQL 8 (o MariaDB 10.6+) corriendo en local
- `ffmpeg` (opcional, solo si quieres regenerar el poster del vídeo)

---

## Puesta en marcha

```bash
# 1. Dependencias
composer install
npm install

# 2. Variables de entorno
cp .env.example .env
php artisan key:generate

# 3. Crea la base de datos en MySQL
#    Por ejemplo desde el cliente de MySQL:
#    CREATE DATABASE tikidb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
#    Después ajusta DB_USERNAME / DB_PASSWORD en .env

# 4. Migraciones + semillas (carta malagueña, mesas y usuarios)
php artisan migrate --seed

# 5. Compila los assets
npm run build

# 6. Arranca el servidor
php artisan serve            # http://127.0.0.1:8000
#  o, para desarrollo con hot reload + cola + vite a la vez:
composer dev
```

---

## Vídeo de portada

La home reproduce un MP4 en bucle como fondo. Por buenas prácticas el binario **no está versionado** (cada commit se queda en el historial de Git para siempre), así que tras clonar el repo tienes dos opciones:

1. **Vídeo local.** Coloca tu archivo en `public/videos/hero.mp4` (formato MP4 H.264, 1920×1080, sin audio, idealmente 5–15 MB). El poster `public/videos/hero-poster.jpg` ya viene en el repo. Más detalles en `public/videos/README.txt`.
2. **Vídeo remoto (CDN).** Edita el `.env`:
   ```env
   TIKI_HERO_VIDEO="https://tu-cdn.com/hero.mp4"
   ```
   No hace falta volver a buildear, es un valor en runtime.

Si el vídeo no carga, la home muestra automáticamente un degradado animado tropical de respaldo — la página nunca se ve rota.

> El vídeo original que usamos en desarrollo viene de Pexels:
> [Beach Waves And Sunset (855633)](https://www.pexels.com/video/beach-waves-and-sunset-855633/) — licencia gratuita Pexels.

---

## Usuarios precargados

Los seeders insertan dos cuentas listas para probar:

| Rol            | Email                 | Contraseña    |
|----------------|-----------------------|---------------|
| Administrador  | `admin@tikibar.es`    | `tikibar123`  |
| Cliente        | `cliente@tikibar.es`  | `password`    |

(Los teléfonos son ficticios y se pueden cambiar en `database/seeders/DatabaseSeeder.php`.)

---

## Rutas principales

| Método | URL                       | Descripción                                  | Acceso       |
|--------|---------------------------|----------------------------------------------|--------------|
| GET    | `/`                       | Home cinematográfica con vídeo y storytelling | Público      |
| GET    | `/carta`                  | Carta completa (10 categorías, 41 platos)    | Público      |
| GET    | `/login` / `/registro`    | Auth                                         | Invitado     |
| POST   | `/logout`                 | Cerrar sesión                                | Autenticado  |
| GET    | `/reservas`               | Mis reservas                                 | Autenticado  |
| GET    | `/reservas/nueva`         | Formulario de reserva                        | Autenticado  |
| POST   | `/reservas`               | Crear reserva (asigna mesa por aforo+zona)   | Autenticado  |
| DELETE | `/reservas/{id}`          | Cancelar reserva propia                      | Autenticado  |

Lista completa: `php artisan route:list`.

---

## Comandos útiles

```bash
# Recargar la base con los seeders desde cero
php artisan migrate:fresh --seed

# Lint y formateo
composer lint        # Pint (PHP)
npm run lint         # ESLint (Vue/TS)
npm run format       # Prettier
npm run types:check  # vue-tsc

# Tests
composer test

# Solo recompilar assets
npm run build        # producción
npm run dev          # desarrollo con HMR
```

---

## Estructura

```
TikiWeb/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Home, Menu, Reservation, Auth/*
│   │   └── Middleware/         # HandleInertiaRequests (comparte auth + tiki + flash)
│   └── Models/                 # Category, MenuItem, RestaurantTable, Reservation, User
├── config/
│   └── tikibar.php             # phone, address, email, hero_video, hero_poster
├── database/
│   ├── migrations/             # users + phone, categories, menu_items, restaurant_tables, reservations
│   └── seeders/                # CategorySeeder, MenuItemSeeder, RestaurantTableSeeder, DatabaseSeeder
├── public/videos/              # hero.mp4 (gitignore), hero-poster.jpg, README.txt
├── resources/
│   ├── css/app.css             # Tailwind + paleta tiki (sand, leaf, sunset, ocean…)
│   └── js/
│       ├── Layouts/TikiLayout.vue
│       └── pages/              # Welcome, Menu, Auth/Login, Auth/Register, Reservations/Index, Reservations/Create
└── routes/web.php
```

---

## Variables de entorno relevantes

```env
APP_NAME="Tiki Bar"
APP_LOCALE=es

DB_CONNECTION=mysql
DB_DATABASE=tikidb
DB_USERNAME=root
DB_PASSWORD=

TIKI_PHONE="+34 952 12 34 56"
TIKI_ADDRESS="Paseo Marítimo Pablo Ruiz Picasso, 29, 29017 Málaga"
TIKI_EMAIL="hola@tikibar.es"
TIKI_HERO_VIDEO="/videos/hero.mp4"
TIKI_HERO_POSTER="/videos/hero-poster.jpg"
```

El resto son las defaults de Laravel (sesiones en BD, cache en BD, queue en BD).

---

## Créditos

- Diseño y código: equipo Tiki Bar.
- Vídeo de portada por defecto: [Pixabay en Pexels](https://www.pexels.com/video/beach-waves-and-sunset-855633/).
- Iconos: emoji nativos del sistema.

Hecho con ❤️ a la sombra de las palmeras en Málaga.
