<div class="max-w-6xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-6">Shopping Cart</h1>
    
    <div class="grid md:grid-cols-3 gap-6">
        <!-- Cart Items -->
        <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
            @forelse ($cart_items as $item)
                <div wire:key="{{$item['product_id']}}" class="flex items-center justify-between border-b pb-4 mb-4 last:border-b-0">
                    <!-- Product Info -->
                    <div class="flex items-center">
                        <img class="w-16 h-16 rounded mr-4" src="{{url('storage', $item['image'])}}" alt="{{$item['name']}}">
                        <div>
                            <h3 class="font-semibold">{{$item['name']}}</h3>
                            <p class="text-gray-600">{{Number::currency($item['unit_amount'], 'IDR')}}</p>
                        </div>
                    </div>
                    
                    <!-- Quantity & Total -->
                    <div class="flex items-center gap-4">
                        <div class="flex items-center border rounded">
                            <button wire:click='decreaseItem({{$item['product_id']}})' class="px-3 py-1 hover:bg-gray-100">-</button>
                            <span class="px-3 py-1 border-x">{{$item['quantity']}}</span>
                            <button wire:click='increaseItem({{$item['product_id']}})' class="px-3 py-1 hover:bg-gray-100">+</button>
                        </div>
                        
                        <span class="font-semibold min-w-24">{{Number::currency($item['total_amount'], 'IDR')}}</span>
                        
                        <button wire:click='removeItem({{$item['product_id']}})' class="text-red-500 hover:text-red-700 ml-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                    </svg>
                    <p class="text-xl text-gray-500">Keranjang kosong</p>
                </div>
            @endforelse
        </div>
        
        <!-- Summary -->
        <div class="bg-white rounded-lg shadow p-6 h-fit">
            <h2 class="text-xl font-semibold mb-4">Ringkasan</h2>
            <div class="flex justify-between mb-2">
                <span>Subtotal</span>
                <span>{{Number::currency($grand_total, 'IDR')}}</span>
            </div>
            <hr class="my-3">
            <div class="flex justify-between mb-4">
                <span class="font-semibold text-lg">Total</span>
                <span class="font-semibold text-lg">{{Number::currency($grand_total, 'IDR')}}</span>
            </div>
            @if ($cart_items)
                <a href="/checkout" class="block w-full bg-blue-500 text-white text-center py-3 rounded-lg hover:bg-blue-600 transition">
                    Checkout
                </a>
            @endif
        </div>
    </div>
</div>