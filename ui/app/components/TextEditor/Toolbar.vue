<template>
  <div class="toolbar-line">
    <!-- Группа поиска -->
    <div class="search-group">
      <input type="text" placeholder="Поиск по оригиналу..." class="t-input" />
      <div class="nav-arrows">
        <button class="t-btn"><i class="fa fa-chevron-up"></i></button>
        <button class="t-btn"><i class="fa fa-chevron-down"></i></button>
      </div>
    </div>

    <!-- Твои кнопки переключения видов (Точка №3) -->
    <div class="mode-switcher">
      <button @click="$emit('mode', 1)" :class="{active: mode === 1}">Стандарт</button>
      <button @click="$emit('mode', 2)" :class="{active: mode === 2}">Таблица</button>
      <button @click="$emit('mode', 3)" :class="{active: mode === 3}">Оригинал</button>
    </div>

    <!-- Кнопки действий -->
    <div class="action-group">
      <button class="btn-orange">Перевести</button>
      <button class="btn-ready">Готово</button>
    </div>
  </div>
</template>

<style scoped>
.toolbar-line {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 4px 10px;
  background: #fff;
  border-bottom: 1px solid #e2e8f0;
}

.search-group { display: flex; align-items: center; gap: 4px; flex: 1; }
.t-input { width: 100%; max-width: 400px; padding: 4px 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 12px; }

.mode-switcher { display: flex; gap: 2px; background: #f1f5f9; padding: 2px; border-radius: 6px; }
.mode-switcher button { 
  padding: 3px 10px; font-size: 11px; border: none; background: none; cursor: pointer; border-radius: 4px;
}
.mode-switcher button.active { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); color: #2563eb; font-weight: 600; }

.t-btn { background: none; border: 1px solid #e2e8f0; padding: 2px 6px; border-radius: 4px; cursor: pointer; color: #64748b; }
.btn-orange { background: #f97316; color: #fff; border: none; padding: 4px 12px; border-radius: 4px; font-size: 11px; }
.btn-ready { background: #2563eb; color: #fff; border: none; padding: 4px 12px; border-radius: 4px; font-size: 11px; }
</style>
