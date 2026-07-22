import type { Product } from './types'

export interface CartItem {
  product: Product
  quantity: number
}

export interface CartResponse {
  items: CartItem[]
  count: number
  total: number
}
