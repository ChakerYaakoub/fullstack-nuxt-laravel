import { computed } from 'vue'
import { useState } from 'nuxt/app'
import type { Product } from '../../shared/types'
import type { CartItem, CartResponse } from '../../shared/cart'

export type { CartItem, CartResponse }

function emptyCart(): CartResponse {
  return { items: [], count: 0, total: 0 }
}

function cloneCart(cart: CartResponse): CartResponse {
  return {
    count: cart.count,
    total: cart.total,
    items: cart.items.map((item) => ({
      product: { ...item.product },
      quantity: item.quantity,
    })),
  }
}

function summarize(items: CartItem[]): Pick<CartResponse, 'count' | 'total'> {
  return {
    count: items.reduce((sum, item) => sum + item.quantity, 0),
    total: items.reduce(
      (sum, item) => sum + item.product.price * item.quantity,
      0,
    ),
  }
}

export function useCart() {
  const { api } = useApi()
  const items = useState<CartItem[]>('cart-items', () => [])
  const count = useState('cart-count', () => 0)
  const total = useState('cart-total', () => 0)
  const pending = useState('cart-pending', () => false)
  const loaded = useState('cart-loaded', () => false)
  const revision = useState('cart-revision', () => 0)
  const inflight = useState('cart-inflight', () => 0)

  const countComputed = computed(() => count.value)
  const totalComputed = computed(() => total.value)

  function snapshot(): CartResponse {
    return cloneCart({
      items: items.value,
      count: count.value,
      total: total.value,
    })
  }

  function apply(cart: CartResponse) {
    items.value = cart.items
    count.value = cart.count
    total.value = cart.total
    loaded.value = true
  }

  function applyIfCurrent(rev: number, cart: CartResponse) {
    if (rev !== revision.value) return false
    apply(cart)
    return true
  }

  function optimisticAdd(product: Product, quantity: number) {
    const next = cloneCart(snapshot())
    const existing = next.items.find((i) => i.product.id === product.id)
    if (existing) {
      existing.quantity += quantity
    } else {
      next.items.push({ product: { ...product }, quantity })
    }
    const sums = summarize(next.items)
    next.count = sums.count
    next.total = sums.total
    apply(next)
  }

  function optimisticSetQuantity(productId: string, quantity: number) {
    const next = cloneCart(snapshot())
    if (quantity <= 0) {
      next.items = next.items.filter((i) => i.product.id !== productId)
    } else {
      const item = next.items.find((i) => i.product.id === productId)
      if (!item) return
      item.quantity = quantity
    }
    const sums = summarize(next.items)
    next.count = sums.count
    next.total = sums.total
    apply(next)
  }

  function optimisticRemove(productId: string) {
    optimisticSetQuantity(productId, 0)
  }

  async function refresh() {
    // Never overwrite UI while a mutation is in flight
    if (inflight.value > 0) return snapshot()

    const rev = revision.value
    const cart = await api<CartResponse>('/cart')

    if (inflight.value > 0 || rev !== revision.value) {
      return cart
    }

    apply(cart)
    return cart
  }

  async function add(product: Product, quantity = 1) {
    const rev = ++revision.value
    const previous = snapshot()
    inflight.value += 1
    pending.value = true
    optimisticAdd(product, quantity)

    try {
      const cart = await api<CartResponse>('/cart', {
        method: 'POST',
        body: { productId: product.id, quantity },
      })
      applyIfCurrent(rev, cart)
      return cart
    } catch (error) {
      applyIfCurrent(rev, previous)
      throw error
    } finally {
      inflight.value = Math.max(0, inflight.value - 1)
      if (inflight.value === 0) pending.value = false
    }
  }

  async function remove(productId: string) {
    const rev = ++revision.value
    const previous = snapshot()
    inflight.value += 1
    pending.value = true
    optimisticRemove(productId)

    try {
      const cart = await api<CartResponse>(`/cart/${productId}`, {
        method: 'DELETE',
      })
      applyIfCurrent(rev, cart)
      return cart
    } catch (error) {
      applyIfCurrent(rev, previous)
      throw error
    } finally {
      inflight.value = Math.max(0, inflight.value - 1)
      if (inflight.value === 0) pending.value = false
    }
  }

  async function setQuantity(productId: string, quantity: number) {
    const rev = ++revision.value
    const previous = snapshot()
    inflight.value += 1
    pending.value = true
    optimisticSetQuantity(productId, quantity)

    try {
      const cart = await api<CartResponse>(`/cart/${productId}`, {
        method: 'PATCH',
        body: { quantity },
      })
      applyIfCurrent(rev, cart)
      return cart
    } catch (error) {
      applyIfCurrent(rev, previous)
      throw error
    } finally {
      inflight.value = Math.max(0, inflight.value - 1)
      if (inflight.value === 0) pending.value = false
    }
  }

  async function clear() {
    const rev = ++revision.value
    const previous = snapshot()
    inflight.value += 1
    pending.value = true
    apply(emptyCart())

    try {
      const cart = await api<CartResponse>('/cart', { method: 'DELETE' })
      applyIfCurrent(rev, cart)
      return cart
    } catch (error) {
      applyIfCurrent(rev, previous)
      throw error
    } finally {
      inflight.value = Math.max(0, inflight.value - 1)
      if (inflight.value === 0) pending.value = false
    }
  }

  return {
    items,
    count: countComputed,
    total: totalComputed,
    pending,
    loaded,
    refresh,
    add,
    remove,
    setQuantity,
    clear,
  }
}
