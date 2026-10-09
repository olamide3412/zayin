<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\State;
use App\Services\Seo\SeoData;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CommerceController extends Controller
{
    protected function getOrCreateCart(Request $request): Cart
    {
        $sessionId = $request->session()->getId();
        $cart = Cart::where('session_id', $sessionId)
            ->when(auth()->check(), fn($q) => $q->orWhere('user_id', auth()->id()))
            ->first();

        if (!$cart) {
            $cart = Cart::create([
                'session_id' => $sessionId,
                'user_id' => auth()->id(),
                'expires_at' => now()->addDays(7),
            ]);
        }

        return $cart;
    }

    public function showCart(Request $request): Response
    {
        $cart = $this->getOrCreateCart($request);
        $cart->load(['items.product.category']);

        $states = State::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code', 'delivery_fee', 'install_fee']);

        $seo = new SeoData(
            title: 'Your Cart',
            description: 'Review your selected solar systems, Starlink hardware, and DSTV kits.',
            robots: 'noindex,nofollow',
            breadcrumbs: [
                'Home' => url('/'),
                'Cart' => url('/cart'),
            ]
        );

        return Inertia::render('Commerce/Cart', [
            'cart' => $cart,
            'states' => $states,
            'seo' => $seo->toArray(),
        ]);
    }

    public function addToCart(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'nullable|integer|min:1|max:20',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        if ($product->requires_quote) {
            return redirect()->route('quotes.create')->with('message', 'This package requires a custom load survey.');
        }

        $cart = $this->getOrCreateCart($request);
        $qty = $validated['qty'] ?? 1;

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $item->update([
                'qty' => $item->qty + $qty,
                'unit_price' => $product->price,
            ]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'qty' => $qty,
                'unit_price' => $product->price,
            ]);
        }

        return redirect()->back()->with('success', "{$product->name} added to cart!");
    }

    public function updateCartItem(int $id, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'qty' => 'required|integer|min:1|max:50',
        ]);

        $item = CartItem::findOrFail($id);
        $item->update(['qty' => $validated['qty']]);

        return redirect()->back()->with('success', 'Cart updated.');
    }

    public function removeCartItem(int $id): RedirectResponse
    {
        $item = CartItem::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'Item removed from cart.');
    }

    public function checkout(Request $request): Response|RedirectResponse
    {
        $cart = $this->getOrCreateCart($request);
        $cart->load(['items.product.category']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Your cart is empty.');
        }

        $states = State::where('is_active', true)->orderBy('name')->get();

        $seo = new SeoData(
            title: 'Checkout & Order Summary',
            description: 'Complete your order with secure Paystack payment or direct company bank transfer.',
            robots: 'noindex,nofollow',
            breadcrumbs: [
                'Home' => url('/'),
                'Cart' => url('/cart'),
                'Checkout' => url('/checkout'),
            ]
        );

        return Inertia::render('Commerce/Checkout', [
            'cart' => $cart,
            'states' => $states,
            'seo' => $seo->toArray(),
        ]);
    }

    public function processCheckout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:120',
            'guest_phone' => 'required|string|max:20',
            'guest_email' => 'nullable|email|max:120',
            'state_id' => 'required|exists:states,id',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'landmark' => 'nullable|string|max:150',
            'fulfilment_type' => 'required|in:installation,delivery,pickup',
            'payment_method' => 'required|in:paystack,bank_transfer',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = $this->getOrCreateCart($request);
        $cart->load(['items.product']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Your cart is empty.');
        }

        $state = State::findOrFail($validated['state_id']);

        // Calculate totals server-side in kobo
        $subtotal = 0;
        foreach ($cart->items as $item) {
            $subtotal += ($item->product->price * $item->qty);
        }

        $deliveryFee = $validated['fulfilment_type'] === 'pickup' ? 0 : $state->delivery_fee;
        $installFee = $validated['fulfilment_type'] === 'installation' ? $state->install_fee : 0;
        $total = $subtotal + $deliveryFee + $installFee;

        $orderNumber = 'ZAY-' . date('Y') . '-' . strtoupper(Str::random(5));

        $order = DB::transaction(function () use ($validated, $cart, $state, $subtotal, $deliveryFee, $installFee, $total, $orderNumber) {
            $order = Order::create([
                'number' => $orderNumber,
                'user_id' => auth()->id(),
                'guest_name' => $validated['guest_name'],
                'guest_phone' => normalize_nigerian_phone($validated['guest_phone']),
                'guest_email' => $validated['guest_email'] ?? null,
                'status' => 'awaiting_payment',
                'payment_status' => 'unpaid',
                'fulfilment_type' => $validated['fulfilment_type'],
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'install_fee' => $installFee,
                'discount' => 0,
                'tax' => 0,
                'total' => $total,
                'amount_paid' => 0,
                'currency' => 'NGN',
                'state_id' => $state->id,
                'city' => $validated['city'],
                'address' => $validated['address'],
                'landmark' => $validated['landmark'] ?? null,
                'shipping_address' => [
                    'recipient' => $validated['guest_name'],
                    'phone' => $validated['guest_phone'],
                    'state' => $state->name,
                    'city' => $validated['city'],
                    'street' => $validated['address'],
                    'landmark' => $validated['landmark'] ?? '',
                ],
                'notes' => $validated['notes'] ?? null,
                'placed_at' => now(),
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'name_snapshot' => $item->product->name,
                    'sku_snapshot' => $item->product->sku,
                    'qty' => $item->qty,
                    'unit_price' => $item->product->price,
                    'total' => $item->product->price * $item->qty,
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'gateway' => $validated['payment_method'],
                'reference' => 'PAY-' . Str::random(12),
                'amount' => $total,
                'status' => 'initiated',
            ]);

            // Clear Cart items
            $cart->items()->delete();

            return $order;
        });

        if ($validated['payment_method'] === 'bank_transfer') {
            return redirect()->route('orders.bank_transfer', $order->number)->with('success', 'Order created successfully! Please transfer to our official bank account.');
        }

        return redirect()->route('orders.track', $order->number)->with('success', 'Order created! Proceed to payment.');
    }

    public function trackOrder(string $number): Response
    {
        $order = Order::where('number', $number)
            ->with(['items.product', 'state', 'payments'])
            ->firstOrFail();

        $seo = new SeoData(
            title: "Track Order #{$order->number}",
            description: "Check the status and fulfillment schedule of Order #{$order->number}.",
            robots: 'noindex,nofollow'
        );

        return Inertia::render('Commerce/TrackOrder', [
            'order' => $order,
            'seo' => $seo->toArray(),
        ]);
    }

    public function bankTransfer(string $number): Response
    {
        $order = Order::where('number', $number)
            ->with(['items', 'state', 'payments'])
            ->firstOrFail();

        $seo = new SeoData(
            title: "Bank Transfer Instructions - #{$order->number}",
            description: "Bank transfer details and proof upload for Order #{$order->number}.",
            robots: 'noindex,nofollow'
        );

        return Inertia::render('Commerce/BankTransfer', [
            'order' => $order,
            'seo' => $seo->toArray(),
        ]);
    }

    public function uploadProof(string $number, Request $request): RedirectResponse
    {
        $order = Order::where('number', $number)->firstOrFail();

        $request->validate([
            'proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $path = $request->file('proof_file')->store('proofs', 'public');

        $payment = $order->payments()->latest()->first();
        if ($payment) {
            $payment->update([
                'proof_file' => $path,
                'status' => 'pending',
            ]);
        }

        $order->update(['status' => 'awaiting_payment_confirmation']);

        return redirect()->route('orders.track', $order->number)->with('success', 'Payment proof uploaded! Our accounts team will verify and confirm your order shortly.');
    }
}
