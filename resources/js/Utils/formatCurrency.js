export function formatCurrency(value, decimals = 2) {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(value);
}

export function formatNaira(amountInKobo, includeDecimals = false) {
    if (amountInKobo === undefined || amountInKobo === null || isNaN(amountInKobo)) {
        return '₦0';
    }
    const naira = Number(amountInKobo) / 100;
    return '₦' + new Intl.NumberFormat('en-NG', {
        minimumFractionDigits: includeDecimals ? 2 : 0,
        maximumFractionDigits: includeDecimals ? 2 : 0
    }).format(naira);
}

export function koboToNaira(amountInKobo) {
    return (Number(amountInKobo || 0)) / 100;
}

export function nairaToKobo(amountInNaira) {
    return Math.round(Number(amountInNaira || 0) * 100);
}
