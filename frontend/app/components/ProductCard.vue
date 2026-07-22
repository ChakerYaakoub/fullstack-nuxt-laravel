<script setup lang="ts">
import { computed } from 'vue'
import type { Product } from '../data/catalog'
import { formatPrice } from '../data/catalog'

const props = defineProps<{
  product: Product
}>()

const { data: categories } = useCategories()
const categoryName = computed(
  () =>
    categories.value?.find((c) => c.slug === props.product.category)?.name
    ?? props.product.category,
)
</script>

<template>
  <NuxtLink :to="`/products/${product.slug}`" class="product">
    <div
      class="product__visual"
      :style="{ background: product.imageTone }"
      aria-hidden="true"
    >
      <div class="product__pattern" />
      <div class="product__glow" />
      <span class="product__cat">{{ categoryName }}</span>
    </div>
    <div class="product__body">
      <h3 class="product__name">{{ product.name }}</h3>
      <p class="product__desc">{{ product.shortDescription }}</p>
      <div class="product__foot">
        <p class="product__price price">{{ formatPrice(product.price) }}</p>
        <span class="product__more">Voir →</span>
      </div>
    </div>
  </NuxtLink>
</template>

<style scoped>
.product {
  display: flex;
  flex-direction: column;
  height: 100%;
  background: var(--color-surface);
  border: 1px solid rgba(139, 161, 128, 0.16);
  border-radius: calc(var(--radius) + 2px);
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  transition:
    transform 0.35s var(--ease),
    box-shadow 0.35s var(--ease),
    border-color 0.3s ease;
}

.product:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-md);
  border-color: rgba(139, 161, 128, 0.38);
}

.product__visual {
  position: relative;
  aspect-ratio: 4 / 3;
  overflow: hidden;
}

.product__pattern {
  position: absolute;
  inset: 0;
  background:
    linear-gradient(145deg, transparent 35%, rgba(255, 255, 255, 0.08) 35%, rgba(255, 255, 255, 0.08) 58%, transparent 58%),
    repeating-linear-gradient(
      90deg,
      transparent,
      transparent 18px,
      rgba(255, 255, 255, 0.035) 18px,
      rgba(255, 255, 255, 0.035) 19px
    );
  transition: transform 0.5s var(--ease);
}

.product:hover .product__pattern {
  transform: scale(1.06);
}

.product__glow {
  position: absolute;
  inset: auto -20% -40% auto;
  width: 70%;
  height: 70%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.22), transparent 65%);
}

.product__cat {
  position: absolute;
  left: 0.9rem;
  bottom: 0.9rem;
  padding: 0.3rem 0.65rem;
  border-radius: var(--radius-pill);
  background: rgba(0, 0, 0, 0.28);
  backdrop-filter: blur(6px);
  font-size: 0.68rem;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.92);
}

.product__body {
  display: flex;
  flex-direction: column;
  flex: 1;
  gap: 0.45rem;
  padding: 1.05rem 1.1rem 1.15rem;
}

.product__name {
  font-size: 1.12rem;
  line-height: 1.25;
}

.product__desc {
  font-size: 0.9rem;
  color: var(--color-zinc);
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product__foot {
  margin-top: auto;
  padding-top: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
}

.product__price {
  font-size: 1.08rem;
  color: var(--color-accent-deep);
}

.product__more {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--color-accent);
  opacity: 0;
  transform: translateX(-4px);
  transition: opacity 0.25s ease, transform 0.25s var(--ease);
}

.product:hover .product__more {
  opacity: 1;
  transform: translateX(0);
}
</style>
