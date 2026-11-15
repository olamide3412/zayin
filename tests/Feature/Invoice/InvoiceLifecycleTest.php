<?php

namespace Tests\Feature\Invoice;

use App\Actions\Invoice\DuplicateInvoiceAction;
use App\Actions\Invoice\IssueInvoiceAction;
use App\Actions\Invoice\RecordPaymentAction;
use App\Actions\Invoice\SaveInvoiceDraftAction;
use App\Actions\Invoice\UpdateIssuedInvoiceAction;
use App\Actions\Invoice\VoidInvoiceAction;
use App\Models\Business;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceTemplate;
use App\Models\User;
use App\Services\Invoice\DomPdfInvoiceRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private Business $business;
    private Customer $customer;
    private InvoiceTemplate $template;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->business = Business::firstOrCreate(
            ['name' => 'Zayin Ltd'],
            [
                'rc_number' => 'RC 10005044',
                'tin' => 'TIN-28491048-0001',
                'phone' => '+234 803 123 4567',
                'email' => 'invoicing@zayin.com.ng',
                'vat_rate_bps' => 750,
                'default_vat_mode' => 'exclusive',
            ]
        );

        $this->customer = Customer::create([
            'business_id' => $this->business->id,
            'name' => 'Alhaji Musa Danjuma',
            'email' => 'musa@danjuma.ng',
            'phone' => '+234 802 334 4556',
            'address' => 'Victoria Island, Lagos',
        ]);

        $this->template = InvoiceTemplate::firstOrCreate(
            ['business_id' => $this->business->id, 'layout' => 'classic'],
            [
                'name' => 'Classic Professional',
                'accent_color' => '#1e3a8a',
                'document_title' => 'INVOICE',
                'number_prefix' => 'ZYN-',
                'number_padding' => 4,
                'is_default' => true,
            ]
        );
    }

    public function test_full_invoice_lifecycle(): void
    {
        $saveDraft = app(SaveInvoiceDraftAction::class);
        $issueAction = app(IssueInvoiceAction::class);
        $updateIssuedAction = app(UpdateIssuedInvoiceAction::class);
        $recordPaymentAction = app(RecordPaymentAction::class);
        $voidAction = app(VoidInvoiceAction::class);
        $duplicateAction = app(DuplicateInvoiceAction::class);
        $renderer = app(DomPdfInvoiceRenderer::class);

        // 1. Create Draft
        $draft = $saveDraft->execute($this->business, [
            'customer_id' => $this->customer->id,
            'template_id' => $this->template->id,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'vat_mode' => 'exclusive',
            'vat_rate_bps' => 750,
            'items' => [
                [
                    'name' => '5kVA Felicity Solar Inverter',
                    'quantity' => 1,
                    'unit' => 'pcs',
                    'unit_price_kobo' => 85000000, // ₦850,000.00
                    'taxable' => true,
                ],
            ],
        ], null, $this->user->id);

        $this->assertSame(Invoice::STATUS_DRAFT, $draft->status);
        $this->assertNull($draft->invoice_number);
        $this->assertSame(85000000, $draft->subtotal_kobo);
        $this->assertSame(6375000, $draft->vat_kobo);
        $this->assertSame(91375000, $draft->total_kobo);

        // 2. Issue Invoice (Assigns gapless number & customer snapshot)
        $issued = $issueAction->execute($draft);
        $this->assertSame(Invoice::STATUS_ISSUED, $issued->status);
        $this->assertNotNull($issued->invoice_number);
        $this->assertStringStartsWith('ZYN-', $issued->invoice_number);
        $this->assertNotNull($issued->issued_at);
        $this->assertSame('Alhaji Musa Danjuma', $issued->customer_snapshot['name']);

        // 3. Edit Issued Invoice (Creates revision audit record)
        $revised = $updateIssuedAction->execute($issued, [
            'customer_id' => $this->customer->id,
            'template_id' => $this->template->id,
            'issue_date' => $issued->issue_date->toDateString(),
            'vat_mode' => 'exclusive',
            'vat_rate_bps' => 750,
            'items' => [
                [
                    'name' => '5kVA Felicity Solar Inverter',
                    'quantity' => 1,
                    'unit' => 'pcs',
                    'unit_price_kobo' => 85000000,
                    'taxable' => true,
                ],
                [
                    'name' => 'Installation Labor',
                    'quantity' => 1,
                    'unit' => 'service',
                    'unit_price_kobo' => 5000000, // ₦50,000.00
                    'taxable' => false,
                ],
            ],
        ], $this->user->id, 'Added installation service to contract');

        $this->assertSame(2, $revised->revision_number);
        $this->assertCount(1, $revised->revisions);
        $this->assertSame('Added installation service to contract', $revised->revisions->first()->reason);

        // 4. Record Partial Payment
        $pay1 = $recordPaymentAction->execute($revised, [
            'amount_kobo' => 50000000, // ₦500,000.00
            'paid_at' => now(),
            'method' => 'bank_transfer',
            'reference' => 'ZEN-9284102',
        ], $this->user);

        $revised->refresh();
        $this->assertSame(Invoice::STATUS_PARTIALLY_PAID, $revised->status);
        $this->assertSame(50000000, $revised->amount_paid_kobo);

        // 5. Duplicate Invoice (Clones into new draft)
        $duplicatedDraft = $duplicateAction->execute($revised, $this->user);
        $this->assertSame(Invoice::STATUS_DRAFT, $duplicatedDraft->status);
        $this->assertNull($duplicatedDraft->invoice_number);
        $this->assertNotSame($revised->id, $duplicatedDraft->id);
        $this->assertCount(2, $duplicatedDraft->items);

        // 6. Test PDF Rendering
        $pdfContent = $renderer->render($revised);
        $this->assertNotEmpty($pdfContent);
        $this->assertStringStartsWith('%PDF', $pdfContent);

        // 7. Test Public URL access
        $response = $this->get(route('invoices.public', $revised->public_token));
        $response->assertOk();

        // 8. Void Invoice
        $voided = $voidAction->execute($revised, 'Client cancelled order due to building delay');
        $this->assertSame(Invoice::STATUS_VOID, $voided->status);
        $this->assertSame('Client cancelled order due to building delay', $voided->void_reason);
    }

    public function test_invoice_can_be_created_and_rendered_without_bank_accounts(): void
    {
        $saveDraftAction = app(SaveInvoiceDraftAction::class);
        $renderer = app(DomPdfInvoiceRenderer::class);

        $invoice = $saveDraftAction->execute($this->business, [
            'customer_id' => $this->customer->id,
            'template_id' => $this->template->id,
            'issue_date' => now()->toDateString(),
            'vat_mode' => 'exclusive',
            'vat_rate_bps' => 750,
            'bank_account_ids' => [], // Explicitly no bank accounts
            'items' => [
                [
                    'name' => 'Consultation Service',
                    'quantity' => 1,
                    'unit_price_kobo' => 2000000,
                    'unit' => 'hrs',
                ],
            ],
        ], userId: $this->user->id);

        $this->assertSame([], $invoice->bank_account_ids);

        // Verify PDF rendering without bank accounts and with company logo alongside company name
        $html = $renderer->renderHtml($invoice);
        $this->assertStringNotContainsString('Payment Bank Accounts:', $html);
        $this->assertStringContainsString('class="company-logo"', $html);
        $this->assertStringContainsString('data:image/png;base64,', $html);
        $this->assertStringContainsString('Zayin Ltd', $html);

        $pdf = $renderer->render($invoice);
        $this->assertNotEmpty($pdf);
        $this->assertStringStartsWith('%PDF', $pdf);
    }

    public function test_invoice_item_suggestion_endpoint_returns_catalog_items(): void
    {
        \App\Models\InvoiceItemCatalog::create([
            'business_id' => $this->business->id,
            'name' => '5kVA Solar Inverter',
            'normalized_name' => \App\Models\InvoiceItemCatalog::normalizeName('5kVA Solar Inverter'),
            'description' => 'Pure Sine Wave',
            'unit' => 'pcs',
            'default_price_kobo' => 85000000,
            'last_price_kobo' => 85000000,
            'is_vatable' => false,
            'category' => 'Inverters',
            'usage_count' => 5,
            'is_archived' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/invoice-items/suggest?q=solar');

        $response->assertOk()
            ->assertJsonFragment([
                'name' => '5kVA Solar Inverter',
                'unit' => 'pcs',
                'unit_price' => 850000,
            ]);
    }

    public function test_business_profile_and_template_toggle_tin_visibility(): void
    {
        $saveDraftAction = app(SaveInvoiceDraftAction::class);
        $renderer = app(DomPdfInvoiceRenderer::class);

        $invoice = $saveDraftAction->execute($this->business, [
            'customer_id' => $this->customer->id,
            'template_id' => $this->template->id,
            'issue_date' => now()->toDateString(),
            'vat_mode' => 'exclusive',
            'vat_rate_bps' => 750,
            'items' => [
                [
                    'name' => 'Diagnostic Assessment',
                    'quantity' => 1,
                    'unit_price_kobo' => 1000000,
                    'unit' => 'visit',
                ],
            ],
        ], userId: $this->user->id);

        // 1. By default with show_tin = true and template tin = true, TIN is shown
        $html = $renderer->renderHtml($invoice);
        $this->assertStringContainsString('TIN-28491048-0001', $html);

        // 2. Turn off show_tin on business profile
        $this->actingAs($this->user)
            ->put(route('invoices.settings.business.update'), [
                'name' => 'Zayin Ltd',
                'rc_number' => 'RC 10005044',
                'tin' => 'TIN-28491048-0001',
                'show_tin' => false,
                'vat_rate_bps' => 750,
                'default_vat_mode' => 'exclusive',
            ])->assertSessionHasNoErrors();

        $this->business->refresh();
        $this->assertFalse((bool) $this->business->show_tin);

        $htmlWithoutTin = $renderer->renderHtml($invoice->fresh());
        $this->assertStringNotContainsString('TIN-28491048-0001', $htmlWithoutTin);
        $this->assertStringContainsString('RC 10005044', $htmlWithoutTin);

        // 3. Turn show_tin back on on business, but toggle it off on the template
        $this->business->update(['show_tin' => true]);
        $this->actingAs($this->user)
            ->put(route('invoices.settings.templates.update', $this->template->id), [
                'name' => $this->template->name,
                'layout' => $this->template->layout,
                'accent_color' => $this->template->accent_color,
                'document_title' => $this->template->document_title,
                'number_prefix' => $this->template->number_prefix,
                'number_padding' => $this->template->number_padding,
                'show_fields' => [
                    'rc_number' => true,
                    'tin' => false,
                ],
            ])->assertSessionHasNoErrors();

        $htmlTemplateNoTin = $renderer->renderHtml($invoice->fresh());
        $this->assertStringNotContainsString('TIN-28491048-0001', $htmlTemplateNoTin);
    }
}
