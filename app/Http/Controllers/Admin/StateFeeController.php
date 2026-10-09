<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class StateFeeController extends Controller
{
    public function index(Request $request): Response
    {
        $query = State::orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $states = $query->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'code' => $s->code,
                'delivery_fee_naira' => $s->delivery_fee ? round($s->delivery_fee / 100, 2) : 0,
                'install_fee_naira' => $s->install_fee ? round($s->install_fee / 100, 2) : 0,
                'is_active' => (bool) $s->is_active,
            ];
        });

        return Inertia::render('Auth/StateFees/Index', [
            'states' => $states,
            'filters' => $request->only(['search']),
        ]);
    }

    public function update(Request $request, State $state): RedirectResponse
    {
        $validated = $request->validate([
            'delivery_fee_naira' => 'required|numeric|min:0',
            'install_fee_naira' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $state->update([
            'delivery_fee' => (int) round(((float) $validated['delivery_fee_naira']) * 100),
            'install_fee' => (int) round(((float) $validated['install_fee_naira']) * 100),
            'is_active' => $validated['is_active'] ?? $state->is_active,
        ]);

        return redirect()->back()->with('message', "Delivery & installation fees for {$state->name} updated successfully!");
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'states' => 'required|array',
            'states.*.id' => 'required|exists:states,id',
            'states.*.delivery_fee_naira' => 'required|numeric|min:0',
            'states.*.install_fee_naira' => 'required|numeric|min:0',
            'states.*.is_active' => 'boolean',
        ]);

        foreach ($validated['states'] as $row) {
            State::where('id', $row['id'])->update([
                'delivery_fee' => (int) round(((float) $row['delivery_fee_naira']) * 100),
                'install_fee' => (int) round(((float) $row['install_fee_naira']) * 100),
                'is_active' => $row['is_active'] ?? true,
            ]);
        }

        return redirect()->back()->with('message', 'All regional delivery and installation fees updated successfully!');
    }
}
