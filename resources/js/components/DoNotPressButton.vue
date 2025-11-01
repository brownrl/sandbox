<script setup lang="ts">
import { ref } from 'vue';

const showModal = ref(false);
const isPressed = ref(false);

const pressButton = () => {
    isPressed.value = true;
    setTimeout(() => {
        showModal.value = true;
    }, 2000); // Show modal after 2 seconds of chaos
};

const closeModal = () => {
    showModal.value = false;
    isPressed.value = false;
};

// Create a specified number of emoji particles
const createParticles = (emoji: string, count: number) => {
    const particles = [];
    for (let i = 0; i < count; i++) {
        particles.push({
            id: i,
            emoji: emoji,
            style: {
                left: `${Math.random() * 100}%`,
                top: `${Math.random() * 100}%`,
                animationDuration: `${Math.random() * 2 + 1}s`,
                animationDelay: `${Math.random() * 2}s`,
            },
        });
    }
    return particles;
};

const fireworks = createParticles('🎆', 30);
const emojis = createParticles('🤯', 30);
</script>

<template>
    <div class="group relative">
        <div
            @click="pressButton"
            class="h-full p-6 rounded-2xl border-4 transition-all duration-500 transform hover:scale-110 hover:z-10 bg-gradient-to-br from-red-600 to-yellow-500 border-white/30 hover:border-white/60 cursor-pointer flex flex-col items-center justify-center text-center"
        >
            <div class="text-7xl mb-4 transform group-hover:scale-125 transition-transform duration-300">
                🛑
            </div>
            <h3 class="text-2xl font-black text-white mb-2 drop-shadow-lg">
                DO NOT PRESS!
            </h3>
            <p class="text-white/90 font-mono text-sm leading-relaxed">
                Seriously, don't. It's for your own good.
            </p>
            <div class="absolute inset-0 bg-white/0 group-hover:bg-white/10 rounded-2xl transition-all duration-300 pointer-events-none"></div>
        </div>

        <!-- The Chaos Layer -->
        <div v-if="isPressed" class="absolute inset-0 pointer-events-none overflow-hidden rounded-2xl">
            <div v-for="p in fireworks" :key="`fw-${p.id}`" class="particle" :style="p.style">
                {{ p.emoji }}
            </div>
            <div v-for="p in emojis" :key="`em-${p.id}`" class="particle" :style="p.style">
                {{ p.emoji }}
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black/80 backdrop-blur-lg flex items-center justify-center z-50" @click="closeModal">
            <div class="relative w-full max-w-4xl p-4" @click.stop>
                <div class="aspect-w-16 aspect-h-9">
                    <iframe
                        src="https://www.youtube.com/embed/NGlTbgcw3t0?autoplay=1&rel=0"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                        class="w-full h-full rounded-lg shadow-2xl"
                    ></iframe>
                </div>
                <button @click="closeModal" class="absolute -top-4 -right-4 w-12 h-12 bg-white text-black rounded-full text-2xl font-bold flex items-center justify-center shadow-lg hover:bg-gray-200 transition-transform transform hover:scale-110">
                    &times;
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.particle {
    position: absolute;
    font-size: 3rem;
    animation: fly-up 3s ease-out forwards;
    opacity: 0;
}

@keyframes fly-up {
    0% {
        transform: translateY(100%) scale(0.5);
        opacity: 1;
    }
    100% {
        transform: translateY(-200%) scale(1.5);
        opacity: 0;
    }
}

/* For aspect ratio video */
.aspect-w-16 {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 */
}
.aspect-h-9 > * {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
</style>