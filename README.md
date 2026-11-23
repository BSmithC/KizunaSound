<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
    </a>
    <a href="https://www.php.net" target="_blank">
        <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
    </a>
    <a href="https://inertiajs.com" target="_blank">
        <img src="https://img.shields.io/badge/Inertia.js-2-9553E9?style=for-the-badge&logo=inertia&logoColor=white" alt="Inertia.js">
    </a>
    <a href="https://vuejs.org" target="_blank">
        <img src="https://img.shields.io/badge/Vue.js-3-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white" alt="Vue.js">
    </a>
</p>

<p align="center">
    <a href="https://pinia.vuejs.org" target="_blank">
        <img src="https://img.shields.io/badge/Pinia-3-F3CB69?style=for-the-badge&logo=pinia&logoColor=black" alt="Pinia">
    </a>
    <a href="https://tailwindcss.com" target="_blank">
        <img src="https://img.shields.io/badge/Tailwind_CSS-3-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
    </a>
    <a href="https://vite.dev" target="_blank">
        <img src="https://img.shields.io/badge/Vite-8-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
    </a>
    <a href="https://ziggy.dev" target="_blank">
        <img src="https://img.shields.io/badge/Ziggy-2-34D399?style=for-the-badge&logo=javascript&logoColor=black" alt="Ziggy">
    </a>
</p>

<p align="center">
    <a href="https://phpunit.de" target="_blank">
        <img src="https://img.shields.io/badge/PHPUnit-12-0B7A5C?style=for-the-badge&logo=php&logoColor=white" alt="PHPUnit">
    </a>
    <a href="https://laravel.com/docs/13.x/breeze" target="_blank">
        <img src="https://img.shields.io/badge/Laravel_Breeze-2-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Breeze">
    </a>
    <a href="https://www.mysql.com" target="_blank">
        <img src="https://img.shields.io/badge/SQLite_%2F_MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="SQLite / MySQL">
    </a>
    <a href="https://git-scm.com" target="_blank">
        <img src="https://img.shields.io/badge/Git-F05033?style=for-the-badge&logo=git&logoColor=white" alt="Git">
    </a>
</p>

<h1 align="center">KizunaSound</h1>

<p align="center">
    <strong>Plataforma musical con identidad japonesa</strong><br />
    Construida con Laravel 13, Inertia 2, Vue 3, Pinia 3 y Tailwind CSS.
</p>

<br />

---

Plataforma musical donde los usuarios descubren, escuchan y organizan música: inicio, exploración, búsqueda, biblioteca, favoritos, listas de reproducción y recomendaciones por estado de ánimo, con un reproductor persistente que nunca se pierde al navegar.

La interfaz combina una paleta japonesa Sumi/Washi/Shu, un layout tipo Koel con barra lateral compacta, panel lateral de detalles y reproductor inferior global, tipografías Inter y Noto Sans/Serif JP, tema oscuro y claro, y animaciones mínimas.

> **Principio central:** todos los datos son reales. No hay canciones, artistas ni imágenes ficticias; los estados de ánimo se agrupan desde `songs.mood`, un campo real asignable desde el formulario de canción.

Documentación técnica adicional: [`docs/PLATFORM.md`](docs/PLATFORM.md).

---

## Características / Features

| Módulo | Descripción |
| --- | --- |
| **Inicio y descubrimiento** | Música reciente sin repeticiones, lanzamientos y secciones alimentadas por `CatalogService`. |
| **Explorar y buscar** | Catálogo de canciones, álbumes, artistas y géneros con búsqueda y historial local. |
| **Reproductor** | Reproductor persistente Inertia/Vue con cola, shuffle, repeat, historial, favoritos, control de volumen y manejo de errores de audio. |
| **Panel de detalles** | `detailsStore.js` independiente del reproductor: canción, álbum, artista, playlist o cola; se abre y cierra sin detener la música. |
| **Biblioteca** | Favoritos, historial, álbumes guardados, artistas seguidos y pestañas organizadas por el usuario. |
| **Playlists** | CRUD de listas y adición/eliminación de canciones mediante `playlists.songs.toggle`. |
| **Ánimo** | Agrupación por estado de ánimo real a partir del campo `songs.mood`. |
| **Catálogo administrable** | Altas, ediciones y eliminación de canciones, géneros, álbumes y artistas con roles y policies. |
| **Cuenta** | Registro, inicio de sesión, verificación de correo, recuperación de contraseña y perfil con Laravel Breeze. |
| **Tema y responsive** | Sumi oscuro por defecto y Washi claro, con bootstrap sin destello y navegación inferior en móvil compartida con el reproductor. |

