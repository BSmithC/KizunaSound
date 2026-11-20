<template>
    <AuthenticatedLayout>
        <Head :title="artist.name" />

        <div class="ks-page">
            <div class="mb-6">
                <Link :href="route('artists.index')" class="text-sm text-warm/55 hover:text-warm">← Volver a artistas</Link>
            </div>

            <section class="ks-hero relative mb-8">
                <div v-if="mediaUrl(artist.cover)" class="h-32 w-full bg-cover bg-center opacity-40"
                    :style="{ backgroundImage: `url('${mediaUrl(artist.cover)}')` }"></div>
                <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-end">
                    <MediaThumb :src="mediaUrl(artist.image)" :alt="artist.name" :label="initials(artist.name)"
                        rounded="rounded-full" class="-mt-16 h-28 w-28 flex-none ring-4 ring-slate-950 sm:-mt-20 sm:h-32 sm:w-32" />
                    <div class="min-w-0 flex-1">
                        <p class="ks-eyebrow">Artista</p>
                        <h1 class="mt-2 text-3xl font-black tracking-tight text-warm sm:text-4xl">{{ artist.name }}</h1>
                        <p class="mt-1 text-sm text-warm/45">/{{ artist.slug }}</p>
                        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-warm/75">{{ artist.bio || 'Este artista aún no tiene biografía.' }}</p>
                        <div class="mt-4 flex flex-wrap gap-2 text-xs text-warm/55">
                            <span class="rounded-full border border-warm/10 bg-warm/5 px-3 py-1">{{ artist.songs_count ?? 0 }} canciones</span>
                            <span class="rounded-full border border-warm/10 bg-warm/5 px-3 py-1">{{ artist.followers_count ?? 0 }} seguidores</span>
                            <span class="rounded-full border px-3 py-1"
                                :class="artist.is_active ? 'border-emerald-500/30 text-emerald-300' : 'border-slate-500/30 text-warm/55'">
                                {{ artist.is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button"
                            class="rounded-full border px-5 py-2.5 text-sm transition"
                            :class="following ? 'border-[var(--ks-accent)] bg-[var(--ks-accent)] text-warm' : 'border-warm/15 text-warm/75 hover:text-warm'"
                            @click="follow">
                            {{ following ? 'Siguiendo' : 'Seguir' }}
                        </button>
                        <Link v-if="canManage" :href="route('artists.edit', artist.id)"
                            class="ks-btn ks-btn-ghost">Editar</Link>
                    </div>
                </div>
            </section>

            <SectionHeading title="Canciones publicadas" subtitle="Reproduce lo mejor de este artista.">
                <TrackList v-if="artist.songs?.length" :songs="artist.songs" />
                <EmptyState v-else title="Sin canciones publicadas" description="Todavía no hay pistas públicas de este artista." />
            </SectionHeading>

            <SectionHeading v-if="artist.albums?.length" title="Álbumes" class="mt-8">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    <Link v-for="album in artist.albums" :key="album.id" :href="route('albums.show', album.id)">
                        <AlbumCard :album="album" />
                    </Link>
                </div>
            </SectionHeading>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import MediaThumb from '@/Components/Music/MediaThumb.vue'
import SectionHeading from '@/Components/Music/SectionHeading.vue'
import TrackList from '@/Components/Music/TrackList.vue'
import AlbumCard from '@/Components/Music/AlbumCard.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'
import { mediaUrl, initials } from '@/helpers/media'
import { toggleArtist } from '@/helpers/libraryApi'

const props = defineProps({
    artist: { type: Object, required: true },
    canManage: { type: Boolean, default: false },
    isFollowing: { type: Boolean, default: false },
})

const following = ref(props.isFollowing)

const follow = async () => {
    try {
        const result = await toggleArtist(props.artist.id)
        following.value = Boolean(result.following)
    } catch (error) {
        console.error('No se pudo seguir al artista', error)
    }
}
</script>
