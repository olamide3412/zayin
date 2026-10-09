<template>
  <section class="core-values" ref="sectionRef">
    <div class="core-values__inner">
      <h2 class="core-values__title">Our Core Values</h2>
      <p class="core-values__subtitle">
        Our values are clear and shape how every team member works with patients.
      </p>

      <div class="core-values__grid">
        <article
          v-for="(value, i) in values"
          :key="value.title"
          class="value-card"
          :class="{ 'is-visible': visible }"
          :style="{ transitionDelay: visible ? `${i * 90}ms` : '0ms' }"
        >
          <span class="value-card__icon" v-html="value.icon" aria-hidden="true" />
          <h3 class="value-card__title">{{ value.title }}</h3>
          <p class="value-card__desc">{{ value.description }}</p>
        </article>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

// Replace copy with your own approved wording — this is placeholder content
// paraphrased for structure/length only.
const values = [
  {
    title: 'Professional',
    description:
      'From clinicians to front-desk and finance staff, every team member holds themselves to a professional standard in every interaction.',
    icon: '<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="3.2"/><path d="M5 20c0-3.6 3.1-6.2 7-6.2s7 2.6 7 6.2"/></svg>',
  },
  {
    title: 'Safe',
    description:
      'Patient safety guides every decision. Staff proactively flag hazards and escalate them immediately to the right lead.',
    icon: '<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z"/><path d="M9 12l2 2 4-4"/></svg>',
  },
  {
    title: 'Friendly',
    description:
      'A hospital visit can feel daunting. We greet every patient warmly and help them feel comfortable throughout their time with us.',
    icon: '<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M8.5 14c1 1.2 2.2 1.8 3.5 1.8s2.5-.6 3.5-1.8"/><path d="M9 9h.01M15 9h.01"/></svg>',
  },
  {
    title: 'Calm',
    description:
      'Patients should leave feeling cared for by a composed, confident team that keeps them relaxed at every step.',
    icon: '<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 3v4M4.2 7l2.8 2.8M19.8 7L17 9.8M3 15h18M6 19h12"/></svg>',
  },
  {
    title: 'Helpful',
    description:
      'No request is too small. Our staff are ready to assist patients with whatever they need, whenever they need it.',
    icon: '<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 21c-4-2.5-8-5.6-8-10a5 5 0 0 1 8-4 5 5 0 0 1 8 4c0 4.4-4 7.5-8 10z"/></svg>',
  },
  {
    title: 'Positive & Flexible',
    description:
      'Our team adapts to deliver a consistently high standard of care, resolving any shortfalls constructively and without blame.',
    icon: '<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 12a8 8 0 0 1 13.3-6M20 12a8 8 0 0 1-13.3 6"/><path d="M17 3v4h-4M7 21v-4h4"/></svg>',
  },
]

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
.core-values {
  /* Swap these three variables for your brand palette. Values below are
     neutral placeholders only — not the reference site's colors. */
  --cv-surface: var(--brand-surface, #eef2f4);
  --cv-primary: var(--brand-primary, #16324a);
  --cv-accent: var(--brand-accent, #3d7a8a);

  background: var(--cv-surface);
  padding: clamp(3rem, 6vw, 5.5rem) clamp(1.25rem, 5vw, 3rem);
}

.core-values__inner {
  max-width: 1180px;
  margin-inline: auto;
}

.core-values__title {
  color: var(--cv-primary);
  font-size: clamp(1.6rem, 2.4vw, 2.2rem);
  font-weight: 700;
  margin: 0 0 0.5rem;
}

.core-values__subtitle {
  color: #45525a;
  margin: 0 0 2.5rem;
  max-width: 60ch;
}

.core-values__grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

@media (max-width: 720px) {
  .core-values__grid {
    grid-template-columns: 1fr;
  }
}

.value-card {
  position: relative;
  background: #fff;
  border-radius: 10px;
  padding: 1.75rem 1.75rem 1.75rem 2.25rem;
  box-shadow: 0 4px 16px rgba(20, 30, 40, 0.06);
  overflow: hidden;

  /* left accent bar, becomes full hover fill */
  isolation: isolate;

  opacity: 0;
  transform: translateY(24px);
  transition: opacity 0.5s ease, transform 0.5s ease, box-shadow 0.25s ease;
}

.value-card.is-visible {
  opacity: 1;
  transform: translateY(0);
}

.value-card::before {
  content: '';
  position: absolute;
  inset: 0;
  width: 6px;
  background: var(--cv-accent);
  z-index: -1;
  transition: width 0.35s ease;
}

.value-card:hover::before,
.value-card:focus-within::before {
  width: 100%;
}

.value-card:hover,
.value-card:focus-within {
  box-shadow: 0 8px 24px rgba(20, 30, 40, 0.12);
}

.value-card__icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: #fff;
  color: var(--cv-accent);
  box-shadow: 0 0 0 1px rgba(20, 30, 40, 0.06);
  margin-bottom: 0.9rem;
}

.value-card__title {
  color: var(--cv-primary);
  font-size: 1.1rem;
  font-weight: 700;
  margin: 0 0 0.5rem;
}

.value-card__desc {
  color: #45525a;
  font-size: 0.95rem;
  line-height: 1.55;
  margin: 0;
}

@media (prefers-reduced-motion: reduce) {
  .value-card {
    transition: none;
    opacity: 1;
    transform: none;
  }
  .value-card::before {
    transition: none;
  }
}
</style>
