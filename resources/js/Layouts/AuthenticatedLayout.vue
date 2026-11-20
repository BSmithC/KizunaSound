<template>
    <div class="ks-app">
        <!-- Sidebar escritorio -->
        <aside
            class="ks-sidebar fixed inset-y-0 left-0 z-30 hidden flex-col border-r backdrop-blur-xl sm:flex"
            :class="sidebarExpanded ? 'w-60' : 'w-[4.5rem]'">
            <div class="flex h-16 items-center border-b border-warm/[0.07] px-4"
                :class="sidebarExpanded ? 'justify-between' : 'justify-center'">
                <Link :href="route('home')" class="flex items-center gap-3" aria-label="Ir al inicio">
                    <span class="relative flex h-10 w-10 flex-none items-center justify-center overflow-hidden rounded-xl bg-[var(--ks-accent)] shadow-ks-accent">
                        <span class="ks-motif ks-motif--sun"></span>
                        <IvoryTusksIcon class="relative z-10 h-6 w-6 text-warm" />
                    </span>
                    <span v-if="sidebarExpanded" class="leading-tight">
                        <span class="block text-base font-black tracking-tight text-warm">Kizuna<span class="text-[var(--ks-accent)]">Sound</span></span>
                        <span class="block text-[9px] uppercase tracking-[0.22em] text-washi/45">音楽で繋がる</span>
                    </span>
                </Link>
            </div>

            <nav class="ks-scroll flex-1 space-y-7 overflow-y-auto px-3 py-6">
                <div>
                    <p v-if="sidebarExpanded" class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-washi/35">探索 · Explora</p>
                    <SidebarLink :href="route('home')" :label="'Inicio'" :icon="'home'" :expanded="sidebarExpanded"
                        :active="isActive(['home'])" />
                    <SidebarLink :href="route('explore')" :label="'Explorar'" :icon="'compass'" :expanded="sidebarExpanded"
                        :active="isActive(['explore'])" />
                    <SidebarLink :href="route('search')" :label="'Buscar'" :icon="'search'" :expanded="sidebarExpanded"
                        :active="isActive(['search'])" />
                    <SidebarLink :href="route('mood.index')" :label="'Ánimo'" :icon="'note'" :expanded="sidebarExpanded"
                        :active="isActive(['mood.index'])" />
                </div>

                <div>
                    <p v-if="sidebarExpanded" class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-washi/35">図書館 · Tu espacio</p>
                    <SidebarLink :href="route('library')" :label="'Tu biblioteca'" :icon="'library'" :expanded="sidebarExpanded"
                        :active="isActive(['library'])" />
                    <SidebarLink :href="route('favorites')" :label="'Favoritos'" :icon="'heart'" :expanded="sidebarExpanded"
                        :active="isActive(['favorites'])" />
                    <SidebarLink :href="route('playlists.index')" :label="'Playlists'" :icon="'list'" :expanded="sidebarExpanded"
                        :active="isActive(['playlists.index', 'playlists.create', 'playlists.edit'])" />
                </div>

                <div>
                    <p v-if="sidebarExpanded" class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.2em] text-washi/35">目録 · Catálogo</p>
                    <SidebarLink :href="route('songs.index')" :label="'Música'" :icon="'note'" :expanded="sidebarExpanded"
                        :active="isActive(['songs.index', 'songs.create', 'songs.edit'])" />
                    <SidebarLink :href="route('albums.index')" :label="'Álbumes'" :icon="'album'" :expanded="sidebarExpanded"
                        :active="isActive(['albums.index', 'albums.create', 'albums.edit'])" />
                    <SidebarLink :href="route('artists.index')" :label="'Artistas'" :icon="'note'" :expanded="sidebarExpanded"
                        :active="isActive(['artists.index', 'artists.create', 'artists.show', 'artists.edit'])" />
                    <SidebarLink :href="route('genders.index')" :label="'Géneros'" :icon="'tag'" :expanded="sidebarExpanded"
                        :active="isActive(['genders.index', 'genders.create', 'genders.edit'])" />
                    <SidebarLink :href="route('dashboard')" :label="'Administración'" :icon="'grid'" :expanded="sidebarExpanded"
                        :active="isActive(['dashboard'])" />
                </div>
            </nav>

            <div class="border-t border-warm/[0.07] p-3">
                <button type="button" @click="toggleSidebar"
                    class="ks-nav-link w-full"
                    :class="{ 'justify-center': !sidebarExpanded }">
                    <component :is="sidebarExpanded ? SidebarIcon : SidebarFilledIcon" class="h-5 w-5 flex-none" />
                    <span v-if="sidebarExpanded">Contraer menú</span>
                </button>
            </div>
        </aside>

        <!-- Contenido -->
        <div class="flex min-h-screen flex-col"
            :class="[
                { 'sm:ml-60': sidebarExpanded, 'sm:ml-[4.5rem]': !sidebarExpanded },
                { 'xl:pr-[30rem] 2xl:pr-[34rem]': details.isOpen },
            ]">
            <!-- Barra superior escritorio -->
            <header class="ks-topbar sticky top-0 z-20 hidden h-16 items-center gap-4 border-b px-5 backdrop-blur-xl sm:flex">
                <TopBarSearch :initial="$page.props.query || ''" class="max-w-md" />
                <div class="ml-auto flex items-center gap-1">
                    <button type="button" @click="details.toggle()" aria-label="Abrir panel de detalles"
                        class="rounded-full p-2 text-warm/55 transition hover:bg-warm/5 hover:text-warm">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" /><path d="M12 11v5M12 8h.01" stroke-linecap="round" />
                        </svg>
                    </button>
                    <button type="button" @click="toggleTheme" :aria-label="isDark ? 'Activar tema claro' : 'Activar tema oscuro'"
                        class="rounded-full p-2 text-warm/55 transition hover:bg-warm/5 hover:text-warm">
                        <component :is="isDark ? LightIcon : DarkIcon" class="h-5 w-5" />
                    </button>

                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button type="button"
                                class="flex items-center gap-2 rounded-full border border-warm/10 bg-warm/5 py-1.5 pl-1.5 pr-3 text-sm text-warm transition hover:border-[var(--ks-accent)]">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-[var(--ks-accent)] text-xs font-bold text-warm">
                                    {{ initials($page.props.auth.user.name) }}
                                </span>
                                <span class="hidden max-w-28 truncate lg:block">{{ $page.props.auth.user.name }}</span>
                                <svg class="h-4 w-4 text-warm/40" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </template>
                        <template #content>
                            <div class="px-4 py-3 text-xs text-warm/40">Conectado como<br /><span class="font-semibold text-warm/85">{{ $page.props.auth.user.email }}</span></div>
                            <DropdownLink :href="route('profile.edit')">Configuración del perfil</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">Cerrar sesión</DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Barra móvil -->
            <header class="ks-topbar sticky top-0 z-20 border-b backdrop-blur-xl sm:hidden">
                <div class="flex h-14 items-center justify-between px-4">
                    <Link :href="route('home')" class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[var(--ks-accent)]">
                            <IvoryTusksIcon class="h-5 w-5 text-warm" />
                        </span>
                        <span class="text-sm font-black text-warm">Kizuna<span class="text-[var(--ks-accent)]">Sound</span></span>
                    </Link>
                    <div class="flex items-center gap-1">
                        <button type="button" @click="details.toggle()" aria-label="Abrir panel de detalles"
                            class="rounded-full p-2 text-warm/55 hover:text-warm">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" /><path d="M12 11v5M12 8h.01" stroke-linecap="round" />
                            </svg>
                        </button>
                        <button type="button" @click="toggleTheme" :aria-label="'Cambiar tema'" class="rounded-full p-2 text-warm/55 hover:text-warm">
                            <component :is="isDark ? LightIcon : DarkIcon" class="h-5 w-5" />
                        </button>
                        <button type="button" aria-label="Abrir menú" class="rounded-full p-2 text-warm/55 hover:text-warm"
                            @click="showingNavigationDropdown = !showingNavigationDropdown">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path :class="{ hidden: showingNavigationDropdown }" d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" />
                                <path :class="{ hidden: !showingNavigationDropdown }" d="M6 18 18 6M6 6l12 12" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div v-show="showingNavigationDropdown" class="border-t border-warm/[0.07] px-4 py-3">
                    <TopBarSearch :initial="$page.props.query || ''" class="max-w-none" />
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <ResponsiveNavLink :href="route('library')" :active="isActive(['library'])">Tu biblioteca</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('mood.index')" :active="isActive(['mood.index'])">Ánimo</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('playlists.index')" :active="isActive(['playlists.index'])">Playlists</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('songs.index')" :active="isActive(['songs.index'])">Música</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('albums.index')" :active="isActive(['albums.index'])">Álbumes</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('artists.index')" :active="isActive(['artists.index'])">Artistas</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('genders.index')" :active="isActive(['genders.index'])">Géneros</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('profile.edit')" :active="isActive(['profile.edit'])">Configuración</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('dashboard')" :active="isActive(['dashboard'])">Administración</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" class="col-span-2">Cerrar sesión</ResponsiveNavLink>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-0 pb-8">
                <slot />
            </main>

            <!-- El espacio de la barra fija se reserva aquí para que el footer nunca quede tapado. -->
            <Footer class="pb-56 sm:pb-40" />

            <!-- Navegación inferior móvil + reproductor: una sola pila fija -->
            <div class="pointer-events-none fixed inset-x-0 bottom-0 z-50 flex flex-col gap-2 px-2 pb-2 sm:px-4 sm:pb-4">
                <nav class="ks-scroll pointer-events-auto flex items-center justify-start gap-1 overflow-x-auto rounded-t-xl border border-warm/[0.07] bg-sumi/95 px-2 py-2 backdrop-blur-xl sm:hidden">
                    <MobileLink :href="route('home')" label="Inicio" icon="home" :active="isActive(['home'])" />
                    <MobileLink :href="route('explore')" label="Explorar" icon="compass" :active="isActive(['explore'])" />
                    <MobileLink :href="route('search')" label="Buscar" icon="search" :active="isActive(['search'])" />
                    <MobileLink :href="route('library')" label="Biblioteca" icon="library" :active="isActive(['library'])" />
                    <MobileLink :href="route('favorites')" label="Favoritos" icon="heart" :active="isActive(['favorites'])" />
                    <MobileLink :href="route('playlists.index')" label="Playlists" icon="list" :active="isActive(['playlists.index'])" />
                    <MobileLink :href="route('mood.index')" label="Ánimo" icon="note" :active="isActive(['mood.index'])" />
                </nav>

                <MusicPlayer class="pointer-events-auto" />
            </div>
        </div>

        <DetailsPanel />
    </div>
