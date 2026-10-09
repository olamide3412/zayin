# PAYMENTS_AND_ORDERS.md

## 1. Principles
- Money in **kobo** (integers). Currency fixed to NGN.
- Server computes all totals from DB prices; never trust client values.
- An order is **Paid** only after server-side verification or a verified webhook.
- Everything idempotent: same reference or event never processed twice.

## 2. Cart
- Guest cart by session id; user cart by `user_id`; merge on login.
- `CartService`: add, update, remove, clear, totals. Item price is re-read from the product at
  checkout; show a notice if price changed.
- Products with `requires_quote = true` cannot be added to cart; show Request Quote instead.

## 3. Checkout Flow
1. Customer details (name, email, phone) and address (state → city/LGA → street, landmark).
2. Fulfilment: delivery only, delivery + installation, or pickup. Fees from `states` table.
3. Review totals: subtotal + delivery + installation + VAT (configurable, default 7.5%) − discount.
4. Choose payment: Paystack, bank transfer, or deposit (if enabled for the product).
5. `PlaceOrder` action (DB transaction): create order + items + status `awaiting_payment`,
   decrement/reserve stock if tracked, create `payments` row (`initiated`).
6. Redirect to Paystack (or show bank details).

## 4. Paystack Integration
- Initialise: `POST https://api.paystack.co/transaction/initialize` with `email`, `amount` (kobo),
  `reference` (our unique reference), `callback_url`, `metadata` (order number).
- Callback `/payment/callback?reference=…`: call **verify** endpoint server-side, check
  `status == success`, **amount and currency match the order**, then mark paid.
- Webhook `/webhooks/paystack`: read raw body, verify `x-paystack-signature` = HMAC-SHA512(body,
  secret key); reject on mismatch; store in `webhook_events` (unique event id); dispatch job to
  process (`charge.success`, `refund.processed`, etc.); return 200 quickly.
- Keys in `.env`: `PAYSTACK_PUBLIC_KEY`, `PAYSTACK_SECRET_KEY`, `PAYSTACK_WEBHOOK_SECRET` (if used).
  Use test keys on non-prod. Verify exact API shapes in Paystack's current docs.
- Handle: abandoned payments (expire after N hours), failed payments (allow retry with a new
  reference), duplicate callbacks, partial amounts (flag as underpaid for admin review).

## 5. Bank Transfer (Manual)
- Show account name/number/bank (from settings) + order reference as narration.
- Customer uploads proof (image/PDF ≤ 5MB). Status `awaiting_payment_confirmation`.
- Admin approves → `payments.status = success`, `approved_by`, order → `paid`; or rejects with reason.
- Emails/notifications at each step.

## 6. Deposits (optional, solar)
Product flag `allow_deposit` + `deposit_percent`. First payment = deposit; order
`payment_status = partial`; balance payable via a generated Paystack link or transfer before
or on installation. Admin can record cash/transfer payments manually.

## 7. Order Lifecycle
```
pending → awaiting_payment → paid → processing → scheduled → delivered/installed → completed
                │                                        └→ cancelled / refunded
```
Each change writes `order_status_histories` and triggers notifications. Enums in
`App\Enums\OrderStatus` and `PaymentStatus` with allowed transitions enforced in one place.

## 8. Notifications (queued)
- Customer: order received, payment confirmed, scheduled/dispatched, completed, invoice PDF.
- Admin/Sales: new order, proof uploaded, new quote/booking, failed payment.
- Channels: mail; SMS (e.g. Termii or similar Nigerian provider); WhatsApp link/notification
  where supported by the WhatsApp Business API provider chosen.

## 9. Invoices & Receipts
PDF (dompdf) with company details, order lines, VAT line, payment status, TIN/RC number if
the client supplies them. Numbering: `INV-YYYY-000001` sequential, no gaps handled via DB
sequence/lock.

## 10. Refunds & Returns
Refund policy page is mandatory. Admin records refund (gateway refund via Paystack API or
manual); order → `refunded`; stock restored if applicable; audit logged.

## 11. Tests (must exist)
Totals and fees; stock; price tamper attempt; Paystack verify success/failure/amount-mismatch;
webhook signature valid/invalid; webhook replay idempotency; guest + auth checkout; bank
transfer approval; status transition rules.
