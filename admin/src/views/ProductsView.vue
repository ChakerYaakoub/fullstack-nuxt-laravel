<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { api } from '../auth'
import AdminShell from '../components/AdminShell.vue'

interface Category {
  id: number
  slug: string
  name: string
}

interface Product {
  id: string
  categoryId: number
  slug: string
  name: string
  category: string
  categoryName: string
  price: number
  shortDescription: string
  description: string
  material: string
  finish: string
  dimensions: string
  featured: boolean
  imageTone: string
}

const emptyForm = () => ({
  name: '',
  categoryId: '' as number | '',
  slug: '',
  price: 0,
  shortDescription: '',
  description: '',
  material: '',
  finish: '',
  dimensions: '',
  featured: false,
  imageTone: '#2c3538',
})

const products = ref<Product[]>([])
const categories = ref<Category[]>([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const editingId = ref<string | null>(null)
const showForm = ref(false)
const form = reactive(emptyForm())
/** null = view all */
const categoryFilter = ref<number | null>(null)

const filteredProducts = computed(() => {
  if (categoryFilter.value === null) return products.value
  return products.value.filter((p) => p.categoryId === categoryFilter.value)
})

function countInCategory(categoryId: number) {
  return products.value.filter((p) => p.categoryId === categoryId).length
}

function formatPrice(cents: number) {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
    maximumFractionDigits: 0,
  }).format(cents / 100)
}

async function load() {
  loading.value = true
  error.value = ''
  try {
    const [prodRes, catRes] = await Promise.all([
      api<{ data: Product[] }>('/admin/products'),
      api<{ data: Category[] }>('/categories'),
    ])
    products.value = prodRes.data
    categories.value = catRes.data
    if (!form.categoryId && catRes.data[0]) {
      form.categoryId = catRes.data[0].id
    }
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Failed to load products'
  } finally {
    loading.value = false
  }
}

function openCreate() {
  editingId.value = null
  Object.assign(form, emptyForm())
  if (categories.value[0]) form.categoryId = categories.value[0].id
  showForm.value = true
}

function openEdit(product: Product) {
  editingId.value = product.id
  Object.assign(form, {
    name: product.name,
    categoryId: product.categoryId,
    slug: product.slug,
    price: product.price,
    shortDescription: product.shortDescription,
    description: product.description,
    material: product.material,
    finish: product.finish,
    dimensions: product.dimensions,
    featured: product.featured,
    imageTone: product.imageTone || '#2c3538',
  })
  showForm.value = true
}

function closeForm() {
  showForm.value = false
  editingId.value = null
}

async function saveProduct() {
  if (!form.categoryId) {
    error.value = 'Category is required'
    return
  }

  saving.value = true
  error.value = ''
  const payload = {
    name: form.name,
    categoryId: Number(form.categoryId),
    slug: form.slug || null,
    price: Number(form.price),
    shortDescription: form.shortDescription,
    description: form.description,
    material: form.material,
    finish: form.finish,
    dimensions: form.dimensions,
    featured: form.featured,
    imageTone: form.imageTone,
  }

  try {
    if (editingId.value) {
      await api(`/admin/products/${editingId.value}`, {
        method: 'PUT',
        body: JSON.stringify(payload),
      })
    } else {
      await api('/admin/products', {
        method: 'POST',
        body: JSON.stringify(payload),
      })
    }
    closeForm()
    await load()
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Save failed'
  } finally {
    saving.value = false
  }
}

