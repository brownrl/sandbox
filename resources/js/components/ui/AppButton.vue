<script setup lang="ts">
interface Props {
  variant?: 'primary' | 'secondary' | 'outline' | 'success';
  size?: 'sm' | 'md' | 'lg';
  disabled?: boolean;
  loading?: boolean;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'md',
  disabled: false,
  loading: false,
  class: ''
});

const getClasses = () => {
  const baseClasses = 'font-semibold rounded-lg shadow-lg focus:outline-none focus:ring-4 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed';
  
  const variants = {
    primary: 'px-8 py-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white hover:from-purple-600 hover:to-pink-600 focus:ring-purple-400/50',
    secondary: 'px-6 py-3 bg-white/10 border border-white/20 text-white hover:bg-white/20 focus:ring-white/20',
    outline: 'px-6 py-3 bg-transparent border-2 border-purple-400 text-purple-400 hover:bg-purple-400 hover:text-white focus:ring-purple-400/50',
    success: 'px-6 py-3 bg-green-500 text-white hover:bg-green-600 focus:ring-green-400/50'
  };
  
  const sizes = {
    sm: 'px-4 py-2 text-sm',
    md: 'px-6 py-3',
    lg: 'px-8 py-4 text-lg'
  };
  
  return `${baseClasses} ${variants[props.variant]} ${sizes[props.size]}`;
};
</script>

<template>
  <button
    :disabled="disabled || loading"
    :class="[getClasses(), props.class]"
    v-bind="$attrs"
  >
    <span v-if="loading">
      <svg class="w-4 h-4 animate-spin inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
        <path d="M8 5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM12 5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM16 5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
      </svg>
    </span>
    <slot />
  </button>
</template>