
<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps<{
  joke: string;
}>();

const showJoke = ref(true);
const currentImageIndex = ref(0);
let jokeTimer: number | undefined;
let imageTimer: number | undefined;

const backgroundImages = [
  '/storage/fly-01.jpg',
  '/storage/fly-02.jpg',
  '/storage/fly-03.jpg',
];

const currentBackground = computed(() => backgroundImages[currentImageIndex.value]);

const fetchNextJoke = () => {
  showJoke.value = false;
  setTimeout(() => {
    router.visit('/jokes', {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        showJoke.value = true;
      },
    });
  }, 1000);
};

const rotateBackground = () => {
  currentImageIndex.value = (currentImageIndex.value + 1) % backgroundImages.length;
};

onMounted(() => {
  jokeTimer = window.setInterval(fetchNextJoke, 20000);
  imageTimer = window.setInterval(rotateBackground, 60000);
});

onUnmounted(() => {
  clearInterval(jokeTimer);
  clearInterval(imageTimer);
});
</script>

<template>
  <Head title="Omar's Jokes" />
  <div class="relative flex h-screen items-center justify-center overflow-hidden">
    <div
      v-for="(image, index) in backgroundImages"
      :key="image"
      class="background-image"
      :class="{ active: currentImageIndex === index }"
      :style="{ backgroundImage: `url(${image})` }"
    ></div>

    <div class="relative z-10 mx-auto max-w-4xl px-8">
      <transition
        enter-active-class="transition-opacity duration-1000 ease-in"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-1000 ease-out"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showJoke"
          class="rounded-lg bg-black/60 p-8 text-center text-white backdrop-blur-sm kenburns"
        >
          <blockquote class="text-4xl italic">
            "{{ props.joke }}"
          </blockquote>
          <cite class="mt-4 block text-right text-2xl not-italic">- Omar</cite>
        </div>
      </transition>
    </div>
  </div>
</template>

<style scoped>
.background-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  opacity: 0;
  transition: opacity 2s ease-in-out;
}

.background-image.active {
  opacity: 1;
  animation: dronePan 60s ease-in-out infinite;
}

@keyframes dronePan {
  0% {
    transform: scale(1.2) translate(0, 0);
  }
  100% {
    transform: scale(1.4) translate(-8%, 6%);
  }
}

.kenburns {
  animation: textFloat 20s ease-in-out both;
}

@keyframes textFloat {
  0% {
    transform: scale(1) translate(0, 0);
    opacity: 1;
  }
  50% {
    transform: scale(1.05) translate(-10px, 5px);
  }
  100% {
    transform: scale(1.08) translate(10px, -8px);
    opacity: 1;
  }
}
</style>
