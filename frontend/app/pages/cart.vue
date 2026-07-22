<script setup lang="ts">
import { ref } from 'vue'
import { useSeoMeta } from 'nuxt/app'
import { useCart } from '../composables/useCart'
import { formatPrice } from '../data/catalog'

const { items, total, count, pending, loaded, setQuantity, remove, clear, refresh } =
  useCart()

const booting = ref(true)
try {
  if (!loaded.value) {
    await refresh()
  }
} finally {
  booting.value = false
}

useSeoMeta({
  title: 'Panier — FERRA',
  description: 'Votre panier FERRA. Finalisez votre sélection ou demandez un devis.',
  robots: 'noindex, follow',
})
</script>

<template>
  <section class="section cart">
    <div class="container">
      <h1 class="cart__title animate-fade-up">Panier</h1>

      <AppLoading v-if="booting" label="Chargement du panier…" />

      <template v-else>
        <p class="cart__lead animate-fade-up delay-1">
          {{ count ? `${count} article${count > 1 ? 's' : ''}` : 'Votre panier est vide.' }}
          <span v-if="pending" class="cart__sync"> · sync…</span>
        </p>

        <div v-if="items.length" class="cart__layout">
          <ul class="cart__list">
            <li v-for="item in items" :key="item.product.id" class="cart-row">
              <div
                class="cart-row__swatch"
                :style="{ background: item.product.imageTone }"
                aria-hidden="true"
              />
              <div class="cart-row__info">
                <NuxtLink :to="`/products/${item.product.slug}`" class="cart-row__name">
                  {{ item.product.name }}
                </NuxtLink>
                <p class="cart-row__meta">{{ formatPrice(item.product.price) }} / unité</p>
              </div>
              <div class="cart-row__qty">
                <label class="sr-only" :for="`qty-${item.product.id}`">Quantité</label>
                <input
                  :id="`qty-${item.product.id}`"
                  type="number"
                  min="1"
                  :value="item.quantity"
                  :disabled="pending"
                  @change="setQuantity(item.product.id, Number(($event.target as HTMLInputElement).value))"
                >
              </div>
              <p class="cart-row__line price">
                {{ formatPrice(item.product.price * item.quantity) }}
              </p>
              <button
                class="cart-row__remove"
                type="button"
                :disabled="pending"
                @click="remove(item.product.id)"
              >
                Retirer
              </button>
            </li>
          </ul>

          <aside class="summary">
            <p class="summary__label">Total estimé</p>
            <p class="summary__total price">{{ formatPrice(total) }}</p>
            <p class="summary__note">
              Panier synchronisé avec l’API. Demandez un devis pour finaliser.
            </p>
            <NuxtLink to="/contact" class="btn btn--primary summary__btn">
              Demander un devis
            </NuxtLink>
            <button
              class="btn btn--ghost summary__btn"
              type="button"
              :disabled="pending"
              @click="clear()"
            >
              Vider le panier
            </button>
          </aside>
        </div>

        <div v-else class="empty">
          <p class="empty__text">Parcourez le catalogue pour ajouter des modèles.</p>
          <NuxtLink to="/" class="btn btn--primary">Parcourir le catalogue</NuxtLink>
        </div>
      </template>
    </div>
  </section>
</template>

<style scoped>
.cart__title {
  font-size: clamp(2rem, 4.5vw, 2.85rem);
  margin-bottom: 0.4rem;
}

.cart__lead {
  color: var(--color-zinc);
  margin-bottom: var(--space-lg);
  font-size: 1.02rem;
}

.cart__sync {
  font-size: 0.85rem;
  opacity: 0.7;
}

.cart__layout {
  display: grid;
  gap: 1.5rem;
}

.cart__list {
  display: grid;
  gap: 0.85rem;
}

.cart-row {
  display: grid;
  grid-template-columns: 4rem 1fr;
  gap: 0.75rem 1rem;
  padding: 1rem;
  align-items: center;
  background: var(--color-surface);
  border: 1px solid rgba(139, 161, 128, 0.18);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
}

.cart-row__swatch {
  width: 4rem;
  height: 4rem;
  border-radius: var(--radius-sm);
  grid-row: span 2;
}

.cart-row__name {
  font-family: var(--font-display);
  font-weight: 700;
  font-size: 1.05rem;
}

.cart-row__name:hover {
  color: var(--color-accent-deep);
}

.cart-row__meta {
  font-size: 0.85rem;
  color: var(--color-zinc);
}

.cart-row__qty input {
  width: 4.25rem;
  padding: 0.5rem 0.55rem;
  border: 1px solid rgba(139, 161, 128, 0.35);
  border-radius: var(--radius-sm);
  background: var(--color-paper);
  font: inherit;
}

.cart-row__line {
  font-size: 1.08rem;
  color: var(--color-accent-deep);
}

.cart-row__remove {
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--color-zinc);
  text-decoration: underline;
  text-underline-offset: 2px;
  justify-self: start;
}

.cart-row__remove:hover {
  color: var(--color-danger);
}

.summary {
  padding: 1.45rem;
  background: var(--color-surface);
  border: 1px solid rgba(139, 161, 128, 0.2);
  border-radius: calc(var(--radius) + 2px);
  box-shadow: var(--shadow-sm);
  align-self: start;
  position: sticky;
  top: calc(var(--header-h) + 1rem);
}

.summary__label {
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--color-zinc);
}

.summary__total {
  font-size: 1.9rem;
  margin: 0.4rem 0 0.8rem;
  color: var(--color-accent-deep);
}

.summary__note {
  font-size: 0.88rem;
  color: var(--color-zinc);
  margin-bottom: 1.25rem;
  line-height: 1.55;
}

.summary__btn {
  width: 100%;
  margin-bottom: 0.55rem;
}

.empty {
  padding: clamp(2rem, 6vw, 3.5rem);
  background: var(--color-surface);
  border: 1px dashed rgba(139, 161, 128, 0.4);
  border-radius: var(--radius);
  display: grid;
  gap: 1rem;
  justify-items: start;
}

.empty__text {
  color: var(--color-zinc);
}

@media (min-width: 640px) {
  .cart-row {
    grid-template-columns: 4rem 1fr auto auto auto;
  }

  .cart-row__swatch {
    grid-row: auto;
  }

  .cart-row__remove {
    justify-self: end;
  }
}

@media (min-width: 920px) {
  .cart__layout {
    grid-template-columns: 1fr 300px;
    align-items: start;
    gap: 1.75rem;
  }
}
</style>
