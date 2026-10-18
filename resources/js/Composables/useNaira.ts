export function useNaira() {
    /**
     * Format a kobo integer (or numeric naira) into standard Nigerian Naira string: ₦1,250,000.00
     */
    function formatKobo(kobo: number | null | undefined, includeSymbol: boolean = true): string {
        if (kobo === null || kobo === undefined || isNaN(kobo)) {
            return includeSymbol ? '₦0.00' : '0.00';
        }
        const naira = kobo / 100;
        const formatted = naira.toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
        return includeSymbol ? `₦${formatted}` : formatted;
    }

    /**
     * Format a Naira number into ₦1,250,000.00
     */
    function formatNaira(naira: number | null | undefined, includeSymbol: boolean = true): string {
        if (naira === null || naira === undefined || isNaN(naira)) {
            return includeSymbol ? '₦0.00' : '0.00';
        }
        const formatted = Number(naira).toLocaleString('en-NG', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
        return includeSymbol ? `₦${formatted}` : formatted;
    }

    /**
     * Convert Naira amount to integer Kobo
     */
    function toKobo(naira: number | string | null | undefined): number {
        if (!naira) return 0;
        const num = typeof naira === 'string' ? parseFloat(naira.replace(/,/g, '')) : naira;
        return Math.round((num || 0) * 100);
    }

    /**
     * Convert Kobo to Naira float
     */
    function toNaira(kobo: number | null | undefined): number {
        if (!kobo) return 0;
        return Math.round(kobo) / 100;
    }

    return {
        formatKobo,
        formatNaira,
        toKobo,
        toNaira,
    };
}
