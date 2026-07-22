import { computed } from 'vue'

/** JSON-LD script tag helper — Unhead types omit application/ld+json. */
export function jsonLdScript(
  data: Record<string, unknown> | (() => Record<string, unknown>),
) {
  return {
    type: 'application/ld+json',
    children:
      typeof data === 'function'
        ? computed(() => JSON.stringify(data()))
        : JSON.stringify(data),
  } as never
}
