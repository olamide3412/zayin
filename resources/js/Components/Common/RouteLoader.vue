<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

// Initial state: true on initial page load
const loading = ref(true);

// Guards against rapid navigation where an earlier timeout could hide the loader of a pending navigation
let navId = 0;
let removeStart = null;
let removeFinish = null;
let removeCancel = null;
let removeError = null;

onMounted(() => {
    // Dismiss initial page load overlay smoothly
    setTimeout(() => {
        if (navId === 0) {
            loading.value = false;
        }
    }, 400);

    removeStart = router.on('start', () => {
        navId++;
        loading.value = true;
    });

    removeFinish = router.on('finish', () => {
        const currentNavId = navId;
        // Minimum display duration (250ms) to ensure smooth animation and prevent 1-frame flashes
        setTimeout(() => {
            if (currentNavId === navId) {
                loading.value = false;
            }
        }, 250);
    });

    removeCancel = router.on('cancel', () => {
        loading.value = false;
    });

    removeError = router.on('error', () => {
        loading.value = false;
    });
});

onUnmounted(() => {
    if (removeStart) removeStart();
    if (removeFinish) removeFinish();
    if (removeCancel) removeCancel();
    if (removeError) removeError();
});
</script>

<template>
    <Transition name="loader-fade">
        <div 
            v-if="loading" 
            class="route-loader-overlay" 
            role="status" 
            aria-live="polite" 
            aria-label="Loading Page"
        >
            <div class="loading-spinner"></div>
        </div>
    </Transition>
</template>

<style scoped>
.route-loader-overlay {
    position: fixed;
    inset: 0;
    width: 100vw;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--color-primary, #0A96EB);
    z-index: 99999;
    pointer-events: all;
    user-select: none;
}

.loading-spinner {
    width: 8vmax;
    height: 8vmax;
    max-width: 96px;
    max-height: 96px;
    min-width: 60px;
    min-height: 60px;
    border-right: 4px solid var(--color-primary-contrast, #ffffff);
    border-top: 4px solid transparent;
    border-bottom: 4px solid transparent;
    border-left: 4px solid transparent;
    border-radius: 50%;
    animation: spinRight 0.8s linear infinite;
    position: relative;
    box-sizing: border-box;
}

.loading-spinner::before {
    content: "";
    position: absolute;
    width: 6vmax;
    height: 6vmax;
    max-width: 72px;
    max-height: 72px;
    min-width: 44px;
    min-height: 44px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border-left: 3px solid var(--color-primary-contrast, #ffffff);
    border-top: 3px solid transparent;
    border-bottom: 3px solid transparent;
    border-right: 3px solid transparent;
    border-radius: 50%;
    animation: spinLeft 0.8s linear infinite;
    box-sizing: border-box;
}

.loading-spinner::after {
    content: "";
    position: absolute;
    width: 4vmax;
    height: 4vmax;
    max-width: 48px;
    max-height: 48px;
    min-width: 28px;
    min-height: 28px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    border-right: 2.5px solid var(--color-primary-contrast, #ffffff);
    border-top: 2.5px solid transparent;
    border-bottom: 2.5px solid transparent;
    border-left: 2.5px solid transparent;
    border-radius: 50%;
    box-sizing: border-box;
}

@keyframes spinRight {
    from {
        transform: rotate(360deg);
    }
    to {
        transform: rotate(0deg);
    }
}

@keyframes spinLeft {
    from {
        transform: translate(-50%, -50%) rotate(0deg);
    }
    to {
        transform: translate(-50%, -50%) rotate(720deg);
    }
}

.loader-fade-enter-active,
.loader-fade-leave-active {
    transition: opacity 0.2s ease-out;
}

.loader-fade-enter-from,
.loader-fade-leave-to {
    opacity: 0;
}
</style>
