<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Pesanan Saya</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach ($orders as $order)
                    @php
                        $status_colors = [
                            'new' => 'bg-blue-100 text-blue-800',
                            'processing' => 'bg-yellow-100 text-yellow-800',
                            'shipped' => 'bg-green-100 text-green-800',
                            'delivered' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800'
                        ];
                    @endphp
                    
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-800">Order #{{$order->id}}</h3>
                                <p class="text-sm text-gray-600">{{$order->created_at->format('d M Y')}}</p>
                            </div>
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $status_colors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Total:</span>
                                <span class="font-semibold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Pembayaran:</span>
                                <span class="font-semibold">
                                    @if($order->payment_status == 'paid')
                                        <span class="text-green-600">Lunas</span>
                                    @elseif($order->payment_status == 'pending')
                                        <span class="text-yellow-600">Pending</span>
                                    @else
                                        <span class="text-red-600">Gagal</span>
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-600">Tracking:</span>
                                @if($order->tracking_number)
                                    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{$order->tracking_number}}</span>
                                @else
                                    <span class="text-gray-500">Belum tersedia</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-4 flex justify-end">
                            <a href="/my-orders/{{$order->id}}" 
                               class="px-4 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition-colors">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6">
                {{$orders->links()}}
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-800 mb-2">Belum ada pesanan</h3>
                <p class="text-gray-600 mb-4">Anda belum memiliki pesanan apapun</p>
                <a href="/products" class="inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                    Mulai Belanja
                </a>
            </div>
        @endif
    </div>
</div>