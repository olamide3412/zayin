<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\State;
use App\Services\Seo\SeoData;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class BookingController extends Controller
{
    public function create(): Response
    {
        $services = Service::published()->get(['id', 'name', 'slug']);
        $states = State::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);

        $seo = new SeoData(
            title: 'Book an Installation or Site Survey',
            description: 'Schedule a certified technician to visit your home or office for solar survey, Starlink installation, or DSTV setup.',
            breadcrumbs: [
                'Home' => url('/'),
                'Book Installation' => url('/book-installation'),
            ]
        );

        return Inertia::render('Booking/Create', [
            'services' => $services,
            'states' => $states,
            'seo' => $seo->toArray(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:120',
            'address' => 'required|string|max:255',
            'landmark' => 'nullable|string|max:150',
            'state_id' => 'required|exists:states,id',
            'city' => 'nullable|string|max:100',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_slot' => 'required|string|in:morning,afternoon',
            'service_type' => 'required|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        $reference = 'ZAY-B-' . strtoupper(Str::random(6));

        $booking = Booking::create([
            'reference' => $reference,
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'phone' => normalize_nigerian_phone($validated['phone']),
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'],
            'landmark' => $validated['landmark'] ?? null,
            'state_id' => $validated['state_id'],
            'city' => $validated['city'] ?? null,
            'preferred_date' => $validated['preferred_date'],
            'preferred_slot' => $validated['preferred_slot'],
            'service_type' => $validated['service_type'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        $serviceLabels = [
            'solar_installation' => 'Solar & Inverter Installation',
            'solar_maintenance' => 'Solar Cleaning & Maintenance',
            'starlink_installation' => 'Starlink Rooftop Mast Setup',
            'dstv_installation' => 'DSTV Installation & Extra View',
            'dish_realignment' => 'DSTV Dish Realignment',
        ];
        $serviceTitle = $serviceLabels[$booking->service_type] ?? Str::headline($booking->service_type);
        $dateFormatted = $booking->preferred_date ? $booking->preferred_date->format('d M, Y') : $validated['preferred_date'];

        $whatsAppMsg = "Hello Zayin, I have booked a service visit ({$serviceTitle}) for {$dateFormatted} ({$booking->preferred_slot}). My Booking Reference is {$reference}.";
        $whatsappNumber = \App\Models\Setting::get('whatsapp') ?: config('services.whatsapp.number', '2348031234567');
        $whatsappLink = whatsapp_url($whatsappNumber, $whatsAppMsg);

        return redirect()->back()->with([
            'success' => "Booking confirmed! Your appointment reference is {$reference}.",
            'booking_reference' => $reference,
            'whatsapp_link' => $whatsappLink,
        ]);
    }
}
