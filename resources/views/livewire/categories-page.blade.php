<div class="max-w-6xl mx-auto p-6">
  <div class="grid md:grid-cols-2 gap-4">  
    @foreach ($categories as $category)
    <a href="{{ url('/products') }}?category={{ $category->slug }}" class="flex items-center p-4 bg-white border rounded-lg hover:shadow-md transition">
      <img class="h-16 w-16 rounded" src="{{ url('storage', $category->image) }}" alt="{{ $category->name }}">
      <h3 class="ml-4 text-xl font-semibold text-gray-800 hover:text-blue-600">
        {{ $category->name }}
      </h3>
    </a>
    @endforeach
  </div>
</div>