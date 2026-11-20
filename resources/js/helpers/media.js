export function mediaUrl(path) {
    if (!path) return null
    return `/storage/${String(path).replace(/^\/+/, '')}`
}

export function formatDuration(seconds) {
    if (seconds === null || seconds === undefined || isNaN(seconds) || !isFinite(seconds)) return '0:00'
    const total = Math.max(0, Math.floor(Number(seconds)))
    const minutes = Math.floor(total / 60)
    const rest = total % 60
    return `${minutes}:${String(rest).padStart(2, '0')}`
}

export function initials(name) {
    if (!name) return 'K'
    return String(name)
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() ?? '')
        .join('')
}
