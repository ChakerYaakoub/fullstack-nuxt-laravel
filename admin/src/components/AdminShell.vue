<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getAdminEmail, logout } from '../auth'

const route = useRoute()
const router = useRouter()
const email = computed(() => getAdminEmail() ?? '')

const links = [
  { to: '/dashboard', label: 'Dashboard' },
  { to: '/products', label: 'Products' },
  { to: '/quotes', label: 'Demande de devis' },
]

async function onLogout() {
  await logout()
  await router.push('/login')
}
</script>

<template>
  <div class="shell">
    <header class="top">
      <div class="top__left">
        <p class="brand">FERRA Admin</p>
        <p class="user">{{ email || '…' }}</p>
      </div>
      <nav class="nav">
        <RouterLink
          v-for="link in links"
          :key="link.to"
          :to="link.to"
          class="nav__link"
          :class="{ 'is-active': route.path === link.to }"
        >
          {{ link.label }}
        </RouterLink>
      </nav>
      <button type="button" class="ghost" @click="onLogout">Logout</button>
    </header>
    <main class="main">
      <slot />
    </main>
  </div>
</template>

<style scoped>
.shell {
  min-height: 100vh;
  background: var(--color-paper);
}

.top {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem 1.5rem;
  background: #fff;
  color: var(--color-ink);
  border-bottom: 1px solid rgba(139, 161, 128, 0.22);
}

.brand {
  margin: 0;
  font-family: Syne, Manrope, sans-serif;
  font-weight: 800;
  letter-spacing: 0.06em;
  color: var(--color-accent);
}

.user {
  margin: 0.2rem 0 0;
  color: var(--color-zinc);
  font-size: 0.85rem;
}

.nav {
  display: flex;
  flex-wrap: wrap;
  gap: 0.35rem;
}

.nav__link {
  color: var(--color-zinc);
  text-decoration: none;
  padding: 0.45rem 0.85rem;
  border-radius: var(--radius-pill);
  font-size: 0.9rem;
  font-weight: 600;
}

.nav__link:hover {
  color: var(--color-ink);
  background: var(--color-accent-soft);
}

.nav__link.is-active {
  color: #fff;
  background: var(--color-accent);
}

.ghost {
  border: 1px solid rgba(139, 161, 128, 0.45);
  background: transparent;
  color: var(--color-ink);
  padding: 0.55rem 1rem;
  border-radius: var(--radius-pill);
  font: inherit;
  cursor: pointer;
  font-weight: 600;
}

.ghost:hover {
  background: var(--color-accent-soft);
}

.main {
  width: min(100% - 2rem, 1100px);
  margin: 2rem auto;
}
</style>