</template>

<script setup>
import { ref, watch, watchEffect, onMounted, h } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import Footer from '@/Components/Footer.vue'
import MusicPlayer from '@/Components/MusicPlayer.vue'
import DetailsPanel from '@/Components/Music/DetailsPanel.vue'
import TopBarSearch from '@/Components/Music/TopBarSearch.vue'
import IvoryTusksIcon from '@/Components/Icons/IvoryTusksIcon.vue'
import LightIcon from '@/Components/Icons/LightIcon.vue'
import DarkIcon from '@/Components/Icons/DarkIcon.vue'
import SidebarIcon from '@/Components/Icons/SidebarIcon.vue'
import SidebarFilledIcon from '@/Components/Icons/SidebarFilledIcon.vue'
import HomeIcon from '@/Components/Icons/HomeIcon.vue'
import MusicalNoteIcon from '@/Components/Icons/MusicalNoteIcon.vue'
import RectangleStackIcon from '@/Components/RectangleStackIcon.vue'
import CategoryIcon from '@/Components/Icons/CategoryIcon.vue'
import HeartIcon from '@/Components/Icons/HeartIcon.vue'
import MagnifyingGlassIcon from '@/Components/Icons/MagnifyingGlassIcon.vue'
import { switchTheme, setThemeOnLoad } from '@/theme'
import { initials } from '@/helpers/media'
import { useDetailsStore } from '@/stores/detailsStore'

