<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\InvoiceTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TemplateController extends Controller
{
    public function index(): Response
    {
        $business = Business::current();
        $templates = InvoiceTemplate::where('business_id', $business->id)->get();

        return Inertia::render('Invoices/Settings/Templates', [
            'templates' => $templates,
        ]);
    }

    public function update(Request $request, InvoiceTemplate $template): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'layout' => ['required', 'in:classic,modern,minimal'],
            'accent_color' => ['required', 'string', 'max:20'],
            'document_title' => ['required', 'string', 'max:50'],
            'number_prefix' => ['required', 'string', 'max:20'],
            'number_padding' => ['required', 'integer', 'min:3', 'max:8'],
            'show_watermark' => ['boolean'],
            'is_default' => ['boolean'],
            'show_fields' => ['nullable', 'array'],
        ]);

        if (!empty($validated['is_default'])) {
            InvoiceTemplate::where('business_id', $template->business_id)
                ->where('id', '!=', $template->id)
                ->update(['is_default' => false]);
        }

        $template->update($validated);

        return back()->with('success', 'Template settings updated.');
    }
}
