<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { api } from '../auth'
import AdminShell from '../components/AdminShell.vue'

interface Quote {
  id: string
  name: string
  email: string
  phone: string | null
  categorySlug: string
  categoryName: string
  message: string
  status: 'received' | 'in_progress' | 'done' | 'rejected'
  createdAt: string | null
}

const statuses: Quote['status'][] = ['received', 'in_progress', 'done', 'rejected']

const quotes = ref<Quote[]>([])
const loading = ref(true)
const error = ref('')
const selected = ref<Quote | null>(null)
const updating = ref(false)

function statusLabel(status: Quote['status']) {
  const map: Record<Quote['status'], string> = {
    received: 'Received',
    in_progress: 'In progress',
    done: 'Done',
    rejected: 'Rejected',
  }
  return map[status]
}

function formatDate(iso: string | null) {
  if (!iso) return '—'
  return new Intl.DateTimeFormat('fr-FR', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(new Date(iso))
}

async function load() {
  loading.value = true
  error.value = ''
  try {
    const res = await api<{ data: Quote[] }>('/admin/quotes')
    quotes.value = res.data
    if (selected.value) {
      selected.value = res.data.find((q) => q.id === selected.value?.id) ?? null
    }
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Failed to load quotes'
  } finally {
    loading.value = false
  }
}

async function updateStatus(quote: Quote, status: Quote['status']) {
  if (quote.status === status) return
  updating.value = true
  error.value = ''
  try {
    const res = await api<{ data: Quote }>(`/admin/quotes/${quote.id}`, {
      method: 'PATCH',
      body: JSON.stringify({ status }),
    })
    const idx = quotes.value.findIndex((q) => q.id === quote.id)
    if (idx >= 0) quotes.value[idx] = res.data
    if (selected.value?.id === quote.id) selected.value = res.data
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Update failed'
  } finally {
    updating.value = false
  }
}

async function removeQuote(quote: Quote) {
  if (!confirm(`Delete request from ${quote.name}?`)) return
  error.value = ''
  try {
    await api(`/admin/quotes/${quote.id}`, { method: 'DELETE' })
    if (selected.value?.id === quote.id) selected.value = null
    await load()
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Delete failed'
  }
}

onMounted(load)
</script>

<template>
  <AdminShell>
    <div class="head">
      <div>
        <h1>Demande de devis</h1>
        <p class="lead">Contact / quote requests from the storefront form.</p>
      </div>
      <button type="button" class="btn btn--ghost" :disabled="loading" @click="load">
        Refresh
      </button>
    </div>

    <p v-if="error" class="error">{{ error }}</p>
    <p v-if="loading" class="lead">Loading…</p>

    <div v-else class="layout">
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Contact</th>
              <th>Category</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="q in quotes"
              :key="q.id"
              :class="{ 'is-selected': selected?.id === q.id }"
              @click="selected = q"
            >
              <td>{{ formatDate(q.createdAt) }}</td>
              <td>
                <strong>{{ q.name }}</strong>
                <div class="muted">{{ q.email }}</div>
              </td>
              <td>{{ q.categoryName }}</td>
              <td>
                <span class="badge" :data-status="q.status">
                  {{ statusLabel(q.status) }}
                </span>
              </td>
            </tr>
            <tr v-if="!quotes.length">
              <td colspan="4" class="muted">No quote requests yet.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <aside v-if="selected" class="detail">
        <h2>{{ selected.name }}</h2>
        <p class="muted">{{ formatDate(selected.createdAt) }}</p>

        <dl>
          <div>
            <dt>Email</dt>
            <dd>
              <a :href="`mailto:${selected.email}`">{{ selected.email }}</a>
            </dd>
          </div>
          <div>
            <dt>Phone</dt>
            <dd>{{ selected.phone || '—' }}</dd>
          </div>
          <div>
            <dt>Category</dt>
            <dd>{{ selected.categoryName }}</dd>
          </div>
          <div>
            <dt>Message</dt>
            <dd class="message">{{ selected.message }}</dd>
          </div>
        </dl>

        <label>
          Status
          <select
            :value="selected.status"
            :disabled="updating"
            @change="updateStatus(selected, ($event.target as HTMLSelectElement).value as Quote['status'])"
          >
            <option v-for="s in statuses" :key="s" :value="s">
              {{ statusLabel(s) }}
            </option>
          </select>
        </label>

        <button type="button" class="btn danger" @click="removeQuote(selected)">
          Delete
        </button>
      </aside>
    </div>
  </AdminShell>
</template>

<style scoped>
.head {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.25rem;
}

h1 {
  margin: 0 0 0.35rem;
  font-family: Syne, Manrope, sans-serif;
  font-size: 2rem;
}

h2 {
  margin: 0 0 0.25rem;
  font-family: Syne, Manrope, sans-serif;
  font-size: 1.25rem;
}

.lead {
  margin: 0;
  color: var(--color-zinc);
}

.error {
  color: var(--color-danger);
  margin: 0 0 1rem;
}

.btn {
  border: 0;
  background: var(--color-accent);
  color: #fff;
  padding: 0.65rem 1.1rem;
  border-radius: var(--radius-pill);
  cursor: pointer;
  font-weight: 600;
}

.btn--ghost {
  background: transparent;
  color: var(--color-ink);
  border: 1px solid rgba(139, 161, 128, 0.35);
}

.btn.danger {
  background: var(--color-danger);
  margin-top: 1rem;
}

.layout {
  display: grid;
  gap: 1rem;
  grid-template-columns: minmax(0, 1.4fr) minmax(260px, 0.8fr);
  align-items: start;
}

.table-wrap {
  overflow-x: auto;
  background: #fff;
  border: 1px solid rgba(139, 161, 128, 0.2);
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
}

th,
td {
  text-align: left;
  padding: 0.85rem 1rem;
  border-bottom: 1px solid rgba(139, 161, 128, 0.12);
  vertical-align: top;
}

th {
  font-size: 0.72rem;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--color-zinc);
}

tbody tr {
  cursor: pointer;
}

tbody tr:hover,
tbody tr.is-selected {
  background: var(--color-accent-soft);
}

.muted {
  color: var(--color-zinc);
  font-size: 0.85rem;
  font-weight: 400;
}

.badge {
  display: inline-block;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  padding: 0.25rem 0.45rem;
  background: var(--color-mist);
}

.badge[data-status='received'] {
  background: var(--color-accent-soft);
  color: #4d6148;
}

.badge[data-status='in_progress'] {
  background: #fef3c7;
  color: #92400e;
}

.badge[data-status='done'] {
  background: #d1fae5;
  color: #065f46;
}

.badge[data-status='rejected'] {
  background: #fee2e2;
  color: #991b1b;
}

.detail {
  background: #fff;
  border: 1px solid rgba(139, 161, 128, 0.2);
  padding: 1.25rem;
}

dl {
  margin: 1rem 0;
  display: grid;
  gap: 0.85rem;
}

dt {
  font-size: 0.72rem;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--color-zinc);
  font-weight: 700;
}

dd {
  margin: 0.2rem 0 0;
}

.message {
  white-space: pre-wrap;
  line-height: 1.55;
}

label {
  display: grid;
  gap: 0.35rem;
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--color-zinc);
}

select {
  width: 100%;
  border: 1px solid rgba(139, 161, 128, 0.35);
  border-radius: 2px;
  padding: 0.55rem 0.65rem;
  background: #fff;
}

a {
  color: var(--color-ink);
}

@media (max-width: 900px) {
  .layout {
    grid-template-columns: 1fr;
  }
}
</style>
