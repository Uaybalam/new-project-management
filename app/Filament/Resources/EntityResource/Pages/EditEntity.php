<?php

namespace App\Filament\Resources\EntityResource\Pages;

use App\Filament\Resources\EntityResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Shareholder;

class EditEntity extends EditRecord
{
    protected static string $resource = EntityResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // First update the entity
        $record->update($data);

        // Delete old shareholders
        $record->shareholders()->delete();

        // Save new ones
        if (!empty($data['shareholders'])) {
            foreach ($data['shareholders'] as $item) {
                if (!isset($item['shareholdable_type'], $item['shareholdable_id'], $item['percentage'])) {
                    continue;
                }

                Shareholder::create([
                    'entity_id' => $record->id,
                    'shareholdable_type' => $item['shareholdable_type'],
                    'shareholdable_id' => $item['shareholdable_id'],
                    'percentage' => $item['percentage'],
                ]);
            }
        }

        return $record;
    }
}
