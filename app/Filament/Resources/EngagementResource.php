<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EngagementResource\Pages;
use App\Filament\Resources\EngagementResource\RelationManagers;
use App\Models\Engagement;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Models\Entity;
use App\Models\Product;

class EngagementResource extends Resource
{
    protected static ?string $model = Engagement::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('entityid')
                ->label('Entidad')
                ->relationship('entity', 'name')
                ->required(),

            TextInput::make('title')
                ->label('Engagement title')
                ->required(),

            TextInput::make('status')
                ->default('pending'),
            Repeater::make('products')
             ->label('Products or Services')
    ->relationship()
    ->schema([
        Select::make('product_id')
    ->label('Product or Service')
    ->options(Product::all()->pluck('name', 'id')->filter(function ($value) {
        return !is_null($value) && $value !== ''; // Filtra valores nulos o vacíos
    }))
    ->reactive()
    ->afterStateUpdated(function ($state, callable $set) {
        $product = Product::find($state);
        if ($product) {
            $set('unit_price', $product->unit_price);
        }
    })
    ->required(),

        TextInput::make('quantity')
            ->numeric()
            ->default(1)
            ->required()
            ->reactive()
            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                $quantity = (int)$state;
                $price = (float)$get('unit_price');
                $set('subtotal', $quantity * $price);
            }),

        TextInput::make('unit_price')
            ->numeric()
            ->required()
            ->reactive()
            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                $quantity = (int)$get('quantity');
                $price = (float)$state;
                $set('subtotal', $quantity * $price);
            }),

        TextInput::make('subtotal')
            ->numeric()
            ->disabled(),
    ])
    ->columns(4)
    ->createItemButtonLabel('Add product or service')
    ->defaultItems(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Title')->searchable(),
                TextColumn::make('entity.name')->label('Entity')->sortable(),
            TextColumn::make('status')->label('Status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEngagements::route('/'),
            'create' => Pages\CreateEngagement::route('/create'),
            'edit' => Pages\EditEngagement::route('/{record}/edit'),
        ];
    } 
    // VERY IMPORTANT! to sync pivot fields after create/update
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $products = $data['products'] ?? [];
        unset($data['products']);
        $this->productsData = $products;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $products = $data['products'] ?? [];
        unset($data['products']);
        $this->productsData = $products;
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->saveProducts();
    }

    protected function afterSave(): void
    {
        $this->saveProducts();

    }

    protected function saveProducts(): void
    {
        if (!isset($this->productsData)) {
            return;
        }

        $productsSyncData = [];

        foreach ($this->productsData as $product) {
            $productsSyncData[$product['product_id']] = [
                'quantity' => $product['quantity'],
                'unit_price' => $product['unit_price'],
                'subtotal' => $product['subtotal'],
            ];
        }

        $this->record->products()->sync($productsSyncData);
    }
    
}
