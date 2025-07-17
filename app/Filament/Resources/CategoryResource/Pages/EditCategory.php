<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use App\Services\HubUmkmService;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model; 

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $statusSebelumnya = $record->is_active;

        $record->update($data);
        $statusSekarang = $record->is_active;

        $hubService = new HubUmkmService();
        $hubService->syncCategory($record);

        if ($statusSebelumnya != $statusSekarang) {
            
            $productsToSync = $record->products;

            if ($productsToSync->isNotEmpty()) {
                
                $statusText = $statusSekarang ? 'Mengaktifkan' : 'Menonaktifkan';
                
                Notification::make()
                    ->title('Proses Produk Terkait')
                    ->body($statusText . ' ' . $productsToSync->count() . ' produk terkait di Hub...')
                    ->info()
                    ->send();

                foreach ($productsToSync as $product) {
                    $product->is_active = $statusSekarang;
                    $product->saveQuietly();
                    $hubService->syncProduct($product);
                }

                Notification::make()
                    ->title('Sinkronisasi Selesai')
                    ->body('Semua produk terkait berhasil disinkronkan!')
                    ->success()
                    ->send();
            }
        }
        
        return $record;
    }
}