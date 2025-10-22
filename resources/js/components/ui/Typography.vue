<script setup lang="ts">
interface Props {
  variant?: 'h1' | 'h2' | 'h3' | 'h4' | 'display' | 'subtitle' | 'body' | 'caption';
  as?: string;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'body',
  as: undefined,
  class: ''
});

const getClasses = (variant: Props['variant']) => {
  const baseClasses = 'text-white';
  
  const variants = {
    display: `${baseClasses} text-4xl font-bold mb-4`,
    h1: `${baseClasses} text-4xl font-bold mb-4`,
    h2: `${baseClasses} text-2xl font-semibold mb-4`,
    h3: `${baseClasses} text-xl font-semibold mb-4`,
    h4: `${baseClasses} text-lg font-medium mb-2`,
    subtitle: `${baseClasses} text-purple-200 text-lg`,
    body: `${baseClasses} text-base`,
    caption: `${baseClasses} text-sm text-purple-300`,
  } as const;
  
  return variants[variant!] || variants.body;
};

const tag = props.as || (props.variant === 'display' ? 'h1' : props.variant);
</script>

<template>
  <component :is="tag" :class="[getClasses(variant), props.class]">
    <slot />
  </component>
</template>