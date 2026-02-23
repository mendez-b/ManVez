<template>
  <div class="skeleton-card">
    <div class="skeleton-img skeleton-animation"></div>
    <div class="skeleton-info">
      <div class="skeleton-title skeleton-animation"></div>
      <div class="skeleton-chapter skeleton-animation"></div>
    </div>
  </div>
</template>

<style scoped>
.skeleton-card {
  border-radius: 8px;
  overflow: hidden;
  background: var(--bg-card);
}

.skeleton-img {
  aspect-ratio: 2/3;
  width: 100%;
}

.skeleton-info {
  padding: 10px;
}

.skeleton-title {
  height: 14px;
  border-radius: 4px;
  margin-bottom: 8px;
  width: 90%;
}

.skeleton-chapter {
  height: 12px;
  border-radius: 4px;
  width: 50%;
}

.skeleton-animation {
  background: linear-gradient(
    90deg,
    var(--bg-secondary) 25%,
    var(--bg-card) 50%,
    var(--bg-secondary) 75%
  );
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>
