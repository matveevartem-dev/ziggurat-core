# app/app.vue
```vue
<script setup lang="ts">
import TableEditor from './pages/table-editor.vue';

</script>

<!-- app.vue -->
<template>
  <div>
    <!-- NuxtLayout ищет настройки в definePageMeta страниц -->
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>
```

# nuxt.config.ts
```vue
export default defineNuxtConfig({
  devtools: { enabled: true },
  app: {
    head: {
      link: [
        { 
          rel: 'stylesheet', 
          href: '/font-awesome-4.7.0/css/font-awesome.min.css' 
        }
      ]
    }
  },
  css: [
    '~/assets/css/table-editor/bootstrap.min.css',
    '~/assets/css/table-editor/styles.css',
    '~/assets/css/table-editor/menu-header.css', // Обязательно
    '~/assets/css/table-editor/editor.css'
  ],
  compatibilityDate: '2024-04-03'
})
```

## app/components/TextEditor/Main.vue
```vue
<script setup lang="ts">
import { ref, watch, nextTick } from 'vue'
import { useDebounceFn } from '@vueuse/core'

const props = defineProps(['sourceConfig', 'targetConfig'])

const viewMode = ref<'work' | 'review'>('work')
const hasPreviewed = ref(false)
const selectedId = ref<number | null>(null)
const rowRefs = ref<any[]>([])
const sourceOfficeRef = ref(null)

const segments = ref([
  { id: 1, source: 'The quick brown fox jumps over the lazy dog.', target: '', status: 'none' },
  { id: 2, source: 'Nuxt 4 provides a great developer experience.', target: '', status: 'none' },
  { id: 3, source: 'OnlyOffice integration is key for CAT systems.', target: '', status: 'none' },
])

const mergeHistory = ref<any[]>([])

const debouncedSave = useDebounceFn((id: number, text: string) => {
  console.log(`[API SAVE] ${id}: ${text}`)
}, 1000)

const onUpdateTarget = (id: number, text: string) => {
  const seg = segments.value.find(s => s.id === id)
  if (seg) {
    seg.target = text
    seg.status = 'progress'
    debouncedSave(id, text)
  }
}

const selectSegment = (seg: any) => {
  selectedId.value = seg.id
  if (sourceOfficeRef.value && viewMode.value === 'work' && seg.source) {
    sourceOfficeRef.value.scrollToText(seg.source.substring(0, 80))
  }
}

const confirmAndNext = (index: number) => {
  const nextIdx = index + 1
  if (nextIdx < segments.value.length) {
    if (!segments.value[nextIdx].source) return confirmAndNext(nextIdx)
    selectSegment(segments.value[nextIdx])
    nextTick(() => rowRefs.value[nextIdx]?.focusInput())
  }
}

const mergeSegments = (index: number) => {
  if (index >= segments.value.length - 1) return
  const current = segments.value[index]
  const next = segments.value[index + 1]
  if (!next.source) return

  mergeHistory.value.push({
    parentId: current.id,
    childId: next.id,
    srcLen: current.source.length,
    trgLen: current.target.length
  })

  current.source += ' ' + next.source
  current.target = (current.target + ' ' + next.target).trim()
  next.source = ''; next.target = ''
  
  debouncedSave(current.id, current.target)
  debouncedSave(next.id, '')
}

const splitSegment = (index: number) => {
  const current = segments.value[index]
  const hIdx = mergeHistory.value.findIndex(h => h.parentId === current.id)
  if (hIdx === -1) return

  const h = mergeHistory.value[hIdx]
  const next = segments.value.find(s => s.id === h.childId)
  if (next) {
    next.source = current.source.substring(h.srcLen).trim()
    next.target = current.target.substring(h.trgLen).trim()
    current.source = current.source.substring(0, h.srcLen).trim()
    current.target = current.target.substring(0, h.trgLen).trim()
    mergeHistory.value.splice(hIdx, 1)
    debouncedSave(current.id, current.target)
    debouncedSave(next.id, next.target)
  }
}

watch(viewMode, (val) => { if (val === 'review') hasPreviewed.value = true })
</script>

<template>
  <div class="main-wrapper">
    <header class="menu-header">
      <div class="container-fluid">
        <div class="header-left-part">
          <div class="btn-group">
            <button class="btn-style" :class="{ active: viewMode === 'work' }" @click="viewMode = 'work'">
              <i class="fa fa-edit"></i> Перевод
            </button>
            <button class="btn-style" :class="{ active: viewMode === 'review' }" @click="viewMode = 'review'">
              <i class="fa fa-columns"></i> Предпросмотр
            </button>
          </div>
        </div>
        <div class="header-right-part">
          <button class="btn-green" :disabled="!hasPreviewed">Сдать работу</button>
        </div>
      </div>
    </header>

    <div class="content-container">
      <Transition name="fade" mode="out-in">
        <div v-if="viewMode === 'work'" key="work" class="editor-layout-grid">
          <div class="segments-side-scroll">
            <TextEditorTableRow 
              v-for="(seg, idx) in segments" :key="seg.id"
              v-show="seg.source !== ''"
              ref="rowRefs"
              v-bind="seg"
              :is-selected="selectedId === seg.id"
              :can-split="mergeHistory.some(h => h.parentId === seg.id)"
              @update:target="onUpdateTarget(seg.id, $event)"
              @select="selectSegment(seg)"
              @confirm="confirmAndNext(idx)"
              @merge="mergeSegments(idx)"
              @split="splitSegment(idx)"
            />
          </div>
          <div class="preview-side-panel">
            <slot name="onlyoffice-source" />
          </div>
        </div>
        <div v-else key="review" class="review-full-grid">
          <slot name="onlyoffice-compare" />
        </div>
      </Transition>
    </div>
  </div>
</template>

<style>
/* Подключаем стили БЕЗ scoped */
@import "~/assets/css/table-editor/bootstrap.min.css";
@import "~/assets/css/table-editor/styles.css";
@import "~/assets/css/table-editor/menu-header.css";
@import "~/assets/css/table-editor/editor.css";

html, body, #__nuxt { height: 100vh; margin: 0; overflow: hidden; }
.main-wrapper { height: 100vh; display: flex; flex-direction: column; }
.content-container { flex: 1; display: flex; overflow: hidden; }
.editor-layout-grid { display: flex; width: 100%; height: 100%; }
.segments-side-scroll { flex: 1; overflow-y: auto; background: #fff; }
.preview-side-panel { width: 40%; border-left: 1px solid #ddd; }
.review-full-grid { display: flex; width: 100%; height: 100%; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
```

