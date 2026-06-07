<template>
  <a-config-provider :theme="antdTheme">
    <router-view />
  </a-config-provider>
</template>

<script setup lang="ts">
import { antdTheme } from '@/theme'
</script>

<style>
#app {
  min-height: 100vh;
}
</style>
