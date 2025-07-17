<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\OrderResource;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('user.name')->label('Customer'),
                TextColumn::make('grand_total')->money('IDR'),
                TextColumn::make('status'),
                TextColumn::make('created_at')->dateTime(),
            ]);
    }
}