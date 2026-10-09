<?php

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

if (!function_exists('log_new')) {
    function log_new ($log_msg) {
        Log::create([
            'log' => $log_msg,
            'user_id' => Auth::check() ? Auth::user()->id : null
        ]);
    }
}

if (!function_exists('format_naira')) {
    function format_naira($kobo, bool $includeDecimals = false): string {
        $naira = ((int) $kobo) / 100;
        return '₦' . number_format($naira, $includeDecimals ? 2 : 0);
    }
}

if (!function_exists('kobo_to_naira')) {
    function kobo_to_naira($kobo): float {
        return ((int) $kobo) / 100;
    }
}

if (!function_exists('naira_to_kobo')) {
    function naira_to_kobo($naira): int {
        return (int) round(((float) $naira) * 100);
    }
}

if (!function_exists('normalize_nigerian_phone')) {
    function normalize_nigerian_phone(?string $phone): string {
        if (!$phone) return '';
        $digits = preg_replace('/[^\d]/', '', $phone);
        if (str_starts_with($digits, '234') && strlen($digits) === 13) {
            return '+' . $digits;
        }
        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            return '+234' . substr($digits, 1);
        }
        if (strlen($digits) === 10) {
            return '+234' . $digits;
        }
        return '+' . $digits;
    }
}

if (!function_exists('whatsapp_url')) {
    function whatsapp_url(?string $phone, string $message = ''): string {
        $normalized = preg_replace('/[^\d]/', '', normalize_nigerian_phone($phone ?: '2348000000000'));
        $query = $message ? '?text=' . urlencode($message) : '';
        return "https://wa.me/{$normalized}{$query}";
    }
}
