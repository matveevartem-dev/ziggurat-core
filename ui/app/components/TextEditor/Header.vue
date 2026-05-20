<template>
  <div class="grid-table-layout header-container">
    <div class="h-cell center">№</div>
    <div class="h-cell">ОРИГИНАЛ</div>
    <div class="h-cell"></div>
    <div class="h-cell">ПЕРЕВОД</div>
    <div class="h-cell center">СТАТУС</div>
  </div>
</template>

<style scoped>
/* [BLOCKCODE] (type: style) */
.header-container {
  width: 100% !important; /* Только внутри своей колонки! */
  height: 36px;
  background: #f1f5f9;
  border-bottom: 2px solid #cbd5e1;
  /* position: relative; — НЕ fixed! */
}
.h-cell {
  padding: 0 12px;
  font-size: 11px;
  font-weight: 800;
  color: #1e293b; /* Глубокий темный цвет */
  text-transform: uppercase;
  display: flex;
  align-items: center;
}
.h-cell.center { justify-content: center; }
</style>
