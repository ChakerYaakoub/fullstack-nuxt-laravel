export default defineNuxtPlugin(async () => {
  const { refresh, loaded, pending } = useCart()
  if (loaded.value || pending.value) return
  try {
    await refresh()
  } catch {
    // ignore — empty cart / cold start
  }
})
