import type { Category, Product } from '../../shared/types'

interface ApiList<T> {
  data: T[]
}

interface ApiOne<T> {
  data: T
}

export function useCatalogApi() {
  const { api } = useApi()

  function getCategories() {
    return api<ApiList<Category>>('/categories')
  }

  function getCategory(slug: string) {
    return api<ApiOne<Category>>(`/categories/${slug}`)
  }

  function getProducts(params?: { category?: string; featured?: boolean }) {
    return api<ApiList<Product>>('/products', { query: params })
  }

  function getProduct(slug: string) {
    return api<ApiOne<Product>>(`/products/${slug}`)
  }

  function sendQuote(body: {
    name: string
    email: string
    phone?: string
    category: string
    message: string
  }) {
    return api<{ data: Record<string, string>; message: string }>('/contact', {
      method: 'POST',
      body,
    })
  }

  return {
    getCategories,
    getCategory,
    getProducts,
    getProduct,
    sendQuote,
  }
}

/** Shared categories list from Laravel (SSR + client). */
export function useCategories() {
  const { getCategories } = useCatalogApi()

  return useAsyncData(
    'ferra-categories',
    async () => {
      const res = await getCategories()
      return res.data
    },
    { default: () => [] },
  )
}
