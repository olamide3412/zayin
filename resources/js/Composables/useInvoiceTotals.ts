import { computed, Ref } from 'vue';

export interface InvoiceItemInput {
    id?: number;
    catalog_id?: number | null;
    name: string;
    description?: string | null;
    quantity: number;
    unit?: string;
    unit_price?: number; // In Naira for input form
    unit_price_kobo?: number; // In Kobo
    discount_type?: 'none' | 'percentage' | 'fixed';
    discount_value?: number;
    taxable?: boolean;
}

export interface ComputedInvoiceTotals {
    subtotalKobo: number;
    subtotalNaira: number;
    totalLineDiscountsKobo: number;
    invoiceDiscountKobo: number;
    invoiceDiscountNaira: number;
    vatableSubtotalKobo: number;
    vatKobo: number;
    vatNaira: number;
    shippingKobo: number;
    shippingNaira: number;
    totalKobo: number;
    totalNaira: number;
    itemTotals: Array<{
        grossKobo: number;
        discountKobo: number;
        lineTotalKobo: number;
        lineTotalNaira: number;
    }>;
}

export function useInvoiceTotals(
    items: Ref<InvoiceItemInput[]>,
    vatMode: Ref<'exclusive' | 'inclusive'>,
    vatRateBps: Ref<number>,
    invoiceDiscountType: Ref<'none' | 'percentage' | 'fixed'>,
    invoiceDiscountValue: Ref<number>,
    shippingNaira: Ref<number>
) {
    const totals = computed<ComputedInvoiceTotals>(() => {
        let subtotalKobo = 0;
        let totalLineDiscountsKobo = 0;
        let vatableBaseKobo = 0;

        const itemTotals = items.value.map((item) => {
            const qty = Math.max(0, Number(item.quantity) || 0);
            const unitKobo = item.unit_price !== undefined && item.unit_price !== null
                ? Math.max(0, Math.round((Number(item.unit_price) || 0) * 100))
                : Math.max(0, Math.round(Number(item.unit_price_kobo) || 0));

            const grossKobo = Math.round(qty * unitKobo);

            let discKobo = 0;
            const discType = item.discount_type || 'none';
            const discVal = Number(item.discount_value) || 0;

            if (discType === 'percentage' && discVal > 0) {
                const bounded = Math.min(100, Math.max(0, discVal));
                discKobo = Math.round((grossKobo * bounded) / 100);
            } else if (discType === 'fixed' && discVal > 0) {
                discKobo = Math.min(grossKobo, Math.round(discVal * 100));
            }

            const lineTotalKobo = Math.max(0, grossKobo - discKobo);

            subtotalKobo += lineTotalKobo;
            totalLineDiscountsKobo += discKobo;

            if (Boolean(item.taxable)) {
                vatableBaseKobo += lineTotalKobo;
            }

            return {
                grossKobo,
                discountKobo: discKobo,
                lineTotalKobo,
                lineTotalNaira: lineTotalKobo / 100,
            };
        });

        // Invoice-level discount
        let invoiceDiscountKobo = 0;
        const invDiscType = invoiceDiscountType.value || 'none';
        const invDiscVal = Number(invoiceDiscountValue.value) || 0;

        if (invDiscType === 'percentage' && invDiscVal > 0) {
            const bounded = Math.min(100, Math.max(0, invDiscVal));
            invoiceDiscountKobo = Math.round((subtotalKobo * bounded) / 100);
        } else if (invDiscType === 'fixed' && invDiscVal > 0) {
            invoiceDiscountKobo = Math.min(subtotalKobo, Math.round(invDiscVal * 100));
        }

        // Net vatable base after invoice-level discount
        let netVatableBaseKobo = vatableBaseKobo;
        if (subtotalKobo > 0 && invoiceDiscountKobo > 0) {
            const vatableShare = vatableBaseKobo / subtotalKobo;
            const discountedPortion = Math.round(invoiceDiscountKobo * vatableShare);
            netVatableBaseKobo = Math.max(0, vatableBaseKobo - discountedPortion);
        }

        // VAT calculation
        const rateBps = Math.max(0, Number(vatRateBps.value) || 0);
        let vatKobo = 0;

        if (rateBps > 0 && netVatableBaseKobo > 0) {
            if (vatMode.value === 'inclusive') {
                vatKobo = Math.round((netVatableBaseKobo * rateBps) / (10000 + rateBps));
            } else {
                vatKobo = Math.round((netVatableBaseKobo * rateBps) / 10000);
            }
        }

        const shippingKobo = Math.max(0, Math.round((Number(shippingNaira.value) || 0) * 100));

        let totalKobo = 0;
        if (vatMode.value === 'inclusive') {
            totalKobo = Math.max(0, subtotalKobo - invoiceDiscountKobo + shippingKobo);
        } else {
            totalKobo = Math.max(0, subtotalKobo - invoiceDiscountKobo + vatKobo + shippingKobo);
        }

        return {
            subtotalKobo,
            subtotalNaira: subtotalKobo / 100,
            totalLineDiscountsKobo,
            invoiceDiscountKobo,
            invoiceDiscountNaira: invoiceDiscountKobo / 100,
            vatableSubtotalKobo: netVatableBaseKobo,
            vatKobo,
            vatNaira: vatKobo / 100,
            shippingKobo,
            shippingNaira: shippingKobo / 100,
            totalKobo,
            totalNaira: totalKobo / 100,
            itemTotals,
        };
    });

    return {
        totals,
    };
}
