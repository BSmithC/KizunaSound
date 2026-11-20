import { defineStore } from 'pinia'
import { computed, onScopeDispose, ref } from 'vue'
import { formatDuration } from '@/helpers/media'
import {
    recordHistory as apiRecordHistory,
    toggleFavorite as apiToggleFavorite,
} from '@/helpers/libraryApi'

const STORAGE_KEY = 'ks_player_state'
const FAVORITES_KEY = 'ks_favorite_songs'
const LEGACY_KEY = 'lastPlayedSong'
const SPEEDS = [1, 1.25, 1.5, 2]
const VALID_REPEAT = ['off', 'all', 'one']
const SAVE_INTERVAL_MS = 5000

export const usePlayerStore = defineStore('player', () => {
    // Estado
    const currentSong = ref(null)
    const isPlaying = ref(false)
    const currentTime = ref(0)
    const duration = ref(0)
    const volume = ref(0.7)
    const previousVolume = ref(0.7)
    const playbackSpeed = ref(1)
    const isFavorite = ref(false)
    const playlist = ref([])
    const currentIndex = ref(-1)
    const shuffle = ref(false)
    const repeat = ref('off')
    const loading = ref(false)
    const error = ref('')
    const warning = ref('')
    const audioElement = ref(null)

    const favoriteIds = ref(new Set())
    const playedIndices = ref([])

    let listenersAttached = false
    let saveTimer = null
    let historyRecordedFor = null

    // Getters y utilidades
    const formatTime = (seconds) => formatDuration(seconds)

    const progressPercentage = computed(() => {
        if (!Number.isFinite(duration.value) || duration.value <= 0) return 0
        return Math.min(100, Math.max(0, (currentTime.value / duration.value) * 100))
    })

    const formattedCurrentTime = computed(() => formatTime(currentTime.value))
    const formattedDuration = computed(() => formatTime(duration.value))
    const isMuted = computed(() => volume.value === 0)

    function setError(message) {
        error.value = message || 'Ha ocurrido un error inesperado en el reproductor.'
    }

    function clearError() {
        error.value = ''
    }

    function setWarning(message) {
        warning.value = message || ''
    }

    function clearWarning() {
        warning.value = ''
    }

    function readLocal(key) {
        try {
            return typeof localStorage === 'undefined' ? null : localStorage.getItem(key)
        } catch {
            setWarning('No se pudo acceder al almacenamiento local del navegador.')
            return null
        }
    }

    function writeLocal(key, value) {
        try {
            if (typeof localStorage !== 'undefined') localStorage.setItem(key, value)
        } catch {
            setWarning('No se pudo guardar la configuración del reproductor.')
        }
    }

    function normalizeIds(value) {
        const source = Array.isArray(value)
            ? value
            : value && typeof value === 'object'
                ? Object.keys(value)
                : []

        return source
            .map((id) => Number(id))
            .filter((id) => Number.isFinite(id) && id > 0)
    }

    function syncFavorite() {
        isFavorite.value = Boolean(
            currentSong.value && favoriteIds.value.has(Number(currentSong.value.id)),
        )
    }

    function applyFavorite(songId, value) {
        const next = new Set(favoriteIds.value)
        if (value) next.add(Number(songId))
        else next.delete(Number(songId))
        favoriteIds.value = next
        syncFavorite()
    }

    function setFavorites(ids) {
        favoriteIds.value = new Set(normalizeIds(ids))
        syncFavorite()
    }

    function availableNextIndices() {
        return playlist.value
            .map((_, index) => index)
            .filter((index) => index !== currentIndex.value && !playedIndices.value.includes(index))
    }

    function randomOtherIndex() {
        const length = playlist.value.length
        if (length <= 1) return currentIndex.value >= 0 ? currentIndex.value : 0

        let index = currentIndex.value
        while (index === currentIndex.value) {
            index = Math.floor(Math.random() * length)
        }
        return index
    }

    const hasNext = computed(() => {
        const length = playlist.value.length
        if (!length) return false
        if (repeat.value !== 'off') return length > 1
        if (shuffle.value) return availableNextIndices().length > 0
        return currentIndex.value < length - 1
    })

    const hasPrevious = computed(() => {
        const length = playlist.value.length
        if (!length || currentIndex.value < 0) return false
        if (repeat.value !== 'off') return length > 1
        if (shuffle.value) return playedIndices.value.some((index) => index !== currentIndex.value)
        return currentIndex.value > 0
    })

    // Persistencia
    function saveState() {
        const payload = {
            volume: volume.value,
            shuffle: shuffle.value,
            repeat: repeat.value,
            playbackSpeed: playbackSpeed.value,
            favorites: Array.from(favoriteIds.value),
            lastPlayedSong: currentSong.value
                ? {
                    id: currentSong.value.id,
                    name: currentSong.value.name ?? null,
                    currentTime: currentTime.value,
                    updatedAt: Date.now(),
                }
                : null,
            updatedAt: Date.now(),
        }

        writeLocal(STORAGE_KEY, JSON.stringify(payload))
        writeLocal(FAVORITES_KEY, JSON.stringify(payload.favorites))

        // Compatibilidad con la clave usada por versiones anteriores del reproductor.
        writeLocal(LEGACY_KEY, JSON.stringify({
            volume: volume.value,
            currentTime: currentTime.value,
            lastPlayedSongId: currentSong.value?.id ?? null,
            timestamp: Date.now(),
        }))
    }

    function loadSavedState() {
        let data = {}

        const raw = readLocal(STORAGE_KEY)
        if (raw) {
            try {
                data = JSON.parse(raw) || {}
            } catch {
                setWarning('El estado guardado del reproductor no era válido y se ha descartado.')
            }
        }

        let legacy = {}
        const legacyRaw = readLocal(LEGACY_KEY)
        if (legacyRaw) {
            try {
                legacy = JSON.parse(legacyRaw) || {}
            } catch {
                legacy = {}
            }
        }

        const savedVolume = data.volume ?? legacy.volume
        if (Number.isFinite(Number(savedVolume)) && savedVolume !== null && savedVolume !== undefined) {
            setVolume(Number(savedVolume), { persist: false })
        }

        if (typeof data.shuffle === 'boolean') shuffle.value = data.shuffle
        if (VALID_REPEAT.includes(data.repeat)) repeat.value = data.repeat

        const savedSpeed = Number(data.playbackSpeed)
        if (SPEEDS.includes(savedSpeed)) playbackSpeed.value = savedSpeed

        let favorites = data.favorites
        if (!favorites) {
            const favoritesRaw = readLocal(FAVORITES_KEY)
            if (favoritesRaw) {
                try {
                    favorites = JSON.parse(favoritesRaw)
                } catch {
                    setWarning('La lista de favoritos guardada no era válida y se ha descartado.')
                }
            }
        }
        setFavorites(favorites)
    }

    const restoreState = () => loadSavedState()

    function startAutoSave() {
        if (saveTimer !== null || typeof setInterval === 'undefined') return
        saveTimer = setInterval(saveState, SAVE_INTERVAL_MS)
    }

    function stopAutoSave() {
        if (saveTimer === null) return
        clearInterval(saveTimer)
        saveTimer = null
    }

    // Audio: un único elemento y listeners registrados una sola vez.
    function handleTimeUpdate() {
        if (!audioElement.value) return
        const time = Number(audioElement.value.currentTime)
        currentTime.value = Number.isFinite(time) ? time : 0
    }

    function updateDuration() {
        const value = Number(audioElement.value?.duration)
        if (Number.isFinite(value) && value > 0) duration.value = value
    }

    function handleLoadedMetadata() {
        updateDuration()
    }

    function handleDurationChange() {
        updateDuration()
    }

    function handlePlay() {
        isPlaying.value = true
        loading.value = false
        clearError()
    }

    function handlePause() {
        isPlaying.value = false
        loading.value = false
    }

    function handleEnded() {
        isPlaying.value = false
        loading.value = false
        Promise.resolve(next(true)).catch(() => {
            setError('No se pudo reproducir la siguiente canción. Inténtalo de nuevo.')
        })
    }

    function mediaErrorMessage(audio) {
        const code = audio?.error?.code
        if (code === 1) return 'La carga de la canción se canceló. Inténtalo de nuevo.'
        if (code === 2) return 'No se pudo cargar la canción. Revisa tu conexión e inténtalo de nuevo.'
        if (code === 3) return 'El archivo de audio está dañado o no se puede decodificar.'
        if (code === 4) return 'El formato de la canción no es compatible con este navegador.'
        return 'No se pudo reproducir la canción. Comprueba tu conexión e inténtalo de nuevo.'
    }

    function handleAudioError() {
        loading.value = false
        isPlaying.value = false
        setError(mediaErrorMessage(audioElement.value))
    }

    function handleWaiting() {
        loading.value = true
    }

    function handleCanPlay() {
        loading.value = false
        clearError()
        if (warning.value.startsWith('La conexión')) clearWarning()
    }

    function handleStalled() {
        loading.value = true
        setWarning('La conexión es lenta; la canción puede tardar unos segundos en cargar.')
    }

    const audioEvents = {
        timeupdate: handleTimeUpdate,
        loadedmetadata: handleLoadedMetadata,
        durationchange: handleDurationChange,
        play: handlePlay,
        pause: handlePause,
        ended: handleEnded,
        error: handleAudioError,
        waiting: handleWaiting,
        canplay: handleCanPlay,
        stalled: handleStalled,
    }

    function attachAudioListeners(audio) {
        if (!audio || listenersAttached) return
        Object.entries(audioEvents).forEach(([event, handler]) => {
            audio.addEventListener(event, handler)
        })
        listenersAttached = true
    }

    function detachAudioListeners(audio) {
        if (!audio || !listenersAttached) return
        Object.entries(audioEvents).forEach(([event, handler]) => {
            audio.removeEventListener(event, handler)
        })
        listenersAttached = false
    }

    function ensureAudio() {
        if (typeof window === 'undefined' || typeof Audio === 'undefined') {
            setError('Este navegador no admite la reproducción de audio.')
            return null
        }

        if (!audioElement.value) {
            const audio = new Audio()
            audio.preload = 'auto'
            audio.volume = volume.value
            audio.playbackRate = playbackSpeed.value
            audioElement.value = audio
            attachAudioListeners(audio)
            startAutoSave()
        }

        return audioElement.value
    }

    function handlePlayFailure(reason) {
        loading.value = false
        isPlaying.value = false

        const name = reason?.name || ''
        if (name === 'NotAllowedError' || name === 'SecurityError') {
            setError('El navegador bloqueó la reproducción. Pulsa de nuevo el botón de reproducir.')
        } else if (name === 'NotSupportedError') {
            setError('El formato de audio no es compatible con este navegador.')
        } else {
            setError('No se pudo iniciar la reproducción. Comprueba tu conexión e inténtalo de nuevo.')
        }
    }

    function registerHistory(songId) {
        if (historyRecordedFor === songId) return
        historyRecordedFor = songId

        Promise.resolve(apiRecordHistory(songId, 'player')).catch(() => {
            setWarning('No se pudo registrar esta escucha en tu historial.')
        })
    }

    // Acciones de reproducción
    function play() {
        const audio = ensureAudio()
        if (!audio) return Promise.resolve(false)

        if (!currentSong.value) {
            setError('Selecciona una canción antes de reproducir.')
            return Promise.resolve(false)
        }

        clearError()
        loading.value = true

        return audio.play().then(() => {
            loading.value = false
            return true
        }).catch((reason) => {
            handlePlayFailure(reason)
            return false
        })
    }

    function pause() {
        const audio = audioElement.value
        if (audio) audio.pause()
        isPlaying.value = false
        loading.value = false
    }

    function togglePlay() {
        if (!currentSong.value) {
            if (playlist.value.length) return playSong(playlist.value[0])
            setError('No hay ninguna canción disponible para reproducir.')
            return Promise.resolve(false)
        }

        if (isPlaying.value) {
            pause()
            return Promise.resolve(true)
        }
        return play()
    }

    async function playSong(song, queue = null, options = {}) {
        if (!song || song.id === undefined || song.id === null) {
            setError('No se pudo identificar la canción seleccionada.')
            return false
        }

        if (Array.isArray(queue)) setPlaylist(queue)

        const audio = ensureAudio()
        if (!audio) return false

        clearError()
        clearWarning()

        const sameSong = currentSong.value?.id === song.id
        if (sameSong && !options.force) {
            if (isPlaying.value) {
                pause()
                return true
            }
            return play()
        }

        const oldIndex = currentIndex.value
        if (!sameSong && oldIndex >= 0) {
            playedIndices.value = [...playedIndices.value.filter((index) => index !== oldIndex), oldIndex]
        }

        currentSong.value = song

        const index = playlist.value.findIndex((item) => item.id === song.id)
        currentIndex.value = index
        if (index >= 0) {
            playedIndices.value = [...playedIndices.value.filter((item) => item !== index), index]
        }

        const fallbackDuration = Number(song.duration_seconds)
        currentTime.value = 0
        duration.value = Number.isFinite(fallbackDuration) && fallbackDuration > 0 ? fallbackDuration : 0
        loading.value = true
        syncFavorite()

        try {
            audio.src = route('songs.stream', song.id)
            audio.playbackRate = playbackSpeed.value
            audio.volume = volume.value
            audio.load()

            registerHistory(song.id)

            // No esperamos a 'canplay': play() encola la reproducción y responde al gesto del usuario.
            await audio.play()
            loading.value = false
            return true
        } catch (reason) {
            handlePlayFailure(reason)
            return false
        }
    }

    function playQueueIndex(index) {
        const song = playlist.value[index]
        if (!song) return Promise.resolve(false)

        const restartSameSong = currentSong.value?.id === song.id
        return playSong(song, null, { force: restartSameSong })
    }

    function pickNextIndex() {
        const length = playlist.value.length
        if (!length) return -1

        if (shuffle.value) {
            const available = availableNextIndices()
            if (available.length) {
                return available[Math.floor(Math.random() * available.length)]
            }
            if (repeat.value !== 'off') {
                playedIndices.value = currentIndex.value >= 0 ? [currentIndex.value] : []
                return randomOtherIndex()
            }
            return -1
        }

        let index = currentIndex.value + 1
        if (index >= length) {
            if (repeat.value === 'off') return -1
            index = 0
        }
        return index
    }

    function next(auto = false) {
        if (!playlist.value.length) {
            if (auto) pause()
            return Promise.resolve(false)
        }

        if (auto && repeat.value === 'one') {
            const audio = ensureAudio()
            if (!audio) return Promise.resolve(false)

            try {
                audio.currentTime = 0
                currentTime.value = 0
                loading.value = true
                return audio.play().then(() => {
                    loading.value = false
                    return true
                }).catch((reason) => {
                    handlePlayFailure(reason)
                    return false
                })
            } catch (reason) {
                handlePlayFailure(reason)
                return Promise.resolve(false)
            }
        }

        const index = pickNextIndex()
        if (index < 0) {
            if (auto) pause()
            return Promise.resolve(false)
        }

        return playQueueIndex(index)
    }

    function previous() {
        if (!playlist.value.length || currentIndex.value < 0) return Promise.resolve(false)

        if (shuffle.value) {
            const played = playedIndices.value.filter((index) => index !== currentIndex.value)
            let index = played.length
                ? played[played.length - 1]
                : repeat.value !== 'off'
                    ? randomOtherIndex()
                    : -1

            if (index < 0) return Promise.resolve(false)
            playedIndices.value = playedIndices.value.filter((item) => item !== index)
            return playQueueIndex(index)
        }

        let index = currentIndex.value - 1
        if (index < 0) {
            if (repeat.value === 'off') return Promise.resolve(false)
            index = playlist.value.length - 1
        }

        return playQueueIndex(index)
    }

    // Navegación dentro de la pista
    function seek(target) {
        const audio = audioElement.value || ensureAudio()
        if (!audio) return

        const max = Number.isFinite(duration.value) && duration.value > 0 ? duration.value : 0
        let seconds = null

        if (typeof target === 'number') {
            seconds = target
        } else if (target && typeof target.clientX === 'number' && target.currentTarget?.getBoundingClientRect) {
            const rect = target.currentTarget.getBoundingClientRect()
            if (rect.width <= 0) return
            seconds = ((target.clientX - rect.left) / rect.width) * max
        }

        if (!Number.isFinite(seconds)) return

        const safeTime = Math.max(0, max > 0 ? Math.min(max, seconds) : seconds)
        try {
            audio.currentTime = safeTime
            currentTime.value = safeTime
            clearError()
        } catch {
            setError('No se pudo mover el reproductor a esa posición.')
        }
    }

    function forward() {
        seek(currentTime.value + 10)
    }

    function rewind() {
        seek(currentTime.value - 10)
    }

    // Volumen y velocidad
    function setVolume(value, options = {}) {
        const numeric = Number(value)
        if (!Number.isFinite(numeric)) {
            setWarning('El valor de volumen indicado no es válido.')
            return
        }

        const safe = Math.max(0, Math.min(1, numeric))
        if (safe > 0) previousVolume.value = safe
        volume.value = safe

        if (audioElement.value) {
            audioElement.value.volume = safe
            audioElement.value.muted = false
        }

        if (!options.persist) saveState()
    }

    function toggleMute() {
        if (volume.value > 0) {
            previousVolume.value = volume.value
            setVolume(0)
            return
        }
        setVolume(previousVolume.value > 0 ? previousVolume.value : 0.7)
    }

    function togglePlaybackSpeed() {
        const currentIndexSpeed = SPEEDS.indexOf(playbackSpeed.value)
        playbackSpeed.value = SPEEDS[(currentIndexSpeed + 1) % SPEEDS.length]

        if (audioElement.value) audioElement.value.playbackRate = playbackSpeed.value
        saveState()
    }

    // Cola de reproducción
    function setPlaylist(songs) {
        const list = Array.isArray(songs) ? songs.filter((song) => song && song.id !== undefined && song.id !== null) : []
        playlist.value = [...list]

        const index = list.findIndex((song) => song.id === currentSong.value?.id)
        currentIndex.value = index
        playedIndices.value = index >= 0 ? [index] : []
        saveState()
    }

    function addToQueue(song) {
        if (!song || song.id === undefined || song.id === null) return
        if (playlist.value.some((item) => item.id === song.id)) return

        playlist.value.push(song)
        if (currentIndex.value < 0 && !currentSong.value) currentIndex.value = 0
        saveState()
    }

    function removeFromQueue(target) {
        const index = typeof target === 'number'
            ? target
            : playlist.value.findIndex((song) => song.id === target?.id)

        if (index < 0 || index >= playlist.value.length) return

        playlist.value.splice(index, 1)
        playedIndices.value = playedIndices.value
            .filter((item) => item !== index)
            .map((item) => (item > index ? item - 1 : item))

        if (index === currentIndex.value) {
            pause()
            currentSong.value = null
            currentIndex.value = -1
            currentTime.value = 0
            duration.value = 0
            syncFavorite()
        } else if (index < currentIndex.value) {
            currentIndex.value -= 1
        }

        saveState()
    }

    function clearQueue() {
        pause()
        playlist.value = []
        playedIndices.value = []
        currentIndex.value = -1
        currentSong.value = null
        isFavorite.value = false
        currentTime.value = 0
        duration.value = 0
        clearError()
        saveState()
    }

    // Favoritos
    async function toggleFavorite() {
        if (!currentSong.value) return false

        const songId = Number(currentSong.value.id)
        const previous = favoriteIds.value.has(songId)
        const optimistic = !previous

        applyFavorite(songId, optimistic)
        saveState()

        try {
            const response = await apiToggleFavorite(songId)
            const confirmed = typeof response?.favorite === 'boolean' ? response.favorite : optimistic
            applyFavorite(songId, confirmed)
            saveState()
            clearWarning()
            return confirmed
        } catch {
            applyFavorite(songId, previous)
            saveState()
            setWarning('No se pudo actualizar tu lista de favoritos. Inténtalo de nuevo.')
            return previous
        }
    }

    function retry() {
        clearError()
        clearWarning()

        if (!currentSong.value) {
            if (playlist.value.length) return playSong(playlist.value[0])
            return Promise.resolve(false)
        }

        return playSong(currentSong.value, null, { force: true })
    }

    function destroy() {
        saveState()
        stopAutoSave()

        const audio = audioElement.value
        if (audio) {
            audio.pause()
            detachAudioListeners(audio)
            audio.removeAttribute('src')
            audioElement.value = null
        }

        historyRecordedFor = null
        playedIndices.value = []
        isPlaying.value = false
        loading.value = false
    }

    loadSavedState()
    startAutoSave()
    onScopeDispose(destroy)

    return {
        currentSong,
        isPlaying,
        currentTime,
        duration,
        volume,
        previousVolume,
        isMuted,
        playbackSpeed,
        isFavorite,
        playlist,
        currentIndex,
        shuffle,
        repeat,
        loading,
        error,
        warning,
        audioElement,
        progressPercentage,
        hasNext,
        hasPrevious,
        formattedCurrentTime,
        formattedDuration,
        formatTime,
        playSong,
        play,
        pause,
        togglePlay,
        next,
        previous,
        forward,
        rewind,
        seek,
        togglePlaybackSpeed,
        setVolume,
        toggleMute,
        setPlaylist,
        addToQueue,
        removeFromQueue,
        clearQueue,
        toggleFavorite,
        retry,
        saveState,
        restoreState,
        loadSavedState,
        destroy,
    }
})
