<?php

namespace App\Http\Controllers;

use App\Models\Testimony;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestimonyController extends Controller
{
    // ─── Public Routes ────────────────────────────────────────────────────────

    /**
     * Public testimonials page (approved only).
     */
    public function publicIndex()
    {
        $testimonials = Testimony::approved()->paginate(12);

        return Inertia::render('Testimonials', [
            'testimonials' => $testimonials,
        ]);
    }

    /**
     * Submit a new testimony (public).
     */
    public function publicStore(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'occupation' => 'nullable|string|max:100',
            'rating'     => 'required|integer|min:1|max:5',
            'message'    => 'required|string|min:20|max:1000',
        ]);

        Testimony::create($validated); // status defaults to 'pending'

        return redirect()->back()->with('success', 'Thank you! Your testimony has been submitted and is awaiting review.');
    }

    // ─── Admin Routes ─────────────────────────────────────────────────────────

    /**
     * Admin testimonials list.
     */
    public function index(Request $request)
    {
        $query = Testimony::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return Inertia::render('Auth/Testimonials/Index', [
            'testimonials' => $query->paginate(20),
            'filters'      => $request->only(['status']),
            'counts' => [
                'pending'  => Testimony::pending()->count(),
                'approved' => Testimony::approved()->count(),
                'all'      => Testimony::count(),
            ],
        ]);
    }

    /**
     * Approve a testimony.
     */
    public function approve(Testimony $testimony)
    {
        $testimony->update(['status' => 'approved']);
        return redirect()->back()->with('message', 'Testimony approved and published.');
    }

    /**
     * Reject a testimony.
     */
    public function reject(Testimony $testimony)
    {
        $testimony->update(['status' => 'rejected']);
        return redirect()->back()->with('message', 'Testimony rejected.');
    }

    /**
     * Delete a testimony.
     */
    public function destroy(Testimony $testimony)
    {
        $testimony->delete();
        return redirect()->back()->with('message', 'Testimony deleted.');
    }
}