---

## Stack tecnológico / Technology Stack

| Tecnología | Propósito |
| --- | --- |
| **Laravel 13** | Framework principal, Eloquent, migraciones y autorización |
| **PHP 8.3+** | Lenguaje del servidor |
| **Inertia 2** | Puente servidor a Vue sin API REST separada |
| **Vue 3 (Composition API)** | Interfaz de usuario reactiva |
| **Pinia 3** | Estado global del reproductor y del panel de detalles |
| **Ziggy 2** | Rutas de Laravel disponibles en JavaScript |
| **Vite 8** | Bundler y servidor de desarrollo |
| **Tailwind CSS 3** | Estilos utilitarios y sistema de clases `.ks-*` |
| **Reka UI y Lucide** | Primitivas accesibles e iconografía |
| **VueUse y vue-toastification** | Utilidades reactivas y notificaciones |
| **Laravel Breeze y Sanctum** | Autenticación, verificación y tokens |
| **SQLite, MySQL o PostgreSQL** | Persistencia (SQLite por defecto) |
| **PHPUnit, Pint y Vite build** | Pruebas, formato y compilación |

---

## Arquitectura / Architecture

### Comunicación servidor a cliente

No existe `routes/api.php` ni una segunda arquitectura frontend. Cada controlador devuelve `Inertia::render(...)` con props consumidas por páginas Vue en `resources/js/Pages`. Las rutas se generan con Ziggy y se invocan por nombre (`route('songs.stream', song)`), sin URLs hardcodeadas.

### Capas principales

- `App\Services\CatalogService`: consultas de descubrimiento, inicio y biblioteca; agrupa el historial por `song_id` conservando la escucha más reciente.
- `App\Http\Controllers\LibraryActionController`: favoritos, historial, álbumes guardados, artistas seguidos y canciones en playlists.
- `App\Http\Controllers\MoodController`: agrupación de música por estado de ánimo desde datos reales.
- `App\Policies\`: autorización por propiedad (`artist_id`) y por rol (`admin`, `editor`).
- `resources/js/stores/playerStore.js`: estado global del reproductor, cola y preferencias de reproducción.
- `resources/js/stores/detailsStore.js`: estado del panel lateral de detalles, desacoplado del reproductor.
- `resources/js/helpers/libraryApi.js`: puente entre Vue y las acciones de biblioteca.
- `resources/css/theme.css` y `resources/css/app.css`: tokens de color y sistema visual `.ks-*`.

### Datos y migraciones

- Se conserva `songs.artist_id` (usuario subidor) y se añade `artist_ref_id` hacia el catálogo de `artists`.
- `genders` sigue siendo la tabla canónica de géneros; `Genre` es el modelo canónico y `Gender` un alias por compatibilidad.
- Las 15 migraciones aditivas de plataforma crean relaciones, backfill real y 38 claves foráneas sin destruir datos existentes.
- No se crean administradores automáticamente.

### Tema

`theme.js` sincroniza `--ks-accent` y `--ks-accent-rgb` con el acento guardado. Los tokens RGB alimentan las utilidades de Tailwind, de modo que el toggle cambia fondos, textos, bordes y superficies entre Sumi y Washi sin perder modificadores de opacidad. `app.blade.php` aplica el tema antes del render para evitar el destello inicial.

---

## Roles y permisos / Roles & Permissions

| Rol (`roles.slug`) | Nombre | Alcance |
| --- | --- | --- |
| `admin` | Administrador | Puede editar y eliminar cualquier contenido |
| `editor` | Editor | Puede editar y eliminar cualquier contenido |
| `listener` | Oyente | Escucha, guarda y organiza su propia biblioteca |

Además de los roles, las policies usan la propiedad: el dueño de una canción (`songs.artist_id`) siempre puede editarla y eliminarla. No se asigna el rol de administrador a ningún usuario de forma automática; la interfaz oculta acciones no permitidas, pero la autorización real ocurre en el servidor.

---

## Modelo de datos / Data model

| Tabla | Propósito |
| --- | --- |
| `users`, `roles`, `role_user` | Cuentas y roles del sistema |
| `songs`, `genders`, `artists` | Catálogo musical base |
| `albums`, `album_song` | Álbumes y posición de las canciones |
| `playlists`, `playlist_song` | Listas del usuario y su orden |
| `favorites`, `followed_artists`, `saved_albums` | Biblioteca personal |
| `listening_history`, `playback_events`, `user_settings` | Historial, eventos de reproducción y preferencias |
| `credits`, `credit_details`, `events` | Créditos y eventos heredados del proyecto original |

`songs` conserva metadatos reales: `name`, `thumbnail`, `lyrics`, `audio`, `duration_seconds`, `release_date`, `is_public`, `play_count`, `license` y `mood`, además de las relaciones con género y artista. El incremento de `play_count` se registra una sola vez por reproducción.

---

## Requisitos / Requirements

- **PHP 8.3+** con las extensiones habituales de Laravel (`pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`).
- **Composer 2**.
- **Node.js 22 LTS** o superior y npm.
- **SQLite** (incluido por defecto), MySQL o PostgreSQL.
- Opcional: Laragon, Laravel Sail o Docker.

---

## Instalación / Installation

Sigue estos pasos para ejecutar KizunaSound en tu entorno local:

```bash
# 1. Clona este repositorio
git clone https://github.com/BSmithC/KizunaSound.git
cd KizunaSound

