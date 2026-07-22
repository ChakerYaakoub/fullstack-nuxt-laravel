export type CategorySlug = string

export interface Category {
  slug: string
  name: string
  short: string
  description: string
  accent: string
}

export interface Product {
  id: string
  slug: string
  name: string
  category: string
  price: number
  shortDescription: string
  description: string
  material: string
  finish: string
  dimensions: string
  featured?: boolean
  imageTone: string
}

export function formatPrice(value: number) {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
    maximumFractionDigits: 0,
  }).format(value)
}
