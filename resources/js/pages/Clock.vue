<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

const currentTime = ref(new Date());

let intervalId: number | null = null;

const updateTime = () => {
  currentTime.value = new Date();
};

const formatTime = (date: Date) => {
  return date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false,
  });
};

const formatDate = (date: Date) => {
  return date.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

onMounted(() => {
  intervalId = setInterval(updateTime, 1000);
});

onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId);
  }
});
</script>

<template>
    <Head title="Clock" />
    
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 dark:from-black dark:via-purple-950 dark:to-black flex items-center justify-center p-8">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25px 25px, rgba(255,255,255,0.1) 2px, transparent 0), radial-gradient(circle at 75px 75px, rgba(255,255,255,0.05) 2px, transparent 0); background-size: 100px 100px;"></div>
        </div>
        
        <!-- Clock Container -->
        <div class="relative z-10 text-center">
            <!-- Main Clock -->
            <div class="mb-8">
                <div class="text-8xl md:text-9xl lg:text-[12rem] xl:text-[14rem] font-mono font-bold text-white drop-shadow-2xl tracking-wide">
                    {{ formatTime(currentTime) }}
                </div>
                
                <!-- Glowing Effect -->
                <div class="absolute inset-0 text-8xl md:text-9xl lg:text-[12rem] xl:text-[14rem] font-mono font-bold text-purple-300 opacity-30 blur-sm tracking-wide">
                    {{ formatTime(currentTime) }}
                </div>
            </div>
            
            <!-- Date Display -->
            <div class="text-2xl md:text-3xl lg:text-4xl font-light text-purple-100 mb-12">
                {{ formatDate(currentTime) }}
            </div>
            
            <!-- Decorative Elements -->
            <div class="flex justify-center space-x-4 mb-8">
                <div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse"></div>
                <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
                <div class="w-2 h-2 bg-pink-400 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
            </div>
            
            <!-- Subtle Enhancement -->
            <div class="text-purple-300 text-lg font-light opacity-75">
                Time never stops
            </div>
        </div>
        
        <!-- Floating Particles -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-1 h-1 bg-white rounded-full opacity-60 animate-ping" style="animation-duration: 3s;"></div>
            <div class="absolute top-3/4 right-1/4 w-1 h-1 bg-purple-300 rounded-full opacity-60 animate-ping" style="animation-duration: 4s; animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-3/4 w-1 h-1 bg-blue-300 rounded-full opacity-60 animate-ping" style="animation-duration: 5s; animation-delay: 2s;"></div>
            <div class="absolute bottom-1/4 left-1/2 w-1 h-1 bg-pink-300 rounded-full opacity-60 animate-ping" style="animation-duration: 3.5s; animation-delay: 1.5s;"></div>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap');

.font-mono {
    font-family: 'JetBrains Mono', 'Courier New', monospace;
}

/* Smooth transitions for time updates */
.transition-all {
    transition: all 0.3s ease-in-out;
}
</style>