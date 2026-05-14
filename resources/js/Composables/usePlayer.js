// resources/js/composables/usePlayer.js
import { ref, computed } from 'vue'

export function usePlayer() {
    const currentSong = ref(null)
    const isPlaying = ref(false)
    const currentTime = ref(0)
    const duration = ref(0)
    const playbackSpeed = ref(1)
    const favorites = ref([])
    const audioElement = ref(null)
    const queue = ref([])
    const currentIndex = ref(-1)
    const allSongs = ref([])

    const progressPercentage = computed(() => {
        if (duration.value === 0) return 0
        return (currentTime.value / duration.value) * 100
    })

    const isFavorite = computed(() => {
        return favorites.value.some(fav => fav.id === currentSong.value?.id)
    })

    const formatTime = (seconds) => {
        if (!seconds || isNaN(seconds)) return '0:00'
        const mins = Math.floor(seconds / 60)
        const secs = Math.floor(seconds % 60)
        return `${mins}:${secs.toString().padStart(2, '0')}`
    }

    const initAudio = () => {
        if (!currentSong.value) return

        if (audioElement.value) {
            audioElement.value.pause()
            audioElement.value.removeEventListener('timeupdate', updateTime)
            audioElement.value.removeEventListener('loadedmetadata', updateDuration)
            audioElement.value.removeEventListener('ended', handleSongEnd)
        }

        // Cambiado de 'file' a 'audio'
        const audio = new Audio(`/storage/${currentSong.value.audio}`)
        audio.playbackRate = playbackSpeed.value
        audio.addEventListener('timeupdate', updateTime)
        audio.addEventListener('loadedmetadata', updateDuration)
        audio.addEventListener('ended', handleSongEnd)

        audioElement.value = audio

        if (isPlaying.value) {
            audio.play().catch(error => {
                console.error('Error playing audio:', error)
                isPlaying.value = false
            })
        }
    }

    const updateTime = () => {
        if (audioElement.value) {
            currentTime.value = audioElement.value.currentTime
        }
    }

    const updateDuration = () => {
        if (audioElement.value) {
            duration.value = audioElement.value.duration
        }
    }

    const handleSongEnd = () => {
        next()
    }

    const togglePlay = () => {
        if (!audioElement.value) {
            initAudio()
        }

        if (isPlaying.value) {
            audioElement.value.pause()
        } else {
            audioElement.value.play()
        }
        isPlaying.value = !isPlaying.value
    }

    const seek = (event) => {
        if (!audioElement.value || !duration.value) return

        const rect = event.currentTarget.getBoundingClientRect()
        const x = event.clientX - rect.left
        const percentage = Math.min(Math.max(x / rect.width, 0), 1)
        const newTime = percentage * duration.value

        audioElement.value.currentTime = newTime
        currentTime.value = newTime
    }

    const rewind = () => {
        if (audioElement.value) {
            audioElement.value.currentTime = Math.max(0, audioElement.value.currentTime - 10)
        }
    }

    const forward = () => {
        if (audioElement.value) {
            audioElement.value.currentTime = Math.min(duration.value, audioElement.value.currentTime + 10)
        }
    }

    const previous = () => {
        if (currentIndex.value > 0) {
            currentIndex.value--
            playSongAtIndex(currentIndex.value)
        }
    }

    const next = () => {
        if (currentIndex.value < queue.value.length - 1) {
            currentIndex.value++
            playSongAtIndex(currentIndex.value)
        }
    }

    const playSongAtIndex = (index) => {
        if (index >= 0 && index < queue.value.length) {
            const wasPlaying = isPlaying.value
            isPlaying.value = false
            currentSong.value = queue.value[index]
            initAudio()
            if (wasPlaying) {
                setTimeout(() => {
                    togglePlay()
                }, 100)
            }
        }
    }

    const togglePlaybackSpeed = () => {
        const speeds = [0.5, 0.75, 1, 1.25, 1.5, 2]
        const currentSpeedIndex = speeds.indexOf(playbackSpeed.value)
        const nextIndex = (currentSpeedIndex + 1) % speeds.length
        playbackSpeed.value = speeds[nextIndex]

        if (audioElement.value) {
            audioElement.value.playbackRate = playbackSpeed.value
        }
    }

    const toggleFavorite = () => {
        if (isFavorite.value) {
            favorites.value = favorites.value.filter(fav => fav.id !== currentSong.value.id)
        } else {
            favorites.value.push(currentSong.value)
        }
        localStorage.setItem('favorites', JSON.stringify(favorites.value))
    }

    const playSong = (song, songsList = null) => {
        if (songsList) {
            const songIndex = songsList.findIndex(s => s.id === song.id)
            if (songIndex !== -1) {
                queue.value = songsList.slice(songIndex)
                currentIndex.value = 0
            }
        } else if (allSongs.value.length > 0) {
            const songIndex = allSongs.value.findIndex(s => s.id === song.id)
            if (songIndex !== -1) {
                queue.value = allSongs.value.slice(songIndex)
                currentIndex.value = 0
            }
        } else {
            queue.value = [song]
            currentIndex.value = 0
        }

        currentSong.value = song
        initAudio()
        if (!isPlaying.value) {
            togglePlay()
        }
    }

    const setSongsList = (songs) => {
        allSongs.value = songs
    }

    // Load favorites from localStorage
    if (typeof window !== 'undefined') {
        const savedFavorites = localStorage.getItem('favorites')
        if (savedFavorites) {
            try {
                favorites.value = JSON.parse(savedFavorites)
            } catch (e) {
                console.error('Error loading favorites:', e)
            }
        }
    }

    return {
        currentSong,
        isPlaying,
        currentTime,
        duration,
        playbackSpeed,
        progressPercentage,
        isFavorite,
        formatTime,
        togglePlay,
        seek,
        rewind,
        forward,
        previous,
        next,
        togglePlaybackSpeed,
        toggleFavorite,
        playSong,
        setSongsList,
        queue,
        allSongs
    }
}
