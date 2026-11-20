const KEY = 'kizunasound.searchHistory'

export function getSearchHistory() {
    try {
        const parsed = JSON.parse(localStorage.getItem(KEY) || '[]')
        return Array.isArray(parsed) ? parsed.filter((item) => typeof item === 'string') : []
    } catch {
        return []
    }
}

export function pushSearchHistory(term) {
    const clean = String(term || '').trim()
    if (clean.length < 2) return getSearchHistory()

    const next = [clean, ...getSearchHistory().filter((item) => item.toLowerCase() !== clean.toLowerCase())].slice(0, 8)
    localStorage.setItem(KEY, JSON.stringify(next))
    return next
}

export function clearSearchHistory() {
    localStorage.removeItem(KEY)
    return []
}
