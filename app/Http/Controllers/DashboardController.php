<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Order;
use App\Models\QuoteRequest;
use App\Models\Testimony;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'awaiting_payment')->orWhere('status', 'awaiting_payment_confirmation')->count(),
            'new_quotes' => QuoteRequest::where('status', 'new')->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'testimonials' => Testimony::count(),
        ];

        return Inertia::render('Auth/Dashboard', [
            'stats' => $stats
        ]);
    }
}
