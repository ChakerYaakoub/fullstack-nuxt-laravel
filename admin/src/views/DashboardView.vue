<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { api, fetchMe, getAdminEmail } from '../auth'
import AdminShell from '../components/AdminShell.vue'

const router = useRouter()
const email = ref(getAdminEmail() ?? '')
const loading = ref(true)
const error = ref('')

const stats = ref([
  { label: 'Products', value: '—' },
  { label: 'Categories', value: '—' },
  { label: 'Devis', value: '—' },
  { label: 'New devis', value: '—' },
])

onMounted(async () => {
  try {
    const user = await fetchMe()
    email.value = user.email

    const res = await api<{
      data: {
        products: number
        categories: number
        quotes: number
        quotesReceived: number
      }
    }>('/admin/dashboard')

    stats.value = [
      { label: 'Products', value: String(res.data.products) },
      { label: 'Categories', value: String(res.data.categories) },
      { label: 'Devis', value: String(res.data.quotes) },
      { label: 'New devis', value: String(res.data.quotesReceived) },
    ]
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Session invalid'
    await router.push('/login')
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <AdminShell>
    <p v-if="loading" class="lead">Checking JWT session…</p>

    <template v-else>
      <h1>Dashboard</h1>
      <p class="lead">
        Signed in as {{ email }}. Manage products and quote requests.
      </p>
      <p v-if="error" class="error">{{ error }}</p>

      <div class="grid">
        <article v-for="stat in stats" :key="stat.label" class="stat">
          <p class="stat__label">{{ stat.label }}</p>
          <p class="stat__value">{{ stat.value }}</p>
        </article>
      </div>

      <div class="cards">
        <RouterLink to="/products" class="card">
          <h2>Products</h2>
          <p>View, create, edit, and delete catalog products.</p>
        </RouterLink>
        <RouterLink to="/quotes" class="card">
          <h2>Demande de devis</h2>
          <p>Review contact / quote requests from the website.</p>
        </RouterLink>
      </div>
    </template>
  </AdminShell>
</template>

<style scoped>
h1 {
  margin: 0 0 0.35rem;
  font-family: Syne, Manrope, sans-serif;
  font-size: 2rem;
}

.lead {
  margin: 0 0 1.5rem;
  color: var(--color-zinc);
}

.error {
  color: var(--color-danger);
}

.grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  margin-bottom: 1.25rem;
}

.stat {
  padding: 1.1rem;
  background: #fff;
  border: 1px solid rgba(139, 161, 128, 0.2);
}

.stat__label {
  margin: 0;
  font-size: 0.75rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--color-zinc);
  font-weight: 600;
}

.stat__value {
  margin: 0.4rem 0 0;
  font-family: Syne, Manrope, sans-serif;
  font-size: 1.8rem;
  font-weight: 700;
}

.cards {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
}

.card {
  display: block;
  padding: 1.25rem;
  background: #fff;
  border: 1px solid rgba(139, 161, 128, 0.2);
  text-decoration: none;
  color: inherit;
}

.card:hover {
  border-color: var(--color-accent);
  background: var(--color-accent-soft);
}

.card h2 {
  margin: 0 0 0.4rem;
  font-size: 1.1rem;
  font-family: Syne, Manrope, sans-serif;
  color: var(--color-accent);
}

.card p {
  margin: 0;
  color: var(--color-zinc);
  line-height: 1.55;
}
</style>
