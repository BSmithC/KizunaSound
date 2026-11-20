const DEFAULT_ACCENT = '#B52B32'
const DEFAULT_ACCENT_RGB = '181, 43, 50'
const DEFAULT_THEME = 'dark'

function hexToRgb(accent) {
    if (typeof accent !== 'string') return null

    const value = accent.trim().replace('#', '')
    const expanded = value.length === 3
        ? value.split('').map((char) => char + char).join('')
        : value

    if (!/^[0-9a-fA-F]{6}$/.test(expanded)) return null

    const r = parseInt(expanded.slice(0, 2), 16)
    const g = parseInt(expanded.slice(2, 4), 16)
    const b = parseInt(expanded.slice(4, 6), 16)

    return `${r}, ${g}, ${b}`
}

function applyAccent(accent = null) {
    const root = document.documentElement
    const rgb = hexToRgb(accent)

    if (rgb) {
        root.style.setProperty('--ks-accent', `#${accent.trim().replace('#', '')}`)
        root.style.setProperty('--ks-accent-rgb', rgb)
    } else {
        root.style.setProperty('--ks-accent', DEFAULT_ACCENT)
        root.style.setProperty('--ks-accent-rgb', DEFAULT_ACCENT_RGB)
    }
}

function storedTheme() {
    const stored = localStorage.getItem('theme')

    return stored === 'light' || stored === 'dark' ? stored : DEFAULT_THEME
}

function applyTheme(theme, accent) {
    const dark = theme === 'dark'
    const root = document.documentElement

    root.classList.toggle('dark', dark)
    root.dataset.theme = theme
    root.style.colorScheme = dark ? 'dark' : 'light'

    if (accent !== undefined) applyAccent(accent)

    return dark
}

function setThemeOnLoad(accent = null) {
    return applyTheme(storedTheme(), accent || DEFAULT_ACCENT)
}

function switchTheme() {
    const current = document.documentElement.classList.contains('dark') ? 'dark' : 'light'
    const next = current === 'dark' ? 'light' : 'dark'

    localStorage.setItem('theme', next)

    return applyTheme(next)
}

export { setThemeOnLoad, switchTheme, applyAccent, storedTheme }
