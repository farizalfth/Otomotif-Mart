<div>
{{-- Hero Section Start --}}
<section class="relative min-h-screen bg-white">
    <div class="absolute inset-0">
        <img src="https://1.bp.blogspot.com/-_LM1045MusY/XiVIn55_FfI/AAAAAAAABtY/plMjdfc0HhU1KESBpDS7h0LHq8oJ5PmkgCLcBGAsYHQ/s1600/r15v3mods___B7AnLtXnAy6___.jpg"
            alt="Motor Service Workshop" 
            class="w-full h-full object-cover"
            style="filter: brightness(0.4);">
    </div>
    
    <div class="relative z-10 flex items-center min-h-screen">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                Bandung Jaya <span class="text-blue-400">Motor</span>
            </h1>
            <p class="text-xl text-gray-200 mb-8 max-w-2xl mx-auto">
                Bengkel motor terpercaya dengan pelayanan profesional dan berkualitas
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#location" class="bg-white/10 hover:bg-white/20 text-white px-8 py-3 rounded-lg font-medium transition-colors backdrop-blur-sm">
                    Lokasi Kami
                </a>
            </div>
        </div>
    </div>
</section>
{{-- Hero Section End --}}

{{-- Products Section Start --}}
<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Produk Kami</h2>
            <p class="text-gray-600">Temukan berbagai produk berkualitas untuk motor Anda</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($products as $product)
            <div class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <a href="/products/{{ $product->slug }}" wire:navigate>
                    <img src="{{url('storage', $product->images[0])}}" alt="{{$product->name}}" class="w-full h-48 object-cover">
                </a>
                <div class="p-4">
                    <h3 class="text-lg font-medium mb-2">{{$product->name}}</h3>
                    <p class="text-green-600 font-bold mb-3">{{ rupiah($product->price) }}</p>
                    <button wire:click='addToCart({{$product->id}})' class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors">
                        <span wire:loading.remove wire:target="addToCart({{$product->id}})">Add to Cart</span>
                        <span wire:loading wire:target="addToCart({{$product->id}})">Adding...</span>
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-center">
            {{$products->links()}}
        </div>
    </div>
</section>
{{-- Products Section End --}}

{{-- Location Section Start --}}
<section id="location" class="py-16 bg-gray-50">
    <div class="max-w-4xl mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Lokasi Bengkel</h2>
            <p class="text-gray-600">Temukan kami di lokasi yang mudah dijangkau</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Address -->
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Alamat</h3>
                            <div class="text-gray-600 space-y-1">
                                <p>Jl. Sakti</p>
                                <p>Kelurahan Bandung, Tunon</p>
                                <p>Kota Tegal, Jawa Tengah</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Telepon</h3>
                            <p class="text-gray-600">+62 859-5281-9036 </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Jam Buka</h3>
                            <p class="text-gray-600">Senin - Sabtu: 08:00 - 17:00 WIB</p>
                        </div>
                    </div>
                </div>
                
                <!-- Map -->
                <div class="space-y-4">
                    <div class="rounded-lg overflow-hidden shadow-sm">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!3m2!1sid!2sid!4v1752756649195!5m2!1sid!2sid!6m8!1m7!1swCAvSAhWWCB7sEWn1CeLbA!2m2!1d-6.892969295795404!2d109.1184843059287!3f14.999004503703942!4f-8.891066634693004!5f0.7820865974627469" 
                            width="100%" 
                            height="300" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                    <a href="https://maps.google.com/maps?q=Tegal,+Jawa+Tengah" 
                    target="_blank" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        <span>Petunjuk Arah</span>
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>
{{-- Location Section End --}}
</div>