<script setup lang="ts">
withDefaults(
  defineProps<{
    label?: string
    block?: boolean
  }>(),
  {
    label: 'Chargement…',
    block: true,
  },
)
</script>

<template>
  <div class="loading" :class="{ 'loading--block': block }" role="status" aria-live="polite">
    <span class="loading__spinner" aria-hidden="true" />
    <span class="loading__label">{{ label }}</span>
  </div>
</template>

<style scoped>
.loading {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  color: var(--color-ink-soft);
  font-size: 0.95rem;
}

.loading--block {
  display: flex;
  justify-content: center;
  width: 100%;
  padding-block: 3.5rem;
}

.loading__spinner {
  width: 1.35rem;
  height: 1.35rem;
  border: 2px solid rgba(139, 161, 128, 0.25);
  border-top-color: var(--color-accent);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

.loading__label {
  font-weight: 500;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
