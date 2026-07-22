import type { AsyncDataOptions } from '#app'

/** Always refetch on client navigation so page data stays fresh. */
export function freshAsyncOptions<T>(
  extra: AsyncDataOptions<T> = {},
): AsyncDataOptions<T> {
  return {
    ...extra,
    getCachedData(key, nuxtApp) {
      if (nuxtApp.isHydrating) {
        return nuxtApp.payload.data[key] as T | undefined
      }
      return undefined
    },
  }
}