const SidebarLink = ({ href, label, icon, expanded, active }) =>
    h(Link, {
        href,
        class: [
            'ks-nav-link group',
            active ? 'ks-nav-link-active' : '',
            { 'justify-center': !expanded },
        ],
    }, () => [
        h(NavIcon, { name: icon, active }),
        expanded ? h('span', { class: 'truncate font-medium' }, label) : null,
    ])

const NavIcon = ({ name, active }) => {
    const map = {
        home: HomeIcon,
        compass: RectangleStackIcon,
        search: MagnifyingGlassIcon,
        library: CategoryIcon,
        heart: HeartIcon,
        list: RectangleStackIcon,
        note: MusicalNoteIcon,
        album: RectangleStackIcon,
        tag: CategoryIcon,
        grid: RectangleStackIcon,
    }
    return h(map[name] ?? MusicalNoteIcon, {
        class: ['h-5 w-5 flex-none', active ? 'text-[var(--ks-accent)]' : 'text-washi/45 group-hover:text-warm'],
    })
}

const MobileLink = ({ href, label, icon, active }) =>
    h(Link, {
        href,
        class: ['flex min-w-[58px] flex-none flex-col items-center gap-1 rounded-lg px-1 py-1.5 text-[10px] transition', active ? 'text-[var(--ks-accent)]' : 'text-warm/45'],
    }, () => [h(NavIcon, { name: icon, active }), h('span', null, label)])

const page = usePage()
const toast = useToast()
const details = useDetailsStore()
const isDark = ref(document.documentElement.classList.contains('dark'))
const sidebarExpanded = ref(true)
const showingNavigationDropdown = ref(false)
const shownFlash = ref(null)

const isActive = (names) => names.some((name) => route().current(name))

const toggleSidebar = () => {
    sidebarExpanded.value = !sidebarExpanded.value
    localStorage.setItem('sidebarExpanded', String(sidebarExpanded.value))
}

const toggleTheme = () => {
    isDark.value = switchTheme()
}

watch(() => page.url, () => {
    showingNavigationDropdown.value = false
    details.close()
})

watchEffect(() => {
    const flash = page.props.flash
    if (flash?.toast && flash.toast !== shownFlash.value) {
        const style = flash.toastStyle || 'success'
        if (style === 'danger' || style === 'error') toast.error(flash.toast)
        else if (style === 'info') toast.info(flash.toast)
        else if (style === 'warning') toast.warning(flash.toast)
        else toast.success(flash.toast)
        shownFlash.value = flash.toast
    }
})

onMounted(() => {
    const saved = localStorage.getItem('sidebarExpanded')
    if (saved !== null) sidebarExpanded.value = saved === 'true'
    isDark.value = setThemeOnLoad(page.props.settings?.accent_color)
})
</script>
