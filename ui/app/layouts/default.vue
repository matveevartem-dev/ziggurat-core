<template>
  <div>
    <header>
      <nav></nav>
    </header>

    <main>
      <slot /> 
    </main>

    <footer></footer>
  </div>
</template>

<script lang="ts" setup>
</script>

<style>
@import url('https://googleapis.com');

body, .app-root {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  font-size: 13px; /* Оптимально для рабочих интерфейсов */
  color: #1e293b;
  -webkit-font-smoothing: antialiased;
}
.icon-clip {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-family: "Segoe UI Symbol", "Apple Color Emoji", sans-serif;
  font-size: 14px;
  color: #64748b; /* Спокойный серый */
  cursor: pointer;
  transition: color 0.2s;
  user-select: none;
}

.icon-clip:hover {
  color: #2563eb; /* Синий при наведении */
  filter: grayscale(1); /* Если это Эмодзи-скрепка, делает её серой */
}
</style>