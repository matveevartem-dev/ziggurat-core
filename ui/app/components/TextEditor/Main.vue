<script setup lang="ts">
import { watch, nextTick } from 'vue'
const props = defineProps({ apiResponse: Object, activeId: Number })
const emit = defineEmits(['update:target', 'transfer', 'merge-down', 'row-selected', 'update:active-id'])

watch(() => props.activeId, async (newId) => {
  if (!newId) return

  await nextTick()
  // Важно: в Row.vue у textarea должен быть id="input-target-{{item.id}}"
  const el = document.querySelector(`#input-target-${newId}`) as HTMLTextAreaElement
  
  if (el) {
    el.focus()
    el.scrollIntoView({ block: 'center', behavior: 'smooth' })
  }
  
  // Сбрасываем сигнал, чтобы родитель мог послать его снова
  emit('update:active-id', null)
})
</script>

<template>
  <div class="h-full w-full bg-white">
    <!-- КЛЮЧЕВОЙ ФИКС: table-layout: fixed заставляет ячейки слушаться заданных пикселей -->
    <table class="border-collapse" style="width: 100% !important; table-layout: fixed !important;">
       <tbody>
          <template v-for="item in apiResponse?.data?.items" :key="item.id">
            <TextEditorRow 
              v-if="item && item.id"
              :item="item"
              @update:target="(text) => emit('update:target', item.id, text)"
              @transfer="emit('transfer', item.id)"
              @merge-down="emit('merge-down', item.id)"
              @click="emit('row-selected', item.source_text)"
            />
          </template>
       </tbody>
    </table>
  </div>
</template>
