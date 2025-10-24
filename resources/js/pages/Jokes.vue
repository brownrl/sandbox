
<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps<{
  joke: string;
}>();

const showJoke = ref(true);
let timer: number | undefined;

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
  }, 1000); // Corresponds to the fade-out duration
};

onMounted(() => {
  timer = window.setInterval(fetchNextJoke, 20000);
});

onUnmounted(() => {
  clearInterval(timer);
});
</script>

<template>
  <Head title="Omar's Jokes" />
  <div class="flex h-screen items-center justify-center bg-gray-900 text-white">
    <transition
      enter-active-class="transition-opacity duration-1000 ease-in"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-1000 ease-out"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="showJoke" class="text-center kenburns">
        <blockquote class="text-4xl italic">
          "{{ props.joke }}"
        </blockquote>
        <cite class="mt-4 block text-right text-2xl not-italic">- Omar</cite>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.kenburns {
  animation: kenburns 20s ease-in-out infinite both;
}

@keyframes kenburns {
  0% {
    transform: scale(1) translate(0, 0);
  }
  100% {
    transform: scale(1.05) translate(-10px, -15px);
  }
}
</style>