# 2. Instala dependencias
composer install
npm install

# 3. Configura el entorno y genera la clave
cp .env.example .env
php artisan key:generate

# 4. Crea la base de datos SQLite (opción por defecto)
touch database/database.sqlite

# 5. Migra y crea el usuario de demostración
php artisan migrate --seed

# 6. Compila el frontend y levanta el entorno completo
npm run build
composer dev
```

En Windows (PowerShell):

```powershell
Copy-Item .env.example .env
New-Item -ItemType File -Path database/database.sqlite -Force
```

Ajusta `APP_NAME` y `APP_URL` en `.env` según tu entorno (por ejemplo `APP_URL=http://kizunasound.test` en Laragon). Para MySQL o PostgreSQL cambia `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.

Abre la URL configurada e inicia sesión con el usuario de demostración.

### Credenciales de demostración / Demo credentials

| Campo | Valor |
| --- | --- |
| Correo | `test@example.com` |
| Contraseña | `12345678` |

Nota: el seeder es solo para desarrollo. Cambia estas credenciales antes de desplegar.

---

## Rutas principales / Main routes

| Grupo | Rutas de ejemplo |
| --- | --- |
| Descubrimiento | `GET /home`, `/explore`, `/search`, `/library`, `/favorites`, `/mood` |
| Streaming | `GET /songs/{song}/stream` |
| Catálogos | `GET/POST /songs`, `/albums`, `/playlists`, `/genders`, `/artists` |
| Biblioteca | `POST /library/favorites/{song}`, `/library/history/{song}`, `/library/albums/{album}`, `/library/artists/{artist}` |
| Playlists | `POST /playlists/{playlist}/songs/{song}` |
| Cuenta | `GET /profile`, `PATCH /profile`, `DELETE /profile`, rutas Breeze de autenticación |
| Panel | `GET /dashboard` |

Para ver el listado completo:

```bash
php artisan route:list --except-vendor
```

---

## Diseño y experiencia de usuario / UI & UX

| Token | Valor |
| --- | --- |
| Sumi (negro principal) | `#171717` |
| Carbón | `#292D32` |
| Shu (rojo japonés) | `#B52B32` |
| Washi (beige) | `#D9CDB8` |
| Musgo | `#778477` |
| Blanco cálido | `#F3F0E9` |
| Tipografía | Inter, Noto Sans JP y Noto Serif JP |

- Tema Sumi oscuro por defecto y alternancia real con Washi claro; bootstrap del tema en `app.blade.php` para evitar destello.
- Sidebar izquierda compacta, área central, panel lateral de detalles y reproductor inferior global.
- Patrones sutiles Washi/Seigaiha/montaña/sol e iconos minimalistas, sin emojis.
- Animaciones reducidas: sin entradas, desplazamientos ni `scale`; solo feedback de color/estado de 150 ms y fundidos de 120 ms.
- Navegación inferior en móvil compartida con el reproductor: Inicio, Explorar, Buscar, Biblioteca, Favoritos, Playlists y Ánimo.
- Componentes compartidos en `Components/Music/` (`SongCard`, `AlbumCard`, `ArtistCard`, `TrackList`, `DetailsPanel`, `EmptyState`) y primitivas en `Components/ui/`.

