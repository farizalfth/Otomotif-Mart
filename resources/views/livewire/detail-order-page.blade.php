<div class="max-w-3xl mx-auto py-8 px-4">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-xl font-bold mb-4">Pesanan #{{$order->id}}</h1>
        
        <!-- Basic Info -->
        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
            <div>
                <span class="text-gray-600">Tanggal:</span> {{ $order->created_at->format('d M Y') }}
            </div>
            <div>
                <span class="text-gray-600">Status:</span> 
                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">{{ ucfirst($order->status) }}</span>
            </div>
            <div>
                <span class="text-gray-600">Customer:</span> {{ $order->address->first_name ?? '' }} {{ $order->address->last_name ?? '' }}
            </div>
            <div>
                <span class="text-gray-600">Pembayaran:</span> 
                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">{{ ucfirst($order->payment_status) }}</span>
            </div>
        </div>
        
        <!-- Products -->
        <div class="mb-6">
            <h2 class="font-semibold mb-3">Produk</h2>
            @foreach ($order->items as $item)
            <div class="flex items-center gap-3 py-2 border-b">
                <img class="w-12 h-12 object-cover rounded" 
                     src="{{ url('storage', $item->product->images[0]) }}" 
                     alt="{{ $item->product->name }}">
                <div class="flex-1">
                    <div class="font-medium">{{ $item->product->name }}</div>
                    <div class="text-sm text-gray-600">
                        {{ Number::currency($item->unit_amount, 'IDR') }} × {{ $item->quantity }}
                    </div>
                </div>
                <div class="font-semibold">{{ Number::currency($item->total_amount, 'IDR') }}</div>
            </div>
            @endforeach
        </div>
        
        <!-- Address -->
        <div class="mb-6">
            <h2 class="font-semibold mb-2">Alamat Pengiriman</h2>
            <div class="text-sm text-gray-700">
                <p>{{ $order->address->street_address ?? '' }}</p>
                <p>{{ $order->address->city ?? '' }}, {{ $order->address->state ?? '' }} {{ $order->address->zip_code ?? '' }}</p>
                <p>{{ $order->address->phone ?? '-' }}</p>
            </div>
        </div>
        
        <!-- Total -->
        <div class="border-t pt-4">
            <div class="flex justify-between items-center text-lg font-semibold">
                <span>Total</span>
                <span>{{ Number::currency($order->grand_total, 'IDR') }}</span>
            </div>
        </div>
        
        <!-- Back Button -->
        <div class="mt-6">
            <a href="/my-orders" class="inline-block px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                ← Kembali
            </a>
        </div>
    </div>
</div>