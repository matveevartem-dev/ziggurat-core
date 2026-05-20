<script setup lang="ts">
/**
 * ONLYOFFICE: Версия v260 (STEEL HEIGHT)
 * Фикс: Принудительные 100% высоты через инлайновый стиль.
 */
import { ref, onMounted, onUnmounted, watch } from 'vue'

const props = defineProps({
  filename: String,
  fkey: String,
  mode: { type: String, default: 'view' },
  searchText: String
})

const config = useRuntimeConfig()
const { ooApiJs } = useBackendConfig()
const containerId = `oo-container-${props.fkey}`
const isScriptLoaded = ref(false)
let docEditor: any = null

const initEditor = () => {
  if (!(window as any).DocsAPI) return
  const container = document.getElementById(containerId)
  if (container) container.innerHTML = '' 

  const crmBase = config.public.crmUrl?.replace(/\/$/, '')
  const fileUrl = `${crmBase}/backend/web/uploads/${props.filename}`

  docEditor = new (window as any).DocsAPI.DocEditor(containerId, {
    width: "100%", height: "100%",
    document: { fileType: "docx", key: props.fkey, title: props.filename, url: fileUrl },
    documentType: "word",
    editorConfig: {
      mode: 'view', lang: "ru",
      customization: { autosave: false, chat: false, comments: false, help: false, compactHeader: true, compactToolbar: true, hideRightMenu: true }
    }
  })
}

onMounted(() => {
  if ((window as any).DocsAPI) { isScriptLoaded.value = true; initEditor(); return }
  const script = document.createElement('script')
  console.log(ooApiJs)
  script.src = ooApiJs
  script.async = true
  script.onload = () => { isScriptLoaded.value = true; initEditor() }
  document.head.appendChild(script)
})

onUnmounted(() => { if (docEditor) { docEditor.destroyEditor(); docEditor = null } })

watch(() => props.searchText, (val) => {
  // Проверяем существование метода перед вызовом
  if (docEditor && typeof docEditor.search === 'function' && val) {
    try {
      docEditor.search(val)
    } catch (e) {
      console.warn("OnlyOffice Search is unavailable")
    }
  }
})
</script>

<template>
  <div class="v260-oo-container" style="height: 100% !important;">
    <!-- ТВОЙ ФИКС: Прямое управление высотой контейнера -->
    <div :id="containerId" class="v260-canvas" style="height: 100% !important;"></div>
    
    <div v-if="!isScriptLoaded" class="v260-loader absolute inset-0 flex items-center justify-center bg-white z-50">
       <div class="animate-spin h-8 w-8 border-2 border-blue-600 border-t-transparent rounded-full"></div>
    </div>
  </div>
</template>

<style scoped>
.v260-oo-container { position: relative; width: 100%; overflow: hidden; background: #fff; }
.v260-canvas { width: 100%; }
:deep(iframe) { width: 100% !important; height: 100% !important; border: none !important; display: block !important; }
</style>
