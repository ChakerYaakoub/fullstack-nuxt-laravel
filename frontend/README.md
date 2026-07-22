# FERRA — Nuxt frontend

E-commerce demo for outdoor metal structures:

- Portails
- Clôtures
- Garde-corps
- Pergolas
- Carports

## Stack

- Nuxt 4 + Vue 3 + TypeScript
- SEO: `useSeoMeta`, JSON-LD, sitemap (`@nuxtjs/sitemap`), `robots.txt`
- Cart: local state via `useCart` composable (Laravel API later)

## Setup

```bash
cd frontend
npm install
npm run dev
```

Open [http://localhost:3000](http://localhost:3000).

## Pages

| Route | Purpose |
|-------|---------|
| `/` | Home + categories + featured products |
| `/categories/[slug]` | Category listing |
| `/products/[slug]` | Product detail + add to cart |
| `/cart` | Cart |
| `/contact` | Quote form (front-only) |

Mock catalog: `app/data/catalog.ts`.
