<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    image: {
        type: String,
        default: '',
    },
    name: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true, // e.g. "Consultant Ophthalmologist & Vitreoretinal Surgeon"
    },
    specialty: {
        type: String,
        default: '',
    },
    qualifications: {
        type: String,
        default: '',
    },
    bio: {
        type: String,
        default: '',
    },
    appointmentUrl: {
        type: String,
        default: '',
    },
    colorDefault: {
        type: String,
        default: '#eef2ff', // Light periwinkle / lavender tint (Moorfields reference)
    },
    colorHover: {
        type: String,
        default: '#ffffff', // Solid white on hover
    },
});

const backgroundStyle = computed(() => ({
    background: `linear-gradient(to left, ${props.colorDefault} 50.1%, ${props.colorHover} 50%) right`,
    backgroundSize: '201% 100%',
}));
</script>

<template>
    <div
        class="team-card relative rounded-[12px] p-6 sm:p-7 overflow-hidden border border-gray-100/80 shadow-sm hover:shadow-xl transition-all duration-500 ease-out cursor-pointer flex flex-col justify-between"
        :style="backgroundStyle"
    >
        <div>
            <!-- Specialist Portrait -->
            <div class="relative w-full h-64 sm:h-72 rounded-[10px] overflow-hidden mb-5 bg-slate-200 shadow-inner">
                <img
                    v-if="image"
                    :src="image"
                    :alt="name"
                    class="w-full h-full object-cover object-top"
                    loading="lazy"
                />
                <div v-else class="w-full h-full bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center text-white">
                    <span class="text-4xl font-extrabold text-white/40">{{ name.charAt(0) }}</span>
                </div>

                <!-- Specialty badge if provided -->
                <div v-if="specialty" class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-[11px] font-bold text-primary shadow-sm">
                    {{ specialty }}
                </div>
            </div>

            <!-- Specialist Info -->
            <h3 class="text-xl font-extrabold text-[#303030] font-['Outfit',sans-serif] leading-tight">
                {{ name }}
            </h3>

            <p class="text-xs sm:text-sm font-semibold text-primary mt-1 leading-snug">
                {{ title }}
            </p>

            <p v-if="qualifications" class="text-[11px] text-slate-500 font-medium mt-1">
                {{ qualifications }}
            </p>

            <p v-if="bio" class="text-xs text-slate-600 mt-3 line-clamp-3 leading-relaxed">
                {{ bio }}
            </p>
        </div>

        <!-- Card Footer Actions -->
        <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center justify-between">
            <Link
                v-if="appointmentUrl"
                :href="appointmentUrl"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:text-primary-dark transition-colors"
            >
                <span>Book Consultation</span>
                <span>→</span>
            </Link>
            <span v-else class="text-xs font-bold text-primary">Senior Specialist</span>
            <span class="text-slate-400 text-xs">MSEH</span>
        </div>
    </div>
</template>

<style scoped>
.team-card {
    transition: background-position 0.5s ease-out, box-shadow 0.35s ease;
}

@media (hover: hover) {
    .team-card:hover {
        background-position: left !important;
    }
}
</style>
