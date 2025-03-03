<template>
    <div class="star-rating">
      <span
        v-for="n in 5"
        :key="n"
        class="star"
        :class="{ filled: n <= rating }"
        @click="setRating(n)"
      >
        ★
      </span>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue';
  
  const props = defineProps({
    modelValue: {
      type: Number,
      required: true
    },
    readonly: {
      type: Boolean,
      default: false
    }
  });
  
  const emit = defineEmits(['update:modelValue']);
  
  const rating = ref(props.modelValue);
  
  watch(() => props.modelValue, (newValue) => {
    rating.value = newValue;
  });
  
  watch(rating, (newRating) => {
    if (newRating !== props.modelValue) {
      emit('update:modelValue', newRating);
    }
  });
  
  const setRating = (value) => {
    if (!props.readonly) {
      rating.value = value;
    }
  };
  </script>
  
  <style scoped>
  .star-rating {
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 2rem;
    color: #ffd700;
    cursor: pointer;
  }
  
  .star {
    margin: 0 0.1rem;
  }
  
  .star.filled {
    color: #ffd700;
  }
  
  .star:not(.filled) {
    color: #e4e5e9;
  }
  </style>