<script setup lang="ts">
/**
 * ГЛАВНЫЙ ТЕРМИНАЛ: Версия v277 (THE FINAL INTEGRATION)
 * Статус: ПОЛНАЯ СБОРКА ВСЕХ СИСТЕМ.
 */
import { ref, computed, watch, nextTick } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const tid = computed(() => Number(route.query.translationId) || 1)

// --- СОСТОЯНИЕ UI ---
const editMode = ref(1)          
const isFinalReview = ref(false) 
const currentSearchText = ref('')
const currentPage = ref(1)
const activeId = ref<number | null>(null)
const originFile = ref<any>(null)
const finalFile = ref<any>(null)
const currentFilename = ref<string | null>(null)

// --- ПОИСК ---
const searchId = ref('')
const searchOrig = ref('')
const searchTrans = ref('')

// --- ДАННЫЕ ---
const localItems = ref<any[]>([])
const currentStats = ref({ verified: 0, total: 0, percent: '0.0' })

const { data: apiResponse, pending } = await useFetch('/api/proxy', {
  key: `editor-sync-v277-${tid.value}`,
  query: { r: 'editor-search', translationId: tid, page: currentPage, q_orig: searchOrig, q_trans: searchTrans, q_id: searchId },
  watch: [currentPage, searchOrig, searchTrans, searchId],
  server: false,
  onResponse({ response }) {
    if (response._data?.data) {
      const d = response._data.data; // Для удобства
      localItems.value = JSON.parse(JSON.stringify(d.items || []))

      originFile.value = d.originFile;
      finalFile.value = d.finalFile;

      const s = d.stats
      if (s) {
        currentStats.value = { verified: s.verified || 0, total: s.total || 0, percent: s.total > 0 ? ((s.verified / s.total) * 100).toFixed(1) : '0.0' }
      }
    }
  }
})

const onUpdateTarget = async (id: number, text: string, origin: string | null = null) => {
  const item = localItems.value.find(i => i.id === id)
  if (!item) return
  
  // 1. ЛОКАЛЬНОЕ ОБНОВЛЕНИЕ (Мгновенная галка в интерфейсе)
  item.target_text = text
  if (origin !== null) item.source_text = origin
  item.is_verified = 1 
  
  // 2. УМНЫЙ ПРЫЖОК (Ищем следующую пустоту)
  const currentIndex = localItems.value.findIndex(i => i.id === id)
  if (currentIndex !== -1) {
    const nextGap = localItems.value.slice(currentIndex + 1).find(i => i.is_verified != 1)
    
    if (nextGap) {
      activeId.value = null // Сброс для триггера watcher'а
      await nextTick()
      activeId.value = nextGap.id 
    }
  }

  // 3. ОТПРАВКА В БАЗУ (С гарантией статуса)
  try {
    const res: any = await $fetch('/api/proxy', { 
      method: 'POST', 
      query: { 
        r: 'update-segment', 
        id, 
        translationId: tid.value, 
        text, 
        orig: origin,
        is_verified: 1 // Прямой приказ серверу: "Запомни галку!"
      } 
    })

    // Обновляем статистику из ответа сервера, если она там есть
    if (res.status === 'success' && res.stats) {
      const v = res.stats.verified; const t = res.stats.total
      currentStats.value = { 
        verified: v, 
        total: t, 
        percent: t > 0 ? ((v / t) * 100).toFixed(1) : '0.0' 
      }
    }
  } catch (e) { 
    console.error("❌ Ошибка синхронизации с БД:", e) 
  }
}

const handleTransfer = (id: number) => {
  const item = localItems.value.find(i => i.id === id)
  if (item) onUpdateTarget(id, item.source_text)
}

const handleMergeDown = async (id: number) => {
  const idx = localItems.value.findIndex(i => i.id === id)
  if (idx !== -1 && localItems.value[idx + 1]) {
    const curr = localItems.value[idx]; const next = localItems.value[idx + 1]
    const nT = ((curr.target_text || '') + ' ' + (next.target_text || '')).trim()
    const nO = (curr.source_text + ' ' + next.source_text).trim()
    await onUpdateTarget(curr.id, nT, nO); await onUpdateTarget(next.id, '', '')
  }
}
const onRowSelected = (t: string) => { currentSearchText.value = t }

