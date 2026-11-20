import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ['class'],

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Noto Sans JP', ...defaultTheme.fontFamily.sans],
                heading: ['Noto Serif JP', 'Inter', ...defaultTheme.fontFamily.serif],
            },

            colors: {
                /*
                 * Paleta japonesa. Los tokens que cambian con el tema usan
                 * variables RGB para conservar los modificadores de opacidad
                 * de Tailwind (text-warm/55, bg-sumi/95, border-warm/10...).
                 */
                sumi: 'rgb(var(--ks-sumi-rgb) / <alpha-value>)',
                charcoal: 'rgb(var(--ks-charcoal-rgb) / <alpha-value>)',
                shu: '#B52B32',
                washi: 'rgb(var(--ks-washi-rgb) / <alpha-value>)',
                moss: '#778477',
                warm: 'rgb(var(--ks-warm-rgb) / <alpha-value>)',

                /*
                 * Reasignamos las escalas ya usadas por las vistas a la nueva
                 * paleta. Así no hay que reescribir cientos de clases y se
                 * conserva el comportamiento de cada componente.
                 */
                white: '#F3F0E9',
                slate: {
                    50: '#F3F0E9',
                    100: '#E8E3D7',
                    200: '#D9CDB8',
                    300: '#C4BCAE',
                    400: '#A6A29B',
                    500: '#8B8A85',
                    600: '#6E6E69',
                    700: '#4A4D4C',
                    800: '#292D32',
                    900: '#1F2021',
                    950: '#171717',
                },
                gray: {
                    50: '#F3F0E9',
                    100: '#E8E3D7',
                    200: '#D9CDB8',
                    300: '#C4BCAE',
                    400: '#A6A29B',
                    500: '#8B8A85',
                    600: '#6E6E69',
                    700: '#4A4D4C',
                    800: '#292D32',
                    900: '#1F2021',
                    950: '#171717',
                },
                red: {
                    50: '#FBF1F1',
                    100: '#F6DADC',
                    200: '#EDB5B8',
                    300: '#E0898E',
                    400: '#CC5C63',
                    500: '#BE3A42',
                    600: '#B52B32',
                    700: '#99242A',
                    800: '#7C1F24',
                    900: '#5E191D',
                    950: '#3A0E11',
                },

                /* Variables semánticas (shadcn / formularios) */
                border: 'hsl(var(--border))',
                input: 'hsl(var(--input))',
                ring: 'hsl(var(--ring))',
                background: 'hsl(var(--background))',
                foreground: 'hsl(var(--foreground))',

                primary: {
                    DEFAULT: 'hsl(var(--primary))',
                    foreground: 'hsl(var(--primary-foreground))',
                },

                secondary: {
                    DEFAULT: 'hsl(var(--secondary))',
                    foreground: 'hsl(var(--secondary-foreground))',
                },

                destructive: {
                    DEFAULT: 'hsl(var(--destructive))',
                    foreground: 'hsl(var(--destructive-foreground))',
                },

                muted: {
                    DEFAULT: 'hsl(var(--muted))',
                    foreground: 'hsl(var(--muted-foreground))',
                },

                accent: {
                    DEFAULT: 'hsl(var(--accent))',
                    foreground: 'hsl(var(--accent-foreground))',
                },

                popover: {
                    DEFAULT: 'hsl(var(--popover))',
                    foreground: 'hsl(var(--popover-foreground))',
                },

                card: {
                    DEFAULT: 'hsl(var(--card))',
                    foreground: 'hsl(var(--card-foreground))',
                },
            },

            borderRadius: {
                xl: 'calc(var(--radius) + 4px)',
                lg: 'var(--radius)',
                md: 'calc(var(--radius) - 2px)',
                sm: 'calc(var(--radius) - 4px)',
            },

            boxShadow: {
                'ks-soft': '0 18px 45px -24px rgba(0, 0, 0, 0.85)',
                'ks-accent': '0 12px 34px -16px rgba(var(--ks-accent-rgb), 0.65)',
            },
        },
    },

    plugins: [forms],
}
