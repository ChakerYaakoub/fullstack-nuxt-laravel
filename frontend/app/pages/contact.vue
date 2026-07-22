<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useSeoMeta } from 'nuxt/app'

useSeoMeta({
  title: 'Contact & devis — FERRA',
  description:
    'Demandez un devis FERRA pour portails, clôtures, garde-corps, pergolas ou carports.',
})

const { sendQuote } = useCatalogApi()
const { data: categories, pending: categoriesPending } = await useCategories()

const form = reactive({
  name: '',
  email: '',
  phone: '',
  category: '',
  message: '',
})

const sent = ref(false)
const error = ref('')
const submitting = ref(false)

async function submit() {
  error.value = ''
  submitting.value = true
  try {
    await sendQuote({ ...form })
    sent.value = true
  } catch (e: unknown) {
    error.value = 'Envoi impossible — réessayez.'
    console.error(e)
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <section class="section contact">
    <div class="container contact__grid">
      <div class="intro animate-fade-up">
        <p class="section__eyebrow">Contact</p>
        <h1 class="intro__title">Demande de devis</h1>
        <p class="intro__lead">
          Décrivez votre projet — dimensions, accès, motorisation. On revient vers vous sous 48 h.
        </p>
        <ul class="intro__facts">
          <li>Showroom & atelier — région Centre</li>
          <li>Devis gratuit sous 48 h</li>
          <li>Pose possible via partenaires</li>
        </ul>
      </div>

      <AppLoading v-if="categoriesPending" label="Chargement des catégories…" />

      <form
        v-else-if="!sent"
        class="form animate-fade-up delay-1"
        @submit.prevent="submit"
      >
        <div class="field">
          <label for="name">Nom</label>
          <input id="name" v-model="form.name" type="text" required autocomplete="name">
        </div>
        <div class="field">
          <label for="email">Email</label>
          <input id="email" v-model="form.email" type="email" required autocomplete="email">
        </div>
        <div class="field">
          <label for="phone">Téléphone</label>
          <input id="phone" v-model="form.phone" type="tel" autocomplete="tel">
        </div>
        <div class="field">
          <label for="category">Catégorie</label>
          <select id="category" v-model="form.category" required>
            <option disabled value="">Choisir…</option>
            <option v-for="cat in categories" :key="cat.slug" :value="cat.slug">
              {{ cat.name }}
            </option>
          </select>
        </div>
        <div class="field field--full">
          <label for="message">Votre projet</label>
          <textarea
            id="message"
            v-model="form.message"
            rows="5"
            required
            placeholder="Dimensions, style, contraintes d’accès…"
          />
        </div>
        <p v-if="error" class="form__error field--full">{{ error }}</p>
        <button class="btn btn--primary field--full" type="submit" :disabled="submitting">
          {{ submitting ? 'Envoi…' : 'Envoyer la demande' }}
        </button>
      </form>

      <div v-else class="thanks animate-fade-up">
        <h2>Merci {{ form.name }}</h2>
        <p>
          Votre demande pour
          <strong>{{ categories.find((c) => c.slug === form.category)?.name }}</strong>
          a bien été reçue.
        </p>
        <NuxtLink to="/" class="btn btn--primary">Retour à l’accueil</NuxtLink>
      </div>
    </div>
  </section>
</template>

<style scoped>
.contact__grid {
  display: grid;
  gap: clamp(1.75rem, 4vw, 3rem);
}

.intro__title {
  font-size: clamp(2rem, 4.5vw, 2.9rem);
  margin-bottom: 0.85rem;
}

.intro__lead {
  color: var(--color-zinc);
  max-width: 30rem;
  margin-bottom: 1.6rem;
  font-size: 1.05rem;
  line-height: 1.65;
}

.intro__facts {
  display: grid;
  gap: 0.7rem;
  padding: 1.15rem 1.2rem;
  background: var(--color-surface);
  border: 1px solid rgba(139, 161, 128, 0.18);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
}

.intro__facts li {
  padding-left: 1.1rem;
  position: relative;
  font-size: 0.95rem;
  color: var(--color-ink-soft);
}

.intro__facts li::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0.55rem;
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--color-accent);
}

.form {
  display: grid;
  gap: 1rem;
  padding: clamp(1.25rem, 3vw, 1.75rem);
  background: var(--color-surface);
  border: 1px solid rgba(139, 161, 128, 0.22);
  border-radius: calc(var(--radius) + 2px);
  box-shadow: var(--shadow-md);
}

.field {
  display: grid;
  gap: 0.4rem;
}

.field label {
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.04em;
  color: var(--color-ink-soft);
}

.field input,
.field select,
.field textarea {
  font: inherit;
  padding: 0.8rem 0.9rem;
  border: 1px solid rgba(139, 161, 128, 0.32);
  border-radius: var(--radius-sm);
  background: var(--color-paper);
  color: var(--color-ink);
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.field input:focus,
.field select:focus,
.field textarea:focus {
  outline: none;
  border-color: var(--color-accent);
  box-shadow: 0 0 0 3px var(--color-accent-soft);
}

.thanks {
  padding: clamp(1.5rem, 4vw, 2.25rem);
  background:
    linear-gradient(145deg, var(--color-accent-soft), #fff 60%);
  border: 1px solid rgba(139, 161, 128, 0.25);
  border-radius: calc(var(--radius) + 2px);
  display: grid;
  gap: 1rem;
  align-content: start;
  box-shadow: var(--shadow-sm);
}

.thanks h2 {
  font-size: clamp(1.5rem, 3vw, 1.9rem);
}

.thanks p {
  color: var(--color-zinc);
  line-height: 1.6;
}

.form__error {
  color: var(--color-danger);
  font-size: 0.9rem;
  margin: 0;
}

@media (min-width: 860px) {
  .contact__grid {
    grid-template-columns: 0.95fr 1.15fr;
    align-items: start;
  }

  .form {
    grid-template-columns: 1fr 1fr;
  }

  .field--full {
    grid-column: 1 / -1;
  }
}
</style>
