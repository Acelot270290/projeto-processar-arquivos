<script setup>
import { RouterView } from 'vue-router'
</script>

<template>
  <main class="app-wrapper">
    <RouterView />
  </main>
</template>

<style>
body, html, #app {
  margin: 0;
  padding: 0;
  height: 100%;
  width: 100%;
  background-color: #f8f9fa;
  font-family: "Segoe UI", Roboto, sans-serif;
}

.app-wrapper {
  min-height: 100vh;
  width: 100%;
  display: flex;
  flex-direction: column;
}
</style>
