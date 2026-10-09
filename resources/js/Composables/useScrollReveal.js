import { ref, onMounted, onUnmounted } from 'vue';

/**
 * Lightweight scroll reveal composable matching the moorfields.ae entrance animation pattern.
 * Uses IntersectionObserver to trigger a one-time 'visible' state once threshold is met.
 * 
 * @param {Object} options
 * @param {number} options.threshold - Viewport intersection ratio (default: 0.15)
 * @param {string} options.rootMargin - Margin around the root (default: '0px 0px -50px 0px')
 * @param {boolean} options.once - Whether to trigger only once (default: true)
 */
export function useScrollReveal(options = {}) {
    const {
        threshold = 0.15,
        rootMargin = '0px 0px -40px 0px',
        once = true,
    } = options;

    const targetRef = ref(null);
    const isVisible = ref(false);
    let observer = null;

    onMounted(() => {
        if (typeof window === 'undefined' || !('IntersectionObserver' in window)) {
            isVisible.value = true;
            return;
        }

        observer = new IntersectionObserver(([entry]) => {
            if (entry.isIntersecting) {
                isVisible.value = true;
                if (once && targetRef.value) {
                    observer.unobserve(targetRef.value);
                    observer.disconnect();
                }
            } else if (!once) {
                isVisible.value = false;
            }
        }, { threshold, rootMargin });

        if (targetRef.value) {
            observer.observe(targetRef.value);
        }
    });

    onUnmounted(() => {
        if (observer) {
            observer.disconnect();
        }
    });

    return {
        targetRef,
        isVisible,
    };
}
