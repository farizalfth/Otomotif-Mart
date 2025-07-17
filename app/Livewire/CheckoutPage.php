<?php

namespace App\Livewire;

use Stripe\Stripe;
use App\Models\Order;
use App\Models\Address;
use Livewire\Component;
use App\Mail\OrderPlaced;
use Stripe\Checkout\Session;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use App\Helpers\CartManagement;
use Illuminate\Support\Facades\Mail;
use App\Services\HubUmkmService; 

class CheckoutPage extends Component
{
    use WithFileUploads;

    public $first_name;
    public $last_name;
    public $phone;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $payment_method = 'manual'; 
    public $payment_proof;

    public function mount()
    {
        $cart_items = CartManagement::getCartItemsFromCookie();

        if (count($cart_items) == 0) {
            return redirect('/products');
        }
    }

    public function render()
    {
        $cart_items = CartManagement::getCartItemsFromCookie();
        $grand_total = CartManagement::calculateGrandTotal($cart_items);

        return view('livewire.checkout-page', [
            'cart_items' => $cart_items,
            'grand_total' => $grand_total,
        ]);
    }

    public function save(HubUmkmService $hubService)
    {
        $rules = [
            'first_name'     => 'required',
            'last_name'      => 'required',
            'phone'          => 'required',
            'address'        => 'required',
            'city'           => 'required',
            'state'          => 'required',
            'zip_code'       => 'required',
            'payment_method' => 'required',
        ];

        if ($this->payment_method !== 'stripe') {
            $rules['payment_proof'] = 'required|image|max:2048';
        }

        $this->validate($rules);

        $cart_items = CartManagement::getCartItemsFromCookie();

        $order = new Order();
        $order->user_id = auth()->user()->id;
        $order->grand_total = CartManagement::calculateGrandTotal($cart_items);
        $order->payment_method = $this->payment_method;
        $order->payment_status = 'pending'; 
        $order->status = 'new';
        $order->currency = 'idr';
        $order->shipping_amount = 0;
        $order->shipping_method = 'none';
        $order->notes = 'Order Placed By ' . auth()->user()->name;

        if ($this->payment_method !== 'stripe') {
            $path = $this->payment_proof->store('payment_proofs', 'public');
            $order->payment_proof = $path;
            $redirect_url = route('success');
        } else {

            Stripe::setApiKey(env('STRIPE_SECRET'));

            $line_items = [];
            foreach ($cart_items as $item) {
                $line_items[] = [
                    'price_data' => [
                        'currency' => 'idr',
                        'unit_amount' => $item['unit_amount'] * 100,
                        'product_data' => [
                            'name' => $item['name']
                        ],
                    ],
                    'quantity' => $item['quantity'],
                ];
            }

            $sessionCheckout = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => auth()->user()->email,
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => route('success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('cancel'),
            ]);

            $redirect_url = $sessionCheckout->url;
        }

        $order->save(); 
        $address = new Address();
        $address->order_id = $order->id;
        $address->first_name = $this->first_name;
        $address->last_name = $this->last_name;
        $address->phone = $this->phone;
        $address->street_address = $this->address;
        $address->city = $this->city;
        $address->state = $this->state;
        $address->zip_code = $this->zip_code;
        $address->save(); 

        foreach ($cart_items as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_amount' => $item['unit_amount'],
                'total_amount' => $item['unit_amount'] * $item['quantity'],
            ]);
        }

        // $hubService->sendOrderToHub($order);

        CartManagement::clearCartItems();


        return redirect($redirect_url);
    }
}