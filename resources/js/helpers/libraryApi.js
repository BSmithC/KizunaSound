function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
}

async function post(url, payload = {}) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify(payload),
    })

    if (!response.ok) {
        const message = await response.text()
        throw new Error(message || `Error ${response.status}`)
    }

    return response.json()
}

export function toggleFavorite(songId) {
    return post(route('library.favorites.toggle', songId))
}

export function recordHistory(songId, source = 'player') {
    return post(route('library.history.record', songId), { source })
}

export function toggleAlbum(albumId) {
    return post(route('library.albums.toggle', albumId))
}

export function toggleArtist(artistId) {
    return post(route('library.artists.toggle', artistId))
}

export function togglePlaylistSong(playlistId, songId) {
    return post(route('playlists.songs.toggle', [playlistId, songId]))
}
