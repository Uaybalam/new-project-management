<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Models\Contact;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput\Mask;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static function getNavigationGroup(): ?string
    {
        return __('Management');
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('first_name')->required(),
            Forms\Components\TextInput::make('last_name')->required(),
            Forms\Components\TextInput::make('email')->email()->required(),
            Forms\Components\TextInput::make('phone')->required()
            ->mask(fn (Mask $mask) => $mask->pattern('000-000-0000')),
            Forms\Components\Textarea::make('address'),
            TextInput::make('ssn')
                ->label('SSN')
                ->mask(fn (Mask $mask) => $mask->pattern('000-00-0000')),

            FileUpload::make('ssn_itin_copy')
                ->label('SSN/ITIN Copy')
                ->disk('public'),

            TextInput::make('drivers_license')
                ->label('Drivers License'),

            FileUpload::make('drivers_license_copy')
                ->label('Drivers License Copy')
                ->disk('public'),

            DatePicker::make('date_of_birth')
                ->label('Date of Birth'),

            TextInput::make('pit_filing_status')
                ->label('PIT Filing Status'),

            FileUpload::make('pit_copy')
                ->label('PIT Copy')
                ->disk('public'),

            TextInput::make('spouse_first_name')
                ->label('Spouse First Name'),

            TextInput::make('spouse_last_name')
                ->label('Spouse Last Name'),

            TextInput::make('spouse_ssn_itin')
                ->label('Spouse SSN/ITIN')
                ->mask(fn (Mask $mask) => $mask->pattern('000-00-0000')),
            Forms\Components\Textarea::make('notes')->rows(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('first_name')->searchable(),
            Tables\Columns\TextColumn::make('last_name')->searchable(),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\TextColumn::make('phone'),
            Tables\Columns\TextColumn::make('company'),
        ])
        ->defaultSort('first_name')
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }    
}
