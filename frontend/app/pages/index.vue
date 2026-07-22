<script setup lang="ts">
import { computed } from 'vue'
import { useHead, useSeoMeta } from 'nuxt/app'
import { jsonLdScript } from '../utils/jsonLd'

const { getProducts } = useCatalogApi()
const { data: categories, pending: categoriesPending } = await useCategories()
const { data: featured, pending: featuredPending } = await useAsyncData(
  'ferra-featured',
  async () => {
    const res = await getProducts({ featured: true })
    return res.data
  },
  freshAsyncOptions({ default: () => [] }),
)

const pagePending = computed(
  () => categoriesPending.value || featuredPending.value,
)

useSeoMeta({
  title: 'FERRA — Portails, clôtures & structures extérieures',
  description:
    'Boutique FERRA : portails, clôtures, garde-corps, pergolas et carports. Qualité artisanale, devis rapide, livraison France.',
  ogTitle: 'FERRA — Structures extérieures',
  ogDescription:
    'Portails, clôtures, garde-corps, pergolas et carports conçus pour durer.',
  ogType: 'website',
  twitterCard: 'summary_large_image',
})

useHead({
  script: [
    jsonLdScript({
      '@context': 'https://schema.org',
      '@type': 'Organization',
      name: 'FERRA',
      url: 'https://ferra.example',
      description:
        'Spécialiste portails, clôtures, garde-corps, pergolas et carports.',
    }),
  ],
})
</script>

