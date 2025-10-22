<script setup lang="ts">
interface Props {
  title: string;
  value: string | number;
  icon?: string;
  color?: 'purple' | 'blue' | 'green' | 'red' | 'yellow';
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  icon: '',
  color: 'purple',
  class: ''
});

const getColorClasses = (color: Props['color']) => {
  const colors = {
    purple: 'from-purple-500 to-pink-500',
    blue: 'from-blue-500 to-cyan-500',
    green: 'from-green-500 to-emerald-500',
    red: 'from-red-500 to-pink-500',
    yellow: 'from-yellow-500 to-orange-500'
  } as const;
  return colors[color!] || colors.purple;
};
</script>

<template>
  <div :class="['p-6 bg-white/5 rounded-lg border border-white/10', props.class]">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-medium text-purple-200">{{ title }}</h3>
      <div v-if="icon" class="text-2xl">{{ icon }}</div>
    </div>
    
    <div class="text-center">
      <div class="text-3xl font-bold text-white mb-2">{{ value }}</div>
      <div :class="['h-1 w-full rounded-full bg-gradient-to-r', getColorClasses(color)]"></div>
    </div>
    
    <slot />
  </div>
</template>