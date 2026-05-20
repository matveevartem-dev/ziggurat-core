<script setup lang="ts">
// @ts-expect-error
import { onMounted, ref } from 'vue'
const props = defineProps({ item: { type: Object, required: true } })
const emit = defineEmits(['update:target', 'transfer', 'merge-down'])
const textarea = ref<HTMLTextAreaElement | null>(null)

// Локальный эффект вспышки
const isFlashing = ref(false)

const handleSave = () => {
  isFlashing.value = true
  setTimeout(() => { isFlashing.value = false }, 600) // Длительность вспышки
  emit('update:target', props.item.target_text)
}

const adjustHeight = () => {
  if (!textarea.value) return
  textarea.value.style.height = 'auto'
  textarea.value.style.height = textarea.value.scrollHeight + 'px'
}
onMounted(() => adjustHeight())
</script>

<template>
  <tr class="v281-row group border-b border-slate-100 bg-white hover:bg-slate-50/50">
    <td style="width: 50px;" class="p-2 text-center text-[10px] text-slate-300 font-mono align-top pt-4">{{ item.id }}</td>
    <td class="p-3 align-top text-[13px] text-slate-800 leading-relaxed min-w-0">
      <div class="whitespace-pre-wrap break-words">{{ item.source_text }}</div>
    </td>
    <td style="width: 50px; min-width: 50px;" class="p-1 align-top border-x border-slate-50 bg-slate-50/10">
       <div class="flex flex-col items-center gap-2 pt-1">
          <button @click="emit('transfer')" class="btn-c blue">→</button>
          <button @click="emit('merge-down')" class="btn-c magnet-gray magnet-rotate"></button>
       </div>
    </td>
    <td class="p-1 align-top relative min-w-0">
      <textarea
        ref="textarea"
        :id="`input-target-${item.id}`"
        v-model="item.target_text"
        :class="['v281-input', { 'v281-flash-success': isFlashing }]"
        placeholder="Введите перевод..."
        @input="adjustHeight"
        @keydown.enter.exact.stop.prevent="handleSave"
      ></textarea>
    </td>
    <td style="width: 60px;" class="p-3 align-top text-center">
       <span v-if="item.is_verified == 1" class="text-green-500 font-bold text-xs">✓</span>
       <span v-else="item.is_verified == 1" class="text-green-500 font-bold text-xs">○</span>
    </td>
  </tr>
</template>

<style scoped>
.v281-input {
  width: 100%; min-height: 36px; padding: 8px; font-family: inherit; font-size: 13px;
  line-height: 1.5; color: #1e293b; border: 1px solid transparent; border-radius: 4px;
  background: transparent; resize: none; outline: none; 
  transition: border-color 0.4s, box-shadow 0.4s;
}
.v281-input:focus { background: #fff; border-color: #3b82f6; }

/* ЭФФЕКТ ВСПЫШКИ (v281) */
.v281-flash-success {
  border-color: #10b981 !important;
  box-shadow: 0 0 10px rgba(16, 185, 129, 0.4) !important;
  background: #fff !important;
}

.btn-c { width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 4px; border: 1px solid #e2e8f011; background: #fff; cursor: pointer;  opacity: 0.5; }
.btn-c.blue { color: #3b82f6; font-size: 16px; font-weight: bold; }
.btn-c.grey { color: #94a3b8; font-size: 10px; }
.btn-c:hover {
  opacity: 1;
  font-weight: 999;
}

.magnet-gray::before {
  color: #94a3b8;
  font-size: 10px;
  content: "\1F9F2";
  filter: grayscale(100%);
  /*opacity: 0.8; /* Необязательно: делает цвет более приглушенным */
}

.magnet-rotate {
  cursor: pointer;
  /* Плавность: свойство | длительность | тип анимации */
  transition: transform 0.1s ease-in-out;
  transform: rotate(-90deg);
}

/* Состояние при наведении */
.magnet-rotate:hover {
  transform: rotate(90deg);
}
</style>
