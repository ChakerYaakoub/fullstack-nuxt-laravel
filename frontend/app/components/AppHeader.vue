<script setup lang="ts">
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useCart } from '../composables/useCart'

const { count } = useCart()
const { data: categories } = await useCategories()
const route = useRoute()
const open = ref(false)

watch(
  () => route.fullPath,
  () => {
    open.value = false
  },
)
</script>

<template>
  <header class="header">
    <div class="container header__inner">
      <NuxtLink to="/" class="logo" aria-label="FERRA — Accueil">
        <span class="logo__mark" aria-hidden="true" />
        <span class="logo__text">FERRA</span>
      </NuxtLink>

      <nav class="nav" aria-label="Catégories">
        <NuxtLink
          v-for="cat in categories"
          :key="cat.slug"
          :to="`/categories/${cat.slug}`"
          class="nav__link"
        >
          {{ cat.name }}
        </NuxtLink>
      </nav>

      <div class="header__actions">
        <NuxtLink to="/contact" class="header__cta">Demander un devis</NuxtLink>

        <NuxtLink to="/cart" class="cart-link" aria-label="Panier">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path
              d="M6 6h15l-1.5 9h-12L6 6zm0 0L5 3H2"
              stroke="currentColor"
              stroke-width="1.6"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
            <circle cx="9" cy="20" r="1.2" fill="currentColor" />
            <circle cx="17" cy="20" r="1.2" fill="currentColor" />
          </svg>
          <span v-if="count" class="cart-link__badge">{{ count }}</span>
        </NuxtLink>

        <button
          class="menu-btn"
          type="button"
          :aria-expanded="open"
          aria-controls="mobile-nav"
          @click="open = !open"
        >
          <span class="sr-only">Menu</span>
          <span class="menu-btn__bar" :class="{ open }" />
          <span class="menu-btn__bar" :class="{ open }" />
        </button>
      </div>
    </div>

    <nav
      id="mobile-nav"
      class="mobile-nav"
      :class="{ 'mobile-nav--open': open }"
      aria-label="Menu mobile"
    >
      <div class="container mobile-nav__inner">
        <NuxtLink
          v-for="cat in categories"
          :key="cat.slug"
          :to="`/categories/${cat.slug}`"
          class="mobile-nav__link"
        >
          <span>{{ cat.name }}</span>
          <span>{{ cat.short }}</span>
        </NuxtLink>
        <NuxtLink to="/contact" class="mobile-nav__cta">Demander un devis</NuxtLink>
      </div>
    </nav>
  </header>
</template>

<style scoped>
.header {
  position: sticky;
  top: 0;
  z-index: 50;
  min-height: var(--header-h);
  backdrop-filter: blur(14px);
  background: rgba(255, 255, 255, 0.92);
  border-bottom: 1px solid rgba(139, 161, 128, 0.18);
}

.header__inner {
  min-height: var(--header-h);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.logo {
  display: inline-flex;
  align-items: center;
  gap: 0.65rem;
  flex-shrink: 0;
}

.logo__mark {
  width: 1.5rem;
  height: 1.5rem;
  border-radius: 50%;
  background: var(--color-accent);
  box-shadow: inset 0 0 0 3px #fff, 0 0 0 1px var(--color-accent);
}

.logo__text {
  font-family: var(--font-display);
  font-weight: 800;
  font-size: 1.3rem;
  letter-spacing: 0.08em;
  color: var(--color-ink);
}

.nav {
  display: none;
  align-items: center;
  gap: 0.1rem;
  flex: 1;
  justify-content: center;
}

.nav__link {
  padding: 0.45rem 0.65rem;
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--color-zinc);
  border-radius: var(--radius-pill);
  transition: color 0.2s ease, background 0.2s ease;
}

.nav__link:hover,
.nav__link.router-link-active {
  color: var(--color-ink);
  background: var(--color-accent-soft);
}

.header__actions {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  flex-shrink: 0;
}

.header__cta {
  display: none;
  padding: 0.65rem 1.15rem;
  border-radius: var(--radius-pill);
  background: var(--color-accent);
  color: #fff;
  font-size: 0.85rem;
  font-weight: 600;
  transition: background 0.2s ease, box-shadow 0.2s ease;
}

.header__cta:hover {
  background: var(--color-accent-hover);
  box-shadow: 0 8px 20px rgba(139, 161, 128, 0.3);
}

.cart-link {
  position: relative;
  display: grid;
  place-items: center;
  width: 2.6rem;
  height: 2.6rem;
  color: var(--color-ink);
  border-radius: 50%;
  transition: background 0.2s ease;
}

.cart-link:hover {
  background: var(--color-accent-soft);
}

.cart-link__badge {
  position: absolute;
  top: 0.1rem;
  right: 0.05rem;
  min-width: 1.15rem;
  height: 1.15rem;
  padding-inline: 0.25rem;
  border-radius: 999px;
  background: var(--color-accent);
  color: #fff;
  font-size: 0.65rem;
  font-weight: 700;
  display: grid;
  place-items: center;
}

.menu-btn {
  display: grid;
  gap: 5px;
  width: 2.6rem;
  height: 2.6rem;
  place-content: center;
  border-radius: 50%;
}

.menu-btn:hover {
  background: var(--color-accent-soft);
}

.menu-btn__bar {
  display: block;
  width: 1.2rem;
  height: 2px;
  background: var(--color-ink);
  transition: transform 0.25s var(--ease), opacity 0.2s ease;
  transform-origin: center;
}

.menu-btn__bar.open:first-child {
  transform: translateY(3.5px) rotate(45deg);
}

.menu-btn__bar.open:last-child {
  transform: translateY(-3.5px) rotate(-45deg);
}

.mobile-nav {
  display: none;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(139, 161, 128, 0.18);
  background: #fff;
}

.mobile-nav--open {
  display: block;
  animation: fade-in 0.25s ease;
}

.mobile-nav__inner {
  display: grid;
  gap: 0.15rem;
  padding-top: 0.35rem;
}

.mobile-nav__link {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 1rem;
  padding: 0.95rem 0.35rem;
  border-bottom: 1px solid rgba(139, 161, 128, 0.12);
  font-weight: 700;
  font-family: var(--font-display);
}

.mobile-nav__link span:last-child {
  font-family: var(--font-body);
  font-weight: 400;
  font-size: 0.8rem;
  color: var(--color-zinc);
}

.mobile-nav__cta {
  margin-top: 0.85rem;
  text-align: center;
  padding: 0.9rem 1rem;
  border-radius: var(--radius-pill);
  background: var(--color-accent);
  color: #fff;
  font-weight: 600;
}

@media (min-width: 980px) {
  .nav {
    display: flex;
  }

  .header__cta {
    display: inline-flex;
  }

  .menu-btn,
  .mobile-nav {
    display: none !important;
  }
}
</style>
