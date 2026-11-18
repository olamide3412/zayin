<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessBankAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BusinessProfileController extends Controller
{
    public function edit(): Response
    {
        $business = Business::current();
        $bankAccounts = BusinessBankAccount::where('business_id', $business->id)->orderBy('sort_order')->get();

        return Inertia::render('Invoices/Settings/BusinessProfile', [
            'business' => $business,
            'bankAccounts' => $bankAccounts,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $business = Business::current();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'rc_number' => ['nullable', 'string', 'max:50'],
            'tin' => ['nullable', 'string', 'max:50'],
            'show_tin' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'string', 'max:50'],
            'phone_alt' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'vat_rate_bps' => ['required', 'integer', 'min:0'],
            'default_vat_mode' => ['required', 'in:exclusive,inclusive'],
            'default_terms' => ['nullable', 'string'],
            'default_notes' => ['nullable', 'string'],
            'footer_text' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'signature' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($business->logo_path) {
                Storage::disk('public')->delete($business->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('invoices/branding', 'public');
        }

        if ($request->hasFile('signature')) {
            if ($business->signature_path) {
                Storage::disk('public')->delete($business->signature_path);
            }
            $validated['signature_path'] = $request->file('signature')->store('invoices/branding', 'public');
        }

        unset($validated['logo'], $validated['signature']);

        $business->update($validated);

        return back()->with('success', 'Business profile updated successfully.');
    }

    public function storeBankAccount(Request $request): mixed
    {
        $business = Business::current();

        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_name' => ['required', 'string', 'max:150'],
            'account_number' => ['required', 'string', 'max:50'],
            'is_default' => ['boolean'],
        ]);

        if (!empty($validated['is_default'])) {
            BusinessBankAccount::where('business_id', $business->id)->update(['is_default' => false]);
        }

        $sortOrder = BusinessBankAccount::where('business_id', $business->id)->max('sort_order') + 1;

        $account = BusinessBankAccount::create(array_merge($validated, [
            'business_id' => $business->id,
            'sort_order' => $sortOrder,
        ]));

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'Bank account added successfully.',
                'account' => $account,
                'accounts' => BusinessBankAccount::where('business_id', $business->id)->orderBy('sort_order')->get(),
            ]);
        }

        return back()->with('success', 'Bank account added successfully.');
    }

    public function updateBankAccount(Request $request, BusinessBankAccount $account): mixed
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_name' => ['required', 'string', 'max:150'],
            'account_number' => ['required', 'string', 'max:50'],
            'is_default' => ['boolean'],
        ]);

        if (!empty($validated['is_default'])) {
            BusinessBankAccount::where('business_id', $account->business_id)
                ->where('id', '!=', $account->id)
                ->update(['is_default' => false]);
        }

        $account->update($validated);

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'Bank account updated successfully.',
                'account' => $account,
                'accounts' => BusinessBankAccount::where('business_id', $account->business_id)->orderBy('sort_order')->get(),
            ]);
        }

        return back()->with('success', 'Bank account updated successfully.');
    }

    public function destroyBankAccount(Request $request, BusinessBankAccount $account): mixed
    {
        $businessId = $account->business_id;
        $account->delete();

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'Bank account removed.',
                'accounts' => BusinessBankAccount::where('business_id', $businessId)->orderBy('sort_order')->get(),
            ]);
        }

        return back()->with('success', 'Bank account removed.');
    }
}
