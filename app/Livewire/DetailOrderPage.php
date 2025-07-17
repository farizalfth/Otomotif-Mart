<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DetailOrderPage extends Component
{
    public $order;

    public function mount($order_id)
    {
        $this->order = Order::where('id', $order_id)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'address'])
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.detail-order-page', [
            'order' => $this->order,
        ]);
    }
}
