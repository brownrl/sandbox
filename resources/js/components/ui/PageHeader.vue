<script setup lang="ts">
interface Props {
  title: string;
  subtitle?: string;
  emoji?: string;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  subtitle: '',
  emoji: '',
  class: ''
});
</script>

<template>
  <div :class="['text-center mb-8', props.class]">
    <h1 class="text-4xl font-bold text-white mb-4">
      <span v-if="emoji">{{ emoji }} </span>{{ title }}<span v-if="emoji"> {{ emoji }}</span>
    </h1>
    <p v-if="subtitle" class="text-purple-200 text-lg">
      {{ subtitle }}
    </p>
    <slot name="additional" />
  </div>
</template>