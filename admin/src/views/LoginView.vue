<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { login } from '../auth'

const router = useRouter()
const route = useRoute()

const email = ref('admin@hotmail.com')
const password = ref('')
const error = ref('')
const loading = ref(false)

const redirectTo = computed(() => {
  const value = route.query.redirect
  return typeof value === 'string' && value ? value : '/dashboard'
})

async function onSubmit() {
  error.value = ''
  loading.value = true

  try {
    await login(email.value.trim(), password.value)
    await router.push(redirectTo.value)
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="login">
    <form class="card" @submit.prevent="onSubmit">
      <p class="brand">FERRA</p>
      <h1>Admin</h1>
      <p class="lead">JWT login against Laravel API.</p>

      <label>
        Email
        <input v-model="email" type="email" autocomplete="username" required>
      </label>

      <label>
        Password
        <input
          v-model="password"
          type="password"
          autocomplete="current-password"
          required
        >
      </label>

      <p v-if="error" class="error">{{ error }}</p>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Signing in…' : 'Sign in' }}
      </button>

      <p class="hint">admin@hotmail.com · password</p>
    </form>
  </div>
</template>

<style scoped>
.login {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 1.5rem;
  background:
    radial-gradient(ellipse 70% 50% at 20% 0%, rgba(139, 161, 128, 0.22), transparent 55%),
    var(--color-paper);
}

.card {
  width: min(100%, 380px);
  display: grid;
  gap: 0.85rem;
  padding: 1.75rem;
  background: #fff;
  border: 1px solid rgba(139, 161, 128, 0.22);
  border-radius: 8px;
}

.brand {
  margin: 0;
  font-family: Syne, Manrope, sans-serif;
  font-weight: 800;
  letter-spacing: 0.08em;
  color: var(--color-accent);
}

h1 {
  margin: 0;
  font-family: Syne, Manrope, sans-serif;
  font-size: 1.75rem;
  color: var(--color-ink);
}

.lead {
  margin: 0 0 0.5rem;
  color: var(--color-zinc);
  font-size: 0.95rem;
}

label {
  display: grid;
  gap: 0.35rem;
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--color-ink-soft);
}

input {
  font: inherit;
  padding: 0.7rem 0.8rem;
  border: 1px solid rgba(139, 161, 128, 0.35);
  border-radius: var(--radius);
  background: var(--color-paper);
}

input:focus {
  outline: 2px solid var(--color-accent-soft);
  border-color: var(--color-accent);
}

button {
  margin-top: 0.35rem;
  padding: 0.85rem 1.2rem;
  border: 0;
  border-radius: var(--radius-pill);
  background: var(--color-accent);
  color: #fff;
  font: inherit;
  font-weight: 600;
  cursor: pointer;
}

button:hover:not(:disabled) {
  background: var(--color-accent-hover);
}

button:disabled {
  opacity: 0.7;
  cursor: wait;
}

.error {
  margin: 0;
  color: var(--color-danger);
  font-size: 0.9rem;
}

.hint {
  margin: 0.25rem 0 0;
  color: var(--color-zinc);
  font-size: 0.8rem;
}
</style>