const totalPages = computed(() => apiResponse.value?.data?.total_pages || 1)
const visiblePages = computed(() => {
  const t = totalPages.value; const c = currentPage.value
  let s = Math.max(1, c - 5); let e = Math.min(t, s + 9)
  if (e === t) s = Math.max(1, e - 9)
  const p = []; for (let i = s; i <= e; i++) { if (i > 0) p.push(i) }
  return p
})

const loadMetadata = async () => {
  try {
    const data: any = await $fetch('/api/proxy', {
      method: 'POST',
      query: { r: 'editor-init', translationId: tid.value }, 
      body: { 
        pageLoaderState: { isOnLoad: true, pagination: { bootstrapPaginationClasses: {}, paginationAnchorTexts: {} } }, 
        origin: "", isNew: true, errorList: [] 
      }
    })

    // КРИТИЧЕСКИЙ ЛОГ: Посмотри, что тут в консоли браузера!
    console.log("📥 ПРИШЛО ОТ PHP:", data)

    // Если данные обернуты в .data, распаковываем
    const res = data?.data || data
    
    if (res && res.originFile) {
      originFile.value = res.originFile
      finalFile.value = res.finalFile
      console.log("✅ Метаданные приняты!")
    } else {
      console.warn("⚠️ Поля originFile нет в ответе. Ключи ответа:", Object.keys(res))
    }
  } catch (e) {
    console.error("❌ Ошибка вызова editor-init:", e)
  }
}

onMounted(() => {
  loadMetadata() 
  console.log("🚀 Зиккурат запущен, разведчик имен файлов отправлен...")
})
</script>

