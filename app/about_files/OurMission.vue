<template>
  <section class="mission" ref="sectionRef">
    <div class="mission__text" :class="{ 'is-visible': visible }">
      <h2>Our Mission</h2>
      <p class="mission__intro">{{ intro }}</p>
      <ul class="mission__list">
        <li v-for="item in points" :key="item">
          <span class="mission__bullet" aria-hidden="true">›</span>
          <span>{{ item }}</span>
        </li>
      </ul>
    </div>

    <div class="mission__media" :class="{ 'is-visible': visible }">
      <img :src="image" :alt="imageAlt" loading="lazy" />
      <span class="mission__stripes" aria-hidden="true" />
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

defineProps({
  intro: { type: String, default: 'Our mission is built around four commitments.' },
  points: { type: Array, default: () => [] },
  image: { type: String, required: true },
  imageAlt: { type: String, default: '' },
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
    { threshold: 0.2 }
  )
  if (sectionRef.value) observer.observe(sectionRef.value)
})
onBeforeUnmount(() => observer && observer.disconnect())
</script>

<style scoped>
.mission {
  --m-surface: var(--brand-surface-alt, #e4edee);
  --m-primary: var(--brand-primary, #16324a);
  --m-accent: var(--brand-accent, #3d7a8a);

  background: var(--m-surface);
  display: grid;
  grid-template-columns: 1fr 1fr;
  align-items: center;
  gap: 2.5rem;
  padding: clamp(2.5rem, 6vw, 4.5rem) clamp(1.25rem, 5vw, 3rem);
}

@media (max-width: 860px) {
  .mission {
    grid-template-columns: 1fr;
  }
}

.mission__text,
.mission__media {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.6s ease, transform 0.6s ease;
}
.mission__media {
  transition-delay: 0.12s;
}
.mission__text.is-visible,
.mission__media.is-visible {
  opacity: 1;
  transform: translateY(0);
}

.mission__text h2 {
  color: var(--m-primary);
  font-size: clamp(1.6rem, 2.4vw, 2.2rem);
  margin: 0 0 1rem;
}

.mission__intro {
  color: #3c4a52;
  margin: 0 0 1.25rem;
}

.mission__list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.mission__list li {
  display: flex;
  gap: 0.6rem;
  color: #3c4a52;
  line-height: 1.5;
}

.mission__bullet {
  color: var(--m-accent);
  font-weight: 700;
  flex: none;
}

.mission__media {
  position: relative;
}

.mission__media img {
  width: 100%;
  border-radius: 6px;
  display: block;
  position: relative;
  z-index: 2;
}

/* decorative striped tab, echoes the reference site's diagonal pattern
   without reusing its colors */
.mission__stripes {
  position: absolute;
  right: -14px;
  bottom: -14px;
  width: 55%;
  height: 40%;
  z-index: 1;
  background-image: repeating-linear-gradient(
    -45deg,
    var(--m-accent) 0 6px,
    transparent 6px 14px
  );
  opacity: 0.85;
}

@media (prefers-reduced-motion: reduce) {
  .mission__text,
  .mission__media {
    transition: none;
    opacity: 1;
    transform: none;
  }
}
</style>