---

## Pruebas y calidad / Testing & Quality

La suite usa **PHPUnit** con `RefreshDatabase` y cubre autenticación, perfil, biblioteca, descubrimiento, CRUD del catálogo y regresiones de la plataforma. La verificación final registró 38 pruebas, 145 aserciones y 0 fallos, además de 67 rutas y 16 páginas con smoke HTTP autenticado.

```bash
php artisan test --compact   # Suite completa
composer test                 # Limpia configuración y ejecuta pruebas
vendor\bin\pint --test        # Comprobación de formato PHP
npm run build                 # Compilación de producción
```

Para ejecutar un archivo concreto:

```bash
php artisan test --filter=CatalogCrudTest
```

---

## Scripts disponibles / Available scripts

### Composer

| Comando | Descripción |
| --- | --- |
| `composer setup` | Dependencias, `.env`, clave, migración, npm y build |
| `composer dev` | Servidor, colas, Pail y Vite en paralelo |
| `composer test` | Limpieza de configuración y suite de pruebas |

### NPM

| Comando | Descripción |
| --- | --- |
| `npm run dev` | Servidor de desarrollo con HMR |
| `npm run build` | Compilación de producción con Vite |

---

## Estructura del proyecto / Project structure

```text
KizunaSound/
├── app/
│   ├── Http/Controllers/       # Controladores de páginas, catálogo, biblioteca y ánimo
│   ├── Http/Middleware/        # HandleInertiaRequests y compartición de props
│   ├── Models/                 # Eloquent: Song, Album, Artist, Playlist, Role...
│   ├── Policies/               # Autorización por propiedad y rol
│   └── Services/               # CatalogService
├── database/
│   ├── migrations/             # Base original + 15 migraciones aditivas
│   └── seeders/                # Usuario de demostración
├── docs/PLATFORM.md            # Documentación técnica de la plataforma
├── resources/
│   ├── css/                    # theme.css (tokens) y app.css (sistema .ks-*)
│   └── js/
│       ├── Components/         # Music/, ui/ y componentes compartidos
│       ├── Pages/              # Páginas Inertia (Home, Explore, Library, Mgmt...)
│       ├── stores/             # playerStore.js y detailsStore.js (Pinia)
│       ├── helpers/            # libraryApi.js, media.js, searchHistory.js
│       └── composables/        # usePlayback.js
├── routes/                     # web.php, auth.php y console.php
├── tests/                      # Feature y Unit con PHPUnit
└── README.md
```

### Convenciones

- Sin `routes/api.php`: Inertia + Ziggy para conectar Laravel con Vue.
- Rutas por nombre (`route(...)`); nunca URLs hardcodeadas, incluida la reproducción de audio.
- Autorización en servidor mediante policies; la UI solo oculta lo que el usuario no puede hacer.
- Los datos musicales son reales: no se generan canciones, artistas ni imágenes de relleno.
- El reproductor y el panel de detalles son stores independientes; cerrar el panel nunca detiene la música.
- Pint para PHP; mantener las animaciones mínimas y los tokens de `theme.css` como única fuente de color.

---

## Limitaciones / Next steps

- La sincronización de favoritos entre pestañas no está implementada: el store usa `localStorage`, sin sondeo ni canal global.
- `caniuse-lite` está desactualizado: es solo un aviso no bloqueante de Vite/Browserslist (`npx update-browserslist-db@latest`).
- `resources/js/Components/RecipeSelector.vue` conserva un `console.log` y una imagen externa de código previo no musical; se dejó intacto para no alterar funcionalidad existente.
- No hay administradores automáticos: los roles se asignan deliberadamente.
- No se simulan catálogos ni métricas: las secciones vacías se muestran como tal.

---

## Licencia / License

El skeleton base de Laravel se distribuye bajo licencia [MIT](https://opensource.org/licenses/MIT). El código de KizunaSound pertenece a sus autores.
