<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use App\Services\HubUmkmService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Actions\Action::make('syncOrders')
                ->label('Sinkronkan Pesanan dari Hub')
                ->icon('heroicon-o-arrow-path')
                ->action('syncOrdersFromHub'),
                
            Actions\CreateAction::make()
                ->label('New order'),
        ];
    }
    

    public function syncOrdersFromHub(HubUmkmService $hubService)
    {
        Notification::make()->title('Memulai sinkronisasi pesanan...')->info()->send();

        $result = $hubService->fetchAndSaveNewOrders();

        if ($result['success']) {
            $message = $result['new_orders'] > 0
                ? $result['new_orders'] . ' pesanan baru berhasil disimpan.'
                : 'Tidak ada pesanan baru yang ditemukan.';

            Notification::make()->title('Sinkronisasi Selesai')->body($message)->success()->send();
            
            $this->dispatch('$refresh');
        } else {
            Notification::make()->title('Sinkronisasi Gagal')->body($result['message'])->danger()->send();
        }
    }


    protected function getHeaderWidgets(): array {
        return [
            OrderStats::class
        ];
    }

    public function getTabs(): array {
        return [
            null => Tab::make('All'),
            'new' => Tab::make()->query(fn ($query) => $query->where('status', 'new')),
            'processing' => Tab::make()->query(fn ($query) => $query->where('status', 'processing')),
            'shipped' => Tab::make()->query(fn ($query) => $query->where('status', 'shipped')),
            'delivered' => Tab::make()->query(fn ($query) => $query->where('status', 'delivered')),
            'cancelled' => Tab::make()->query(fn ($query) => $query->where('status', 'cancelled')),
        ];
    }
}