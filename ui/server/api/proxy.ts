export default defineEventHandler(async (event) => {
  const query = getQuery(event)
  // Извлекаем ту самую куку (_identity), которую мы видели в дампе
  const cookieHeader = getHeader(event, 'cookie')

  // Направляем запрос на наш PHP-бэкенд (8080 порт)
  // CORE_IP берется из системного окружения контейнера
  // @ts-expect-error
  const targetUrl = `http://${process.env.CORE_IP}:${process.env.API_PORT}`
  console.log('targetUrl: ' + targetUrl)
    try {
    return await $fetch(targetUrl, {
      method: event.method,
      params: query,
      // Если это POST, читаем тело запроса
      body: event.method !== 'GET' ? await readBody(event) : undefined,
      headers: {
        // ПЕРЕДАЕМ КУКИ ОДИН-В-ОДИН
        'Cookie': cookieHeader || '',
        'X-Forwarded-For': getHeader(event, 'x-forwarded-for') || ''
      }
    })
  } catch (e: any) {
    throw createError({ 
      statusCode: e.response?.status || 500, 
      statusMessage: 'Backend Communication Error' 
    })
  }
})
