<script setup lang="ts">
// [BLOCKCODE] (type: script)
const props = defineProps({
  currentPage: { type: Number, default: 1 },
  totalSegments: { type: Number, default: 0 }
})
</script>

<template>
  <!-- [BLOCKCODE] (type: template) -->
  <footer class="f-container">
  </footer>
  <footer class="ui-footer">
    <div v-if="!isFinalReview" class="footer-inner">
      <!-- Твоя пагинация слева -->
      <div class="pagination">
        <div class="f-left">
          <span class="f-info">Стр: <b>{{ currentPage }}</b></span>
        </div>
        <div class="f-right">
          <span>Сегментов: <b>{{ totalSegments }}</b></span>
          <div class="f-progress-min"><div class="f-bar-min" style="width: 20%"></div></div>
        </div>
      </div>

      <!-- Твои проценты справа (как на скрине) -->
      <div class="stats" v-if="apiResponse?.data?.stats">
        Переведено сегментов: 
        <b>{{ apiResponse.data.stats.verified }}</b> из <b>{{ apiResponse.data.stats.total }}</b>
        <span class="percentage">[{{ Math.round((apiResponse.data.stats.verified / apiResponse.data.stats.total) * 100) }}%]</span>
      </div>
    </div>
  </footer>
</template>

<style scoped>
/* [BLOCKCODE] (type: style) */
.f-container {
  /* ЖЕСТКИЕ ПРАВИЛА: */
  position: relative !important; /* УБИРАЕМ FIXED, если был */
  width: 100% !important; 
  max-width: 100%;
  height: 32px;
  background: #f8fafc;
  border-top: 1px solid #cbd5e1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 15px;
  font-size: 11px;
  box-sizing: border-box; /* Важно для ширины */
}
.f-progress-min { width: 60px; height: 4px; background: #e2e8f0; border-radius: 2px; overflow: hidden; margin-left: 10px; }
.f-bar-min { height: 100%; background: #10b981; }
.f-right { display: flex; align-items: center; }
.footer-inner { width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 0 20px; }
.stats { font-size: 11px; color: #475569; }
.percentage { color: #16a34a; font-weight: bold; margin-left: 5px; }
</style>
