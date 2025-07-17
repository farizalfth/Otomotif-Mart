<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>
    
    <form wire:submit.prevent="save">
        <div class="bg-white rounded-lg p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Form Data -->
                <div class="space-y-6">
                    <!-- Data Pembeli -->
                    <div>
                        <h2 class="text-lg font-semibold mb-4">Data Pembeli</h2>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <input wire:model="first_name" type="text" placeholder="Nama Depan" 
                                       class="w-full border rounded-lg px-3 py-2 @error('first_name') border-red-500 @enderror">
                                @error('first_name')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                            
                            <div>
                                <input wire:model="last_name" type="text" placeholder="Nama Belakang" 
                                       class="w-full border rounded-lg px-3 py-2 @error('last_name') border-red-500 @enderror">
                                @error('last_name')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <input wire:model="phone" type="tel" placeholder="Nomor Telepon" 
                                   class="w-full border rounded-lg px-3 py-2 @error('phone') border-red-500 @enderror">
                            @error('phone')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        
                        <div class="mb-4">
                            <input wire:model="address" type="text" placeholder="Alamat" 
                                   class="w-full border rounded-lg px-3 py-2 @error('address') border-red-500 @enderror">
                            @error('address')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <input wire:model="city" type="text" placeholder="Kota" 
                                       class="w-full border rounded-lg px-3 py-2 @error('city') border-red-500 @enderror">
                                @error('city')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                            
                            <div>
                                <input wire:model="state" type="text" placeholder="Provinsi" 
                                       class="w-full border rounded-lg px-3 py-2 @error('state') border-red-500 @enderror">
                                @error('state')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                            
                            <div>
                                <input wire:model="zip_code" type="text" placeholder="Kode Pos" 
                                       class="w-full border rounded-lg px-3 py-2 @error('zip_code') border-red-500 @enderror">
                                @error('zip_code')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pembayaran -->
                    <div class="border-t pt-6">
                        <h2 class="text-lg font-semibold mb-4">Pembayaran</h2>
                        
                        <div class="mb-4">
                            <img src="{{ asset('images/QRIS.png') }}" alt="QRIS" class="w-40 mx-auto rounded-lg mb-4">
                            
                            <select wire:model="payment_method" 
                                    class="w-full border rounded-lg px-3 py-2 @error('payment_method') border-red-500 @enderror">
                                <option value="">-- Pilih E-Wallet --</option>
                                <option value="DANA">DANA</option>
                                <option value="OVO">OVO</option>
                                <option value="GoPay">GoPay</option>
                                <option value="ShopeePay">ShopeePay</option>
                                <option value="LinkAja">LinkAja</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                            @error('payment_method')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Upload Bukti Pembayaran</label>
                            <input type="file" wire:model="payment_proof" accept="image/*" 
                                   class="w-full border rounded-lg px-3 py-2">
                            @error('payment_proof')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="border-l pl-6">
                    <h3 class="text-lg font-semibold mb-4">Ringkasan Pesanan</h3>
                    
                    <!-- Produk -->
                    <div class="space-y-3 mb-6">
                        @foreach ($cart_items as $item)
                        <div class="flex items-center gap-3" wire:key="{{ $item['product_id'] }}">
                            <img src="{{ url('storage', $item['image']) }}" alt="{{ $item['name'] }}" 
                                 class="w-10 h-10 rounded object-cover">
                            <div class="flex-1">
                                <p class="font-medium text-sm">{{ $item['name'] }}</p>
                                <p class="text-xs text-gray-500">Qty: {{ $item['quantity'] }}</p>
                            </div>
                            <span class="font-semibold text-sm">{{ Number::currency($item['total_amount'], 'IDR') }}</span>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Total -->
                    <div class="border-t pt-4">
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span class="font-semibold">{{ Number::currency($grand_total, 'IDR') }}</span>
                            </div>
                            <p class="text-xs text-gray-500">Ongkir akan dihitung berdasarkan lokasi</p>
                            <hr>
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span>{{ Number::currency($grand_total, 'IDR') }}</span>
                            </div>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-lg">
                            Pesan Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>