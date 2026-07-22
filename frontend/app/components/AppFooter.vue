<script setup lang="ts">
const { data: categories } = await useCategories()
</script>

<template>
  <footer class="footer">
    <div class="container footer__grid">
      <div class="footer__brand">
        <p class="footer__logo">FERRA</p>
        <p class="footer__tagline">
          Portails, clôtures, garde-corps, pergolas et carports — conçus pour durer.
        </p>
      </div>

      <div>
        <p class="footer__label">Catégories</p>
        <ul class="footer__links">
          <li v-for="cat in categories" :key="cat.slug">
            <NuxtLink :to="`/categories/${cat.slug}`">{{ cat.name }}</NuxtLink>
          </li>
        </ul>
      </div>

      <div class="footer__contact">
        <p class="footer__label">Nous contacter</p>
        <ul class="footer__links">
          <li><NuxtLink to="/contact">Demande de devis</NuxtLink></li>
          <li><a href="mailto:contact@ferra.example">contact@ferra.example</a></li>
          <li><a href="tel:+33123456789">01 23 45 67 89</a></li>
        </ul>
      </div>
    </div>
    <div class="container footer__bottom">
      <p>© {{ new Date().getFullYear() }} FERRA — Structures extérieures</p>
    </div>
  </footer>
</template>

<style scoped>
.footer {
  margin-top: clamp(3.5rem, 9vw, 6rem);
  padding-top: clamp(2.75rem, 5vw, 4rem);
  background: var(--color-accent);
  color: rgba(255, 255, 255, 0.9);
}

.footer__grid {
  display: grid;
  gap: 2.5rem;
  padding-bottom: 2.5rem;
}

.footer__logo {
  font-family: var(--font-display);
  font-weight: 800;
  font-size: clamp(1.6rem, 3vw, 2rem);
  letter-spacing: 0.08em;
  color: #fff;
  margin-bottom: 0.85rem;
}

.footer__tagline {
  max-width: 22rem;
  font-size: 0.98rem;
  line-height: 1.6;
}

.footer__label {
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: #fff;
  margin-bottom: 0.95rem;
}

.footer__links {
  display: grid;
  gap: 0.5rem;
}

.footer__links a {
  font-size: 0.95rem;
  opacity: 0.92;
  transition: opacity 0.2s ease;
}

.footer__links a:hover {
  opacity: 1;
  text-decoration: underline;
  text-underline-offset: 3px;
}

.footer__bottom {
  padding-block: 1.1rem;
  border-top: 1px solid rgba(255, 255, 255, 0.22);
  font-size: 0.82rem;
  opacity: 0.85;
}

@media (min-width: 720px) {
  .footer__grid {
    grid-template-columns: 1.5fr 1fr 1fr;
    gap: 2.5rem;
  }

  .footer__contact {
    padding-left: 1.5rem;
    border-left: 1px solid rgba(255, 255, 255, 0.28);
  }
}
</style>
