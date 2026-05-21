<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Datos de contacto del Tiki Bar
    |--------------------------------------------------------------------------
    |
    | Teléfono ficticio que aparece en la web (botón de llamada y reservas).
    | Puedes sobreescribirlo desde el .env con TIKI_PHONE.
    |
    */

    'phone' => env('TIKI_PHONE', '+34 952 12 34 56'),

    'address' => env('TIKI_ADDRESS', 'Paseo Marítimo Pablo Ruiz Picasso, 29, 29017 Málaga'),

    'email' => env('TIKI_EMAIL', 'hola@tikibar.es'),

    /*
    |--------------------------------------------------------------------------
    | Video de portada
    |--------------------------------------------------------------------------
    |
    | URL del vídeo MP4 que se reproduce de fondo en la home. Puedes colocar
    | un archivo en `public/videos/hero.mp4` (ruta por defecto) o apuntar a
    | un CDN externo configurando TIKI_HERO_VIDEO en el .env.
    | Si no se encuentra el archivo, la home muestra un degradado animado.
    |
    */

    'hero_video'  => env('TIKI_HERO_VIDEO', '/videos/hero.mp4'),
    'hero_poster' => env('TIKI_HERO_POSTER', '/videos/hero-poster.jpg'),

    /*
    |--------------------------------------------------------------------------
    | Turnos de reserva
    |--------------------------------------------------------------------------
    |
    | Tandas de reserva cada 1h30. Cada mesa admite una reserva por turno;
    | el turno siguiente de esa mesa queda libre de nuevo. Las horas deben
    | ir en formato "H:i".
    |
    */

    'turns' => ['12:30', '14:00', '15:30', '17:00', '18:30', '20:00', '21:30', '23:00'],
];
