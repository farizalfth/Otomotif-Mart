<?php

namespace App\Livewire;

use App\Models\Brand;
use Livewire\Component;
use App\Models\Category;
use App\Models\Product;
use Livewire\WithPagination;

class HomePage extends Component
{
    use WithPagination;

    public function addToCart($productId)
    {
        // Cek apakah keranjang sudah ada di sesi
        $cart = session()->get('cart', []);

        // Cek apakah produk sudah ada di keranjang
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $product = Product::find($productId);
            $cart[$productId] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->images[0], // Pastikan ada image yang valid
            ];
        }

        // Simpan kembali ke sesi
        session()->put('cart', $cart);

        // Menampilkan notifikasi
        session()->flash('message', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function render()
    {
        $brands = Brand::where('is_active', 1)->get();
        $categories = Category::where('is_active', 1)->get();
        $products = Product::where('is_active', 1)->paginate(12);

        return view('livewire.home-page', compact('brands', 'categories', 'products'));
    }
}
