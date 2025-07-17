<div class="max-w-7xl mx-auto p-6">
  <div class="flex gap-6">
    <!-- Sidebar -->
    <div class="w-1/4">
      <div class="bg-white p-4 rounded-lg mb-4">
        <h2 class="text-xl font-bold mb-4">Categories</h2>
        <ul>
          @foreach ($categories as $category)
          <li class="mb-2">
            <label class="flex items-center">
              <input type="checkbox" wire:model.live="selected_categories" value="{{$category->id}}" class="mr-2">
              <span>{{$category->name}}</span>
            </label>
          </li>
          @endforeach
        </ul>
      </div>
    </div>

    <!-- Products -->
    <div class="w-3/4">

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($products as $product)
        <div class="bg-white border rounded-lg overflow-hidden">
          <a href="/products/{{ $product->slug }}" wire:navigate>
            <img src="{{url('storage', $product->images[0])}}" alt="{{$product->name}}" class="w-full h-48 object-cover">
          </a>
          <div class="p-4">
            <h3 class="text-lg font-medium mb-2">{{$product->name}}</h3>
            <p class="text-green-600 font-bold">{{ rupiah($product->price) }}</p>
            <button wire:click='addToCart({{$product->id}})' class="mt-2 w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
              <span wire:loading.remove wire:target="addToCart({{$product->id}})">Add to Cart</span>
              <span wire:loading wire:target="addToCart({{$product->id}})">Adding...</span>
            </button>
          </div>
        </div>
        @endforeach
      </div>

      <div class="mt-6">
        {{$products->links()}}
      </div>
    </div>
  </div>
</div>