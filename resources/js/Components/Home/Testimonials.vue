<script setup>
import { computed } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => []
    }
});

const defaultReviews = [
    {
        name: 'Chief Oladipo Adeleke',
        occupation: 'Business Executive, Lekki Phase 1, Lagos',
        rating: 5,
        message: 'Zayin installed a 5kVA hybrid solar system in my residence 8 months ago. I have completely stopped buying petrol for my generator during the day. The installation was neat, concealed, and finished in a single day.',
    },
    {
        name: 'Mrs. Ngozi Okonjo',
        occupation: 'Architect, Maitama, Abuja',
        rating: 5,
        message: 'Their Starlink rooftop installation was exceptional. They fabricated a clean steel pole that cleared the trees around my compound and set up mesh Wi-Fi. My whole family now streams with zero buffering.',
    },
    {
        name: 'Dr. Femi Adelekan',
        occupation: 'Medical Director, Bodija, Ibadan',
        rating: 5,
        message: 'We called Zayin for DSTV Extra View across three consulting rooms after two previous technicians failed to configure the heartbeat cable. Their technician sorted it in 45 minutes with crystal clear HD signal.',
    },
    {
        name: 'Barrister Chukwuma Obi',
        occupation: 'Managing Partner, Port Harcourt',
        rating: 5,
        message: 'Fast delivery to Port Harcourt and the technician was courteous and knowledgeable. The 3.5kVA system powers our legal chambers without any downtime.',
    },
];

const reviews = computed(() => {
    return props.items && props.items.length > 0 ? props.items : defaultReviews;
});
</script>

<template>
    <section class="py-16 sm:py-24 bg-slate-50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto mb-14" data-aos="fade-up">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-widest bg-amber-100/80 px-3.5 py-1.5 rounded-full inline-block mb-3">
                    Customer Reviews
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    Trusted by Nigerian Homes & Commercial Facilities
                </h2>
                <p class="mt-4 text-slate-600 text-base leading-relaxed">
                    Real feedback from verified clients enjoying uninterrupted solar power, high-speed Starlink broadband, and crisp DSTV entertainment.
                </p>
            </div>

            <!-- Reviews Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div 
                    v-for="(item, idx) in reviews" 
                    :key="idx"
                    class="rounded-3xl bg-white p-8 border border-slate-200/80 shadow-md hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                    data-aos="fade-up"
                    :data-aos-delay="idx * 100"
                >
                    <div class="space-y-4">
                        <!-- Rating Stars -->
                        <div class="flex items-center space-x-1 text-amber-400 text-sm">
                            <font-awesome-icon v-for="star in (item.rating || 5)" :key="star" :icon="['fas', 'star']" />
                        </div>

                        <!-- Quote Text -->
                        <p class="text-slate-700 text-sm sm:text-base leading-relaxed italic">
                            "{{ item.message }}"
                        </p>
                    </div>

                    <!-- Author Info -->
                    <div class="pt-6 border-t border-slate-100 mt-6 flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-full bg-slate-900 text-amber-400 font-bold flex items-center justify-center text-sm">
                            {{ item.name.charAt(0) }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-900 text-sm">{{ item.name }}</div>
                            <div class="text-xs text-slate-500">{{ item.occupation }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
