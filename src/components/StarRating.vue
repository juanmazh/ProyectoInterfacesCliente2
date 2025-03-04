<template>
  <div class="star-rating">
    <span
      v-for="n in 5"
      :key="n"
      class="star"
      :class="{ filled: n <= rating }"
      @click="setRating(n)"
      :aria-label="`Calificar con ${n} puntuacion`"
      role="button"
      tabindex="0"
      @keydown.enter="setRating(n)"
      @keydown.space="setRating(n)"
      :style="readonly ? {} : { cursor: 'pointer' }"
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
    required: true,
    default: 0 // Valor predeterminado para evitar que sea null
  },
  readonly: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue']);

const rating = ref(props.modelValue || 0); // Si modelValue es null o undefined, se asigna 0

watch(rating, (newRating) => {
  if (newRating !== props.modelValue) {
    emit('update:modelValue', newRating); // Emitimos la actualización de la valoración al padre
  }
});

const setRating = (value) => {
  if (!props.readonly && value !== rating.value) {
    rating.value = value; // Solo se actualiza si no está en readonly
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
}

.star {
  margin: 0 0.1rem;
  cursor: pointer;
}

.star.filled {
  color: #ffd700;
}

.star:not(.filled) {
  color: #e4e5e9;
}

.star:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.2);
}
</style>
