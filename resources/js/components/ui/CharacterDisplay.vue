<script setup lang="ts">
interface Props {
    characterSlug: string;
    characterLabel: string;
    count?: number;
    subtitle?: string;
    class?: string;
}

const props = withDefaults(defineProps<Props>(), {
    count: undefined,
    subtitle: '',
    class: ''
});
</script>

<template>
    <div :class="['flex items-center gap-4 p-4 bg-black/30 rounded-lg border border-red-900/50', props.class]">
        <!-- Character Image -->
        <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-red-700 shrink-0">
            <img
                :src="`/storage/sw/${characterSlug}-opt.jpg`"
                :alt="characterLabel"
                class="w-full h-full object-cover"
                @error="(e) => {
                    const target = e.target as HTMLImageElement;
                    target.style.display = 'none';
                    (target.nextElementSibling as HTMLElement).style.display = 'flex';
                }"
            />
            <div class="w-full h-full bg-black/20 items-center justify-center" style="display: none;">
                <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>
        
        <!-- Character Info -->
        <div class="flex-1 min-w-0">
            <div class="text-lg font-semibold text-red-500 truncate">{{ characterLabel }}</div>
            <div v-if="subtitle" class="text-sm text-gray-400 mt-0.5">{{ subtitle }}</div>
            <div v-if="count !== undefined" class="text-2xl font-bold text-white mt-1">
                {{ count }} {{ count === 1 ? 'survey' : 'surveys' }}
            </div>
        </div>
    </div>
</template>
