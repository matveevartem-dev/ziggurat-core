// composables/useConnection.ts
export const useConnection = () => {
  const isOnline = ref(true)
  const pendingRequests = ref(0) // Сколько сохранений висит в очереди

  const monitor = (error: any) => {
    // Если поймали FetchError с кодом 502 или отсутствием сети
    if (error.response?.status === 502 || !error.response) {
      isOnline.value = false
    }
  }

  const setOnline = () => { isOnline.value = true }

  return { isOnline, pendingRequests, monitor, setOnline }
}