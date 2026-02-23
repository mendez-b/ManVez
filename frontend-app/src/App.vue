<template>
  <Navbar />
  <RouterView />
  <button 
    v-show="showScrollBtn" 
    @click="scrollToTop" 
    class="scroll-top-btn"
    title="Volver arriba"
  >
    ▲
  </button>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import Navbar from './components/Navbar.vue'

const showScrollBtn = ref(false)

function handleScroll() {
  showScrollBtn.value = window.scrollY > 300
}

function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>

<style>
.scroll-top-btn {
  position: fixed;
  bottom: 24px;
  right: 24px;
  width: 44px;
  height: 44px;
  background: var(--accent);
  color: white;
  border: none;
  border-radius: 50%;
  font-size: 1.1rem;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  transition: background 0.2s, transform 0.2s;
  z-index: 999;
}

.scroll-top-btn:hover {
  background: var(--accent-hover);
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .scroll-top-btn {
    bottom: 16px;
    right: 16px;
    width: 40px;
    height: 40px;
  }
}
</style>
```