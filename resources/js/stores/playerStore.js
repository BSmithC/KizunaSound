import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const usePlayerStore = defineStore('player', () => {
    // Estado
    const currentSong = ref(null)
    const isPlaying = ref(false)
    const currentTime = ref(0)
    const duration = ref(0)
    const playbackSpeed = ref(1)
    const playlist = ref([])
    const currentIndex = ref(-1)
    const audioElement = ref(null)
    const isFavorite = ref(false)
    const volume = ref(0.7) // Volúmen inicial (70%)

    // Getters
    const progressPercentage = computed(() => {
        if (duration.value === 0) return 0
        return (currentTime.value / duration.value) * 100
    })

    const formatTime = (seconds) => {
        if (isNaN(seconds) || !isFinite(seconds)) return '0:00'
        const mins = Math.floor(seconds / 60)
        const secs = Math.floor(seconds % 60)
        return `${mins}:${secs.toString().padStart(2, '0')}`
    }

    // Inicializar elemento de audio
    const initAudio = () => {
        if (!audioElement.value) {
            audioElement.value = new Audio()
            audioElement.value.volume = volume.value // Asignar volumen inicial
            setupAudioListeners()
        }
        return audioElement.value
    }

    // Configurar listeners del audio - VERSIÓN CORREGIDA
    const setupAudioListeners = () => {
        if (!audioElement.value) return

        // Limpiar listeners existentes para evitar duplicados
        audioElement.value.removeEventListener('timeupdate', handleTimeUpdate)
        audioElement.value.removeEventListener('durationchange', handleDurationChange)
        audioElement.value.removeEventListener('ended', handleEnded)
        audioElement.value.removeEventListener('play', handlePlay)
        audioElement.value.removeEventListener('pause', handlePause)
        audioElement.value.removeEventListener('loadedmetadata', handleLoadedMetadata)
        audioElement.value.removeEventListener('canplay', handleCanPlay)

        // Agregar nuevos listeners
        audioElement.value.addEventListener('timeupdate', handleTimeUpdate)
        audioElement.value.addEventListener('durationchange', handleDurationChange)
        audioElement.value.addEventListener('ended', handleEnded)
        audioElement.value.addEventListener('play', handlePlay)
        audioElement.value.addEventListener('pause', handlePause)
        audioElement.value.addEventListener('loadedmetadata', handleLoadedMetadata)
        audioElement.value.addEventListener('canplay', handleCanPlay)
    }

    // Handlers separados para mejor depuración
    const handleTimeUpdate = () => {
        if (audioElement.value) {
            currentTime.value = audioElement.value.currentTime
            console.log('Time update:', currentTime.value)
        }
    }

    const handleDurationChange = () => {
        if (audioElement.value && !isNaN(audioElement.value.duration)) {
            duration.value = audioElement.value.duration
            console.log('Duration change:', duration.value)
        }
    }

    const handleEnded = () => {
        console.log('Song ended')
        next()
    }

    const handlePlay = () => {
        isPlaying.value = true
        console.log('Play event')
    }

    const handlePause = () => {
        isPlaying.value = false
        console.log('Pause event')
    }

    const handleLoadedMetadata = () => {
        if (audioElement.value && !isNaN(audioElement.value.duration)) {
            duration.value = audioElement.value.duration
            console.log('Loaded metadata - duration:', duration.value)
        }
    }

    const handleCanPlay = () => {
        console.log('Can play event')
    }

    // Reproducir una canción
    const playSong = async (song) => {
        try {
            const audio = initAudio()

            console.log('Playing song:', song.name)

            // Si es la misma canción, solo reproducir/pausar
            if (currentSong.value?.id === song.id) {
                if (isPlaying.value) {
                    pause()
                } else {
                    await play()
                }
                return
            }

            // Cambiar a nueva canción
            currentSong.value = song

            // Actualizar índice en la playlist
            const index = playlist.value.findIndex(s => s.id === song.id)
            if (index !== -1) {
                currentIndex.value = index
            }

            // IMPORTANTE: Resetear tiempos antes de cargar nueva canción
            currentTime.value = 0
            duration.value = 0

            // Configurar URL del audio
            const streamUrl = `/songs/${song.id}/stream`
            console.log('Stream URL:', streamUrl)

            audio.src = streamUrl
            audio.playbackRate = playbackSpeed.value
            audio.volume = volume.value // Asegurar que mantiene el volumen actual

            // Cargar y reproducir
            audio.load()

            // Esperar a que esté listo para reproducir
            await new Promise((resolve) => {
                audio.addEventListener('canplay', resolve, { once: true })
            })

            await audio.play()

            // Verificar si es favorita
            checkFavoriteStatus(song.id)

        } catch (error) {
            console.error('Error al reproducir:', error)
        }
    }

    // Reproducir
    const play = async () => {
        if (audioElement.value && currentSong.value) {
            try {
                await audioElement.value.play()
            } catch (error) {
                console.error('Error al reproducir:', error)
            }
        }
    }

    // Pausar
    const pause = () => {
        if (audioElement.value) {
            audioElement.value.pause()
        }
    }

    // Alternar play/pause
    const togglePlay = () => {
        if (isPlaying.value) {
            pause()
        } else {
            play()
        }
    }

    // Siguiente canción
    const next = () => {
        if (playlist.value.length === 0) return

        let nextIndex = currentIndex.value + 1
        if (nextIndex >= playlist.value.length) {
            nextIndex = 0
        }

        const nextSong = playlist.value[nextIndex]
        if (nextSong) {
            playSong(nextSong)
        }
    }

    // Canción anterior
    const previous = () => {
        if (playlist.value.length === 0) return

        let prevIndex = currentIndex.value - 1
        if (prevIndex < 0) {
            prevIndex = playlist.value.length - 1
        }

        const prevSong = playlist.value[prevIndex]
        if (prevSong) {
            playSong(prevSong)
        }
    }

    // Adelantar 10 segundos
    const forward = () => {
        if (audioElement.value) {
            audioElement.value.currentTime += 10
        }
    }

    // Retroceder 10 segundos
    const rewind = () => {
        if (audioElement.value) {
            audioElement.value.currentTime -= 10
        }
    }

    // Buscar posición
    const seek = (event) => {
        if (!audioElement.value || duration.value === 0) return

        const rect = event.currentTarget.getBoundingClientRect()
        const x = event.clientX - rect.left
        const percentage = x / rect.width
        const newTime = percentage * duration.value

        audioElement.value.currentTime = newTime
    }

    // Cambiar velocidad
    const togglePlaybackSpeed = () => {
        const speeds = [1, 1.25, 1.5, 2]
        const currentSpeedIndex = speeds.indexOf(playbackSpeed.value)
        const nextIndex = (currentSpeedIndex + 1) % speeds.length
        playbackSpeed.value = speeds[nextIndex]

        if (audioElement.value) {
            audioElement.value.playbackRate = playbackSpeed.value
        }
    }

    // --- NUEVO: Control de Sonido de la Música ---
    const setVolume = (value) => {
        // Convertimos explícitamente a número por si acaso llega como String desde el input HTML
        const numericVolume = parseFloat(value)

        // Si por algún motivo no es un número válido (NaN), lo dejamos en un valor por defecto seguro (ej: 0.5)
        if (isNaN(numericVolume)) return

        // Aseguramos que el volumen esté estrictamente entre 0 y 1
        const safeVolume = Math.max(0, Math.min(1, numericVolume))

        volume.value = safeVolume

        if (audioElement.value) {
            audioElement.value.volume = safeVolume
            console.log('Volumen del elemento de audio actualizado a:', audioElement.value.volume) // Para depuración
        }
    }

    // Establecer lista de reproducción
    const setPlaylist = (songs) => {
        playlist.value = songs
        console.log('Playlist set:', songs.length, 'songs')
    }

    const addToQueue = (song) => {
        playlist.value.push(song)
    }

    const clearQueue = () => {
        playlist.value = []
        currentSong.value = null
        currentIndex.value = -1
        if (audioElement.value) {
            audioElement.value.pause()
            audioElement.value.src = ''
        }
        isPlaying.value = false
        currentTime.value = 0
        duration.value = 0
    }

    const checkFavoriteStatus = async (songId) => {
        try {
            isFavorite.value = false
        } catch (error) {
            console.error('Error al verificar favorito:', error)
        }
    }

    const toggleFavorite = async () => {
        if (!currentSong.value) return
        isFavorite.value = !isFavorite.value
    }

    const saveState = () => {
        if (currentSong.value) {
            localStorage.setItem('lastPlayedSong', JSON.stringify({
                id: currentSong.value.id,
                currentTime: currentTime.value,
                volume: volume.value, // Guardar también el volumen configurado
                timestamp: Date.now()
            }))
        }
    }

    const loadSavedState = () => {
        const saved = localStorage.getItem('lastPlayedSong')
        if (saved) {
            try {
                const data = JSON.parse(saved)
                console.log('Loaded saved state:', data)
                if (data.volume !== undefined) {
                    setVolume(data.volume)
                }
            } catch (e) {
                console.error('Error al cargar estado:', e)
            }
        }
    }

    // Salvar estado periódicamente
    setInterval(() => {
        if (isPlaying.value) {
            saveState()
        }
    }, 5000)

    return {
        currentSong,
        isPlaying,
        currentTime,
        duration,
        playbackSpeed,
        playlist,
        currentIndex,
        isFavorite,
        volume,
        progressPercentage,
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
        setPlaylist,
        addToQueue,
        clearQueue,
        toggleFavorite,
        loadSavedState
    }
})
