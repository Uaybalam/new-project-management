<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntityResource\Pages;
use App\Filament\Resources\EntityResource\RelationManagers;
use App\Models\Entity;
use App\Models\Contact;
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
use Filament\Forms\Components\Textarea;

class EntityResource extends Resource
{
    protected static ?string $model = Entity::class;

    protected static ?string $navigationIcon = 'heroicon-o-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('tax_id'),
                TextInput::make('type'),
                TextInput::make('phone'),
                TextInput::make('email')->email(),
                TextInput::make('address'),
                TextArea::make('notes')->columnSpanFull(),

            // Sección de shareholders
            Repeater::make('shareholders')
            ->label('Shareholders')
            ->relationship('shareholders')
            ->schema([
                Select::make('shareholdable_type')
                    ->label('Shareholder Type')
                    ->options([
                        Contact::class => 'Contact',
                        Entity::class => 'Entity',
                    ])
                    ->required()
                    ->reactive(),

                Select::make('shareholdable_id')
                    ->label('Shareholder')
                    ->options(function (callable $get) {
                        $type = $get('shareholdable_type');

                        if ($type === Contact::class) {
                            return Contact::all()->pluck('name', 'id');
                        }

                        if ($type === Entity::class) {
                            return Entity::all()->pluck('name', 'id');
                        }

                        return [];
                    })
                    ->searchable()
                    ->required(),

                TextInput::make('percentage')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->label('% Ownership')
                    ->suffix('%'),
            ])
            ->columns(3)
            ->collapsible()
            ->defaultItems(1)
            ->createItemButtonLabel('Add Shareholder'),
            ]);
    
            
    }
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone'),
            ])
            ->defaultSort('name')
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
            'index' => Pages\ListEntities::route('/'),
            'create' => Pages\CreateEntity::route('/create'),
            'edit' => Pages\EditEntity::route('/{record}/edit'),
        ];
    }    
}
