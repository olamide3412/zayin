<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $business = Business::current();

        $query = Customer::where('business_id', $business->id)->withCount('invoices');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('tin', 'like', "%{$search}%");
            });
        }

        $perPage = max(25, (int) $request->input('per_page', 25));
        $customers = $query->latest()->paginate($perPage)->withQueryString();

        return Inertia::render('Invoices/Customers/Index', [
            'customers' => $customers,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Lightweight JSON search for the invoice customer picker.
     * Returns at most 25 matches (name, phone, email or TIN).
     */
    public function search(Request $request): JsonResponse
    {
        $business = Business::current();
        $term = trim((string) $request->query('q', ''));

        $query = Customer::where('business_id', $business->id)
            ->select('id', 'name', 'phone', 'email', 'address', 'tin');

        if ($term !== '') {
            $digits = preg_replace('/\D+/', '', $term);
            $query->where(function ($q) use ($term, $digits) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%")
                  ->orWhere('tin', 'like', "%{$term}%");

                // Match Nigerian numbers typed as 0803... against stored +234 803... and vice versa
                if (strlen($digits) >= 4) {
                    $local = ltrim(preg_replace('/^234/', '', $digits), '0');
                    $q->orWhereRaw(
                        "REPLACE(REPLACE(REPLACE(phone, ' ', ''), '+', ''), '-', '') LIKE ?",
                        ["%{$local}%"]
                    );
                }
            })
            // Names starting with the term rank first, then alphabetical
            ->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', ["{$term}%"])
            ->orderBy('name');
        } else {
            $query->latest('id');
        }

        return response()->json($query->limit(25)->get());
    }

    public function store(Request $request): RedirectResponse
    {
        $business = Business::current();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'tin' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Customer::create(array_merge($validated, ['business_id' => $business->id]));

        return back()->with('success', 'Customer created successfully.');
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'tin' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $customer->update($validated);

        return back()->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->invoices()->exists()) {
            return back()->with('error', 'Cannot delete customer with existing invoices.');
        }

        $customer->delete();

        return back()->with('success', 'Customer deleted successfully.');
    }
}
