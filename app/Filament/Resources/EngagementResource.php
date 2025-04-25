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

class EngagementResource extends Resource
{
    protected static ?string $model = Engagement::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static function getNavigationGroup(): ?string
    {
        return __('Management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('entities_id')
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
    ->relationship() // usa la relación `productos()` definida en el modelo
    ->schema([
        Select::make('product_id')
            ->label('Product or Service')
            ->options(\App\Models\Product::all()->pluck('name', 'id'))
            ->reactive()
            ->afterStateUpdated(function ($state, callable $set) {
                $product = \App\Models\Product::find($state);
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
            TextColumn::make('entitie.name')->label('Entity')->sortable(),
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
}