## app/components/TextEditor/OnlyOffice.vue
```vue
<script setup lang="ts">
const props = defineProps(['id', 'config'])
let docEditor: any = null

useHead({ script: [{ src: 'https://onlyoffice.tcrm.test/web-apps/apps/api/documents/api.js' }] })

const init = () => {
  if (typeof window !== 'undefined' && window.DocsAPI) {
    if (docEditor) docEditor.destroyEditor()
    docEditor = new window.DocsAPI.DocEditor(props.id, props.config)
  }
}

const scrollToText = (text: string) => {
  if (docEditor) docEditor.executeMethod("Search", [text])
}

defineExpose({ scrollToText })
onMounted(() => setTimeout(init, 350))
onBeforeUnmount(() => { if (docEditor) docEditor.destroyEditor() })
watch(() => props.config, init, { deep: true })
</script>

<template>
  <div :id="id" class="h-100 w-100"></div>
</template>
```

## app/components/TextEditor/TableRow.vue
```vue
<script setup lang="ts">
const props = defineProps(['id', 'source', 'target', 'status', 'isSelected', 'canSplit'])
const emit = defineEmits(['update:target', 'select', 'confirm', 'merge', 'split'])
const inputRef = ref<HTMLTextAreaElement | null>(null)

const autoResize = (e: any) => {
  const el = e.target
  el.style.height = 'auto'
  el.style.height = el.scrollHeight + 'px'
}
defineExpose({ focusInput: () => inputRef.value?.focus() })
</script>

<template>
  <div class="table_stroke" :class="{ 'active': isSelected }" @click="$emit('select')">
    <div class="page"><span class="segment-count">#{{ id }}</span><br>1 стр.</div>
    <div class="original">{{ source }}</div>
    
    <div class="stroke-nav">
      <button class="btn-nav" title="Перенести вправо"><i class="fa fa-chevron-right blue-icon"></i></button>
      <button class="btn-nav btn-magnet" title="Объединить" @click.stop="$emit('merge')">
        <i class="fa fa-magnet orange-icon"></i>
      </button>
      <button v-if="canSplit" class="btn-nav" title="Разъединить" @click.stop="$emit('split')">
        <i class="fa fa-scissors blue-icon"></i>
      </button>
    </div>

    <div class="translate">
      <textarea 
        ref="inputRef"
        :value="target" 
        class="text-edit"
        rows="1"
        @input="(e) => { $emit('update:target', (e.target as any).value); autoResize(e); }"
        @focus="$emit('select')"
        @keydown.ctrl.enter.prevent="$emit('confirm')"
        @keydown.ctrl.j.prevent="$emit('merge')"
      ></textarea>
    </div>

    <div class="status">
      <div class="status_translated">
        <button class="toolbar-btn no-bg-btn btn-check">
          <i class="fa fa-circle" :class="target ? 'green-icon' : 'yellow-icon'"></i>
        </button>
      </div>
      <div class="status_who-translated">
        <button class="toolbar-btn no-bg-btn btn-who"><i class="fa fa-user"></i></button>
      </div>
    </div>
  </div>
</template>

<style>
/* Стили для имитации дизайна из архива */
.table_stroke { display: flex; border-bottom: 1px solid #eee; cursor: pointer; min-height: 60px; }
.table_stroke.active { background-color: #f0f7ff; }

.original, .translate { width: 45%; padding: 10px; font-size: 14px; line-height: 1.4; }
.page { width: 60px; padding: 10px; font-size: 11px; color: #999; text-align: center; }

.text-edit {
  width: 100%; border: none !important; outline: none !important;
  background: transparent !important; resize: none; overflow: hidden;
  padding: 0; font-family: inherit; font-size: inherit;
}

.stroke-nav { display: flex; flex-direction: column; gap: 5px; padding: 10px 5px; width: 35px; }
.btn-nav { background: none; border: none; padding: 0; cursor: pointer; font-size: 14px; }

.blue-icon { color: #007bff !important; }
.orange-icon { color: #ff8c00 !important; }
.yellow-icon { color: #ffc107 !important; }
.green-icon { color: #28a745 !important; }

.status { width: 50px; padding: 10px; display: flex; flex-direction: column; align-items: center; }
.no-bg-btn { background: none; border: none; padding: 2px; cursor: pointer; }
</style>
```
