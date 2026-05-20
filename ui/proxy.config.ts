// proxy.config.ts

export default {
  // Добавляем /** чтобы ловить все вложенные параметры
  '/api/**': {
    target: 'https://tcrm.test',
    changeOrigin: true,
    secure: false,
    // Важно для Nitro в Nuxt 4: явно удаляем префикс
    rewrite: (path: string) => path.replace(/^\/api/, '')
  }
}
