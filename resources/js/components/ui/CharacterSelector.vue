<script setup lang="ts">
interface CharacterOption {
  value: string;
  label: string;
  description: string;
}

interface Props {
  characters: CharacterOption[];
  modelValue: string | null;
  label?: string;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  label: '',
  class: ''
});

const emit = defineEmits<{
  'update:modelValue': [value: string | null];
}>();

const updateValue = (value: string) => {
  emit('update:modelValue', value);
};
</script>

<template>
  <div :class="props.class">
    <label v-if="label" class="block text-sm font-medium text-purple-200 mb-4">
      {{ label }}
    </label>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
      <label
        v-for="character in characters"
        :key="character.value"
        class="relative cursor-pointer flex"
      >
        <input
          type="radio"
          :value="character.value"
          :checked="modelValue === character.value"
          @input="updateValue(character.value)"
          class="sr-only"
        />
        <div
          class="p-4 bg-white/10 border-2 rounded-lg transition-all duration-200 hover:bg-white/20 w-full h-full"
          :class="[
            modelValue === character.value
              ? 'border-purple-400 bg-purple-500/20'
              : 'border-white/20'
          ]"
        >
          <!-- Character Image -->
          <div class="w-16 h-16 mx-auto mb-3 rounded-full overflow-hidden border border-white/30">
            <img
              :src="`/storage/sw/${character.value}-opt.jpg`"
              :alt="character.label"
              class="w-full h-full object-cover"
              @error="(e) => {
                const target = e.target as HTMLImageElement;
                target.style.display = 'none';
                (target.nextElementSibling as HTMLElement).style.display = 'flex';
              }"
            />
            <div class="w-full h-full bg-white/20 items-center justify-center border border-white/30" style="display: none;">
              <svg class="w-8 h-8 text-purple-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>
          
          <!-- Character Name -->
          <div class="text-center">
            <div class="font-medium text-white text-sm">{{ character.label }}</div>
            <div class="text-xs text-purple-300 mt-1">{{ character.description }}</div>
          </div>
          
          <!-- Selected Indicator -->
          <div
            v-if="modelValue === character.value"
            class="absolute top-2 right-2 w-5 h-5 bg-purple-500 rounded-full flex items-center justify-center"
          >
            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </div>
        </div>
      </label>
    </div>
  </div>
</template>