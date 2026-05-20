// app/composables/useAuthGuard.ts
export const useAuthGuard = () => {
  return {
    // Тот самый длинный маскированный токен из --data-raw
    csrfToken: "dIVEouDtzqAtWL6TTeCNJixWJIZ9tgWEvedYm2SiwbUAyhPa0L6v1ltg1NUY1fdJXwFy1jHDNdz5kTT5Dfqmgg==",
    
    // Куки для нашего прокси-руткита
    sessionCookies: {
      PHPSESSID: "d0f3cf844a08094259c910095fea72fd",
      identity: "e847290b3454c55e3a58b602c11e512345ba57fa82bd49a6322ba5a5f206a67fa%3A2%3A%7Bi%3A0%3Bs%3A9%3A%22_identity%22%3Bi%3A1%3Bs%3A46%3A%22%5B4%2C%22KR9Nj15oz4mMeDLNcw_ipOvZEmS4hDCj%22%2C2592000%5D%22%3B%7D",
      csrf: "161679e9b911d24837595b2331513c05a12c97f055ceb8a3e6d3856cc29fff77a%3A2%3A%7Bi%3A0%3Bs%3A5%3A%22_csrf%22%3Bi%3A1%3Bs%3A32%3A%22tOWx0Savv8jFU5zosWVPLu0XDvlbiXg7%22%3B%7D"
    }
  }
}