<template>
  <div class="v280-root font-inter select-none bg-white">
    
    <!-- [БЛОК 1] HEADER: Тулбар поиска -->
    <header class="v280-header border-b border-slate-200 bg-white z-40 w-full overflow-hidden">
      <div v-if="!isFinalReview" class="v280-header-inner h-full w-full flex">
        <div :class="['v280-search-portal', { 'mix-active': editMode === 1 }]">
           <div class="v280-search-grid">
              <div class="col-id center"><input v-model="searchId" type="text" placeholder="#" class="in-mini" /></div>
              <div class="col-main px-2"><input v-model="searchOrig" type="text" placeholder="Поиск в оригинале..." class="in-text" /></div>
              <div class="col-nav"></div>
              <div class="col-main px-2"><input v-model="searchTrans" type="text" placeholder="Поиск в переводе..." class="in-text" /></div>
              <div class="col-stat"></div>
           </div>
        </div>
      </div>
    </header>

    <!-- [БЛОК 2] ПУЛЬТ: ФИКСИРОВАННЫЙ В УГЛУ -->
    <div v-if="!isFinalReview" class="v280-floating-area">
       <div class="v280-capsule shadow-xl border border-slate-200 bg-white">
          <div class="v280-mode-pills">
            <button @click="editMode = 1" :class="{ active: editMode === 1 }">Микс</button>
            <button @click="editMode = 2" :class="{ active: editMode === 2 }">Табл</button>
          </div>
          <div class="v-sep"></div>
          <button @click="isFinalReview = true" class="btn-ready">Готово</button>
       </div>
    </div>

    <!-- [БЛОК 3] MAIN VIEWPORT: ЗАПРЕТ НА РАСШИРЕНИЕ (min-h-0) -->
    <main class="v280-viewport bg-slate-50 relative overflow-hidden">
      <template v-if="!isFinalReview">
        <div v-if="editMode === 1" class="v280-mix-grid h-full w-full">
          <div class="v280-pane-left border-r border-slate-200 bg-white h-full overflow-hidden">
             <div class="v280-scroller h-full overflow-y-auto">
                <TextEditorMain 
                  v-if="localItems.length > 0"
                  :api-response="{ data: { items: localItems } }" :active-id="activeId"
                  @update:active-id="activeId = $event" @update:target="onUpdateTarget" 
                  @row-selected="onRowSelected" @transfer="handleTransfer" @merge-down="handleMergeDown" 
                />
             </div>
          </div>
          <div class="v280-pane-right bg-white relative h-full overflow-hidden">
             <div class="absolute inset-0 overflow-hidden h-full w-full" style="height: 100%;">

      <!-- ПОКАЗЫВАЕМ РЕДАКТОР, ЕСЛИ ЕСТЬ ФАЙЛ -->
      <TextEditorOnlyOffice 
        v-if="originFile"
        :key="`oo-v280-${tid}`" 
        :filename="originFile.name" 
        :fkey="originFile.fileKey || `v2_m_${tid}`" 
        :search-text="currentSearchText" 
        mode="view" 
      />
      <!-- ИНАЧЕ ПОКАЗЫВАЕМ ХОТЯ БЫ СТАТУС, ЧТОБЫ НЕ БЫЛО ПУСТОТЫ -->
      <div v-else class="h-full flex flex-col items-center justify-center bg-slate-50 text-slate-400">
         <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-slate-300 mb-2"></div>
         <span class="text-[10px] uppercase font-black tracking-widest">Ожидание метаданных...</span>
         <span class="text-[9px] mt-1 italic">TID: {{ tid }} | status: {{ !!originFile }}</span>
      </div>
             </div>
          </div>
        </div>
        <div v-else class="v280-full-view bg-white h-full overflow-y-auto">
           <TextEditorMain v-if="localItems.length > 0" :api-response="{ data: { items: localItems } }" :active-id="activeId" @update:active-id="activeId = $event" @update:target="onUpdateTarget" @merge-down="handleMergeDown" @transfer="handleTransfer" />
        </div>
      </template>

      <!-- [БЛОК 4] РЕЖИМ "ГОТОВО" (ДВА ОКНА ONLYOFFICE) -->
      <template v-else>
        <div v-if="!originFile || !finalFile" class="absolute z-50 bg-red-500 text-white p-2 text-[10px]">
          DEBUG: origin: {{ !!originFile }} | final: {{ !!finalFile }} | tid: {{ tid }}
        </div>
        <div class="v280-mix-grid h-full w-full bg-slate-300 gap-[1px]">
          
          <!-- ЛЕВО: ПЕРЕВОД (Final) -->
          <div class="v280-pane-left bg-white relative h-full overflow-hidden">
            <!-- ВАЖНО: Добавляем v-if="finalFile" -->
            <TextEditorOnlyOffice 
              v-if="finalFile"
              :key="`oo-final-${tid}-${finalFile.name}`" 
              :filename="finalFile.name" 
              :fkey="'v2_f_'+tid" 
              mode="edit" 
            />
          </div>

          <!-- ПРАВО: ОРИГИНАЛ (Source) -->
          <div class="v280-pane-right bg-white relative h-full overflow-hidden">
            <!-- ВАЖНО: Добавляем v-if="originFile" -->
            <TextEditorOnlyOffice 
              v-if="originFile" 
              :key="`oo-${tid}-${originFile.name}`" 
              :filename="originFile.name" 
              :fkey="originFile.fileKey || `v2_${tid}`" 
              :search-text="currentSearchText" 
              mode="view" 
            />
          </div>
          
        </div>
      </template>
    </main>

    <!-- [БЛОК 4] FOOTER: ВОЗВРАЩЕН И ЗАФИКСИРОВАН -->
    <footer class="v280-footer border-t border-slate-200 bg-white z-40 w-full overflow-hidden">
       <div class="v280-footer-inner px-8 h-full flex flex-row items-center justify-between w-full">
          <div class="v280-pagin flex flex-row items-center gap-1">
            <button @click="currentPage = 1" :disabled="currentPage === 1" class="btn-p">«</button>
            <button v-for="p in visiblePages" :key="p" @click="currentPage = p" :class="['btn-n', { active: p === currentPage }]">{{ p }}</button>
            <button @click="currentPage = totalPages" :disabled="currentPage >= totalPages" class="btn-p">»</button>
          </div>
          <div class="v280-stats-wrap flex flex-row items-center gap-6">
            <div class="flex flex-row items-baseline gap-1.5">
               <span class="text-[9px] font-black text-slate-400">PROCESSED</span>
               <span class="text-[9px] font-black text-slate-400">&nbsp;</span>
               <span class="text-[14px] font-black text-slate-800 tabular-nums">{{ currentStats.verified }} / {{ currentStats.total }}</span>
            </div>
            <div class="v280-badge">{{ currentStats.percent }}%</div>
          </div>
       </div>
    </footer>

  </div>
</template>

<style>
/* --- 1. ГЛОБАЛЬНЫЙ БЛОК (БЕЗ SCOPED) --- */
@import url('https://googleapis.com');

* { 
  box-sizing: border-box !important; 
}