<template>
  <div>
    <section class="hero">
      <div class="hero__media" aria-hidden="true">
        <div class="hero__orb hero__orb--a" />
        <div class="hero__orb hero__orb--b" />
        <div class="hero__grain" />
        <div class="hero__lines" />
      </div>

      <div class="container hero__layout">
        <div class="hero__content">
          <p class="hero__brand animate-fade-up">FERRA</p>
          <div class="hero__rule animate-fade-up delay-1" aria-hidden="true" />
          <h1 class="hero__title animate-fade-up delay-1">
            Structures qui cadrent<br />votre extérieur
          </h1>
          <p class="hero__lead animate-fade-up delay-2">
            Portails, clôtures, garde-corps, pergolas et carports — aluminium et acier, prêts à poser.
          </p>
          <div class="hero__cta animate-fade-up delay-3">
            <NuxtLink to="/categories/portails" class="btn btn--primary">
              Voir les portails
            </NuxtLink>
            <NuxtLink to="/contact" class="btn btn--ghost">Demander un devis</NuxtLink>
          </div>
        </div>

        <div class="hero__stage animate-fade-in delay-2" aria-hidden="true">
          <div class="hero__frame">
            <svg class="hero__structure" viewBox="0 0 480 520" fill="none">
              <rect x="48" y="40" width="384" height="440" rx="4" stroke="rgba(255,255,255,0.55)" stroke-width="3" />
              <rect x="72" y="68" width="152" height="384" rx="2" stroke="rgba(255,255,255,0.35)" stroke-width="2" />
              <rect x="256" y="68" width="152" height="384" rx="2" stroke="rgba(255,255,255,0.35)" stroke-width="2" />
              <path d="M148 68 V452" stroke="rgba(255,255,255,0.22)" stroke-width="2" />
              <path d="M332 68 V452" stroke="rgba(255,255,255,0.22)" stroke-width="2" />
              <path d="M72 164 H224" stroke="rgba(255,255,255,0.2)" stroke-width="2" />
              <path d="M72 260 H224" stroke="rgba(255,255,255,0.2)" stroke-width="2" />
              <path d="M72 356 H224" stroke="rgba(255,255,255,0.2)" stroke-width="2" />
              <path d="M256 164 H408" stroke="rgba(255,255,255,0.2)" stroke-width="2" />
              <path d="M256 260 H408" stroke="rgba(255,255,255,0.2)" stroke-width="2" />
              <path d="M256 356 H408" stroke="rgba(255,255,255,0.2)" stroke-width="2" />
              <circle cx="214" cy="260" r="7" fill="rgba(255,255,255,0.55)" />
              <circle cx="266" cy="260" r="7" fill="rgba(255,255,255,0.55)" />
              <path d="M40 480 H440" stroke="rgba(255,255,255,0.4)" stroke-width="3" />
              <path d="M88 480 V500" stroke="rgba(255,255,255,0.35)" stroke-width="3" />
              <path d="M392 480 V500" stroke="rgba(255,255,255,0.35)" stroke-width="3" />
            </svg>

            <div
              v-for="(product, i) in (featured ?? []).slice(0, 3)"
              :key="product.id"
              class="hero__swatch"
              :class="`hero__swatch--${i}`"
              :style="{ background: product.imageTone }"
            />
          </div>
          <p class="hero__caption">Portail aluminium · ligne contemporaine</p>
        </div>
      </div>

      <div class="hero__scroll" aria-hidden="true">
        <span />
      </div>
    </section>

    <section class="promises" aria-label="Atouts FERRA">
      <div class="container promises__grid">
        <article class="promise">
          <span class="promise__mark" aria-hidden="true" />
          <h2>Aluminium & acier</h2>
          <p>Matériaux durables, finitions soignées pour l’extérieur.</p>
        </article>
        <article class="promise">
          <span class="promise__mark" aria-hidden="true" />
          <h2>Prêt à poser</h2>
          <p>Kits complets, dimensions claires, montage maîtrisé.</p>
        </article>
        <article class="promise">
          <span class="promise__mark" aria-hidden="true" />
          <h2>Devis sous 48 h</h2>
          <p>Sur-mesure, motorisation, teinte RAL — on vous répond vite.</p>
        </article>
      </div>
    </section>

    <section class="section categories">
      <div class="container">
        <p class="section__eyebrow">Gamme</p>
        <h2 class="section__title">Cinq familles, une même exigence</h2>
        <p class="section__lead">
          Choisissez une catégorie pour explorer les modèles, matériaux et tarifs.
        </p>

        <AppLoading v-if="pagePending" label="Chargement du catalogue…" />

        <ul v-else class="cat-grid">
          <li v-for="(cat, i) in categories" :key="cat.slug">
            <NuxtLink
              :to="`/categories/${cat.slug}`"
              class="cat-card"
              :style="{ '--accent': cat.accent, '--delay': `${i * 0.07}s` }"
            >
              <span class="cat-card__index">0{{ i + 1 }}</span>
              <span class="cat-card__name">{{ cat.name }}</span>
              <span class="cat-card__short">{{ cat.short }}</span>
              <span class="cat-card__go">Découvrir →</span>
            </NuxtLink>
          </li>
        </ul>
      </div>
    </section>

    <section class="section featured">
      <div class="container">
        <div class="featured__head">
          <div>
            <p class="section__eyebrow">Sélection</p>
            <h2 class="section__title">Pièces fortes du catalogue</h2>
            <p class="section__lead">
              Une sélection courte pour démarrer — panier et fiche produit inclus.
            </p>
          </div>
        </div>

        <AppLoading v-if="pagePending" label="Chargement des produits…" />

        <div v-else class="product-grid">
          <ProductCard
            v-for="product in featured"
            :key="product.id"
            :product="product"
          />
        </div>
      </div>
    </section>

    <section class="section process">
      <div class="container">
        <p class="section__eyebrow">Parcours</p>
        <h2 class="section__title">Du choix à la pose</h2>
        <ol class="steps">
          <li class="step">
            <span class="step__num">01</span>
            <h3>Choisissez</h3>
            <p>Parcourez les 5 familles et sélectionnez vos modèles.</p>
          </li>
          <li class="step">
            <span class="step__num">02</span>
            <h3>Composez</h3>
            <p>Ajoutez au panier, ajustez les quantités en un clic.</p>
          </li>
          <li class="step">
            <span class="step__num">03</span>
            <h3>Demandez</h3>
            <p>Envoyez votre devis — réponse sous 48 h ouvrées.</p>
          </li>
        </ol>
      </div>
    </section>

    <section class="section strip">
      <div class="container">
        <div class="strip__inner">
          <div>
            <h2 class="strip__title">Besoin d’un sur-mesure ?</h2>
            <p class="strip__text">
              Dimensions hors standard, motorisation, teinte RAL — on vous répond sous 48 h.
            </p>
          </div>
          <NuxtLink to="/contact" class="btn btn--light">Parler à un conseiller</NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.hero {
  position: relative;
  min-height: min(94vh, 880px);
  display: grid;
  align-items: center;
  overflow: hidden;
  color: #fff;
  padding-block: clamp(3.5rem, 8vh, 5.5rem) clamp(5rem, 11vh, 7rem);
  padding-inline: 0;
}

.hero__media {
  position: absolute;
  inset: 0;
  background:
    linear-gradient(
      105deg,
      rgba(36, 44, 34, 0.55) 0%,
      rgba(40, 48, 38, 0.25) 48%,
      rgba(36, 44, 34, 0.35) 100%
    ),
    linear-gradient(128deg, #44563e 0%, #6d8464 42%, #8ba180 78%, #9aaf90 100%);
}

.hero__orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(40px);
  opacity: 0.42;
  animation: float-soft 8s ease-in-out infinite;
}

