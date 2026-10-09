<template>
  <section class="history" ref="sectionRef">
    <h2 class="history__title">Our History</h2>

    <div class="history__track">
      <span class="history__line" aria-hidden="true" />

      <div
        v-for="(item, i) in milestones"
        :key="item.year"
        class="milestone"
        :class="[i % 2 === 0 ? 'milestone--top' : 'milestone--bottom', { 'is-visible': visible }]"
        :style="{ transitionDelay: visible ? `${i * 120}ms` : '0ms' }"
      >
        <div class="milestone__content">
          <img v-if="item.image" :src="item.image" :alt="item.imageAlt || ''" loading="lazy" />
          <ul v-if="item.points?.length">
            <li v-for="p in item.points" :key="p">{{ p }}</li>
          </ul>
        </div>
        <span class="milestone__dot" aria-hidden="true" />
        <span class="milestone__year">{{ item.year }}</span>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

defineProps({
  // [{ year, points: [], image, imageAlt }]
  milestones: { type: Array, required: true },
})

const sectionRef = ref(null)
const visible = ref(false)
let observer

onMounted(() => {
  observer = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) {
        visible.value = true
        observer.disconnect()
      }
    },
    { threshold: 0.15 }
  )
  if (sectionRef.value) observer.observe(sectionRef.value)
})
onBeforeUnmount(() => observer && observer.disconnect())
</script>

<style scoped>
.history {
  --h-bg: var(--brand-primary, #16324a);
  --h-accent: var(--brand-accent, #3d7a8a);

  background: var(--h-bg);
  color: #fff;
  padding: clamp(2.5rem, 6vw, 4.5rem) clamp(1.25rem, 5vw, 3rem);
  overflow-x: auto;
}

.history__title {
  font-size: clamp(1.6rem, 2.4vw, 2.2rem);
  margin: 0 0 3rem;
}

.history__track {
  position: relative;
  display: flex;
  gap: clamp(2rem, 4vw, 4rem);
  min-width: max-content;
  padding: 5rem 1rem;
}

.history__line {
  position: absolute;
  left: 0;
  right: 0;
  top: 50%;
  height: 2px;
  background: rgba(255, 255, 255, 0.25);
}

.milestone {
  position: relative;
  width: 220px;
  display: flex;
  flex-direction: column;
  align-items: center;
  opacity: 0;
  transition: opacity 0.5s ease, transform 0.5s ease;
}
.milestone--top {
  transform: translateY(16px);
  justify-content: flex-end;
}
.milestone--bottom {
  transform: translateY(-16px);
}
.milestone.is-visible {
  opacity: 1;
  transform: translateY(0);
}

.milestone--top .milestone__content {
  order: 1;
  margin-bottom: 1.25rem;
}
.milestone--top .milestone__dot {
  order: 2;
}
.milestone--top .milestone__year {
  order: 4;
  margin-top: 0.75rem;
}
.milestone--top::after {
  content: '';
  order: 3;
  width: 2px;
  height: 1.25rem;
  background: rgba(255, 255, 255, 0.4);
}

.milestone--bottom .milestone__dot {
  order: 1;
}
.milestone--bottom::after {
  content: '';
  order: 2;
  width: 2px;
  height: 1.25rem;
  background: rgba(255, 255, 255, 0.4);
}
.milestone--bottom .milestone__content {
  order: 3;
  margin-top: 1.25rem;
}
.milestone--bottom .milestone__year {
  order: 0;
  margin-bottom: 0.75rem;
}

.milestone__dot {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  border: 2px solid var(--h-accent);
  background: var(--h-bg);
}

.milestone__year {
  color: var(--h-accent);
  font-weight: 700;
  font-size: 1.2rem;
}

.milestone__content {
  text-align: center;
  font-size: 0.9rem;
}

.milestone__content img {
  width: 100%;
  border-radius: 4px;
  margin-bottom: 0.5rem;
}

.milestone__content ul {
  list-style: none;
  margin: 0;
  padding: 0;
  text-align: left;
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  opacity: 0.9;
}

@media (prefers-reduced-motion: reduce) {
  .milestone {
    transition: none;
    opacity: 1;
    transform: none;
  }
}
</style>