html, body { 
  overflow: hidden !important; 
  height: 100vh !important; 
  width: 100vw !important; 
  margin: 0; 
  padding: 0; 
  position: fixed; 
  font-family: 'Inter', sans-serif;
  background-color: #ffffff;
}

/* Фикс для таблиц внутри компонентов, чтобы они слушались 100% ширины */
.v280-scroller table { 
  width: 100% !important; 
  table-layout: fixed !important; 
  border-collapse: collapse; 
}
</style>

<style scoped>
/* --- 2. ЛОКАЛЬНЫЙ БЛОК (SCOPED) --- */
.v280-root { 
  display: grid; 
  grid-template-rows: 36px 1fr 40px; 
  height: 100vh; 
  width: 100vw; 
  max-width: 100vw;
  overflow: hidden; 
}

/* HEADER */
.v280-header { height: 36px; display: flex; align-items: center; background: #fff; width: 100%; }
.v280-header-inner { display: flex; align-items: center; width: 100%; height: 100%; }

.v280-search-portal.mix-active { 
  width: 50% !important; 
  max-width: 50% !important; 
  border-right: 1px solid #e2e8f0; 
  height: 100%; 
  display: flex; 
  align-items: center; 
  padding: 0 15px; 
}

.v280-search-grid { 
  display: grid; 
  grid-template-columns: 50px 1fr 50px 1fr 60px; 
  width: 100%; 
  align-items: center; 
}

.in-text { width: 100%; height: 24px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 11px; padding: 0 8px; outline: none; background: #f8fafc; }
.in-mini { width: 38px; height: 24px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 10px; text-align: center; }

/* VIEWPORT & GRID */
.v280-viewport { min-height: 0; flex: 1; position: relative; overflow: hidden; height: 100%; }
.v280-mix-grid { display: grid; grid-template-columns: 50% 50%; height: 100%; width: 100%; }

.v280-pane-left { height: 100%; display: flex; flex-direction: column; overflow: hidden; border-right: 1px solid #e2e8f0; background: #fff; }
.v280-pane-right { height: 100%; position: relative; overflow: hidden; background: #f1f5f9; }

.v280-scroller { flex: 1; width: 100%; height: 100%; overflow-y: auto; }

/* FOOTER */
.v280-footer { height: 40px; display: flex; align-items: center; background: #fff; border-top: 1px solid #e2e8f0; width: 100vw; }
.v280-footer-inner { display: flex; flex-direction: row; align-items: center; justify-content: space-between; width: 100%; padding: 0 20px; }

.v280-pagin { display: flex; flex-direction: row; align-items: center; gap: 4px; }

.btn-p { padding: 0 10px; height: 26px; border: 1px solid #e2e8f0; border-radius: 5px; background: #fff; font-size: 16px; font-weight: 900; color: #94a3b8; cursor: pointer; display: flex; align-items: center; }
.btn-n { min-width: 28px; height: 26px; border: 1px solid #e2e8f0; border-radius: 5px; background: #fff; font-size: 11px; font-weight: 800; color: #1e293b; cursor: pointer; }
.btn-n.active { background: #2563eb; color: #fff; border-color: #2563eb; }

/* STATS */
.v280-stats-wrap { display: flex; flex-direction: row; align-items: center; gap: 15px; }
.v280-badge { background: #10b981; color: #fff; padding: 2px 12px; border-radius: 4px; font-weight: 900; font-size: 11px; min-width: 55px; text-align: center; }

/* FLOATING CONTROLS */
.v280-floating-area { position: fixed; top: 6px; right: 15px; z-index: 1000; }
.v280-capsule { display: flex; align-items: center; background: #fff; padding: 4px 10px; border-radius: 10px; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }

.v280-mode-pills { display: flex; background: #f1f5f9; padding: 2px; border-radius: 6px; }
.v280-mode-pills button { padding: 3px 15px; border: none; background: none; font-size: 10px; cursor: pointer; color: #64748b; font-weight: 700; }
.v280-mode-pills button.active { background: #fff; color: #2563eb; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }

.v-sep { width: 1px; height: 18px; background: #e2e8f0; }
.btn-ready { background: #2563eb; color: #fff; border: none; padding: 5px 18px; border-radius: 7px; font-size: 11px; font-weight: 900; cursor: pointer; }

.center { display: flex; justify-content: center; }
.tabular-nums { font-variant-numeric: tabular-nums; }
</style>
