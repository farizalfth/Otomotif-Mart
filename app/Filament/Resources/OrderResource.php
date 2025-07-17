<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Number;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Order Information')->schema([
                        Select::make('user_id')
                        ->label('Customer')
                        ->relationship('user', 'name')
                        ->preload()
                        ->searchable()
                        ->required(),

                        Select::make('payment_method')
                        ->options([
                            'cod' => 'Cash On Delivery',
                            'bank_transfer' => 'Bank Transfer',
                            'gopay' => 'GoPay',
                            'ovo' => 'OVO',
                            'dana' => 'DANA',
                            'shopee_pay' => 'ShopeePay',
                        ])
                        ->required(),

                        Select::make('payment_status')
                        ->options([
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'failed' => 'Failed',
                        ])
                        ->default('pending')
                        ->required(),

                        FileUpload::make('payment_proof')
                        ->label('Bukti Pembayaran')
                        ->image()
                        ->imageEditor()
                        ->maxSize(5120) 
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->directory('payment-proofs')
                        ->visibility('public')
                        ->columnSpanFull(),

                        ToggleButtons::make('status')
                        ->inline()
                        ->default('new')
                        ->options([
                            'new' => 'New',
                            'processing' => 'Processing',
                            'shipped' => 'Shipped',
                            'delivered' => 'Delivered',
                            'cancelled' => 'Cancelled',
                        ])
                        ->colors([
                            'new' => 'info',
                            'processing' => 'warning',
                            'shipped' => 'success',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                        ])
                        ->icons([
                            'new' => 'heroicon-m-sparkles',
                            'processing' => 'heroicon-m-arrow-path',
                            'shipped' => 'heroicon-m-truck',
                            'delivered' => 'heroicon-m-check-badge',
                            'cancelled' => 'heroicon-m-x-circle',
                        ]),

                        Textarea::make('notes')
                        ->columnSpanFull()

                    ])->columns(2),

                    Section::make('Shipping Information')->schema([
                        Select::make('shipping_method')
                        ->options([
                            'jne' => 'JNE',
                            'j&t' => 'J&T Express',
                            'tiki' => 'TIKI',
                            'pos_indonesia' => 'Pos Indonesia',
                            'sicepat' => 'SiCepat',
                            'anteraja' => 'AnterAja',
                            'ninja_express' => 'Ninja Express',
                            'grab_express' => 'Grab Express',
                            'gojek' => 'GoSend',
                            'shopee_express' => 'Shopee Express',
                            'lazada_express' => 'Lazada Express',
                            'lion_parcel' => 'Lion Parcel',
                            'wahana' => 'Wahana',
                            'rpm' => 'RPM Express',
                            'ide' => 'ID Express',
                        ])
                        ->reactive()
                        ->required(),

                        TextInput::make('tracking_number')
                        ->label('Nomor Resi')
                        ->placeholder('Masukkan nomor resi pengiriman')
                        ->visible(fn (Get $get) => in_array($get('status'), ['shipped', 'delivered']))
                        ->required(fn (Get $get) => in_array($get('status'), ['shipped', 'delivered'])),

                    ])->columns(2),

                    Section::make('Order Items')->schema([
                        Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->columnSpan(4)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, Set $set) => $set('unit_amount', Product::find($state)?->price ?? 0))
                            ->afterStateUpdated(fn ($state, Set $set) => $set('total_amount', Product::find($state)?->price ?? 0)),

                            TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->columnSpan(2)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, Set $set, Get $get) => $set('total_amount', $state*$get('unit_amount'))),

                            TextInput::make('unit_amount')
                            ->numeric()
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(3),

                            TextInput::make('total_amount')
                            ->numeric()
                            ->required()
                            ->dehydrated()
                            ->columnSpan(3),

                        ])->columns(12),

                        Placeholder::make('grand_total_placeholder')
                        ->label('Grand Total')
                        ->content(function (Get $get, Set $set) {
                            $total = 0;
                            if(!$repeaters = $get('items')) {
                                return $total;
                            }

                            foreach($repeaters as $key => $repeaters) {
                                $total += $get("items.{$key}.total_amount");
                            }
                            
                            $set('grand_total', $total);
                            return Number::currency($total, 'IDR');
                        }),

                        Hidden::make('grand_total')
                        ->default(0)
                    ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                ->label('Order ID')
                ->sortable()
                ->searchable(),

                TextColumn::make('user.name')
                ->label('Customer')
                ->sortable()
                ->searchable(),

                TextColumn::make('grand_total')
                ->numeric()
                ->sortable()
                ->money('IDR')
                ->searchable(),

                TextColumn::make('payment_method')
                ->searchable()
                ->sortable(),

                TextColumn::make('payment_status')
                ->searchable()
                ->sortable(),

                ImageColumn::make('payment_proof')
                ->label('Bukti Pembayaran')
                ->disk('public')
                ->url(fn ($record) => $record->payment_proof ? asset('storage/' . $record->payment_proof) : null)
                ->circular()
                ->size(40)
                ->toggleable(),




                TextColumn::make('shipping_method')
                ->searchable()
                ->sortable(),

                TextColumn::make('tracking_number')
                ->label('Nomor Resi')
                ->searchable()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

                SelectColumn::make('status')
                ->options([
                    'new' => 'New',
                    'processing' => 'Processing',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ])
                ->searchable()
                ->sortable(),

                TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AddressRelationManager::class
        ];
    }

    public static function getNavigationBadge(): ?string {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null {
        return static::getModel()::count() > 10 ? 'success' : 'danger';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}