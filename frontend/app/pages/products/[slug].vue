<script setup lang="ts">
import { computed, ref, watchEffect } from 'vue'
import { createError, useHead, useRoute, useSeoMeta } from 'nuxt/app'
import { useCart } from '../../composables/useCart'
import { formatPrice } from '../../data/catalog'
import { jsonLdScript } from '../../utils/jsonLd'

const route = useRoute()
const { add, pending: cartPending } = useCart()
const added = ref(false)
const { getProduct, getCategory } = useCatalogApi()
const slug = computed(() => route.params.slug as string)

const {
  data: product,
  pending: productPending,
  error: productError,
} = await useAsyncData(
  () => `ferra-product-${slug.value}`,
  async () => {
    const res = await getProduct(slug.value)
    return res.data
  },
  freshAsyncOptions({ watch: [slug] }),
)

const categorySlug = computed(() => product.value?.category ?? '')

const { data: category, pending: categoryPending } = await useAsyncData(
  () => `ferra-product-cat-${categorySlug.value || 'none'}`,
  async () => {
    if (!categorySlug.value) return null
    const res = await getCategory(categorySlug.value)
    return res.data
  },
  freshAsyncOptions({ watch: [categorySlug] }),
)

const pagePending = computed(
  () => productPending.value || categoryPending.value,
)

watchEffect(() => {
  if (!productPending.value && (productError.value || !product.value)) {
    throw createError({ statusCode: 404, statusMessage: 'Produit introuvable' })
  }
})

useSeoMeta({
  title: () => `${product.value?.name ?? 'Produit'} — FERRA`,
  description: () => product.value?.shortDescription ?? '',
  ogTitle: () => product.value?.name ?? 'Produit',
  ogDescription: () => product.value?.shortDescription ?? '',
  ogType: 'website',
})

useHead({
  script: [
    jsonLdScript(() =>
      product.value
        ? {
            '@context': 'https://schema.org',
            '@type': 'Product',
            name: product.value.name,
            description: product.value.description,
            sku: product.value.id,
            brand: { '@type': 'Brand', name: 'FERRA' },
            category: category.value?.name,
            offers: {
              '@type': 'Offer',
              priceCurrency: 'EUR',
              price: product.value.price,
              availability: 'https://schema.org/InStock',
            },
          }
        : {},
    ),
  ],
})

function addToCart() {
  if (!product.value) return
  void add(product.value).then(() => {
    added.value = true
    setTimeout(() => {
      added.value = false
    }, 2000)
  })
}
</script>

<template>
  <div>
    <AppLoading v-if="pagePending" label="Chargement du produit…" />

    <div v-else-if="product" class="section product-page">
      <div class="container product-page__grid">
        <div class="visual-wrap">
          <div
            class="visual"
            :style="{ background: product.imageTone }"
            aria-hidden="true"
          >
            <div class="visual__pattern" />
            <div class="visual__glow" />
            <p class="visual__label">{{ category?.name }}</p>
          </div>
        </div>

        <div class="info animate-fade-up">
          <nav class="breadcrumb" aria-label="Fil d’Ariane">
            <NuxtLink to="/">Accueil</NuxtLink>
            <span>/</span>
            <NuxtLink :to="`/categories/${product.category}`">
              {{ category?.name }}
            </NuxtLink>
            <span>/</span>
            <span>{{ product.name }}</span>
          </nav>

          <h1 class="info__title">{{ product.name }}</h1>
          <p class="info__price price">{{ formatPrice(product.price) }}</p>
          <p class="info__desc">{{ product.description }}</p>

          <dl class="specs">
            <div>
              <dt>Matériau</dt>
              <dd>{{ product.material }}</dd>
            </div>
            <div>
              <dt>Finition</dt>
              <dd>{{ product.finish }}</dd>
            </div>
            <div>
              <dt>Dimensions</dt>
              <dd>{{ product.dimensions }}</dd>
            </div>
          </dl>

          <div class="actions">
            <button
              class="btn btn--primary"
              type="button"
              :disabled="cartPending"
              @click="addToCart"
            >
              {{
                cartPending
                  ? 'Ajout…'
                  : added
                    ? 'Ajouté au panier ✓'
                    : 'Ajouter au panier'
              }}
            </button>
            <NuxtLink to="/contact" class="btn btn--ghost">Demander un devis</NuxtLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.product-page__grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
  align-items: start;
}

.visual-wrap {
  width: 100%;
  min-width: 0;
}

.visual {
  position: relative;
  width: 100%;
  max-width: 100%;
  aspect-ratio: 4 / 3;
  border-radius: calc(var(--radius) + 4px);
  overflow: hidden;
  box-shadow: var(--shadow-md);
}

.visual__pattern {
  position: absolute;
  inset: 0;
  background:
    linear-gradient(160deg, transparent 30%, rgba(255, 255, 255, 0.08)),
    repeating-linear-gradient(
      0deg,
      transparent,
      transparent 24px,
      rgba(255, 255, 255, 0.04) 24px,
      rgba(255, 255, 255, 0.04) 25px
    );
}

.visual__glow {
  position: absolute;
  inset: auto -15% -30% auto;
  width: 65%;
  height: 65%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.25), transparent 70%);
  pointer-events: none;
}

.visual__label {
  position: absolute;
  left: 1rem;
  bottom: 1rem;
  padding: 0.35rem 0.75rem;
  border-radius: var(--radius-pill);
  background: rgba(0, 0, 0, 0.28);
  backdrop-filter: blur(6px);
  color: rgba(255, 255, 255, 0.92);
  font-size: 0.72rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  font-weight: 600;
}

.info {
  min-width: 0;
  width: 100%;
  position: relative;
  z-index: 1;
}

.info__title {
  font-size: clamp(1.8rem, 4vw, 2.6rem);
  margin-bottom: 0.55rem;
  overflow-wrap: anywhere;
}

.info__price {
  font-size: clamp(1.4rem, 3vw, 1.75rem);
  color: var(--color-accent-deep);
  margin-bottom: 1.2rem;
}

.info__desc {
  color: var(--color-zinc);
  margin-bottom: 1.5rem;
  line-height: 1.7;
}

.specs {
  display: grid;
  gap: 0.85rem;
  margin-bottom: 1.75rem;
  padding: 1.2rem 1.15rem;
  background: var(--color-surface);
  border: 1px solid rgba(139, 161, 128, 0.18);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
}

.specs div {
  display: grid;
  grid-template-columns: 7rem 1fr;
  gap: 0.75rem;
  align-items: baseline;
}

.specs dt {
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--color-zinc);
}

.specs dd {
  margin: 0;
  font-weight: 500;
}

.actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

@media (max-width: 640px) {
  .actions .btn {
    width: 100%;
  }

  .specs div {
    grid-template-columns: 1fr;
    gap: 0.2rem;
  }
}

@media (min-width: 960px) {
  .product-page__grid {
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    column-gap: 3.5rem;
    row-gap: 2rem;
  }

  .visual-wrap {
    position: sticky;
    top: calc(var(--header-h) + 1rem);
  }

  .visual {
    min-height: 0;
  }
}
</style>
