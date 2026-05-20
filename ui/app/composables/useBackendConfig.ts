export const useBackendConfig = () => {
  const config = useRuntimeConfig()
  
  const crmBase = config.public.crmUrl?.replace(/\/$/, '') || ''
  let ooBase = config.public.ooUrl?.replace(/\/$/, '') || ''
  
  // РЕГУЛЯРКА: Если ooBase по ошибке содержит в себе crmBase (или любой повтор протокола/домена)
  // Мы очищаем его, оставляя только чистый целевой домен OnlyOffice
  if (crmBase && ooBase.includes(new URL(crmBase).hostname)) {
     // Убираем всё, что до второго протокола или домена OnlyOffice
     ooBase = ooBase.replace(/^https?:\/\/[^\/]+/, '').replace(/^\//, '')
     // Если после чистки остался только путь, восстанавливаем протокол из конфига
     if (!ooBase.startsWith('http')) {
        ooBase = config.public.ooUrl?.replace(/\/$/, '') || ''
     }
  }

  // Финальный безопасный сборщик
  const fullOoPath = `${ooBase}/web-apps/apps/api/documents/api.js`.replace(/([^:]\/)\/+/g, "$1")

  return {
    apiBase: crmBase,
    uploadsBase: `${crmBase}/uploads`,
    ooApiJs: fullOoPath,
    ooCallbackUrl: `${crmBase}/api/onlyoffice/callback`,
    crmDomain: crmBase ? new URL(crmBase).hostname : '',
    ooDomain: ooBase ? (ooBase.startsWith('http') ? new URL(ooBase).hostname : ooBase) : ''
  }
}