.hero__orb--a {
  width: min(55vw, 420px);
  height: min(55vw, 420px);
  right: -6%;
  top: 4%;
  background: rgba(255, 255, 255, 0.3);
}

.hero__orb--b {
  width: min(40vw, 280px);
  height: min(40vw, 280px);
  left: 4%;
  bottom: 12%;
  background: rgba(95, 115, 88, 0.55);
  animation-delay: -3s;
}

.hero__grain {
  position: absolute;
  inset: 0;
  opacity: 0.28;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.5'/%3E%3C/svg%3E");
  mix-blend-mode: overlay;
}

.hero__lines {
  position: absolute;
  inset: 0;
  background:
    repeating-linear-gradient(
      90deg,
      transparent,
      transparent 90px,
      rgba(255, 255, 255, 0.035) 90px,
      rgba(255, 255, 255, 0.035) 91px
    );
}

.hero__layout {
  position: relative;
  z-index: 1;
  display: grid;
  gap: clamp(2rem, 5vw, 3.5rem);
  align-items: center;
  width: 100%;
}

.hero__content {
  max-width: 34rem;
}

.hero__brand {
  font-family: var(--font-display);
  font-weight: 800;
  font-size: clamp(3rem, 10vw, 5.5rem);
  letter-spacing: 0.08em;
  line-height: 0.92;
  margin-bottom: 0.85rem;
  text-shadow: 0 8px 40px rgba(0, 0, 0, 0.18);
}

.hero__rule {
  width: 3.5rem;
  height: 3px;
  background: #fff;
  margin-bottom: 1.35rem;
  transform-origin: left;
  animation: draw-line 0.8s var(--ease) 0.2s both;
}

.hero__title {
  font-size: clamp(1.35rem, 3.4vw, 2.05rem);
  font-weight: 600;
  letter-spacing: -0.015em;
  margin-bottom: 1rem;
  color: rgba(255, 255, 255, 0.95);
}

.hero__lead {
  font-size: clamp(0.98rem, 2.2vw, 1.08rem);
  max-width: 30rem;
  opacity: 0.86;
  margin-bottom: 1.85rem;
  line-height: 1.65;
}

.hero__cta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.hero__cta .btn--ghost {
  border-color: rgba(255, 255, 255, 0.42);
  color: #fff;
  backdrop-filter: blur(6px);
  background: rgba(255, 255, 255, 0.06);
}

.hero__cta .btn--ghost:hover {
  border-color: #fff;
  background: rgba(255, 255, 255, 0.14);
  color: #fff;
}

.hero__stage {
  display: block;
  width: min(100%, 380px);
  justify-self: start;
  padding-inline: 0.5rem;
}

.hero__frame {
  position: relative;
  padding: 1.5rem;
  margin-inline: 0.75rem;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.18);
  backdrop-filter: blur(10px);
  box-shadow: 0 24px 60px rgba(0, 0, 0, 0.22);
}

.hero__structure {
  width: 100%;
  height: auto;
  display: block;
  opacity: 0.95;
}

.hero__swatch {
  position: absolute;
  width: 72px;
  height: 72px;
  border-radius: 14px;
  border: 2px solid rgba(255, 255, 255, 0.55);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.25);
  animation: float-soft 5.5s ease-in-out infinite;
}

.hero__swatch--0 {
  top: 12%;
  right: 0.35rem;
}

.hero__swatch--1 {
  bottom: 22%;
  left: 0.35rem;
  width: 58px;
  height: 58px;
  animation-delay: -1.8s;
}

.hero__swatch--2 {
  bottom: 8%;
  right: 12%;
  width: 48px;
  height: 48px;
  animation-delay: -3.2s;
}

.hero__caption {
  margin-top: 0.9rem;
  text-align: center;
  font-size: 0.82rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  opacity: 0.72;
  font-weight: 600;
}

.hero__scroll {
  position: absolute;
  left: 50%;
  bottom: 1.15rem;
  transform: translateX(-50%);
  z-index: 1;
  width: 22px;
  height: 34px;
  border: 1.5px solid rgba(255, 255, 255, 0.45);
  border-radius: 999px;
  display: grid;
  justify-items: center;
  padding-top: 6px;
}

.hero__scroll span {
  width: 3px;
  height: 7px;
  border-radius: 999px;
  background: #fff;
  animation: float-soft 1.6s ease-in-out infinite;
}

