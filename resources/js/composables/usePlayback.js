import { usePlayerStore } from '@/stores/playerStore'

export function usePlayback() {
    const player = usePlayerStore()

    const play = async (song, queue = []) => {
        if (!song) return
        if (queue.length) player.setPlaylist(queue)
        await player.playSong(song)
    }

    return { player, play }
}
