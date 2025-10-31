import { ref, onMounted } from 'vue';

type StarWarsTheme = 'light' | 'dark';

const COOKIE_NAME = 'star_wars_theme';
const COOKIE_DAYS = 365;

const setCookie = (name: string, value: string, days = COOKIE_DAYS) => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;
    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const getCookie = (name: string): string | null => {
    if (typeof document === 'undefined') {
        return null;
    }

    const matches = document.cookie.match(new RegExp(
        '(?:^|; )' + name.replace(/([.$?*|{}()[\]\\/+^])/g, '\\$1') + '=([^;]*)'
    ));
    return matches ? decodeURIComponent(matches[1]) : null;
};

const theme = ref<StarWarsTheme>('dark');

export function useStarWarsTheme() {
    onMounted(() => {
        // Load theme from cookie on mount
        const savedTheme = getCookie(COOKIE_NAME) as StarWarsTheme | null;
        if (savedTheme === 'light' || savedTheme === 'dark') {
            theme.value = savedTheme;
        }
    });

    const setTheme = (newTheme: StarWarsTheme) => {
        theme.value = newTheme;
        setCookie(COOKIE_NAME, newTheme);
    };

    const toggleTheme = () => {
        const newTheme = theme.value === 'light' ? 'dark' : 'light';
        setTheme(newTheme);
    };

    const isLightSide = () => theme.value === 'light';
    const isDarkSide = () => theme.value === 'dark';

    return {
        theme,
        setTheme,
        toggleTheme,
        isLightSide,
        isDarkSide,
    };
}
