<div class="max-w-lg mx-auto py-8 px-4">
    <div class="bg-white rounded-lg shadow-md p-6 text-center">
        <!-- Success Icon -->
        <div class="mb-4">
            <svg class="w-16 h-16 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>
        </div>
        
        <h1 class="text-xl font-bold text-gray-800 mb-6">
            Pesanan Berhasil!
        </h1>
        
        <!-- Order Details -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">No. Pesanan</span>
                <span class="font-semibold">{{$order->id}}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Total</span>
                <span class="font-semibold text-blue-600">{{Number::currency($order->grand_total, 'IDR')}}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Pembayaran</span>
                <span class="font-semibold">{{$order->payment_method == 'cod'? 'COD': 'Card'}}</span>
            </div>
        </div>
        
        <!-- Shipping Info -->
        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                </svg>
                <span class="font-semibold text-blue-800">Pengiriman 24 Jam</span>
            </div>
            <p class="text-sm text-gray-600 text-left">
                Ke: {{$order->address->full_name}}<br>
                {{$order->address->city}}, {{$order->address->state}}
            </p>
        </div>
        
        <!-- Action Buttons -->
        <div class="space-y-3">
            <a href="/my-orders" 
               class="block w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                Lihat Pesanan Saya
            </a>
            <a href="/products" 
               class="block w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Belanja Lagi
            </a>
        </div>
    </div>
</div>