<template>
  <div class="editor-layout">
    <slot />
  </div>
</template>

<style scoped>
.editor-layout {
  height: 100vh;
  width: 100vw;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  background: #fff;
}

/* Сбрасываем возможные отступы от глобальных стилей только для этой страницы */
:deep(body) {
  margin: 0;
  padding: 0;
}
</style>
