<script setup lang="ts">
interface Props {
  title: string;
  value: string | number;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  class: ''
});
</script>

<template>
  <div :class="['bg-white/10 p-4 rounded-lg', props.class]">
    <h3 class="text-lg font-medium text-purple-200 mb-2">{{ title }}</h3>
    <p class="text-2xl font-bold text-white">{{ value }}</p>
  </div>
</template>