async function removeProduct(product: Product) {
  if (!confirm(`Delete “${product.name}”?`)) return
  error.value = ''
  try {
    await api(`/admin/products/${product.id}`, { method: 'DELETE' })
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
        <h1>Products</h1>
        <p class="lead">Create, edit, and remove catalog products.</p>
      </div>
      <button type="button" class="btn" @click="openCreate">Add product</button>
    </div>

    <p v-if="error" class="error">{{ error }}</p>
    <p v-if="loading" class="lead">Loading…</p>

    <div v-if="!loading" class="filters">
      <p class="filters__label">Category</p>
      <div class="filters__chips">
        <button
          type="button"
          class="chip"
          :class="{ 'is-active': categoryFilter === null }"
          @click="categoryFilter = null"
        >
          View all
          <span class="chip__count">{{ products.length }}</span>
        </button>
        <button
          v-for="c in categories"
          :key="c.id"
          type="button"
          class="chip"
          :class="{ 'is-active': categoryFilter === c.id }"
          @click="categoryFilter = c.id"
        >
          {{ c.name }}
          <span class="chip__count">{{ countInCategory(c.id) }}</span>
        </button>
      </div>
    </div>

    <section v-if="showForm" class="panel">
      <h2>{{ editingId ? 'Edit product' : 'New product' }}</h2>
      <form class="form" @submit.prevent="saveProduct">
        <label>
          Name
          <input v-model="form.name" required />
        </label>
        <label>
          Category
          <select v-model="form.categoryId" required>
            <option disabled value="">Select…</option>
            <option v-for="c in categories" :key="c.id" :value="c.id">
              {{ c.name }}
            </option>
          </select>
        </label>
        <label>
          Slug (optional)
          <input v-model="form.slug" placeholder="auto from name" />
        </label>
        <label>
          Price (cents)
          <input v-model.number="form.price" type="number" min="0" required />
        </label>
        <label class="full">
          Short description
          <input v-model="form.shortDescription" required maxlength="255" />
        </label>
        <label class="full">
          Description
          <textarea v-model="form.description" rows="4" required />
        </label>
        <label>
          Material
          <input v-model="form.material" required />
        </label>
        <label>
          Finish
          <input v-model="form.finish" required />
        </label>
        <label>
          Dimensions
          <input v-model="form.dimensions" required />
        </label>
        <label>
          Image tone
          <input v-model="form.imageTone" />
        </label>
        <label class="check">
          <input v-model="form.featured" type="checkbox" />
          Featured
        </label>
        <div class="actions full">
          <button type="submit" class="btn" :disabled="saving">
            {{ saving ? 'Saving…' : 'Save' }}
          </button>
          <button type="button" class="btn btn--ghost" @click="closeForm">
            Cancel
          </button>
        </div>
      </form>
    </section>

    <div v-if="!loading" class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Featured</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in filteredProducts" :key="p.id">
            <td>
              <strong>{{ p.name }}</strong>
              <div class="muted">{{ p.slug }}</div>
            </td>
            <td>{{ p.categoryName }}</td>
            <td>{{ formatPrice(p.price) }}</td>
            <td>{{ p.featured ? 'Yes' : 'No' }}</td>
            <td class="row-actions">
              <button type="button" class="link" @click="openEdit(p)">Edit</button>
              <button type="button" class="link danger" @click="removeProduct(p)">
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="!filteredProducts.length">
            <td colspan="5" class="muted">
              {{
                categoryFilter === null
                  ? 'No products yet.'
                  : 'No products in this category.'
              }}
            </td>
          </tr>
        </tbody>
      </table>
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
  margin: 0 0 1rem;
  font-family: Syne, Manrope, sans-serif;
  font-size: 1.15rem;
}

.lead {
  margin: 0;
  color: var(--color-zinc);
}

.error {
  color: var(--color-danger);
  margin: 0 0 1rem;
}

.filters {
  margin-bottom: 1.25rem;
}

.filters__label {
  margin: 0 0 0.5rem;
  font-size: 0.72rem;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--color-zinc);
  font-weight: 700;
}

.filters__chips {
  display: flex;
  flex-wrap: wrap;
  gap: 0.45rem;
}

.chip {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  border: 1px solid rgba(139, 161, 128, 0.28);
  background: #fff;
  color: var(--color-ink);
  padding: 0.45rem 0.75rem;
  border-radius: 2px;
  font: inherit;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
}

.chip:hover {
  border-color: rgba(139, 161, 128, 0.45);
}

.chip.is-active {
  background: var(--color-accent);
  border-color: var(--color-accent);
  color: #fff;
}

.chip__count {
  font-size: 0.75rem;
  font-weight: 700;
  opacity: 0.7;
}

.chip.is-active .chip__count {
  opacity: 0.85;
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

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn--ghost {
  background: transparent;
  color: var(--color-ink);
  border: 1px solid rgba(139, 161, 128, 0.35);
}

.panel {
  background: #fff;
  border: 1px solid rgba(139, 161, 128, 0.2);
  padding: 1.25rem;
  margin-bottom: 1.25rem;
}

.form {
  display: grid;
  gap: 0.85rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

label {
  display: grid;
  gap: 0.35rem;
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--color-zinc);
}

.full {
  grid-column: 1 / -1;
}

.check {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 1.5rem;
}

input,
select,
textarea {
  width: 100%;
  border: 1px solid rgba(139, 161, 128, 0.35);
  border-radius: 2px;
  padding: 0.55rem 0.65rem;
  background: #fff;
  color: var(--color-ink);
}

.actions {
  display: flex;
  gap: 0.6rem;
  margin-top: 0.5rem;
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

.muted {
  color: var(--color-zinc);
  font-size: 0.85rem;
  font-weight: 400;
}

.row-actions {
  white-space: nowrap;
  text-align: right;
}

.link {
  border: 0;
  background: none;
  color: var(--color-ink);
  font: inherit;
  font-weight: 600;
  cursor: pointer;
  padding: 0.2rem 0.35rem;
  text-decoration: underline;
}

.link.danger {
  color: var(--color-danger);
}

@media (max-width: 700px) {
  .form {
    grid-template-columns: 1fr;
  }
}
</style>
