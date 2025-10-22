<script setup lang="ts">
interface Props {
  modelValue: number;
  questionText: string;
  questionNumber?: number;
  error?: string;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  questionNumber: undefined,
  error: '',
  class: ''
});

const emit = defineEmits<{
  'update:modelValue': [value: number];
}>();

const updateRating = (rating: number) => {
  emit('update:modelValue', rating);
};
</script>

<template>
  <div :class="['p-6 bg-white/5 rounded-lg border border-white/10', props.class]">
    <div class="mb-4">
      <h3 v-if="questionNumber" class="text-lg font-medium text-white mb-2">
        Question {{ questionNumber }}
      </h3>
      <p class="text-purple-200">
        {{ questionText }}
      </p>
    </div>

    <!-- Rating Scale -->
    <div class="grid grid-cols-10 gap-2">
      <button
        v-for="rating in 10"
        :key="rating"
        type="button"
        @click="updateRating(rating)"
        class="aspect-square flex items-center justify-center rounded-lg border-2 transition-all duration-200 text-sm font-medium"
        :class="[
          modelValue === rating
            ? 'bg-purple-500 border-purple-400 text-white shadow-lg'
            : 'bg-white/10 border-white/20 text-purple-200 hover:bg-white/20 hover:border-purple-400'
        ]"
      >
        {{ rating }}
      </button>
    </div>

    <div class="flex justify-between text-xs text-purple-300 mt-2">
      <span>Strongly Disagree</span>
      <span>Strongly Agree</span>
    </div>

    <div v-if="error && !modelValue" class="mt-2 text-red-400 text-sm">
      {{ error }}
    </div>
  </div>
</template>