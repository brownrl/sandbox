<script setup lang="ts">
import { useStarWarsTheme } from '@/composables/useStarWarsTheme';

const { theme, toggleTheme, isLightSide, isDarkSide } = useStarWarsTheme();
</script>

<template>
    <div class="theme-switcher">
        <button
            @click="toggleTheme"
            class="flex items-center gap-2 px-4 py-2 rounded-lg border-2 transition-all duration-300 hover:scale-105"
            :class="[
                isLightSide() 
                    ? 'bg-gradient-to-r from-blue-500 to-cyan-400 border-blue-300 text-white hover:from-blue-600 hover:to-cyan-500' 
                    : 'bg-gradient-to-r from-red-700 to-black border-red-900 text-red-500 hover:from-red-800 hover:to-gray-900'
            ]"
            :title="isLightSide() ? 'Switch to Dark Side' : 'Switch to Light Side'"
        >
            <svg 
                v-if="isLightSide()" 
                class="w-5 h-5" 
                fill="currentColor" 
                viewBox="0 0 20 20"
            >
                <path 
                    fill-rule="evenodd" 
                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" 
                    clip-rule="evenodd" 
                />
            </svg>
            <svg 
                v-else 
                class="w-5 h-5" 
                fill="currentColor" 
                viewBox="0 0 20 20"
            >
                <path 
                    d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" 
                />
            </svg>
            <span class="font-semibold text-sm">
                {{ isLightSide() ? 'Light Side' : 'Dark Side' }}
            </span>
        </button>
    </div>
</template>

<style scoped>
.theme-switcher button {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
}

.theme-switcher button:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
}
</style>
