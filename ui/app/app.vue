// app/app.vue
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