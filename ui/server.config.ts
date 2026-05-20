// front/server.config.ts
const backendUrl = process.env.NUXT_PUBLIC_CRM_URL || 'https://perevodpravo.ru';
const backendHost = new URL(backendUrl).host;

export const proxyConfig = {
  '/api': {
    target: backendUrl, 
    changeOrigin: true,
    secure: false, // Оставляем false для самоподписанных сертификатов
    pathRewrite: { '^/api': '' },
    onProxyReq: (proxyReq: any) => {
      // Подставляем хост динамически!
      proxyReq.setHeader('Host', backendHost);
    }
  }
}
