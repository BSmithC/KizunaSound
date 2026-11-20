# KizunaSound — Plataforma musical

## Arquitectura

- **Backend**: Laravel 13, Eloquent, MySQL, migraciones aditivas.
- **Frontend**: Inertia 2 + Vue 3 ( Composition API ) + Pinia 3 + Vite 8 + Ziggy.
- **Estilos**: Tailwind 3 + sistema de clases propio `.ks-*`.
- **Identidad visual**: paleta japonesa Sumi/Washi/Shu, layout tipo Koel y animaciones mínimas.
- **No se introdujo** `routes/api.php` ni una segunda arquitectura frontend.

## Fases

| Fase | Estado | Contenido |
| --- | --- | --- |
| 1 | Completada | Auditoría del proyecto sin modificaciones. |
| 2 | Completada | 15 migraciones aditivas, modelos y relaciones, 38 FK, backfill real. |
| 3 | Completada | Diseño oscuro, layout, navegación y páginas Inicio/Explorar/Buscar/Biblioteca/Favoritos. |
| 4 | Completada | Reproductor persistente, cola, shuffle/repeat, historial, favoritos y errores de audio. |
| 5 | Completada | Acciones de biblioteca, policies, CRUD de playlists y páginas de detalle. |
| 6 | Completada | Catálogo administrable: canciones, géneros, álbumes y artistas. |
| 7 | Completada | Seguridad: policies por propiedad/rol, sin asignación automática de admin. |
| 8 | Completada | Pruebas de regresión y limpieza de estilo. |
| 9 | Completada | Pulido responsive y accesibilidad. |
| 10 | Completada | Música por estado de ánimo basada en datos reales. |
| 11 | Completada | Rediseño visual completo, panel lateral de detalles y reducción de animaciones. |
| 12 | Completada | Alternancia de tema Sumi/Washi realmente operativa y música reciente sin canciones repetidas. |

## Sistema visual

- **Paleta**: Negro Sumi `#171717`, gris carbón `#292D32`, rojo japonés `#B52B32`, beige Washi `#D9CDB8`, verde musgo `#778477` y blanco cálido `#F3F0E9`.
- **Distribución**: sidebar izquierda compacta, área central, panel lateral derecho de detalles y reproductor inferior global.
- **Panel de detalles**: `detailsStore.js` es independiente del reproductor; muestra canción, álbum, artista, playlist o cola y se abre/cierra sin detener la música.
- **Motivos**: patrones sutiles Washi/Seigaiha/montaña/sol y tipografías Inter + Noto Sans JP + Noto Serif JP; iconos minimalistas sin emojis.
- **Animaciones**: sin entradas, desplazamientos ni `scale`. Solo feedback corto de color/estado (150 ms) y fundidos de 120 ms para el panel y la vista ampliada del reproductor. Se conservan los indicadores de carga `animate-spin`.
- **Tema**: Sumi oscuro por defecto con bootstrap en `app.blade.php` para evitar destello; `theme.js` sincroniza `--ks-accent` y `--ks-accent-rgb` con el acento guardado. Los tokens RGB de la paleta (`theme.css`) alimentan las utilidades de Tailwind, de modo que el botón alterna fondo, textos, bordes y superficies entre Sumi y Washi sin perder los modificadores de opacidad. El texto sobre fondos de acento permanece en blanco cálido en ambos temas. El reproductor y el panel de detalles usan los mismos tokens en sus estilos con ámbito (iconos de control, hover de la cola y sombras), de modo que los controles siguen siendo legibles en Washi.
- **Música reciente**: `CatalogService` agrupa el historial por `song_id` y conserva la escucha más reciente, tanto en Inicio (8 canciones) como en la pestaña Historial de Biblioteca (30), evitando repetidos.

## Estructura relevante

- `app/Services/CatalogService.php`: consultas de descubrimiento y biblioteca.
- `app/Http/Controllers/LibraryActionController.php`: favoritos, historial, álbumes guardados, artistas seguidos y canciones en playlists.
- `app/Http/Controllers/MoodController.php`: agrupación por estado de ánimo real.
- `app/Policies/`: propiedad y roles para contenido.
- `resources/js/stores/playerStore.js`: estado global del reproductor.
- `resources/js/stores/detailsStore.js`: estado del panel lateral de detalles.
- `resources/js/Components/MusicPlayer.vue`: reproductor persistente.
- `resources/js/Components/Music/DetailsPanel.vue`: panel lateral de detalles/cola.
- `resources/css/theme.css`, `resources/css/app.css`: tokens de color y sistema visual `.ks-*`.
- `resources/js/helpers/libraryApi.js`: puente JS con las acciones de biblioteca.
- `resources/js/Components/Music/`: componentes reutilizables del catálogo.
- `tests/Feature/PlatformLibraryTest.php`, `CatalogServiceTest.php` y `CatalogCrudTest.php`: regresiones de biblioteca, descubrimiento y CRUD.

## Decisiones de datos

- Se conserva `songs.artist_id` original (usuario subidor) y se añade `artist_ref_id` hacia `artists`.
- `genders` sigue siendo la tabla canónica de géneros; `Genre` es el modelo canónico y `Gender` un alias.
- No se crean administradores automáticamente.
- No hay canciones, artistas ni imágenes ficticias. Los estados de ánimo se agrupan desde `songs.mood`, un campo real Asignable desde el formulario de canción.

## Verificación final

- `php artisan test --compact`: **38 pruebas, 145 aserciones, 0 fallos**.
- `npm run build`: correcto (862 módulos; único aviso no bloqueante: `caniuse-lite` desactualizado).
- `vendor\bin\pint --test`: correcto en todo el proyecto.
- `php artisan route:list --except-vendor`: 67 rutas, incluidas `artists.*`, `library.*`, `mood.index` y `playlists.songs.toggle`.
- Smoke HTTP autenticado con datos reales: 16 páginas responden 200 y renderizan el componente Inertia esperado.
- Smoke adicional temporal del rediseño: 24 páginas y 241 aserciones Inertia correctas (prueba eliminada después de verificar).
- Smoke temporal tras el arreglo de tema: 8 páginas autenticadas y 80 aserciones Inertia correctas (prueba eliminada después de verificar).
- Móvil: la navegación inferior y el reproductor comparten una única pila fija; la nav incluye Inicio, Explorar, Buscar, Biblioteca, Favoritos, Playlists y Ánimo, y el footer ya no queda cubierto.
- Auditoría final Vue/Inertia: sin errores bloqueantes; se corrigieron el doble incremento de `play_count`, la URL de stream hardcodeada (ahora `route('songs.stream', ...)`), el estado inicial de favoritos/guardados/siguiendo y la uniformidad de imports `@/`.

### Pendientes no bloqueantes

- `caniuse-lite` desactualizado (solo aviso de Vite/Browserslist; se resuelve con `npx update-browserslist-db@latest`).
- `resources/js/Components/RecipeSelector.vue` conserva `console.log` y una imagen externa de recetas; es código previo no relacionado con el catálogo musical y se dejó intacto para no alterar funcionalidad existente.
- Sincronización de favoritos entre pestañas: el store usa `localStorage`; no se añadió sondeo ni canal global.

## Comandos

```bash
php artisan migrate
php artisan test --compact
npm run build
vendor\bin\pint --test
```

## Rutas principales

- `/home`, `/explore`, `/search`, `/library`, `/favorites`, `/mood`
- Resource routes: `/songs`, `/albums`, `/playlists`, `/genders`, `/artists`
- Acciones: `library.favorites.toggle`, `library.history.record`, `library.albums.toggle`, `library.artists.toggle`, `playlists.songs.toggle`
