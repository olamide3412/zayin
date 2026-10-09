<script setup>
import Layout from '@/Layouts/Layout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';

defineOptions({ layout: Layout });

const props = defineProps({
    pageSlug: {
        type: String,
        required: true,
    },
    pageTitle: {
        type: String,
        required: true,
    },
    seo: {
        type: Object,
        default: () => ({}),
    },
});

const legalLinks = [
    { slug: 'privacy-policy', label: 'Privacy Policy (NDPA 2023)' },
    { slug: 'terms', label: 'Terms of Service' },
    { slug: 'warranty', label: 'Warranty & Quality Guarantee' },
    { slug: 'refund-policy', label: 'Refund & Cancellation Policy' },
];
</script>

<template>
    <SeoHead :seo="seo" />

    <div class="bg-slate-50 min-h-screen py-10 lg:py-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <nav class="flex items-center text-xs text-slate-500 mb-6">
                <Link href="/" class="hover:text-amber-500 transition-colors">Home</Link>
                <span class="mx-2">/</span>
                <span class="text-slate-800 font-medium">{{ pageTitle }}</span>
            </nav>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                <!-- Sidebar -->
                <div class="md:col-span-4 bg-white rounded-2xl p-5 border border-slate-200 shadow-2xs sticky top-24">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Legal &amp; Compliance</div>
                    <nav class="space-y-1">
                        <Link
                            v-for="item in legalLinks"
                            :key="item.slug"
                            :href="route('legal', item.slug)"
                            :class="[
                                'block px-3.5 py-2.5 rounded-xl text-xs font-bold transition',
                                pageSlug === item.slug
                                    ? 'bg-amber-500 text-navy-950 font-extrabold shadow-2xs'
                                    : 'text-slate-600 hover:bg-slate-100 hover:text-navy-900'
                            ]"
                        >
                            {{ item.label }}
                        </Link>
                    </nav>

                    <div class="mt-6 pt-4 border-t border-slate-100 text-[11px] text-slate-400">
                        Zayin Technologies Limited is registered with the Corporate Affairs Commission (CAC) of the Federal Republic of Nigeria.
                    </div>
                </div>

                <!-- Main Content -->
                <div class="md:col-span-8 bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm prose prose-slate max-w-none prose-headings:text-navy-950 prose-headings:font-bold prose-a:text-amber-600">
                    <h1 class="text-2xl sm:text-3xl font-black text-navy-950 mb-2 not-prose">{{ pageTitle }}</h1>
                    <p class="text-xs text-slate-400 mb-8 not-prose">Last Updated: January 2025 &bull; In effect for all clients and site deployments across Nigeria</p>

                    <!-- Content conditional on pageSlug -->
                    <div v-if="pageSlug === 'privacy-policy'" class="space-y-4 text-sm text-slate-600 leading-relaxed">
                        <h2 class="text-lg font-bold text-navy-900">1. Commitment to NDPA 2023</h2>
                        <p>
                            Zayin Technologies Limited ("we", "our", "us") is committed to safeguarding the privacy and personal data of our customers, website visitors, and corporate clients in accordance with the <strong>Nigeria Data Protection Act (NDPA) 2023</strong> and international best practices.
                        </p>

                        <h2 class="text-lg font-bold text-navy-900">2. Information We Collect</h2>
                        <p>We only collect information necessary to fulfill installation orders, conduct engineering site audits, and deliver technical support. This includes:</p>
                        <ul class="list-disc pl-5 space-y-1 text-xs">
                            <li>Name, phone number, and physical deployment address / GPS coordinates</li>
                            <li>Electrical appliance inventory details provided in quote forms</li>
                            <li>Order history and proof of payment slips</li>
                        </ul>

                        <h2 class="text-lg font-bold text-navy-900">3. How Your Data is Used</h2>
                        <p>
                            Your information is never sold or rented. We share site addresses exclusively with our vetted field engineers and logistics partners for dispatch purposes. Communication via WhatsApp and SMS is conducted only regarding your active service requests or system status updates.
                        </p>

                        <h2 class="text-lg font-bold text-navy-900">4. Data Security &amp; Retention</h2>
                        <p>
                            All customer databases and payment references are protected by modern encryption (TLS 1.3, AES-256). You have the right under NDPA to request access to, correction of, or permanent deletion of your stored records by contacting <a href="mailto:privacy@zayin.ng">privacy@zayin.ng</a>.
                        </p>
                    </div>

                    <div v-else-if="pageSlug === 'terms'" class="space-y-4 text-sm text-slate-600 leading-relaxed">
                        <h2 class="text-lg font-bold text-navy-900">1. Service Agreements &amp; Quotations</h2>
                        <p>
                            All engineering quotations issued by Zayin Technologies are valid for a period of fourteen (14) days from the date of issuance, subject to foreign exchange adjustments on imported Tier-1 equipment (lithium batteries, inverters, solar panels).
                        </p>

                        <h2 class="text-lg font-bold text-navy-900">2. Site Preparation &amp; Access</h2>
                        <p>
                            The client agrees to grant safe physical access to roof structures, distribution boards (DBs), and wiring risers during agreed installation windows. Our technicians reserve the right to pause site work if safety hazards (such as ungrounded high voltage or structurally compromised roofing) are discovered until rectified.
                        </p>

                        <h2 class="text-lg font-bold text-navy-900">3. Equipment Ownership</h2>
                        <p>
                            Title and ownership of all supplied hardware remains with Zayin Technologies until payment in full has cleared into our corporate accounts. Risk of loss transfers upon physical handover and client sign-off.
                        </p>
                    </div>

                    <div v-else-if="pageSlug === 'warranty'" class="space-y-4 text-sm text-slate-600 leading-relaxed">
                        <h2 class="text-lg font-bold text-navy-900">1. Comprehensive Hardware Warranties</h2>
                        <p>
                            All products supplied by Zayin Technologies come with authentic manufacturer warranties:
                        </p>
                        <ul class="list-disc pl-5 space-y-1 text-xs">
                            <li><strong>Lithium-Iron Phosphate (LiFePO4) Batteries:</strong> 5 to 10-year manufacturer warranty (6,000+ deep cycles at 80% DoD).</li>
                            <li><strong>Pure Sine Wave Inverters:</strong> 2 to 5-year replacement / factory repair warranty depending on brand tier (Victron, Deye, Felicity).</li>
                            <li><strong>Monocrystalline Solar Panels:</strong> 12-year product warranty &amp; 25-year 80% linear power output guarantee.</li>
                            <li><strong>Starlink &amp; Networking Hardware:</strong> 12-month standard hardware replacement warranty.</li>
                        </ul>

                        <h2 class="text-lg font-bold text-navy-900">2. 12-Month Workmanship Guarantee</h2>
                        <p>
                            Zayin Technologies guarantees all cabling, conduit work, earthing connections, breaker calibrations, and roof mounting brackets for 12 calendar months from handover. If any connection loosens or fails under normal operating conditions, our technicians will repair it at zero labor charge.
                        </p>

                        <h2 class="text-lg font-bold text-navy-900">3. Warranty Exclusions</h2>
                        <p>
                            Warranties do not cover damage caused by deliberate overloading beyond rated capacity, unauthorized third-party tampering, lightning strikes where surge protection was declined, or catastrophic natural floods.
                        </p>
                    </div>

                    <div v-else-if="pageSlug === 'refund-policy'" class="space-y-4 text-sm text-slate-600 leading-relaxed">
                        <h2 class="text-lg font-bold text-navy-900">1. Equipment Returns</h2>
                        <p>
                            Clients may return unopened, undamaged equipment in its original factory packaging within seven (7) days of delivery for a full refund minus applicable logistics fees.
                        </p>

                        <h2 class="text-lg font-bold text-navy-900">2. Cancellation of Installation Bookings</h2>
                        <p>
                            Site inspection deposits or scheduled technician bookings may be rescheduled or cancelled with at least 24 hours notice prior to technician dispatch. Cancellations after a team has arrived on site incur a standard mobilization fee.
                        </p>

                        <h2 class="text-lg font-bold text-navy-900">3. Refund Processing</h2>
                        <p>
                            Approved refunds are credited directly to the originating Nigerian bank account within 3 to 5 business days after inspection of returned hardware.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
