<script setup lang="ts">
import { computed, watchEffect } from 'vue'
import { createError, useHead, useRoute, useSeoMeta } from 'nuxt/app'
import { formatPrice } from '../../data/catalog'
import { jsonLdScript } from '../../utils/jsonLd'

const route = useRoute()
const slug = computed(() => route.params.slug as string)
const { getCategory, getProducts } = useCatalogApi()

const {
  data: category,
  pending: categoryPending,
  error: categoryError,
} = await useAsyncData(
  () => `ferra-category-${slug.value}`,
  async () => {
    const res = await getCategory(slug.value)
    return res.data
  },
  freshAsyncOptions({ watch: [slug] }),
)

const { data: products, pending: productsPending } = await useAsyncData(
  () => `ferra-products-${slug.value}`,
  async () => {
    const res = await getProducts({ category: slug.value })
    return res.data
  },
  freshAsyncOptions({ default: () => [], watch: [slug] }),
)

const pagePending = computed(
  () => categoryPending.value || productsPending.value,
)

watchEffect(() => {
  if (!pagePending.value && (categoryError.value || !category.value)) {
    throw createError({ statusCode: 404, statusMessage: 'Catégorie introuvable' })
  }
})

useSeoMeta({
  title: () => `${category.value?.name ?? 'Catégorie'} — FERRA`,
  description: () => category.value?.description ?? '',
  ogTitle: () => `${category.value?.name ?? 'Catégorie'} | FERRA`,
  ogDescription: () => category.value?.description ?? '',
})

useHead({
  script: [
    jsonLdScript(() =>
      category.value
        ? {
            '@context': 'https://schema.org',
            '@type': 'CollectionPage',
            name: category.value.name,
            description: category.value.description,
            isPartOf: { '@type': 'WebSite', name: 'FERRA' },
          }
        : {},
    ),
  ],
})

const minPrice = computed(() => {
  const list = products.value ?? []
  if (!list.length) return 0
  return Math.min(...list.map((p) => p.price))
})
</script>

<template>
  <div>
    <AppLoading v-if="pagePending" label="Chargement de la catégorie…" />

    <template v-else-if="category">
      <section
        class="cat-hero"
        :style="{ '--accent': category.accent }"
      >
        <div class="container">
          <nav class="breadcrumb" aria-label="Fil d’Ariane">
            <NuxtLink to="/">Accueil</NuxtLink>
            <span aria-hidden="true">/</span>
            <span>{{ category.name }}</span>
          </nav>
          <p class="section__eyebrow">{{ category.short }}</p>
          <h1 class="cat-hero__title animate-fade-up">{{ category.name }}</h1>
          <p class="cat-hero__desc animate-fade-up delay-1">
            {{ category.description }}
          </p>
        </div>
      </section>

      <section class="section">
        <div class="container">
          <div class="toolbar">
            <p class="count">
              {{ products.length }} modèle{{ products.length > 1 ? 's' : '' }}
            </p>
            <NuxtLink to="/contact" class="btn btn--ghost toolbar__cta">
              Demander un devis
            </NuxtLink>
          </div>

          <div class="product-grid">
            <ProductCard
              v-for="product in products"
              :key="product.id"
              :product="product"
            />
          </div>

          <div v-if="products.length" class="hint">
            <p>
              Prix indicatifs — à partir de
              <strong class="price">{{ formatPrice(minPrice) }}</strong>
            </p>
            <NuxtLink to="/contact" class="btn btn--primary">Obtenir un devis</NuxtLink>
          </div>
        </div>
      </section>
    </template>
  </div>
</template>

<style scoped>
.cat-hero {
  position: relative;
  padding-block: clamp(2rem, 5vw, 3.25rem) clamp(1.75rem, 4vw, 2.5rem);
  overflow: hidden;
  background:
    linear-gradient(115deg, color-mix(in srgb, var(--accent) 16%, #fff), #fff 58%),
    var(--color-paper);
  border-bottom: 1px solid rgba(139, 161, 128, 0.18);
}

.cat-hero::after {
  content: '';
  position: absolute;
  right: -4%;
  top: -20%;
  width: min(42vw, 320px);
  height: min(42vw, 320px);
  border-radius: 50%;
  background: color-mix(in srgb, var(--accent) 22%, transparent);
  filter: blur(8px);
  pointer-events: none;
}

.cat-hero__title {
  font-size: clamp(2.2rem, 6vw, 3.5rem);
  margin-bottom: 0.8rem;
}

.cat-hero__desc {
  max-width: 40rem;
  color: var(--color-zinc);
  font-size: 1.05rem;
  line-height: 1.65;
}

.toolbar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.85rem;
  margin-bottom: 0.25rem;
}

.count {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--color-zinc);
}

.toolbar__cta {
  display: none;
}

.hint {
  margin-top: var(--space-xl);
  padding: 1.35rem 1.4rem;
  border-radius: var(--radius);
  background: var(--color-surface);
  border: 1px solid rgba(139, 161, 128, 0.2);
  box-shadow: var(--shadow-sm);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  color: var(--color-zinc);
}

@media (min-width: 640px) {
  .toolbar__cta {
    display: inline-flex;
  }
}
</style>