.promises {
  position: relative;
  z-index: 2;
  margin-top: -1rem;
  margin-bottom: var(--space-md);
  padding-inline: 0;
}

.promises__grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: 1fr;
  padding: clamp(1.25rem, 3vw, 1.75rem);
  background: var(--color-surface);
  border: 1px solid rgba(139, 161, 128, 0.22);
  border-radius: calc(var(--radius) + 4px);
  box-shadow: var(--shadow-md);
}

.promise {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 0.4rem 1rem;
  padding: 0.85rem 0.65rem;
}

.promise__mark {
  grid-row: span 2;
  width: 10px;
  height: 10px;
  margin-top: 0.4rem;
  border-radius: 50%;
  background: var(--color-accent);
  box-shadow: 0 0 0 6px var(--color-accent-soft);
}

.promise h2 {
  font-size: 1.05rem;
  font-weight: 700;
}

.promise p {
  color: var(--color-zinc);
  font-size: 0.92rem;
  line-height: 1.5;
}

.steps {
  margin-top: var(--space-lg);
  display: grid;
  gap: 1rem;
  counter-reset: none;
}

.step {
  padding: 1.35rem 1.25rem;
  background: var(--color-surface);
  border: 1px solid rgba(139, 161, 128, 0.18);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
}

.step__num {
  display: block;
  font-family: var(--font-display);
  font-weight: 800;
  font-size: 0.85rem;
  letter-spacing: 0.08em;
  color: var(--color-accent);
  margin-bottom: 0.55rem;
}

.step h3 {
  font-size: 1.2rem;
  margin-bottom: 0.4rem;
}

.step p {
  color: var(--color-zinc);
  font-size: 0.95rem;
  line-height: 1.55;
}

.cat-grid {
  margin-top: var(--space-lg);
  display: grid;
  gap: 1rem;
  grid-template-columns: 1fr;
}

.cat-card {
  display: grid;
  gap: 0.45rem;
  padding: 1.35rem 1.25rem;
  background: var(--color-surface);
  border: 1px solid rgba(139, 161, 128, 0.18);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
  animation: fade-up 0.6s var(--ease) both;
  animation-delay: var(--delay);
  transition:
    transform 0.3s var(--ease),
    box-shadow 0.3s var(--ease),
    border-color 0.3s ease;
  position: relative;
  overflow: hidden;
}

.cat-card::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  background: var(--accent, var(--color-accent));
}

.cat-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
  border-color: rgba(139, 161, 128, 0.4);
}

.cat-card__index {
  font-family: var(--font-display);
  font-size: 0.8rem;
  font-weight: 700;
  color: var(--accent, var(--color-accent));
  letter-spacing: 0.06em;
}

.cat-card__name {
  font-family: var(--font-display);
  font-size: clamp(1.25rem, 2.4vw, 1.55rem);
  font-weight: 700;
}

.cat-card__short {
  color: var(--color-zinc);
  font-size: 0.92rem;
  line-height: 1.5;
}

.cat-card__go {
  margin-top: 0.55rem;
  font-size: 0.88rem;
  font-weight: 600;
  color: var(--color-accent-deep);
}

.strip {
  padding-block: 0 0.5rem;
}

.strip__inner {
  display: grid;
  gap: 1.25rem;
  padding: clamp(1.75rem, 4vw, 2.75rem);
  background:
    linear-gradient(135deg, var(--color-accent-deep), var(--color-accent) 55%, #9bb392);
  color: #fff;
  border-radius: calc(var(--radius) + 4px);
  box-shadow: var(--shadow-md);
}

.strip__title {
  font-size: clamp(1.45rem, 3vw, 2rem);
  margin-bottom: 0.5rem;
}

.strip__text {
  max-width: 34rem;
  opacity: 0.9;
  line-height: 1.6;
}

@media (min-width: 640px) {
  .promises__grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    padding: 1.5rem 1.75rem;
  }

  .cat-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
  }

  .steps {
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
  }
}

@media (min-width: 960px) {
  .hero__layout {
    grid-template-columns: 1.05fr 0.95fr;
    gap: 3.5rem;
  }

  .hero__stage {
    width: min(100%, 420px);
    justify-self: end;
  }

  .cat-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 1.35rem;
  }

  .strip__inner {
    grid-template-columns: 1fr auto;
    align-items: center;
    padding: 2.75rem 3rem;
  }
}

@media (max-width: 540px) {
  .hero__cta .btn {
    width: 100%;
  }
}
</style>
