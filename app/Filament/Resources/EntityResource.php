<?php

namespace App\Filament\Resources;
use Filament\Resources\Pages\Page;
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
use App\Filament\Resources\EntityResource\Pages\CreateEntity;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput\Mask;

class EntityResource extends Resource
{
    protected static ?string $model = Entity::class;

    protected static ?string $navigationIcon = 'heroicon-o-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->reactive(),
                TextInput::make('phone')
                    ->mask(fn (Mask $mask) => $mask->pattern('000-000-0000'))
                    ->label('Phone'),
                TextInput::make('email')->email(),
                Select::make('entity_status')
                        ->options([
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                            'pending' => 'Pending',
                        ])
                        ->label('Entity Status'),
                        Section::make('Addresses')
                        ->schema([
                            TextInput::make('billing_address')
                                ->label('Billing Address')
                                ->columnSpanFull(),
        
                            TextInput::make('business_address')
                                ->label('Business Address')
                                ->columnSpanFull(),
                        ])
                        ->columns(1),
                        Section::make('Additional Information')
                ->schema([
                    TextInput::make('document_folder_link')
                        ->label('Document Folder Link')
                        ->url(),

                    DatePicker::make('incorporation_date')
                        ->label('Incorporation Date'),

                    TextInput::make('formally_known_as')
                        ->label('Formally Known As'),

                    TextInput::make('doing_business_as')
                        ->label('Doing Business As'),

                    DatePicker::make('effective_entity_type_date')
                        ->label('Effective Entity Type Date'),

                    TextInput::make('state_of_registration')
                        ->label('State of Registration'),

                    TextInput::make('industry')
                        ->label('Industry'),

                    TextInput::make('number_of_employees')
                        ->numeric()
                        ->label('Number of Employees'),

                    TextInput::make('revenue_range')
                        ->label('Revenue Range'),

                    Select::make('assigned_am')
                        ->relationship('assignedAm', 'name') // assuming a relation
                        ->searchable()
                        ->label('Assigned AM'),

                    Select::make('assigned_tm')
                        ->relationship('assignedTm', 'name')
                        ->searchable()
                        ->label('Assigned TM'),

                    Select::make('assigned_sa')
                        ->relationship('assignedSa', 'name')
                        ->searchable()
                        ->label('Assigned SA'),
                ])
                ->columns(2),
                TextArea::make('notes')->columnSpanFull(),

             // Shareholders Section
             Section::make('Shareholders')
             ->schema([
                 Repeater::make('shareholders')
                     ->label('Shareholders')
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
                                     return Contact::all()->pluck('first_name', 'id');
                                 }

                                 if ($type === Entity::class) {
                                     return Entity::all()->pluck('name', 'id');
                                 }

                                 return [];
                             })
                             ->searchable()
                             ->reactive()
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
             ])
             ->collapsible()
             ->label('Shareholder Information'),
            ]);
    
            
    }
    public static function mutateFormDataBeforeSave(array $data): array
{
    // Guardamos los accionistas para después y los eliminamos del array principal
    session(['temp_shareholders' => $data['shareholders'] ?? []]);

    unset($data['shareholders']); // eliminamos para que no intente guardar automáticamente

    return $data;
}
public static function afterSave(Model $record): void
{
    $shareholders = session('temp_shareholders', []);

    // Borramos los existentes primero
    DB::table('shareholders')->where('entities_id', $record->id)->delete();

    // Insertamos los nuevos accionistas
    foreach ($shareholders as $shareholder) {
        DB::table('shareholders')->insert([
            'entities_id' => $record->id,
            'shareholdable_type' => $accionista['shareholdable_type'],
            'shareholdable_id' => $accionista['shareholdable_id'],
            'percentage' => $accionista['percentage'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Limpiamos la sesión
    session()->forget('temp_shareholders');
}

public static function afterCreate(CreateRecord $operation, Model $record): void
{
    self::syncShareholders($record, $operation->getForm()->getState()['shareholders'] ?? []);
}

public static function afterEdit(EditRecord $operation, Model $record): void
{
    // Limpia los anteriores
    $record->shareholders()->delete();

    self::syncShareholders($record, $operation->getForm()->getState()['shareholders'] ?? []);
}

protected static function syncShareholders(Entity $entity, array $shareholders): void
{
    foreach ($shareholders as $item) {
        if (!isset($item['shareholdable_type'], $item['shareholdable_id'], $item['percentage'])) {
            continue; // validación mínima
        }

        $entity->shareholders()->create([
            'shareholdable_type' => $item['shareholdable_type'],
            'shareholdable_id'   => $item['shareholdable_id'],
            'percentage'      => $item['percentage'],
        ]);
    }
}
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->searchable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone')
                ->formatStateUsing(fn ($state) => preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $state))
                ->sortable()
                ->searchable(),
                /*bles\Columns\TextColumn::make('shareholders_list')
                    ->label('Shareholders')
                    ->formatStateUsing(function ($record) {
                    return $record->shareholders
                        ->map(fn ($a) => optional($a->shareholdable)->name)
                        ->filter()
                        ->implode(', ');
                    })
                    ->wrap()
                    ->searchable(),*/
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
    public static function getViewFormSchema(): array
{
    return [
        Section::make('Detail View')
            ->schema([
                TextInput::make('name')->label('Name'),
                TextInput::make('tax_id')->label('Tax Id'),
                // otros campos...
            ]),

        Section::make('ShareHolders')
            ->schema([
                Repeater::make('shareholders')
                    ->label('Shareholders')
                    ->schema([
                        Select::make('shareholdable_type')
                            ->label('Type')
                            ->options([
                                \App\Models\Contacto::class => 'Contact',
                                \App\Models\Entidad::class => 'Entity',
                            ])
                            ->disabled(),

                        Select::make('shareholdable_id')
                            ->label('Shareholder Name')
                            ->options(function (callable $get, callable $set, $state) {
                                $type = $get('shareholdable_type');

                                if ($type === \App\Models\Contacto::class) {
                                    return \App\Models\Contacto::pluck('first_name', 'id');
                                }

                                if ($type === \App\Models\Entidad::class) {
                                    return \App\Models\Entidad::pluck('name', 'id');
                                }

                                return [];
                            })
                            ->disabled(),

                        TextInput::make('percentage')
                            ->label('Percentage')
                            ->disabled(),
                    ])
                    ->disabled() // el Repeater completo en modo solo lectura
                    ->columnSpanFull(),
            ]),
    ];
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
