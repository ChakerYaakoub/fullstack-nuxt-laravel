// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: "2025-07-15",
  devtools: { enabled: true },

  // Full SSR on every request — no static prerender
  ssr: true,

  routeRules: {
    "/**": { ssr: true, prerender: false },
  },

  nitro: {
    prerender: {
      crawlLinks: false,
      routes: [],
    },
  },

  runtimeConfig: {
    public: {
      // Laravel API (SQLite). Use `/api` to fall back to Nuxt stub.
      apiBase: "http://localhost:8000/api",
    },
  },

  modules: ["@nuxtjs/sitemap"],

  css: ["~/assets/css/main.css"],

  app: {
    head: {
      htmlAttrs: { lang: "fr" },
      title: "FERRA — Portails, clôtures & structures",
      meta: [
        {
          name: "description",
          content:
            "Portails, clôtures, garde-corps, pergolas et carports sur mesure. Qualité artisanale, livraison France.",
        },
        { name: "theme-color", content: "#1B1F24" },
        { property: "og:site_name", content: "FERRA" },
        { property: "og:type", content: "website" },
        { property: "og:locale", content: "fr_FR" },
      ],
      link: [
        { rel: "preconnect", href: "https://fonts.googleapis.com" },
        {
          rel: "preconnect",
          href: "https://fonts.gstatic.com",
          crossorigin: "",
        },
        {
          rel: "stylesheet",
          href: "https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap",
        },
      ],
    },
  },

  site: {
    url: "https://ferra.example",
    name: "FERRA",
  },

  sitemap: {
    defaults: {
      changefreq: "weekly",
      priority: 0.8,
    },
  },
});
