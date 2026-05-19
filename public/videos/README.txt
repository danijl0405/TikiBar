Tiki Bar — vídeo de portada
============================

Archivos servidos como fondo de la home:

  hero.mp4         (vídeo principal del fondo, formato MP4 H.264, sin audio)
  hero-poster.jpg  (imagen de respaldo si el navegador no puede reproducir el vídeo)

Material por defecto incluido:
  Vídeo "Beach Waves And Sunset" de Pixabay, descargado de Pexels.
  https://www.pexels.com/video/beach-waves-and-sunset-855633/
  Licencia gratuita Pexels (uso comercial sin atribución obligatoria).

Rutas configuradas en config/tikibar.php y en .env:

  TIKI_HERO_VIDEO="/videos/hero.mp4"
  TIKI_HERO_POSTER="/videos/hero-poster.jpg"

Si no encuentra el vídeo, la página muestra un degradado animado de respaldo,
así la web nunca se rompe.

Para cambiar el vídeo:
  - Sustituye hero.mp4 (y opcionalmente hero-poster.jpg) por los tuyos,
    manteniendo los mismos nombres, o
  - Sube el vídeo a un CDN y apunta TIKI_HERO_VIDEO al URL absoluto en .env.

Recomendaciones:
- 1920x1080, 24-30 fps
- 5-15 MB para una carga ágil
- Sin audio (los navegadores requieren mute para autoplay)
- Plano con movimiento sutil (olas, palmeras, gente paseando)
- Stocks gratuitos: pexels.com/videos, coverr.co, mixkit.co
