<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Address;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache; 

class HubUmkmService
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $sellerId;
    protected $oauthClientId;       
    protected $oauthClientSecret;  

    public function __construct()
    {
        $this->baseUrl = config('services.hub_umkm.base_url');
        $this->clientId = config('services.hub_umkm.client_id');
        $this->clientSecret = config('services.hub_umkm.client_secret');
        $this->sellerId = config('services.hub_umkm.seller_id');
        $this->oauthClientId = config('services.hub_umkm.oauth_client_id', $this->clientId); 
        $this->oauthClientSecret = config('services.hub_umkm.oauth_client_secret', $this->clientSecret); 
    }


    protected function getAccessToken(): ?string
    {
        if (Cache::has('hub_umkm_access_token')) {
            return Cache::get('hub_umkm_access_token');
        }

       
        if (!$this->oauthClientId || !$this->oauthClientSecret) {
            Log::error('Kredensial OAuth (HUB_UMKM_OAUTH_CLIENT_ID/SECRET) tidak diatur.');
            return null;
        }

        try {
            $response = Http::asForm()->post($this->baseUrl . '/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->oauthClientId,
                'client_secret' => $this->oauthClientSecret,
                'scope' => '*', 
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'];
                $expiresIn = $data['expires_in'] ?? 3600;
                Cache::put('hub_umkm_access_token', $accessToken, now()->addSeconds($expiresIn - 60)); 
                
                return $accessToken;
            }

            Log::error('Gagal mendapatkan access token dari Hub UMKM', ['status' => $response->status(), 'body' => $response->json()]);
            return null;

        } catch (\Exception $e) {
            Log::error('EXCEPTION SAAT MENDAPATKAN ACCESS TOKEN DARI HUB UMKM', ['message' => $e->getMessage()]);
            return null;
        }
    }


    public function syncProduct(Product $product): array
    {
        if (!$product->category || !$product->category->hub_id) {
            $errorMessage = 'Sinkronisasi produk ID: ' . $product->id . ' gagal karena kategori terkait belum memiliki hub_id.';
            Log::error($errorMessage);
            return ['error' => $errorMessage];
        }

        $payload = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'seller_product_id' => (string) $product->id,
            'name' => $product->name,
            'description' => $product->description ?? '',
            'price' => (float) $product->price,
            'stock' => $product->in_stock ? 100 : 0,
            'category_id' => (float) $product->category->hub_id,
            'is_active' => (bool) $product->is_active,
            'image_url' => isset($product->images[0]) ? asset('storage/' . $product->images[0]) : null,
        ];
        
        Log::info('MENGIRIM PAYLOAD PRODUK KE HUB UMKM:', $payload);
        try {
            $response = Http::post($this->baseUrl . '/product/sync', $payload);
            if (!$response->successful()) { 
                Log::error('GAGAL SINKRONISASI PRODUK KE HUB UMKM', ['product_id' => $product->id, 'response_status' => $response->status(), 'response_body' => $response->json() ?? $response->body()]); 
                return ['error' => 'Sync failed', 'status' => $response->status()];
            } else { 
                $responseData = $response->json();
                Log::info('BERHASIL SINKRONISASI PRODUK', ['response' => $responseData]); 
                if (isset($responseData['product_id']) && $product->hub_id != $responseData['product_id']) {
                    $product->hub_id = $responseData['product_id'];
                    $product->saveQuietly();
                    Log::info('Hub ID berhasil disimpan/diperbarui untuk produk lokal ID: ' . $product->id . ' -> Hub ID: ' . $product->hub_id);
                }
                return $responseData;
            }
        } catch (\Exception $e) { 
            Log::error('EXCEPTION SAAT MENGHUBUNGI API HUB UMKM', ['message' => $e->getMessage()]); 
            return ['error' => $e->getMessage()];
        }
    }


    public function syncCategory(Category $category): array
    {
        $payload = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'seller_product_category_id' => (string) $category->id,
            'name' => $category->name,
            'description' => '', 
            'is_active' => (bool) $category->is_active,
        ];
        Log::info('MENGIRIM PAYLOAD KATEGORI KE HUB UMKM:', $payload);
        try {
            $response = Http::post($this->baseUrl . '/product-category/sync', $payload);
            if (!$response->successful()) {
                Log::error('GAGAL SINKRONISASI KATEGORI KE HUB UMKM', ['category_id' => $category->id, 'response_status' => $response->status(), 'response_body' => $response->json() ?? $response->body()]);
                return ['error' => 'Sync category failed', 'status' => $response->status()];
            }
            $responseData = $response->json();
            Log::info('BERHASIL SINKRONISASI KATEGORI', ['response' => $responseData]);
            if (isset($responseData['product_category_id'])) {
                $category->hub_id = $responseData['product_category_id'];
                $category->saveQuietly();
                Log::info('Hub ID berhasil disimpan untuk kategori lokal ID: ' . $category->id);
            }
            return $responseData;
        } catch (\Exception $e) {
            Log::error('EXCEPTION SAAT MENGHUBUNGI API HUB UMKM', ['message' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }


    public function getMyProductsFromHub(): ?array
    {
        if (!$this->sellerId) {
            Log::error('HUB_UMKM_SELLER_ID tidak diatur di file .env. Skrip tidak bisa melanjutkan.');
            return null;
        }

        try {
            $endpoint = $this->baseUrl . '/seller/' . $this->sellerId . '/products'; 
            
            $response = Http::get($endpoint);

            if (!$response->successful()) {
                Log::error('Gagal mengambil daftar produk dari Hub UMKM', [
                    'endpoint_called' => $endpoint,
                    'status' => $response->status(), 
                    'body' => $response->json() ?? $response->body()
                ]);
                return null;
            }

            $responseData = $response->json();
            return $responseData['data']['products'] ?? [];
        } catch (\Exception $e) {
            Log::error('EXCEPTION SAAT MENGAMBIL PRODUK DARI HUB', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function deactivateOrphanProductOnHub(array $orphanHubData)
    {
        $payload = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'seller_product_id' => (string) ($orphanHubData['seller_product_id'] ?? ''),
            'name' => $orphanHubData['name'] ?? 'Product Deleted',
            'price' => (float) ($orphanHubData['price'] ?? 0),
            'category_id' => (float) ($orphanHubData['hub_category_id'] ?? $orphanHubData['category_id'] ?? 0),
            'stock' => 0,
            'is_active' => false,
        ];

        Log::info('MENONAKTIFKAN PRODUK YATIM DI HUB:', $payload);
        Http::post($this->baseUrl . '/product/sync', $payload);
    }


    public function fetchAndSaveNewOrders(): array
    {
        if (!$this->sellerId) {
            $errorMessage = 'HUB_UMKM_SELLER_ID tidak diatur di file .env. Sinkronisasi tidak bisa dilanjutkan.';
            Log::error($errorMessage);
            return ['success' => false, 'message' => $errorMessage];
        }

        $endpoint = $this->baseUrl . '/seller/' . $this->sellerId . '/orders'; 

        try {
            Log::info('Mencoba mengambil pesanan dari Hub. Endpoint: ' . $endpoint);
            
            $response = Http::get($endpoint);

            if (!$response->successful()) {
                $errorMessage = 'Gagal mengambil data pesanan dari Hub. Status: ' . $response->status();
                Log::error($errorMessage, ['body' => $response->body()]);
                return ['success' => false, 'message' => $errorMessage];
            }

            $ordersFromHub = $response->json()['data'] ?? [];
            if (empty($ordersFromHub)) {
                return ['success' => true, 'new_orders' => 0];
            }

            $newOrdersCount = 0;
            DB::beginTransaction();

            foreach ($ordersFromHub as $hubOrder) {
                if (Order::where('hub_order_id', $hubOrder['order_number'])->exists()) {
                    continue;
                }

                $user = User::where('is_admin', true)->first() ?? User::first();
                if (!$user) {
                    throw new \Exception('Tidak ada user di database untuk assign pesanan.');
                }

                $order = Order::create([
                    'hub_order_id' => $hubOrder['order_number'],
                    'user_id' => $user->id,
                    'grand_total' => $hubOrder['total_amount'],
                    'payment_method' => 'hub_umkm',
                    'payment_status' => 'paid',
                    'status' => 'new',
                    'notes' => $hubOrder['notes'] ?? '',
                ]);

                Address::create([
                    'order_id' => $order->id,
                    'street_address' => $hubOrder['shipping_address'],
                    'city' => $hubOrder['shipping_city'],
                    'state' => $hubOrder['shipping_province'],
                    'zip_code' => $hubOrder['shipping_postal_code'],
                    'first_name' => $hubOrder['customer_name'] ?? 'Pelanggan',
                    'last_name' => 'Hub',
                    'phone' => $hubOrder['customer_phone'] ?? '000',
                ]);

                foreach ($hubOrder['items'] as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_amount' => $item['price'],
                        'total_amount' => $item['price'] * $item['quantity'],
                    ]);
                }
                $newOrdersCount++;
            }
            
            DB::commit();
            return ['success' => true, 'new_orders' => $newOrdersCount];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exception saat mengambil dan menyimpan pesanan dari Hub', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem saat menyimpan pesanan.'];
        }
    }

    public function deleteProductOnHub(string $hubProductId): array
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            Log::error('Gagal menghapus produk dari Hub: Tidak ada Access Token.');
            return ['error' => 'No access token'];
        }

        try {
            $response = Http::withToken($accessToken)->delete($this->baseUrl . '/products/' . $hubProductId);

            if (!$response->successful()) {
                Log::error('Gagal menghapus produk dari Hub UMKM', [
                    'hub_product_id' => $hubProductId,
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body()
                ]);
                return ['error' => 'Delete failed', 'status' => $response->status()];
            }

            Log::info('Produk berhasil dihapus dari Hub UMKM', ['hub_product_id' => $hubProductId, 'response' => $response->json()]);
            return $response->json();

        } catch (\Exception $e) {
            Log::error('EXCEPTION SAAT MENGHAPUS PRODUK DARI HUB UMKM', ['message' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }
}